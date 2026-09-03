<?php
/**
 * Events facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

/**
 * Static proxy for EventDispatcherInterface.
 */
class Events extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWooSmartUpsells\Core\Events\EventDispatcherInterface::class;
	}
}
