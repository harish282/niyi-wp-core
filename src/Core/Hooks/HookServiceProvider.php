<?php
/**
 * Hook service provider.
 *
 * Registers the hook manager into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Hooks;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the hook manager.
 */
class HookServiceProvider extends AbstractServiceProvider {

	/**
	 * Register hook bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			HookManagerInterface::class,
			fn() => new HookManager()
		);
	}

	/**
	 * Bootstrap the hook service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
