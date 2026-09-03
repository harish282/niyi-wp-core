<?php
/**
 * In-memory logger spy for service tests.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Support;

use NiyiWPCore\Core\Logging\LoggerInterface;

/**
 * Records every log entry so tests can assert on them.
 */
class SpyLogger implements LoggerInterface {

	/**
	 * Recorded entries, each with level, message, and context.
	 *
	 * @var array<int, array{level: string, message: string, context: array<string, mixed>}>
	 */
	public array $entries = array();

	/**
	 * Record an entry with an explicit level.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void {
		$this->entries[] = array(
			'level'   => $level,
			'message' => $message,
			'context' => $context,
		);
	}

	/**
	 * All messages logged at a given level.
	 *
	 * @param string $level Log level.
	 * @return array<int, string>
	 */
	public function messages_at( string $level ): array {
		return array_values(
			array_map(
				static fn( array $entry ): string => $entry['message'],
				array_filter(
					$this->entries,
					static fn( array $entry ): bool => $entry['level'] === $level
				)
			)
		);
	}

	/**
	 * Record an emergency entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function emergency( string $message, array $context = array() ): void {
		$this->log( 'emergency', $message, $context );
	}

	/**
	 * Record an alert entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function alert( string $message, array $context = array() ): void {
		$this->log( 'alert', $message, $context );
	}

	/**
	 * Record an error entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function error( string $message, array $context = array() ): void {
		$this->log( 'error', $message, $context );
	}

	/**
	 * Record a warning entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function warning( string $message, array $context = array() ): void {
		$this->log( 'warning', $message, $context );
	}

	/**
	 * Record an info entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function info( string $message, array $context = array() ): void {
		$this->log( 'info', $message, $context );
	}

	/**
	 * Record a debug entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function debug( string $message, array $context = array() ): void {
		$this->log( 'debug', $message, $context );
	}

	/**
	 * Record a notice entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function notice( string $message, array $context = array() ): void {
		$this->log( 'notice', $message, $context );
	}

	/**
	 * Record a critical entry.
	 *
	 * @param string $message Log message.
	 * @param array  $context Context data.
	 * @return void
	 */
	public function critical( string $message, array $context = array() ): void {
		$this->log( 'critical', $message, $context );
	}
}
