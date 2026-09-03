<?php
/**
 * Scheduled task definition.
 *
 * Value object that represents a task registered with the scheduler.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Scheduler;

/**
 * Represents a scheduled task.
 */
class ScheduledTask {

	/**
	 * Action hook name.
	 *
	 * @var string
	 */
	private string $hook;

	/**
	 * Cron schedule slug or 'once' for single-run tasks.
	 *
	 * @var string
	 */
	private string $interval;

	/**
	 * Arguments passed to the hook.
	 *
	 * @var array
	 */
	private array $arguments;

	/**
	 * Unix timestamp for one-time tasks.
	 *
	 * @var int|null
	 */
	private ?int $timestamp = null;

	/**
	 * Build a recurring scheduled task.
	 *
	 * @param string $hook      Action hook name.
	 * @param string $interval  Cron schedule slug.
	 * @param array  $arguments Hook arguments.
	 */
	public function __construct( string $hook, string $interval, array $arguments = array() ) {
		$this->hook      = $hook;
		$this->interval  = $interval;
		$this->arguments = $arguments;
	}

	/**
	 * Build a one-time scheduled task.
	 *
	 * @param string $hook      Action hook name.
	 * @param int    $timestamp Unix timestamp.
	 * @param array  $arguments Hook arguments.
	 * @return static
	 */
	public static function once( string $hook, int $timestamp, array $arguments = array() ): static {
		$task            = new static( $hook, 'once', $arguments );
		$task->timestamp = $timestamp;

		return $task;
	}

	/**
	 * Action hook name.
	 *
	 * @return string
	 */
	public function hook(): string {
		return $this->hook;
	}

	/**
	 * Cron schedule slug.
	 *
	 * @return string
	 */
	public function interval(): string {
		return $this->interval;
	}

	/**
	 * Hook arguments.
	 *
	 * @return array
	 */
	public function arguments(): array {
		return $this->arguments;
	}

	/**
	 * Unix timestamp for one-time tasks.
	 *
	 * @return int|null
	 */
	public function timestamp(): ?int {
		return $this->timestamp;
	}

	/**
	 * Whether this is a one-time task.
	 *
	 * @return bool
	 */
	public function is_once(): bool {
		return 'once' === $this->interval;
	}
}
