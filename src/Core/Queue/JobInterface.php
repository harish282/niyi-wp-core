<?php
/**
 * Job contract.
 *
 * Represents a unit of work that can be queued and processed asynchronously.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Queue;

/**
 * Contract for queued jobs.
 */
interface JobInterface {

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle(): void;

	/**
	 * Unique job identifier.
	 *
	 * @return string
	 */
	public function job_id(): string;

	/**
	 * Return serializable payload data for the queue.
	 *
	 * @return array<string, mixed>
	 */
	public function to_payload(): array;

	/**
	 * Restore job state from a serialized payload.
	 *
	 * @param array<string, mixed> $data Payload data.
	 * @return void
	 */
	public function from_payload( array $data ): void;
}
