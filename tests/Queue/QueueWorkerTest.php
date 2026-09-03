<?php
/**
 * Tests for the queue worker.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Queue;

use NiyiWPCore\Core\Queue\Job;
use NiyiWPCore\Core\Queue\QueueInterface;
use NiyiWPCore\Core\Queue\QueueWorker;
use NiyiWPCore\Tests\Support\RecordingWpdb;
use NiyiWPCore\Tests\Support\SpyLogger;
use NiyiWPCore\Tests\TestCase;

/**
 * Job that records how it was handled and restored.
 */
class RecordingJob extends Job {

	/**
	 * How many times handle() ran.
	 *
	 * @var int
	 */
	public static int $handled = 0;

	/**
	 * Payload restored through from_payload().
	 *
	 * @var array<string, mixed>
	 */
	public static array $payload = array();

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle(): void {
		self::$handled++;
	}

	/**
	 * Serialize the job payload.
	 *
	 * @return array<string, mixed>
	 */
	public function to_payload(): array {
		return array( 'recorded' => true );
	}

	/**
	 * Restore state from the payload.
	 *
	 * @param array<string, mixed> $data Payload data.
	 * @return void
	 */
	public function from_payload( array $data ): void {
		self::$payload = $data;
	}
}

/**
 * Job that always throws while running.
 */
class ThrowingJob extends Job {

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle(): void {
		throw new \RuntimeException( 'job exploded' );
	}
}

/**
 * Plain class that does not implement the job contract.
 */
class NotAJob {

	/**
	 * Marker method so the class is not empty.
	 *
	 * @return string
	 */
	public function label(): string {
		return 'not-a-job';
	}
}

/**
 * Queue worker tests.
 */
class QueueWorkerTest extends TestCase {

	/**
	 * In-memory database.
	 *
	 * @var RecordingWpdb
	 */
	private RecordingWpdb $db;

	/**
	 * Logger spy.
	 *
	 * @var SpyLogger
	 */
	private SpyLogger $logger;

	/**
	 * Worker under test.
	 *
	 * @var QueueWorker
	 */
	private QueueWorker $worker;

	/**
	 * Queue mock.
	 *
	 * @var QueueInterface&\PHPUnit\Framework\MockObject\MockObject
	 */
	private $queue;

	/**
	 * Set up the worker with fakes.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		RecordingJob::$handled = 0;
		RecordingJob::$payload = array();

		$this->db        = new RecordingWpdb();
		$GLOBALS['wpdb'] = $this->db;
		$this->logger    = new SpyLogger();
		$this->worker    = new QueueWorker( $this->logger );
		$this->queue     = $this->createMock( QueueInterface::class );
	}

	/**
	 * Restore the default global database mock.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		$GLOBALS['wpdb'] = new \wpdb();

		parent::tearDown();
	}

	/**
	 * Test process() is a no-op when the queue is empty.
	 *
	 * @return void
	 */
	public function test_process_with_no_pending_jobs_logs_debug(): void {
		$this->queue->expects( $this->never() )->method( 'fail' );

		$this->worker->process( $this->queue );

		$this->assertContains( 'Queue worker: no pending jobs.', $this->logger->messages_at( 'debug' ) );
	}

	/**
	 * Test process() runs a pending job and completes it.
	 *
	 * @return void
	 */
	public function test_process_handles_and_completes_a_job(): void {
		$this->add_pending_row( 'job_1', RecordingJob::class, '{"foo":"bar"}' );
		$this->queue->expects( $this->never() )->method( 'fail' );

		$this->worker->process( $this->queue );

		$this->assertSame( 1, RecordingJob::$handled );
		$this->assertSame( array( 'foo' => 'bar' ), RecordingJob::$payload );
		$this->assertSame( 'completed', $this->db->tables['wp_niyi_queue'][1]['status'] );
		$this->assertContains( 'Queue worker: job job_1 completed.', $this->logger->messages_at( 'info' ) );
	}

	/**
	 * Test process() handles every pending job.
	 *
	 * @return void
	 */
	public function test_process_handles_every_pending_job(): void {
		$this->add_pending_row( 'job_1', RecordingJob::class );
		$this->add_pending_row( 'job_2', RecordingJob::class );
		$this->queue->expects( $this->never() )->method( 'fail' );

		$this->worker->process( $this->queue );

		$this->assertSame( 2, RecordingJob::$handled );
		$this->assertContains( 'Queue worker: processing 2 pending job(s).', $this->logger->messages_at( 'info' ) );
	}

	/**
	 * Test process() fails a job whose handler throws.
	 *
	 * @return void
	 */
	public function test_process_fails_a_throwing_job(): void {
		$this->add_pending_row( 'job_1', ThrowingJob::class );
		$this->queue->expects( $this->once() )->method( 'fail' )->with( 'job_1', 'job exploded' );

		$this->worker->process( $this->queue );

		$this->assertContains( 'Queue job failed: job_1', $this->logger->messages_at( 'error' ) );
		$this->assertSame( 'pending', $this->db->tables['wp_niyi_queue'][1]['status'] );
	}

	/**
	 * Test process() fails a job class that is not a JobInterface.
	 *
	 * @return void
	 */
	public function test_process_fails_a_job_without_the_interface(): void {
		$this->add_pending_row( 'job_1', NotAJob::class );
		$this->queue->expects( $this->once() )
			->method( 'fail' )
			->with( 'job_1', $this->stringContains( 'must implement JobInterface' ) );

		$this->worker->process( $this->queue );
	}

	/**
	 * Test process() fails a job whose class cannot be resolved.
	 *
	 * @return void
	 */
	public function test_process_fails_an_unknown_job_class(): void {
		$this->add_pending_row( 'job_1', 'Vendor\\Missing\\JobClass' );
		$this->queue->expects( $this->once() )
			->method( 'fail' )
			->with( 'job_1', $this->stringContains( 'not found' ) );

		$this->worker->process( $this->queue );
	}

	/**
	 * Add a pending row to the fake queue table.
	 *
	 * @param string $job_id    Job ID.
	 * @param string $job_class Job class name.
	 * @param string $payload   JSON payload.
	 * @return void
	 */
	private function add_pending_row( string $job_id, string $job_class, string $payload = '{}' ): void {
		$index = count( $this->db->tables['wp_niyi_queue'] ?? array() ) + 1;

		$this->db->tables['wp_niyi_queue'][ $index ] = array(
			'id'           => $index,
			'job_id'       => $job_id,
			'job_class'    => $job_class,
			'payload'      => $payload,
			'status'       => 'pending',
			'attempts'     => 0,
			'available_at' => '2026-01-01 00:00:00',
			'created_at'   => '2026-01-01 00:00:00',
		);
	}
}
