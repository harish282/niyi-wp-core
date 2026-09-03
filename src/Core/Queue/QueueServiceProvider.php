<?php
/**
 * Queue service provider.
 *
 * Registers the queue and worker into the container as singletons.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Queue;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
