<?php
/**
 * Events facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

/**
 * Static proxy for EventDispatcherInterface.
 */
class Events extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWPCore\Core\Events\EventDispatcherInterface::class;
	}
}
