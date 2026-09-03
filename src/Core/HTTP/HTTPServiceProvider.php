<?php
/**
 * HTTP service provider.
 *
 * Registers the HTTP client into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\HTTP;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

/**
 * Registers the HTTP client.
 */
class HTTPServiceProvider extends AbstractServiceProvider {

	/**
	 * Register HTTP bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			HTTPClientInterface::class,
			fn() => new HTTPClient()
		);
	}

	/**
	 * Bootstrap the HTTP service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
