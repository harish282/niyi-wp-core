<?php
/**
 * Event listener contract.
 *
 * Implement this interface to create typed listeners that handle specific
 * event classes.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Events;

use NiyiWooSmartUpsells\Core\Events\Event;

/**
 * Contract for event listeners.
 */
interface Listener {

	/**
	 * Handle the given event.
	 *
	 * @param Event $event Event object.
	 * @return void
	 */
	public function handle( Event $event ): void;
}
