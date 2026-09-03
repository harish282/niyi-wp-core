<?php
/**
 * Tests for Cache service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Cache;

use NiyiWPCore\Core\Cache\Cache;
use NiyiWPCore\Tests\TestCase;

/**
 * Cache tests.
 */
class CacheTest extends TestCase {

	/**
	 * Test get returns default for missing key.
	 *
	 * @return void
	 */
	public function test_get_returns_default_for_missing_key(): void {
		$cache = new Cache();

		$this->assertNull( $cache->get( 'missing' ) );
		$this->assertSame( 'default', $cache->get( 'missing', 'default' ) );
	}

	/**
	 * Test set and get work together.
	 *
	 * @return void
	 */
	public function test_set_and_get_work_together(): void {
		$cache = new Cache();

		$cache->set( 'key', 'value' );

		$this->assertSame( 'value', $cache->get( 'key' ) );
	}

	/**
	 * Test has returns true for existing key.
	 *
	 * @return void
	 */
	public function test_has_returns_true_for_existing_key(): void {
		$cache = new Cache();
		$cache->set( 'key', 'value' );

		$this->assertTrue( $cache->has( 'key' ) );
		$this->assertFalse( $cache->has( 'missing' ) );
	}

	/**
	 * Test forget removes key.
	 *
	 * @return void
	 */
	public function test_forget_removes_key(): void {
		$cache = new Cache();
		$cache->set( 'key', 'value' );

		$this->assertTrue( $cache->forget( 'key' ) );
		$this->assertFalse( $cache->has( 'key' ) );
	}

	/**
	 * Test flush returns true.
	 *
	 * @return void
	 */
	public function test_flush_returns_true(): void {
		$cache = new Cache();

		$this->assertTrue( $cache->flush() );
	}
}
