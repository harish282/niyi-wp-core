<?php
/**
 * Core service registrar.
 *
 * Centralizes registration of all core subsystem providers so the bootstrap
 * and future plugins do not need to register each provider manually.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Cache\CacheServiceProvider;
use NiyiWPCore\Core\Events\EventServiceProvider;
use NiyiWPCore\Core\HTTP\HTTPServiceProvider;
use NiyiWPCore\Core\Hooks\HookServiceProvider;
use NiyiWPCore\Core\Logging\LoggingServiceProvider;
use NiyiWPCore\Core\Queue\QueueServiceProvider;
use NiyiWPCore\Core\Scheduler\SchedulerServiceProvider;
use NiyiWPCore\Core\Settings\SettingsServiceProvider;
use NiyiWPCore\Core\Assets\AssetServiceProvider;
use NiyiWPCore\Core\Validation\ValidationServiceProvider;
use NiyiWPCore\Core\Notifications\NotificationServiceProvider;
use NiyiWPCore\Core\View\ViewServiceProvider;

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
