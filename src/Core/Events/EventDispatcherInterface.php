<?php
/**
 * Event dispatcher contract.
 *
 * Defines the interface for registering listeners and dispatching events
 * across modules without direct coupling.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Events;

/**
 * Contract for event dispatchers.
 */
interface EventDispatcherInterface {

	/**
	 * Register a listener for an event.
	 *
	 * @param string   $event   Event name or class name.
	 * @param callable $listener Listener to invoke when the event is dispatched.
	 * @return void
	 */
	public function listen( string $event, callable $listener ): void;

	/**
	 * Dispatch an event to all registered listeners.
	 *
	 * @param object $event Event object. The event name is derived from the event class.
	 * @return void
	 */
	public function dispatch( object $event ): void;

	/**
	 * Remove all listeners for an event.
	 *
	 * @param string $event Event name or class name.
	 * @return void
	 */
	public function forget( string $event ): void;

	/**
	 * Whether the event has registered listeners.
	 *
	 * @param string $event Event name or class name.
	 * @return bool
	 */
	public function has_listeners( string $event ): bool;
}
