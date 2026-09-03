<?php
/**
 * Scheduler contract.
 *
 * Defines the interface for scheduling recurring and one-time tasks using
 * the WordPress Cron API. Framework-agnostic except for delegating to
 * WordPress cron functions internally.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Scheduler;

/**
 * Contract for scheduler services.
 */
interface SchedulerInterface {

	/**
	 * Schedule a recurring task.
	 *
	 * @param string $hook      Action hook to fire.
	 * @param string $interval Cron schedule slug (e.g. 'hourly', 'daily').
	 * @param array  $arguments Arguments passed to the hook.
	 * @return bool
	 */
	public function schedule( string $hook, string $interval, array $arguments = array() ): bool;

	/**
	 * Schedule a one-time task.
	 *
	 * @param string $hook      Action hook to fire.
	 * @param int    $timestamp Unix timestamp when the task should run.
	 * @param array  $arguments Arguments passed to the hook.
	 * @return bool
	 */
	public function schedule_once( string $hook, int $timestamp, array $arguments = array() ): bool;

	/**
	 * Remove a scheduled task.
	 *
	 * @param string $hook Action hook to unschedule.
	 * @return bool
	 */
	public function unschedule( string $hook ): bool;

	/**
	 * Whether a task is currently scheduled.
	 *
	 * @param string $hook Action hook to check.
	 * @return bool
	 */
	public function is_scheduled( string $hook ): bool;

	/**
	 * Execute a task immediately.
	 *
	 * @param string $hook      Action hook to fire.
	 * @param array  $arguments Arguments passed to the hook.
	 * @return void
	 */
	public function run_now( string $hook, array $arguments = array() ): void;
}
