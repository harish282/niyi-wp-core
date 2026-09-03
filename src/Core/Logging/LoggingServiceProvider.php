<?php
/**
 * Logging service provider.
 *
 * Registers the core logger service into the container as a singleton if no
 * other logger has been registered already.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Logging;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
		if ( ! $this->container->has( LoggerInterface::class ) ) {
			$this->container->singleton(
				LoggerInterface::class,
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
