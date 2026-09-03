<?php
/**
 * Service definition.
 *
 * Wraps a single registered service: its factory, whether it is a shared
 * singleton, and the cached instance once resolved. Kept deliberately small —
 * the container owns the storage and resolution flow.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Container;

/**
 * Describes one registered service.
 */
class ServiceDefinition {

	/**
	 * Service identifier.
	 *
	 * @var string
	 */
	private string $id;

	/**
	 * Factory used to build the service instance.
	 *
	 * @var callable
	 */
	private $factory;

	/**
	 * Whether the built instance is shared across resolutions.
	 *
	 * @var bool
	 */
	private bool $shared;

	/**
	 * Cached instance, populated after the first resolution when shared.
	 *
	 * @var mixed
	 */
	private $instance;

	/**
	 * Build the definition.
	 *
	 * @param string   $id      Service identifier.
	 * @param callable $factory Factory returning the service instance.
	 * @param bool     $shared  Whether the instance is cached and reused.
	 */
	public function __construct( string $id, callable $factory, bool $shared ) {
		$this->id      = $id;
		$this->factory = $factory;
		$this->shared  = $shared;
	}

	/**
	 * Resolve the service, reusing a cached instance for shared definitions.
	 *
	 * @return mixed
	 */
	public function resolve() {
		if ( $this->shared && null !== $this->instance ) {
			return $this->instance;
		}

		$instance = ( $this->factory )();

		if ( $this->shared ) {
			$this->instance = $instance;
		}

		return $instance;
	}

	/**
	 * Drop any cached instance so the next resolution rebuilds it.
	 *
	 * @return void
	 */
	public function forget(): void {
		$this->instance = null;
	}

	/**
	 * The service identifier.
	 *
	 * @return string
	 */
	public function id(): string {
		return $this->id;
	}

	/**
	 * Whether the instance is shared (singleton).
	 *
	 * @return bool
	 */
	public function is_shared(): bool {
		return $this->shared;
	}
}
