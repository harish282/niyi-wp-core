<?php
/**
 * Base job class.
 *
 * Provides a default implementation for JobInterface with a UUID-based job ID.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Queue;

/**
 * Base queued job.
 */
abstract class Job implements JobInterface {

	/**
	 * Unique job identifier.
	 *
	 * @var string
	 */
	private string $job_id;

	/**
	 * Build the job with a unique ID.
	 */
	public function __construct() {
		$this->job_id = uniqid( 'job_', true );
	}

	/**
	 * {@inheritDoc}
	 */
	final public function job_id(): string {
		return $this->job_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function to_payload(): array {
		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function from_payload( array $data ): void {
		// Override in subclasses to restore state.
	}
}
