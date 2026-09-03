<?php
/**
 * Cache service provider.
 *
 * Registers the cache service into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Cache;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
