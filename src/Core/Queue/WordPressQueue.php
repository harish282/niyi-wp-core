<?php

/**
 * WordPress database-backed queue.
 *
 * Stores jobs in a custom table and provides methods for dispatching,
 * releasing, deleting, failing, and counting jobs.
 *
 * @package NiyiWPCore
 */

declare(strict_types=1);

namespace NiyiWPCore\Core\Queue;

use NiyiWPCore\Core\Queue\QueueInterface;

/**
 * Queue implementation backed by a WordPress custom table.
 */
class WordPressQueue implements QueueInterface
{

	/**
	 * Default queue table name (unprefixed).
	 *
	 * @var string
	 */
	public const TABLE_NAME = 'niyi_queue';

	/**
	 * Resolve the queue table name.
	 *
	 * Uses the NIYI_QUEUE_TABLE constant if defined, otherwise falls back to
	 * the class constant with the WordPress prefix.
	 *
	 * @return string
	 */
	public static function resolve_table(): string
	{
		global $wpdb;

		return $wpdb->prefix . (defined('NIYI_QUEUE_TABLE') && NIYI_QUEUE_TABLE ? NIYI_QUEUE_TABLE : self::TABLE_NAME);
	}

	/**
	 * WordPress database object.
	 *
	 * @var \wpdb
	 */
	private \wpdb $db;

	/**
	 * Table name (prefixed).
	 *
	 * @var string
	 */
	private string $table;

	/**
	 * Build the queue.
	 *
	 * @param \wpdb|null $db Optional database object; uses global $wpdb by default.
	 */
	public function __construct(?\wpdb $db = null)
	{
		$this->db    = $db ?? $GLOBALS['wpdb'];
		$this->table = self::resolve_table();
	}

	/**
	 * Dispatch a job immediately.
	 *
	 * @param JobInterface $job Job to dispatch.
	 * @return string Job ID.
	 */
	public function dispatch(JobInterface $job): string
	{
		return $this->insert($job, 0);
	}

	/**
	 * Dispatch a job after a delay.
	 *
	 * @param JobInterface $job   Job to dispatch.
	 * @param int          $delay Delay in seconds.
	 * @return string Job ID.
	 */
	public function dispatch_later(JobInterface $job, int $delay): string
	{
		return $this->insert($job, $delay);
	}

	/**
	 * Release a reserved job back to the queue with an optional delay.
	 *
	 * @param string $job_id Job ID.
	 * @param int    $delay  Delay in seconds.
	 * @return bool
	 */
	public function release(string $job_id, int $delay = 0): bool
	{
		$available_at = gmdate('Y-m-d H:i:s', time() + $delay);

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$updated = $this->db->update(
			$this->table,
			array(
				'status'       => 'pending',
				'reserved_at'  => null,
				'available_at' => $available_at,
			),
			array('job_id' => $job_id),
			array('%s', '%s', '%s'),
			array('%s')
		);
		// phpcs:enable

		return false !== $updated;
	}

	/**
	 * Delete a job from the queue.
	 *
	 * @param string $job_id Job ID.
	 * @return bool
	 */
	public function delete(string $job_id): bool
	{
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$deleted = $this->db->delete(
			$this->table,
			array('job_id' => $job_id),
			array('%s')
		);
		// phpcs:enable

		return false !== $deleted;
	}

	/**
	 * Mark a job as failed.
	 *
	 * @param string $job_id  Job ID.
	 * @param string $message Failure message.
	 * @return bool
	 */
	public function fail(string $job_id, string $message = ''): bool
	{
		$failed_at = gmdate('Y-m-d H:i:s');

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$updated = $this->db->update(
			$this->table,
			array(
				'status'    => 'failed',
				'failed_at' => $failed_at,
			),
			array('job_id' => $job_id),
			array('%s', '%s'),
			array('%s')
		);
		// phpcs:enable

		return false !== $updated;
	}

	/**
	 * Whether the queue holds a pending job of the given class.
	 *
	 * @param string $job_class Fully-qualified job class name.
	 * @return bool
	 */
	public function has_pending(string $job_class): bool
	{
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$count = $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM {$this->table} WHERE status = %s AND job_class = %s",
				'pending',
				$job_class
			)
		);
		// phpcs:enable

		return (int) $count > 0;
	}

	/**
	 * Delete every pending job of the given class.
	 *
	 * Used when aborting a generation so queued batch jobs stop consuming AI
	 * requests before they run.
	 *
	 * @param string $job_class Fully-qualified job class name.
	 * @return int Number of deleted jobs.
	 */
	public function delete_pending(string $job_class): int
	{
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$deleted = $this->db->query(
			$this->db->prepare(
				"DELETE FROM {$this->table} WHERE status = %s AND job_class = %s",
				'pending',
				$job_class
			)
		);
		// phpcs:enable

		return (int) $deleted;
	}

	/**
	 * Number of pending jobs in the queue.
	 *
	 * @return int
	 */
	public function size(): int
	{
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$count = $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM {$this->table} WHERE status = %s",
				'pending'
			)
		);
		// phpcs:enable

		return (int) $count;
	}

	/**
	 * Insert a job into the queue.
	 *
	 * @param JobInterface $job   Job to insert.
	 * @param int          $delay Delay in seconds.
	 * @return string Job ID.
	 */
	private function insert(JobInterface $job, int $delay): string
	{
		$now          = gmdate('Y-m-d H:i:s');
		$available_at = gmdate('Y-m-d H:i:s', time() + $delay);

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$this->db->insert(
			$this->table,
			array(
				'job_id'       => $job->job_id(),
				'queue'        => 'default',
				'job_class'    => get_class($job),
				'payload'      => wp_json_encode($this->build_payload($job)),
				'status'       => 'pending',
				'attempts'     => 0,
				'available_at' => $available_at,
				'created_at'   => $now,
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
			)
		);
		// phpcs:enable

		return $job->job_id();
	}

	/**
	 * Build a serializable payload from a job.
	 *
	 * @param JobInterface $job Job to serialize.
	 * @return array<string, mixed>
	 */
	private function build_payload(JobInterface $job): array
	{
		return array_merge(
			array('job_class' => get_class($job)),
			$job->to_payload()
		);
	}
}
