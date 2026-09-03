<?php
/**
 * Tests for HookManager service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Hooks;

use NiyiWPCore\Core\Hooks\HookManager;
use NiyiWPCore\Tests\TestCase;

/**
 * Hook manager tests.
 */
class HookManagerTest extends TestCase {

	/**
	 * Test action method exists.
	 *
	 * @return void
	 */
	public function test_action_method_exists(): void {
		$manager = new HookManager();

		$this->assertTrue( method_exists( $manager, 'action' ) );
	}

	/**
	 * Test filter method exists.
	 *
	 * @return void
	 */
	public function test_filter_method_exists(): void {
		$manager = new HookManager();

		$this->assertTrue( method_exists( $manager, 'filter' ) );
	}

	/**
	 * Test remove_action method exists.
	 *
	 * @return void
	 */
	public function test_remove_action_method_exists(): void {
		$manager = new HookManager();

		$this->assertTrue( method_exists( $manager, 'remove_action' ) );
	}

	/**
	 * Test remove_filter method exists.
	 *
	 * @return void
	 */
	public function test_remove_filter_method_exists(): void {
		$manager = new HookManager();

		$this->assertTrue( method_exists( $manager, 'remove_filter' ) );
	}
}
