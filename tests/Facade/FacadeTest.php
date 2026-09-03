<?php
/**
 * Tests for the base facade static proxy.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Facade;

use NiyiWPCore\Core\Container\Container;
use NiyiWPCore\Core\Facade\Facade;
use NiyiWPCore\Core\Plugin as CorePlugin;
use NiyiWPCore\Tests\TestCase;

/**
 * Test facade exposing a configurable service accessor.
 */
class ProxyFacade extends Facade {

	/**
	 * Service accessor.
	 *
	 * @var string
	 */
	private static string $accessor = '';

	/**
	 * Set the service accessor used for resolution.
	 *
	 * @param string $accessor Service accessor.
	 * @return void
	 */
	public static function set_accessor( string $accessor ): void {
		self::$accessor = $accessor;
	}

	/**
	 * The service accessor.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor(): string {
		return self::$accessor;
	}
}

/**
 * Base facade tests.
 */
class FacadeTest extends TestCase {

	/**
	 * Clear the shared container and accessor before every test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->set_container( null );
		ProxyFacade::set_accessor( '' );
	}

	/**
	 * Test a facade proxies a static call to its underlying service.
	 *
	 * @return void
	 */
	public function test_facade_proxies_calls_to_the_service(): void {
		$service  = new class {
			public function ping( string $name ): string {
				return 'pong:' . $name;
			}
		};
		$accessor = $service::class;

		$container = new Container();
		$container->instance( $accessor, $service );

		$this->set_container( $container );
		ProxyFacade::set_accessor( $accessor );

		$this->assertSame( 'pong:test', ProxyFacade::ping( 'test' ) );
	}

	/**
	 * Test a facade resolves the shared service even for arguments.
	 *
	 * @return void
	 */
	public function test_facade_forwards_multiple_arguments(): void {
		$service  = new class {
			public function sum( int $a, int $b ): int {
				return $a + $b;
			}
		};
		$accessor = $service::class;

		$container = new Container();
		$container->instance( $accessor, $service );

		$this->set_container( $container );
		ProxyFacade::set_accessor( $accessor );

		$this->assertSame( 7, ProxyFacade::sum( 3, 4 ) );
	}

	/**
	 * Test a facade throws when no container is initialized.
	 *
	 * @return void
	 */
	public function test_facade_throws_without_a_container(): void {
		ProxyFacade::set_accessor( 'missing.service' );

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'Container not initialized' );

		ProxyFacade::anyMethod();
	}

	/**
	 * Test a facade throws when the service is not registered.
	 *
	 * @return void
	 */
	public function test_facade_throws_for_unregistered_service(): void {
		$this->set_container( new Container() );
		ProxyFacade::set_accessor( 'missing.service' );

		$this->expectException( \NiyiWPCore\Core\Container\Exceptions\ContainerException::class );

		ProxyFacade::anyMethod();
	}

	/**
	 * Set or clear the shared plugin container used by facades.
	 *
	 * @param Container|null $container Container to register, or null to clear.
	 * @return void
	 */
	private function set_container( ?Container $container ): void {
		$property = new \ReflectionProperty( CorePlugin::class, 'container_instance' );
		$property->setValue( null, $container );
	}
}
