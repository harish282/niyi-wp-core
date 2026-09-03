<?php
/**
 * Cache facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\Cache\CacheInterface;

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
