<?php
/**
 * Lightweight WordPress hook manager.
 *
 * Provides a consistent, object-oriented wrapper around the native WordPress
 * Hooks API. Keeps WordPress interaction inside Core and enables dependency
 * injection for testability.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wraps the native WordPress Hooks API.
 */
class HookManager implements HookManagerInterface {

	/**
	 * Register an action hook.
	 *
	 * @param string   $hook         Action hook name.
	 * @param callable $callback     Callback to execute.
	 * @param int      $priority     Hook priority. Default 10.
	 * @param int      $accepted_args Number of arguments passed to the callback. Default 1.
	 * @return void
	 */
	public function action( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		add_action( $hook, $callback, $priority, $accepted_args );
	}

	/**
	 * Register a filter hook.
	 *
	 * @param string   $hook         Filter hook name.
	 * @param callable $callback     Callback to execute.
	 * @param int      $priority     Hook priority. Default 10.
	 * @param int      $accepted_args Number of arguments passed to the callback. Default 1.
	 * @return void
	 */
	public function filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		add_filter( $hook, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove an action hook.
	 *
	 * @param string   $hook     Action hook name.
	 * @param callable $callback Callback to remove.
	 * @param int      $priority Hook priority. Default 10.
	 * @return void
	 */
	public function remove_action( string $hook, callable $callback, int $priority = 10 ): void {
		remove_action( $hook, $callback, $priority );
	}

	/**
	 * Remove a filter hook.
	 *
	 * @param string   $hook     Filter hook name.
	 * @param callable $callback Callback to remove.
	 * @param int      $priority Hook priority. Default 10.
	 * @return void
	 */
	public function remove_filter( string $hook, callable $callback, int $priority = 10 ): void {
		remove_filter( $hook, $callback, $priority );
	}
}
