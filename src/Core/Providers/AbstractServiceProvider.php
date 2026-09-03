<?php
/**
 * Abstract service provider.
 *
 * Provides a base implementation for service providers, including
 * container access for registering services.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Providers;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;

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
