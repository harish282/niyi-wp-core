<?php
/**
 * Notifications facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

/**
 * Static proxy for NotificationManagerInterface.
 */
class Notifications extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWooSmartUpsells\Core\Notifications\NotificationManagerInterface::class;
	}
}
