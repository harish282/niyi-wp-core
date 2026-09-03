<?php
/**
 * Base facade class.
 *
 * Provides static proxy access to services registered in the container.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Bootstrap\Plugin;

/**
 * Abstract base facade.
 */
abstract class Facade {

	/**
	 * Resolve the underlying service from the container.
	 *
	 * @return object
	 * @throws \RuntimeException When the container is not initialized.
	 */
	protected static function resolve(): object {
		$container = Plugin::container_instance();

		if ( ! $container ) {
			throw new \RuntimeException( 'Container not initialized. Plugin may not be booted yet.' );
		}

		return $container->get( static::getFacadeAccessor() );
	}

	/**
	 * Get the service accessor for the facade.
	 *
	 * @return string
	 */
	abstract protected static function getFacadeAccessor(): string;

	/**
	 * Handle dynamic static method calls.
	 *
	 * @param string $method      Method name.
	 * @param array  $arguments   Method arguments.
	 * @return mixed
	 */
	public static function __callStatic( string $method, array $arguments ) {
		$instance = static::resolve();

		return $instance->$method( ...$arguments );
	}
}
