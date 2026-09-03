<?php
/**
 * Tests for Logger service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Logging;

use NiyiWPCore\Core\Logging\Logger;
use NiyiWPCore\Tests\TestCase;

/**
 * Logger tests.
 */
class LoggerTest extends TestCase {

	/**
	 * Test that logger methods do not throw exceptions.
	 *
	 * @return void
	 */
	public function test_log_levels_do_not_throw(): void {
		$logger = new Logger();

		$logger->emergency( 'Emergency message' );
		$logger->alert( 'Alert message' );
		$logger->critical( 'Critical message' );
		$logger->error( 'Error message' );
		$logger->warning( 'Warning message' );
		$logger->notice( 'Notice message' );
		$logger->info( 'Info message' );
		$logger->debug( 'Debug message' );
		$logger->log( 'custom', 'Custom level message' );

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Test that context is included in log output.
	 *
	 * @return void
	 */
	public function test_log_with_context_does_not_throw(): void {
		$logger = new Logger();

		$logger->info( 'Message with context', array( 'key' => 'value' ) );

		$this->addToAssertionCount( 1 );
	}
}
