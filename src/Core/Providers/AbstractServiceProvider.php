<?php
/**
 * Abstract service provider.
 *
 * Provides a base implementation for service providers, including
 * container access for registering services.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Providers;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;

/**
 * Abstract base class for service providers.
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface {

	/**
	 * The service container instance.
	 *
	 * @var ContainerInterface
	 */
	protected ContainerInterface $container;

	/**
	 * Create a new service provider instance.
	 *
	 * @param ContainerInterface $container The service container.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}
}
