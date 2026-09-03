<?php
/**
 * Tests for the global niyi_view() helper.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\View;

use NiyiWPCore\Core\Container\Container;
use NiyiWPCore\Core\Plugin as CorePlugin;
use NiyiWPCore\Core\View\ViewInterface;
use NiyiWPCore\Tests\TestCase;

/**
 * Global view helper tests.
 */
class GlobalViewHelperTest extends TestCase {

	/**
	 * Clear the shared container before every test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$property = new \ReflectionProperty( CorePlugin::class, 'container_instance' );
		$property->setValue( null, null );
	}

	/**
	 * Test niyi_view() function exists.
	 *
	 * @return void
	 */
	public function test_niyi_view_function_exists(): void {
		$this->assertTrue( function_exists( 'niyi_view' ) );
	}

	/**
	 * Test niyi_view() returns null when container is not available.
	 *
	 * @return void
	 */
	public function test_niyi_view_returns_null_when_no_container(): void {
		$result = niyi_view();

		$this->assertNull( $result );
	}

	/**
	 * Test niyi_view() returns the view service when no view name is given.
	 *
	 * @return void
	 */
	public function test_niyi_view_returns_the_view_service(): void {
		$view = $this->createMock( ViewInterface::class );

		$this->register_view( $view );

		$this->assertSame( $view, niyi_view() );
	}

	/**
	 * Test niyi_view() renders a view when a name is given.
	 *
	 * @return void
	 */
	public function test_niyi_view_renders_a_named_view(): void {
		$view = $this->createMock( ViewInterface::class );
		$view->expects( $this->once() )
			->method( 'render' )
			->with( 'Admin.Settings.index', array( 'settings' => array() ) );

		$this->register_view( $view );

		niyi_view( 'Admin.Settings.index', array( 'settings' => array() ) );
	}

	/**
	 * Register the view service in the shared plugin container.
	 *
	 * @param ViewInterface $view View service.
	 * @return void
	 */
	private function register_view( ViewInterface $view ): void {
		$container = new Container();
		$container->instance( ViewInterface::class, $view );

		$property = new \ReflectionProperty( CorePlugin::class, 'container_instance' );
		$property->setValue( null, $container );
	}
}
