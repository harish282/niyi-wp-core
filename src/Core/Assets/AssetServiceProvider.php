<?php
/**
 * Asset service provider.
 *
 * Registers the asset manager into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Assets;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
