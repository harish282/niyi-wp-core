<?php
/**
 * Tests for Queue service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Queue;

use NiyiWPCore\Core\Queue\Job;
use NiyiWPCore\Core\Queue\WordPressQueue;
use NiyiWPCore\Tests\Support\FakeWpdb;
use NiyiWPCore\Tests\TestCase;

/**
 * Queue tests.
 */
class QueueTest extends TestCase {

	/**
	 * In-memory database.
	 *
	 * @var FakeWpdb
	 */
	private FakeWpdb $db;

	/**
	 * Set up the in-memory database.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->db        = new FakeWpdb();
		$GLOBALS['wpdb'] = $this->db;
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
	 * Test job ID generation.
	 *
	 * @return void
	 */
	public function test_job_generates_unique_id(): void {
		$job = new class extends Job {
			public function handle(): void {
			}
		};

		$this->assertNotEmpty( $job->job_id() );
		$this->assertStringStartsWith( 'job_', $job->job_id() );
	}

	/**
	 * Test job IDs are unique.
	 *
	 * @return void
	 */
	public function test_job_ids_are_unique(): void {
		$job1 = new class extends Job {
			public function handle(): void {
			}
		};
		$job2 = new class extends Job {
			public function handle(): void {
			}
		};

		$this->assertNotSame( $job1->job_id(), $job2->job_id() );
	}

	/**
	 * Test the default job payload is empty.
	 *
	 * @return void
	 */
	public function test_job_payload_defaults_to_empty(): void {
		$job = new class extends Job {
			public function handle(): void {
			}
		};

		$this->assertSame( array(), $job->to_payload() );
	}

	/**
	 * Test from_payload() accepts data without throwing.
	 *
	 * @return void
	 */
	public function test_job_from_payload_accepts_data(): void {
		$job = new class extends Job {
			public function handle(): void {
			}
		};

		$job->from_payload( array( 'key' => 'value' ) );

		$this->assertTrue( true );
	}

	/**
	 * Test WordPressQueue table name constant.
	 *
	 * @return void
	 */
	public function test_queue_has_table_name_constant(): void {
		$this->assertSame( 'niyi_queue', WordPressQueue::TABLE_NAME );
	}

	/**
	 * Test dispatch method exists.
	 *
	 * @return void
	 */
	public function test_dispatch_method_exists(): void {
		$queue = new WordPressQueue();

		$this->assertTrue( method_exists( $queue, 'dispatch' ) );
	}

	/**
	 * Test dispatch_later method exists.
	 *
	 * @return void
	 */
	public function test_dispatch_later_method_exists(): void {
		$queue = new WordPressQueue();

		$this->assertTrue( method_exists( $queue, 'dispatch_later' ) );
	}

	/**
	 * Test release method exists.
	 *
	 * @return void
	 */
	public function test_release_method_exists(): void {
		$queue = new WordPressQueue();

		$this->assertTrue( method_exists( $queue, 'release' ) );
	}

	/**
	 * Test delete method exists.
	 *
	 * @return void
	 */
	public function test_delete_method_exists(): void {
		$queue = new WordPressQueue();

		$this->assertTrue( method_exists( $queue, 'delete' ) );
	}

	/**
	 * Test fail method exists.
	 *
	 * @return void
	 */
	public function test_fail_method_exists(): void {
		$queue = new WordPressQueue();

		$this->assertTrue( method_exists( $queue, 'fail' ) );
	}

	/**
	 * Test size method exists.
	 *
	 * @return void
	 */
	public function test_size_method_exists(): void {
		$queue = new WordPressQueue();

		$this->assertTrue( method_exists( $queue, 'size' ) );
	}

	/**
	 * Test dispatch() stores the job payload with its class name.
	 *
	 * @return void
	 */
	public function test_dispatch_stores_payload_with_job_class(): void {
		$queue = new WordPressQueue( $this->db );
		$job   = new class extends Job {
			public function handle(): void {
			}

			public function to_payload(): array {
				return array( 'recorded' => true );
			}
		};

		$job_id = $queue->dispatch( $job );

		$this->assertSame( $job->job_id(), $job_id );

		$row     = $this->db->tables['wp_niyi_queue'][1];
		$payload = json_decode( $row['payload'], true );

		$this->assertSame( get_class( $job ), $payload['job_class'] );
		$this->assertTrue( $payload['recorded'] );
		$this->assertSame( 'pending', $row['status'] );
	}

	/**
	 * Test dispatch_later() schedules the job for a future time.
	 *
	 * @return void
	 */
	public function test_dispatch_later_sets_future_available_at(): void {
		$queue = new WordPressQueue( $this->db );
		$job   = new class extends Job {
			public function handle(): void {
			}
		};

		$queue->dispatch_later( $job, 3600 );

		$row           = $this->db->tables['wp_niyi_queue'][1];
		$available_at  = strtotime( $row['available_at'] );
		$expected_floor = time() + 3599;

		$this->assertGreaterThanOrEqual( $expected_floor, $available_at );
	}

	/**
	 * Test release() returns a job to pending and updates availability.
	 *
	 * @return void
	 */
	public function test_release_returns_a_job_to_pending(): void {
		$queue = new WordPressQueue( $this->db );
		$job   = new class extends Job {
			public function handle(): void {
			}
		};

		$queue->dispatch( $job );
		$this->assertTrue( $queue->release( $job->job_id(), 60 ) );

		$row = $this->db->tables['wp_niyi_queue'][1];

		$this->assertSame( 'pending', $row['status'] );
		$this->assertNull( $row['reserved_at'] );
	}

	/**
	 * Test delete() removes a job from the queue.
	 *
	 * @return void
	 */
	public function test_delete_removes_a_job(): void {
		$queue = new WordPressQueue( $this->db );
		$job   = new class extends Job {
			public function handle(): void {
			}
		};

		$queue->dispatch( $job );
		$this->assertTrue( $queue->delete( $job->job_id() ) );

		$this->assertArrayNotHasKey( 1, $this->db->tables['wp_niyi_queue'] );
	}

	/**
	 * Test fail() marks a job as failed.
	 *
	 * @return void
	 */
	public function test_fail_marks_a_job_as_failed(): void {
		$queue = new WordPressQueue( $this->db );
		$job   = new class extends Job {
			public function handle(): void {
			}
		};

		$queue->dispatch( $job );
		$this->assertTrue( $queue->fail( $job->job_id(), 'failure message' ) );

		$row = $this->db->tables['wp_niyi_queue'][1];

		$this->assertSame( 'failed', $row['status'] );
	}
}
