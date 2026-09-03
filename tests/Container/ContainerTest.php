<?php
/**
 * Tests for the service container.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Container;

use NiyiWPCore\Core\Container\Container;
use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Container\Exceptions\ContainerException;
use NiyiWPCore\Tests\TestCase;

/**
 * Service container tests.
 */
class ContainerTest extends TestCase {

	/**
	 * Container under test.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * Build a fresh container.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->container = new Container();
	}

	/**
	 * Test the container implements the interface.
	 *
	 * @return void
	 */
	public function test_container_implements_interface(): void {
		$this->assertInstanceOf( ContainerInterface::class, $this->container );
	}

	/**
	 * Test a bound factory is resolved and reported as registered.
	 *
	 * @return void
	 */
	public function test_bind_resolves_fresh_instances(): void {
		$this->container->bind(
			'test.service',
			static fn() => new \stdClass()
		);

		$this->assertTrue( $this->container->has( 'test.service' ) );

		$first  = $this->container->get( 'test.service' );
		$second = $this->container->get( 'test.service' );

		$this->assertInstanceOf( \stdClass::class, $first );
		$this->assertNotSame( $first, $second );
	}

	/**
	 * Test a singleton factory returns the same instance every time.
	 *
	 * @return void
	 */
	public function test_singleton_reuses_the_same_instance(): void {
		$this->container->singleton(
			'test.singleton',
			static fn() => new \stdClass()
		);

		$first  = $this->container->get( 'test.singleton' );
		$second = $this->container->get( 'test.singleton' );

		$this->assertSame( $first, $second );
	}

	/**
	 * Test a pre-built instance is returned as-is.
	 *
	 * @return void
	 */
	public function test_instance_returns_the_registered_value(): void {
		$service = new \stdClass();

		$this->container->instance( 'test.instance', $service );

		$this->assertSame( $service, $this->container->get( 'test.instance' ) );
	}

	/**
	 * Test registering a null instance is rejected.
	 *
	 * @return void
	 */
	public function test_instance_rejects_null(): void {
		$this->expectException( ContainerException::class );

		$this->container->instance( 'test.null', null );
	}

	/**
	 * Test resolving an unregistered service throws.
	 *
	 * @return void
	 */
	public function test_get_throws_for_unregistered_service(): void {
		$this->expectException( ContainerException::class );
		$this->expectExceptionMessage( 'is not registered' );

		$this->container->get( 'missing.service' );
	}

	/**
	 * Test has() reports whether a service is registered.
	 *
	 * @return void
	 */
	public function test_has_reflects_registration(): void {
		$this->assertFalse( $this->container->has( 'test.service' ) );

		$this->container->bind( 'test.service', static fn() => new \stdClass() );

		$this->assertTrue( $this->container->has( 'test.service' ) );
	}

	/**
	 * Test forget() removes a single registration.
	 *
	 * @return void
	 */
	public function test_forget_removes_a_single_service(): void {
		$this->container->bind( 'test.one', static fn() => 1 );
		$this->container->bind( 'test.two', static fn() => 2 );

		$this->container->forget( 'test.one' );

		$this->assertFalse( $this->container->has( 'test.one' ) );
		$this->assertTrue( $this->container->has( 'test.two' ) );
	}

	/**
	 * Test flush() removes every registration.
	 *
	 * @return void
	 */
	public function test_flush_removes_every_service(): void {
		$this->container->bind( 'test.one', static fn() => 1 );
		$this->container->bind( 'test.two', static fn() => 2 );

		$this->container->flush();

		$this->assertFalse( $this->container->has( 'test.one' ) );
		$this->assertFalse( $this->container->has( 'test.two' ) );
	}

	/**
	 * Test the set() alias registers a shared service.
	 *
	 * @return void
	 */
	public function test_set_alias_registers_a_singleton(): void {
		$this->container->set( 'test.alias', static fn() => new \stdClass() );

		$first  = $this->container->get( 'test.alias' );
		$second = $this->container->get( 'test.alias' );

		$this->assertSame( $first, $second );
	}

	/**
	 * Test re-registering a service replaces its factory.
	 *
	 * @return void
	 */
	public function test_rebinding_replaces_the_factory(): void {
		$this->container->bind( 'test.value', static fn() => 'first' );
		$this->container->bind( 'test.value', static fn() => 'second' );

		$this->assertSame( 'second', $this->container->get( 'test.value' ) );
	}
}
