<?php
/**
 * WordPress Cron wrapper.
 *
 * Provides a clean API for scheduling recurring and one-time tasks using
 * the WordPress Cron API. Prevents duplicate scheduling and normalizes
 * arguments.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Scheduler;

use NiyiWooSmartUpsells\Contracts\LoggerInterface;
use NiyiWooSmartUpsells\Helpers\WpLogger;

/**
 * Wraps the WordPress Cron API.
 */
class Scheduler implements SchedulerInterface {

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;

	/**
	 * Build the scheduler.
	 *
	 * @param LoggerInterface|null $logger Optional logger; defaults to WpLogger.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger ?? new WpLogger();
	}

	/**
	 * Schedule a recurring task.
	 *
	 * @param string $hook      Action hook to fire.
	 * @param string $interval  Cron schedule slug.
	 * @param array  $arguments Arguments passed to the hook.
	 * @return bool
	 * @throws SchedulerException When the interval is invalid.
	 */
	public function schedule( string $hook, string $interval, array $arguments = array() ): bool {
		if ( $this->is_scheduled( $hook ) ) {
			return false;
		}

		if ( ! $this->validInterval( $interval ) ) {
			throw new SchedulerException(
				sprintf( 'Invalid cron interval "%s".', esc_html( $interval ) )
			);
		}

		wp_schedule_event( time(), $interval, $hook, $arguments );

		return true;
	}

	/**
	 * Schedule a one-time task.
	 *
	 * @param string $hook      Action hook to fire.
	 * @param int    $timestamp Unix timestamp.
	 * @param array  $arguments Arguments passed to the hook.
	 * @return bool
	 */
	public function schedule_once( string $hook, int $timestamp, array $arguments = array() ): bool {
		if ( $this->is_scheduled( $hook ) ) {
			return false;
		}

		wp_schedule_single_event( $timestamp, $hook, $arguments );

		return true;
	}

	/**
	 * Remove a scheduled task.
	 *
	 * @param string $hook Action hook to unschedule.
	 * @return bool
	 */
	public function unschedule( string $hook ): bool {
		return wp_clear_scheduled_hook( $hook );
	}

	/**
	 * Whether a task is currently scheduled.
	 *
	 * @param string $hook Action hook to check.
	 * @return bool
	 */
	public function is_scheduled( string $hook ): bool {
		return false !== wp_next_scheduled( $hook );
	}

	/**
	 * Execute a task immediately.
	 *
	 * @param string $hook      Action hook to fire.
	 * @param array  $arguments Arguments passed to the hook.
	 * @return void
	 */
	public function run_now( string $hook, array $arguments = array() ): void {
		$this->logger->info( sprintf( 'Scheduler: running hook "%s" immediately.', $hook ) );

		do_action( $hook, ...$arguments ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Wrapper: the hook is always a plugin-defined hook passed in by the caller.
	}

	/**
	 * Whether the cron interval is valid.
	 *
	 * @param string $interval Cron schedule slug.
	 * @return bool
	 */
	private function validInterval( string $interval ): bool {
		if ( in_array( $interval, array( 'hourly', 'twicedaily', 'daily' ), true ) ) {
			return true;
		}

		$schedules = wp_get_schedules();

		return isset( $schedules[ $interval ] );
	}
}
