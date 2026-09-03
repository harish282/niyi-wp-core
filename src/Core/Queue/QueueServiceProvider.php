<?php
/**
 * Queue service provider.
 *
 * Registers the queue and worker into the container as singletons.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Queue;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the queue service.
 */
class QueueServiceProvider extends AbstractServiceProvider {

	/**
	 * Register queue bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			QueueInterface::class,
			fn() => new WordPressQueue()
		);

		$this->container->singleton(
			QueueWorker::class,
			fn() => new QueueWorker()
		);
	}

	/**
	 * Bootstrap the queue service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
