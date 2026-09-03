<?php
/**
 * Event service provider.
 *
 * Registers the event dispatcher into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Events;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
