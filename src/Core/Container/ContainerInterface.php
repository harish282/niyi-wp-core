<?php
/**
 * Service container contract.
 *
 * Defines the small, predictable surface the plugin uses to register and
 * resolve services. Concrete implementations must stay framework-agnostic.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Container;

use NiyiWooSmartUpsells\Core\Container\Exceptions\ContainerException;

/**
 * Contract for the dependency-injection container.
 */
interface ContainerInterface {

	/**
	 * Register a factory that returns a fresh instance on each resolution.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $factory  Factory returning the service instance.
	 * @return void
	 */
	public function bind( string $id, callable $factory ): void;

	/**
	 * Register a factory whose result is cached and reused on each resolution.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $factory  Factory returning the shared service instance.
	 * @return void
	 */
	public function singleton( string $id, callable $factory ): void;

	/**
	 * Register an already-built instance as a shared service.
	 *
	 * @param string $id        Service identifier.
	 * @param mixed  $instance  Concrete instance to return on resolution.
	 * @return void
	 */
	public function instance( string $id, $instance ): void;

	/**
	 * Resolve a registered service.
	 *
	 * @param string $id Service identifier.
	 * @return mixed
	 * @throws ContainerException When the service is not registered.
	 */
	public function get( string $id );

	/**
	 * Whether a service identifier is registered.
	 *
	 * @param string $id Service identifier.
	 * @return bool
	 */
	public function has( string $id ): bool;

	/**
	 * Remove a single service registration.
	 *
	 * @param string $id Service identifier.
	 * @return void
	 */
	public function forget( string $id ): void;

	/**
	 * Remove every service registration.
	 *
	 * @return void
	 */
	public function flush(): void;
}
