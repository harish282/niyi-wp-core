<?php
/**
 * Event listener contract.
 *
 * Implement this interface to create typed listeners that handle specific
 * event classes.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Events;

use NiyiWPCore\Core\Events\Event;

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
