<?php
/**
 * Cache facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\Cache\CacheInterface;

/**
 * Static proxy for CacheInterface.
 */
class Cache extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return CacheInterface::class;
	}
}
