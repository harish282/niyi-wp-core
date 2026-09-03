<?php
/**
 * Service container.
 *
 * Lightweight dependency-injection container. Services are registered as
 * closures and resolved manually — no reflection, auto-wiring, or directory
 * scanning. Supports transient (`bind`), shared (`singleton`), and pre-built
 * (`instance`) registrations. Framework-agnostic: it must not depend on
 * WordPress.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Container;

use NiyiWPCore\Core\Container\Exceptions\ContainerException;

/**
 * Stores service registrations and resolves them when requested.
 */
class Container implements ContainerInterface {

	/**
	 * Registered service definitions keyed by identifier.
	 *
	 * @var array<string, ServiceDefinition>
	 */
	private array $bindings = array();

	/**
	 * Register a factory that returns a fresh instance on every resolution.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $factory  Factory returning the service instance.
	 * @return void
	 */
	public function bind( string $id, callable $factory ): void {
		$this->bindings[ $id ] = new ServiceDefinition( $id, $factory, false );
	}

	/**
	 * Register a factory whose result is cached and reused on each resolution.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $factory  Factory returning the shared service instance.
	 * @return void
	 */
	public function singleton( string $id, callable $factory ): void {
		$this->bindings[ $id ] = new ServiceDefinition( $id, $factory, true );
	}

	/**
	 * Register an already-built instance as a shared service.
	 *
	 * @param string $id        Service identifier.
	 * @param mixed  $instance  Concrete instance to return on resolution.
	 * @return void
	 * @throws ContainerException When the instance is null.
	 */
	public function instance( string $id, $instance ): void {
		if ( null === $instance ) {
			throw new ContainerException(
				wp_kses_post( sprintf( 'Cannot register a null instance for "%s".', $id ) )
			);
		}

		$this->bindings[ $id ] = new ServiceDefinition(
			$id,
			static function () use ( $instance ) {
				return $instance;
			},
			true
		);
	}

	/**
	 * Resolve a registered service.
	 *
	 * @param string $id Service identifier.
	 * @return mixed
	 * @throws ContainerException When the service is not registered.
	 */
	public function get( string $id ) {
		if ( ! $this->has( $id ) ) {
			throw new ContainerException(
				wp_kses_post( sprintf( 'Service "%s" is not registered in the container.', $id ) )
			);
		}

		return $this->bindings[ $id ]->resolve();
	}

	/**
	 * Whether a service identifier is registered.
	 *
	 * @param string $id Service identifier.
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->bindings[ $id ] );
	}

	/**
	 * Remove a single service registration.
	 *
	 * @param string $id Service identifier.
	 * @return void
	 */
	public function forget( string $id ): void {
		unset( $this->bindings[ $id ] );
	}

	/**
	 * Remove every service registration.
	 *
	 * @return void
	 */
	public function flush(): void {
		$this->bindings = array();
	}

	/**
	 * Backwards-compatible alias for registering a shared service.
	 *
	 * Retained so callers that used the earlier minimal container keep working;
	 * prefer `singleton()` for new code.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $factory  Factory returning the shared service instance.
	 * @return void
	 */
	public function set( string $id, callable $factory ): void {
		$this->singleton( $id, $factory );
	}
}
