<?php
/**
 * Notification service provider.
 *
 * Registers the notification manager into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Notifications;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the notification manager.
 */
class NotificationServiceProvider extends AbstractServiceProvider {

	/**
	 * Register notification bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			NotificationManagerInterface::class,
			fn() => new NotificationManager()
		);
	}

	/**
	 * Bootstrap the notification service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
