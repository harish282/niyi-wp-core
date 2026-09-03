<?php
/**
 * Tests for service definitions.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Container;

use NiyiWPCore\Core\Container\ServiceDefinition;
use NiyiWPCore\Tests\TestCase;

/**
 * Service definition tests.
 */
class ServiceDefinitionTest extends TestCase {

	/**
	 * Test a non-shared definition builds a fresh instance on each resolve.
	 *
	 * @return void
	 */
	public function test_non_shared_resolves_fresh_instances(): void {
		$definition = new ServiceDefinition(
			'test.service',
			static fn() => new \stdClass(),
			false
		);

		$this->assertNotSame( $definition->resolve(), $definition->resolve() );
	}

	/**
	 * Test a shared definition caches its instance.
	 *
	 * @return void
	 */
	public function test_shared_resolves_the_same_instance(): void {
		$definition = new ServiceDefinition(
			'test.service',
			static fn() => new \stdClass(),
			true
		);

		$this->assertSame( $definition->resolve(), $definition->resolve() );
	}

	/**
	 * Test forget() drops the cached instance of a shared definition.
	 *
	 * @return void
	 */
	public function test_forget_drops_the_cached_instance(): void {
		$definition = new ServiceDefinition(
			'test.service',
			static fn() => new \stdClass(),
			true
		);

		$first = $definition->resolve();

		$definition->forget();

		$this->assertNotSame( $first, $definition->resolve() );
	}

	/**
	 * Test id() returns the service identifier.
	 *
	 * @return void
	 */
	public function test_id_returns_the_identifier(): void {
		$definition = new ServiceDefinition( 'test.service', static fn() => null, false );

		$this->assertSame( 'test.service', $definition->id() );
	}

	/**
	 * Test is_shared() reflects the sharing flag.
	 *
	 * @return void
	 */
	public function test_is_shared_reflects_the_flag(): void {
		$transient = new ServiceDefinition( 't', static fn() => null, false );
		$shared    = new ServiceDefinition( 's', static fn() => null, true );

		$this->assertFalse( $transient->is_shared() );
		$this->assertTrue( $shared->is_shared() );
	}

	/**
	 * Test the factory receives no arguments and its result is returned.
	 *
	 * @return void
	 */
	public function test_resolve_returns_the_factory_result(): void {
		$definition = new ServiceDefinition(
			'test.service',
			static fn() => 42,
			false
		);

		$this->assertSame( 42, $definition->resolve() );
	}
}
