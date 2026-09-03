<?php
/**
 * Notification service provider.
 *
 * Registers the notification manager into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Notifications;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
