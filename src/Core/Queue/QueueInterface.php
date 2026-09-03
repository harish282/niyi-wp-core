<?php
/**
 * Queue contract.
 *
 * Defines the public API for dispatching and processing background jobs.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Queue;

use NiyiWPCore\Core\Queue\JobInterface;

/**
 * Contract for queue services.
 */
interface QueueInterface {

	/**
	 * Dispatch a job immediately.
	 *
	 * @param JobInterface $job Job to dispatch.
	 * @return string Job ID.
	 */
	public function dispatch( JobInterface $job ): string;

	/**
	 * Dispatch a job after a delay.
	 *
	 * @param JobInterface $job  Job to dispatch.
	 * @param int          $delay Delay in seconds.
	 * @return string Job ID.
	 */
	public function dispatch_later( JobInterface $job, int $delay ): string;

	/**
	 * Release a reserved job back to the queue with an optional delay.
	 *
	 * @param string $job_id Job ID.
	 * @param int    $delay  Delay in seconds.
	 * @return bool
	 */
	public function release( string $job_id, int $delay = 0 ): bool;

	/**
	 * Delete a job from the queue.
	 *
	 * @param string $job_id Job ID.
	 * @return bool
	 */
	public function delete( string $job_id ): bool;

	/**
	 * Mark a job as failed.
	 *
	 * @param string $job_id   Job ID.
	 * @param string $message  Failure message.
	 * @return bool
	 */
	public function fail( string $job_id, string $message = '' ): bool;

	/**
	 * Number of pending jobs in the queue.
	 *
	 * @return int
	 */
	public function size(): int;
}
