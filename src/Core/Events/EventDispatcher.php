<?php
/**
 * Simple in-memory event dispatcher.
 *
 * Registers listeners and dispatches events synchronously. Listeners are
 * identified by event class name or string event name. Framework-agnostic.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Events;

use NiyiWooSmartUpsells\Core\Events\Event;

/**
 * In-memory event dispatcher.
 */
class EventDispatcher implements EventDispatcherInterface {

	/**
	 * Registered listeners keyed by event name.
	 *
	 * @var array<string, list<callable>>
	 */
	private array $listeners = array();

	/**
	 * Register a listener for an event.
	 *
	 * @param string   $event   Event name or class name.
	 * @param callable $listener Listener to invoke when the event is dispatched.
	 * @return void
	 */
	public function listen( string $event, callable $listener ): void {
		$this->listeners[ $event ][] = $listener;
	}

	/**
	 * Dispatch an event to all registered listeners.
	 *
	 * @param object $event Event object. The event name is derived from the event class.
	 * @return void
	 */
	public function dispatch( object $event ): void {
		$name = $event::class;

		if ( ! $this->has_listeners( $name ) ) {
			return;
		}

		foreach ( $this->listeners[ $name ] as $listener ) {
			$listener( $event );
		}
	}

	/**
	 * Remove all listeners for an event.
	 *
	 * @param string $event Event name or class name.
	 * @return void
	 */
	public function forget( string $event ): void {
		unset( $this->listeners[ $event ] );
	}

	/**
	 * Whether the event has registered listeners.
	 *
	 * @param string $event Event name or class name.
	 * @return bool
	 */
	public function has_listeners( string $event ): bool {
		return ! empty( $this->listeners[ $event ] );
	}
}
