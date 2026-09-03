<?php
/**
 * Scheduler service provider.
 *
 * Registers the scheduler into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Scheduler;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the scheduler.
 */
class SchedulerServiceProvider extends AbstractServiceProvider {

	/**
	 * Register scheduler bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			SchedulerInterface::class,
			fn() => new Scheduler()
		);
	}

	/**
	 * Bootstrap the scheduler.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
