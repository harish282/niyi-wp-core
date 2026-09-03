<?php
/**
 * Logger contract.
 *
 * The canonical logging contract for the core library. Any logger
 * implementation (error_log, WooCommerce, file, etc.) must satisfy it.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Logging;

/**
 * Contract for logging services.
 */
interface LoggerInterface {

	/**
	 * System is unusable.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function emergency( string $message, array $context = array() ): void;

	/**
	 * Action must be taken immediately.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function alert( string $message, array $context = array() ): void;

	/**
	 * Runtime errors that do not require immediate action.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function error( string $message, array $context = array() ): void;

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function warning( string $message, array $context = array() ): void;

	/**
	 * Normal but significant events.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function info( string $message, array $context = array() ): void;

	/**
	 * Detailed debug information.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function debug( string $message, array $context = array() ): void;

	/**
	 * Normal but significant events.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function notice( string $message, array $context = array() ): void;

	/**
	 * Critical conditions.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function critical( string $message, array $context = array() ): void;

	/**
	 * Log a message with a dynamic level.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Optional contextual data.
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void;
}
