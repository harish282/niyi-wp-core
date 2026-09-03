<?php
/**
 * Tests for Settings service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Settings;

use NiyiWPCore\Core\Settings\Settings;
use NiyiWPCore\Tests\TestCase;

/**
 * Settings tests.
 */
class SettingsTest extends TestCase {

	/**
	 * Test retrieving a setting value.
	 *
	 * @return void
	 */
	public function test_get_returns_value(): void {
		$settings = new Settings( 'test_option', array( 'key' => 'value' ) );

		$this->assertSame( 'value', $settings->get( 'key' ) );
	}

	/**
	 * Test retrieving a setting value with default.
	 *
	 * @return void
	 */
	public function test_get_returns_default_when_missing(): void {
		$settings = new Settings( 'test_option' );

		$this->assertNull( $settings->get( 'missing' ) );
		$this->assertSame( 'default', $settings->get( 'missing', 'default' ) );
	}

	/**
	 * Test storing a setting value.
	 *
	 * @return void
	 */
	public function test_set_stores_value(): void {
		$settings = new Settings( 'test_option' );

		$result = $settings->set( 'key', 'value' );

		$this->assertSame( 'value', $settings->get( 'key' ) );
		$this->assertSame( $settings, $result );
	}

	/**
	 * Test checking if a setting exists.
	 *
	 * @return void
	 */
	public function test_has_returns_true_for_existing_key(): void {
		$settings = new Settings( 'test_option', array( 'key' => 'value' ) );

		$this->assertTrue( $settings->has( 'key' ) );
		$this->assertFalse( $settings->has( 'missing' ) );
	}

	/**
	 * Test deleting a setting value.
	 *
	 * @return void
	 */
	public function test_delete_removes_value(): void {
		$settings = new Settings( 'test_option', array( 'key' => 'value' ) );

		$this->assertTrue( $settings->delete( 'key' ) );
		$this->assertFalse( $settings->has( 'key' ) );
		$this->assertFalse( $settings->delete( 'key' ) );
	}

	/**
	 * Test retrieving all settings.
	 *
	 * @return void
	 */
	public function test_all_returns_all_items(): void {
		$settings = new Settings( 'test_option', array( 'a' => 1, 'b' => 2 ) );

		$this->assertSame( array( 'a' => 1, 'b' => 2 ), $settings->all() );
	}

	/**
	 * Test that initial values override stored values.
	 *
	 * @return void
	 */
	public function test_constructor_merges_initial_values(): void {
		$settings = new Settings( 'test_option', array( 'new' => 'value' ) );

		$this->assertSame( 'value', $settings->get( 'new' ) );
	}
}
