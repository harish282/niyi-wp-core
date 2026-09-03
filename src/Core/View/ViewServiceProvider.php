<?php
/**
 * View service provider.
 *
 * Registers the view service into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\View;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the view service.
 */
class ViewServiceProvider extends AbstractServiceProvider {

	/**
	 * Register view bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			ViewInterface::class,
			function () {
				return new View( NIYI_WOO_SMART_UPSELLS_DIR . 'src' );
			}
		);
	}

	/**
	 * Bootstrap the view service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
