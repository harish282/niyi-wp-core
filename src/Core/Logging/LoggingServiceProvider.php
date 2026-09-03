<?php
/**
 * Logging service provider.
 *
 * Registers the core logger service into the container as a singleton if no
 * other logger has been registered already.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Logging;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the logging service.
 */
class LoggingServiceProvider extends AbstractServiceProvider {

	/**
	 * Register logging bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! $this->container->has( \NiyiWooSmartUpsells\Contracts\LoggerInterface::class ) ) {
			$this->container->singleton(
				\NiyiWooSmartUpsells\Contracts\LoggerInterface::class,
				fn() => new Logger()
			);
		}
	}

	/**
	 * Bootstrap the logging service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
