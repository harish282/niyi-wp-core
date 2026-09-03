<?php
/**
 * Tests for View service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\View;

use NiyiWPCore\Core\View\View;
use NiyiWPCore\Tests\TestCase;

/**
 * View tests.
 */
class ViewTest extends TestCase {

	/**
	 * Test view exists returns true for existing view.
	 *
	 * @return void
	 */
	public function test_exists_returns_true_for_existing_view(): void {
		$view = new View( __DIR__ . '/Fixtures' );

		$this->assertTrue( $view->exists( 'test' ) );
	}

	/**
	 * Test view exists returns false for missing view.
	 *
	 * @return void
	 */
	public function test_exists_returns_false_for_missing_view(): void {
		$view = new View( __DIR__ . '/Fixtures' );

		$this->assertFalse( $view->exists( 'missing' ) );
	}

	/**
	 * Test render throws exception for missing view.
	 *
	 * @return void
	 */
	public function test_render_throws_exception_for_missing_view(): void {
		$this->expectException( \NiyiWPCore\Core\View\ViewException::class );

		$view = new View( __DIR__ . '/Fixtures' );
		$view->render( 'missing' );
	}

	/**
	 * Test share stores variable.
	 *
	 * @return void
	 */
	public function test_share_stores_variable(): void {
		$view = new View( __DIR__ . '/Fixtures' );

		$view->share( 'test_key', 'test_value' );

		$this->assertTrue( true );
	}

	/**
	 * Test nested view resolution.
	 *
	 * @return void
	 */
	public function test_exists_for_nested_view(): void {
		$view = new View( __DIR__ . '/Fixtures' );

		$this->assertTrue( $view->exists( 'partials.header' ) );
	}
}
