<?php
/**
 * Tests for the WordPress-backed logger.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Logging;

use NiyiWooSmartUpsells\Helpers\WpLogger;
use NiyiWPCore\Tests\TestCase;

/**
 * Logging sink that records WooCommerce-style log calls.
 */
class RecordingWcLogger {

	/**
	 * Recorded log calls.
	 *
	 * @var array<int, array{level: string, message: string, context: array<string, mixed>}>
	 */
	public array $calls = array();

	/**
	 * Record a log call.
	 *
	 * @param string $level   Log level.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void {
		$this->calls[] = array(
			'level'   => $level,
			'message' => $message,
			'context' => $context,
		);
	}
}

/**
 * WpLogger tests.
 */
class WpLoggerTest extends TestCase {

	/**
	 * Original error_log target.
	 *
	 * @var string|false
	 */
	private $original_error_log;

	/**
	 * Temporary log file used to intercept error_log output.
	 *
	 * @var string
	 */
	private string $log_file;

	/**
	 * Logger under test.
	 *
	 * @var WpLogger
	 */
	private WpLogger $logger;

	/**
	 * Point error_log at a temporary file and build a fresh logger.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->original_error_log = ini_set( 'error_log', '/tmp/opencode/niyi_wp_logger_test.log' );
		$this->log_file           = '/tmp/opencode/niyi_wp_logger_test.log';
		@unlink( $this->log_file );

		$this->logger = new WpLogger();
	}

	/**
	 * Restore the original error_log target.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		ini_set( 'error_log', (string) $this->original_error_log );
		@unlink( $this->log_file );

		parent::tearDown();
	}

	/**
	 * Test every level writes to error_log when no WC logger exists.
	 *
	 * @return void
	 */
	public function test_all_levels_write_to_error_log_fallback(): void {
		$this->logger->emergency( 'Emergency message' );
		$this->logger->alert( 'Alert message' );
		$this->logger->critical( 'Critical message' );
		$this->logger->error( 'Error message' );
		$this->logger->warning( 'Warning message' );
		$this->logger->notice( 'Notice message' );
		$this->logger->info( 'Info message' );
		$this->logger->debug( 'Debug message' );

		$content = $this->read_log();

		$this->assertStringContainsString( '[niyi-woo-smart-upsells] EMERGENCY: Emergency message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] ALERT: Alert message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] CRITICAL: Critical message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] ERROR: Error message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] WARNING: Warning message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] NOTICE: Notice message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] INFO: Info message', $content );
		$this->assertStringContainsString( '[niyi-woo-smart-upsells] DEBUG: Debug message', $content );
	}

	/**
	 * Test log() routes a dynamic level to error_log.
	 *
	 * @return void
	 */
	public function test_log_writes_a_dynamic_level(): void {
		$this->logger->log( 'custom', 'Custom level message' );

		$this->assertStringContainsString( '[niyi-woo-smart-upsells] CUSTOM: Custom level message', $this->read_log() );
	}

	/**
	 * Test context is appended as JSON.
	 *
	 * @return void
	 */
	public function test_context_is_appended_as_json(): void {
		$this->logger->info( 'Message with context', array( 'key' => 'value' ) );

		$this->assertStringContainsString( '[niyi-woo-smart-upsells] INFO: Message with context {"key":"value"}', $this->read_log() );
	}

	/**
	 * Test entries route to the WooCommerce logger when available.
	 *
	 * @return void
	 */
	public function test_entries_route_to_the_wc_logger(): void {
		$wc_logger                     = new RecordingWcLogger();
		$GLOBALS['niyi_test_wc_logger'] = $wc_logger;

		$this->logger->info( 'Info message', array( 'x' => 1 ) );
		$this->logger->error( 'Error message' );

		$this->assertCount( 2, $wc_logger->calls );
		$this->assertSame( 'info', $wc_logger->calls[0]['level'] );
		$this->assertSame( 'error', $wc_logger->calls[1]['level'] );
		$this->assertSame( array( 'source' => 'niyi-woo-smart-upsells' ), $wc_logger->calls[0]['context'] );
		$this->assertSame( '', $this->read_log() );
	}

	/**
	 * Read the intercepted log file.
	 *
	 * @return string
	 */
	private function read_log(): string {
		return is_file( $this->log_file ) ? (string) file_get_contents( $this->log_file ) : '';
	}
}
