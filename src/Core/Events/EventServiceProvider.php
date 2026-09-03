<?php
/**
 * Event service provider.
 *
 * Registers the event dispatcher into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Events;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the event dispatcher.
 */
class EventServiceProvider extends AbstractServiceProvider {

	/**
	 * Register event bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			EventDispatcherInterface::class,
			fn() => new EventDispatcher()
		);
	}

	/**
	 * Bootstrap the event service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
