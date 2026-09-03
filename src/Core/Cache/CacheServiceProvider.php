<?php
/**
 * Cache service provider.
 *
 * Registers the cache service into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Cache;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the cache service.
 */
class CacheServiceProvider extends AbstractServiceProvider {

	/**
	 * Register cache bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			CacheInterface::class,
			fn() => new Cache()
		);
	}

	/**
	 * Bootstrap the cache service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
