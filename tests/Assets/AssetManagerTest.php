<?php
/**
 * Tests for AssetManager service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Assets;

use NiyiWPCore\Core\Assets\AssetManager;
use NiyiWPCore\Tests\TestCase;

/**
 * Asset manager tests.
 */
class AssetManagerTest extends TestCase {

	/**
	 * Test asset URL generation.
	 *
	 * @return void
	 */
	public function test_asset_url_generates_correct_url(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$url = $manager->asset_url( 'js/admin.js' );

		$this->assertStringContainsString( 'assets/js/admin.js', $url );
	}

	/**
	 * Test asset URL trims leading slash.
	 *
	 * @return void
	 */
	public function test_asset_url_trims_leading_slash(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$url = $manager->asset_url( '/js/admin.js' );

		$this->assertStringContainsString( 'assets/js/admin.js', $url );
	}

	/**
	 * Test register_script method exists.
	 *
	 * @return void
	 */
	public function test_register_script_method_exists(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$this->assertTrue( method_exists( $manager, 'register_script' ) );
	}

	/**
	 * Test register_style method exists.
	 *
	 * @return void
	 */
	public function test_register_style_method_exists(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$this->assertTrue( method_exists( $manager, 'register_style' ) );
	}

	/**
	 * Test enqueue_script method exists.
	 *
	 * @return void
	 */
	public function test_enqueue_script_method_exists(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$this->assertTrue( method_exists( $manager, 'enqueue_script' ) );
	}

	/**
	 * Test enqueue_style method exists.
	 *
	 * @return void
	 */
	public function test_enqueue_style_method_exists(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$this->assertTrue( method_exists( $manager, 'enqueue_style' ) );
	}

	/**
	 * Test localize_script method exists.
	 *
	 * @return void
	 */
	public function test_localize_script_method_exists(): void {
		$manager = new AssetManager( '/path/to/plugin.php', '1.0.0' );

		$this->assertTrue( method_exists( $manager, 'localize_script' ) );
	}
}
