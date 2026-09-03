<?php
/**
 * Core service registrar.
 *
 * Centralizes registration of all core subsystem providers so the bootstrap
 * and future plugins do not need to register each provider manually.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Cache\CacheServiceProvider;
use NiyiWooSmartUpsells\Core\Events\EventServiceProvider;
use NiyiWooSmartUpsells\Core\HTTP\HTTPServiceProvider;
use NiyiWooSmartUpsells\Core\Hooks\HookServiceProvider;
use NiyiWooSmartUpsells\Core\Logging\LoggingServiceProvider;
use NiyiWooSmartUpsells\Core\Queue\QueueServiceProvider;
use NiyiWooSmartUpsells\Core\Scheduler\SchedulerServiceProvider;
use NiyiWooSmartUpsells\Core\Settings\SettingsServiceProvider;
use NiyiWooSmartUpsells\Core\Assets\AssetServiceProvider;
use NiyiWooSmartUpsells\Core\Validation\ValidationServiceProvider;
use NiyiWooSmartUpsells\Core\Notifications\NotificationServiceProvider;
use NiyiWooSmartUpsells\Core\View\ViewServiceProvider;

/**
 * Registers all core service providers.
 */
class CoreServiceProvider {

	/**
	 * Core service provider classes.
	 *
	 * @var list<class-string>
	 */
	private static array $providers = array(
		SettingsServiceProvider::class,
		LoggingServiceProvider::class,
		EventServiceProvider::class,
		HTTPServiceProvider::class,
		SchedulerServiceProvider::class,
		CacheServiceProvider::class,
		HookServiceProvider::class,
		AssetServiceProvider::class,
		ValidationServiceProvider::class,
		QueueServiceProvider::class,
		NotificationServiceProvider::class,
		ViewServiceProvider::class,
	);

	/**
	 * Register all core providers into the container.
	 *
	 * @param ContainerInterface $container Service container.
	 * @return void
	 */
	public function register( ContainerInterface $container ): void {
		foreach ( static::$providers as $provider ) {
			$container->singleton(
				$provider,
				fn() => new $provider( $container )
			);

			$instance = $container->get( $provider );
			$instance->register();
		}
	}
}
