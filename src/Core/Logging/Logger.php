<?php
/**
 * Simple in-memory logger.
 *
 * Writes log entries using PHP's error_log. Intentionally lightweight; future
 * versions may support file, database, or external logging destinations.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Logging;

use NiyiWooSmartUpsells\Contracts\LoggerInterface;

/**
 * Basic error_log-backed logger.
 */
class Logger implements LoggerInterface {

	/**
	 * Plugin source slug used in log entries.
	 *
	 * @var string
	 */
	private string $source = 'niyi-woo-smart-upsells';

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
	public function notice( string $message, array $context = array() ): void {
		$this->write( 'notice', $message, $context );
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
	 * Format and write a log entry.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	private function write( string $level, string $message, array $context ): void {
		$entry = $message;

		if ( ! empty( $context ) ) {
			$entry .= ' ' . wp_json_encode( $context );
		}

		error_log( sprintf( '[%s] %s: %s', $this->source, strtoupper( $level ), $entry ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Fallback logger used before WooCommerce is available.
	}
}
