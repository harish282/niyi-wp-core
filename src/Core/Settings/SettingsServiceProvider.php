<?php
/**
 * Settings service provider.
 *
 * Registers the settings service into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Settings;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the settings service.
 */
class SettingsServiceProvider extends AbstractServiceProvider {

	/**
	 * Register settings bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			SettingsInterface::class,
			fn() => new Settings()
		);
	}

	/**
	 * Bootstrap the settings service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
