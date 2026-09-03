<?php
/**
 * Asset service provider.
 *
 * Registers the asset manager into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Assets;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the asset manager.
 */
class AssetServiceProvider extends AbstractServiceProvider {

	/**
	 * Register asset bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			AssetManagerInterface::class,
			function () {
				return new AssetManager( NIYI_WOO_SMART_UPSELLS_FILE, NIYI_WOO_SMART_UPSELLS_VERSION );
			}
		);
	}

	/**
	 * Bootstrap the asset service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
