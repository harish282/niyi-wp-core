<?php
/**
 * Queue worker.
 *
 * Processes pending jobs from the queue. Intentionally lightweight;
 * future versions may add concurrency, timeouts, and progress reporting.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Queue;

use NiyiWooSmartUpsells\Contracts\LoggerInterface;
use NiyiWooSmartUpsells\Helpers\WpLogger;

/**
 * Processes queued jobs.
 */
class QueueWorker {

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;

	/**
	 * Build the worker.
	 *
	 * @param LoggerInterface|null $logger Optional logger; defaults to WpLogger.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger ?? new WpLogger();
	}

	/**
	 * Process all pending jobs.
	 *
	 * @param QueueInterface $queue Queue to process.
	 * @return void
	 */
	public function process( QueueInterface $queue ): void {
		$jobs = $this->fetch_pending();

		if ( empty( $jobs ) ) {
			$this->logger->debug( 'Queue worker: no pending jobs.' );
			return;
		}

		$this->logger->info( sprintf( 'Queue worker: processing %d pending job(s).', count( $jobs ) ) );

		foreach ( $jobs as $job ) {
			$this->run( $queue, $job );
		}
	}

	/**
	 * Run a single job.
	 *
	 * @param QueueInterface $queue Queue instance.
	 * @param object         $job   Job row from the database.
	 * @return void
	 * @throws \RuntimeException When the job class cannot be resolved.
	 */
	private function run( QueueInterface $queue, object $job ): void {
		$this->logger->info( sprintf( 'Queue worker: processing job %s.', $job->job_id ) );

		try {
			$payload = json_decode( $job->payload ?? '{}', true ) ?: array();

			$instance = $this->resolve_job( $job->job_class );

			if ( ! $instance instanceof JobInterface ) {
				throw new \RuntimeException(
					sprintf( 'Job class %s must implement JobInterface.', $job->job_class )
				);
			}

			$instance->from_payload( $payload );

			$instance->handle();

			$this->complete( $job->job_id );

			$this->logger->info( sprintf( 'Queue worker: job %s completed.', $job->job_id ) );
		} catch ( \Throwable $exception ) {
			$this->logger->error(
				sprintf( 'Queue job failed: %s', $job->job_id ),
				array( 'message' => $exception->getMessage() )
			);

			$queue->fail( $job->job_id, $exception->getMessage() );
		}
	}

	/**
	 * Mark a job as completed.
	 *
	 * @param string $job_id Job ID.
	 * @return void
	 */
	private function complete( string $job_id ): void {
		global $wpdb;

		$completed_at = gmdate( 'Y-m-d H:i:s' );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB
		$wpdb->update(
			WordPressQueue::resolve_table(),
			array(
				'status'       => 'completed',
				'completed_at' => $completed_at,
			),
			array( 'job_id' => $job_id ),
			array( '%s', '%s' ),
			array( '%s' )
		);
		// phpcs:enable
	}

	/**
	 * Fetch pending jobs that are available.
	 *
	 * @return list<object>
	 */
	private function fetch_pending(): array {
		global $wpdb;

		$table = WordPressQueue::resolve_table();
		$now   = gmdate( 'Y-m-d H:i:s' );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE status = %s AND available_at <= %s ORDER BY id ASC LIMIT 10",
				'pending',
				$now
			)
		);
		// phpcs:enable

		return is_array( $results ) ? $results : array();
	}

	/**
	 * Instantiate a job class.
	 *
	 * @param string $class_name Fully-qualified job class name.
	 * @return object
	 * @throws \RuntimeException When the class cannot be instantiated.
	 */
	private function resolve_job( string $class_name ): object {
		if ( ! class_exists( $class_name ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new \RuntimeException( sprintf( 'Job class %s not found.', $class_name ) );
		}

		return new $class_name();
	}
}
