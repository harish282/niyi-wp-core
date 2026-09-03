<?php
/**
 * WordPress-backed logger.
 *
 * Routes log entries to WooCommerce's logger when available, falling back to
 * error_log so diagnostics still work in minimal environments.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Logging;

/**
 * Logger backed by WordPress/WooCommerce logging.
 */
class WpLogger implements LoggerInterface {

	/**
	 * Source slug used in WooCommerce log entries.
	 *
	 * @var string
	 */
	private string $source = 'niyi-wp-core';

	/**
	 * Map of log levels to WooCommerce severity strings.
	 *
	 * @var array<string, string>
	 */
	private array $levels = array(
		'emergency' => 'emergency',
		'alert'     => 'alert',
		'error'     => 'error',
		'warning'   => 'warning',
		'info'      => 'info',
		'debug'     => 'debug',
	);

	/**
	 * Build the logger.
	 *
	 * @param string $source Optional source slug used in log entries.
	 */
	public function __construct( string $source = 'niyi-wp-core' ) {
		$this->source = $source;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function emergency( string $message, array $context = array() ): void {
		$this->write( 'emergency', $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function alert( string $message, array $context = array() ): void {
		$this->write( 'alert', $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function error( string $message, array $context = array() ): void {
		$this->write( 'error', $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function warning( string $message, array $context = array() ): void {
		$this->write( 'warning', $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function info( string $message, array $context = array() ): void {
		$this->write( 'info', $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function debug( string $message, array $context = array() ): void {
		$this->write( 'debug', $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function notice( string $message, array $context = array() ): void {
		$this->write( 'notice', $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function critical( string $message, array $context = array() ): void {
		$this->write( 'critical', $message, $context );
	}

	/**
	 * Log a message with a dynamic level.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void {
		$this->write( $level, $message, $context );
	}

	/**
	 * Write a log entry.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	private function write( string $level, string $message, array $context ): void {
		$entry = $this->format( $message, $context );

		$wc_logger = $this->wc_logger();
		if ( null !== $wc_logger ) {
			$wc_logger->log( $this->levels[ $level ] ?? $level, $entry, array( 'source' => $this->source ) );
			return;
		}

		error_log( sprintf( '[%s] %s: %s', $this->source, strtoupper( $level ), $entry ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Fallback when WooCommerce's logger is unavailable.
	}

	/**
	 * Retrieve the WooCommerce logger instance if available.
	 *
	 * @return \WC_Logger_Interface|null
	 */
	private function wc_logger() {
		if ( function_exists( 'wc_get_logger' ) ) {
			return wc_get_logger();
		}

		return null;
	}

	/**
	 * Render a message with optional context for storage.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return string
	 */
	private function format( string $message, array $context ): string {
		if ( empty( $context ) ) {
			return $message;
		}

		return $message . ' ' . wp_json_encode( $context );
	}
}
