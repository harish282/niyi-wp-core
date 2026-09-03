<?php
/**
 * Base test case.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base test case for all core library tests.
 */
class TestCase extends PHPUnitTestCase {

	/**
	 * Reset shared test state before every test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		niyi_test_reset_globals();
	}
}
