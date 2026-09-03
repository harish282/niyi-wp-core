<?php
/**
 * Scheduler service provider.
 *
 * Registers the scheduler into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Scheduler;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
