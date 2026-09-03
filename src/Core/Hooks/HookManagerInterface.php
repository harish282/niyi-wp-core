<?php
/**
 * Hook manager contract.
 *
 * Defines the public API for registering and removing WordPress hooks
 * through a consistent, object-oriented interface.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Hooks;

/**
 * Contract for hook managers.
 */
interface HookManagerInterface {

	/**
	 * Register an action hook.
	 *
	 * @param string   $hook         Action hook name.
	 * @param callable $callback     Callback to execute.
	 * @param int      $priority     Hook priority. Default 10.
	 * @param int      $accepted_args Number of arguments passed to the callback. Default 1.
	 * @return void
	 */
	public function action( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void;

	/**
	 * Register a filter hook.
	 *
	 * @param string   $hook         Filter hook name.
	 * @param callable $callback     Callback to execute.
	 * @param int      $priority     Hook priority. Default 10.
	 * @param int      $accepted_args Number of arguments passed to the callback. Default 1.
	 * @return void
	 */
	public function filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void;

	/**
	 * Remove an action hook.
	 *
	 * @param string   $hook     Action hook name.
	 * @param callable $callback Callback to remove.
	 * @param int      $priority Hook priority. Default 10.
	 * @return void
	 */
	public function remove_action( string $hook, callable $callback, int $priority = 10 ): void;

	/**
	 * Remove a filter hook.
	 *
	 * @param string   $hook     Filter hook name.
	 * @param callable $callback Callback to remove.
	 * @param int      $priority Hook priority. Default 10.
	 * @return void
	 */
	public function remove_filter( string $hook, callable $callback, int $priority = 10 ): void;
}
