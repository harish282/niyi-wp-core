<?php
/**
 * View service provider.
 *
 * Registers the view service into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\View;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
