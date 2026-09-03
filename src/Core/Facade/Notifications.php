<?php
/**
 * Notifications facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

/**
 * Static proxy for NotificationManagerInterface.
 */
class Notifications extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWPCore\Core\Notifications\NotificationManagerInterface::class;
	}
}
