<?php
/**
 * Supported log levels.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Logging;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Log severity levels.
 */
enum LogLevel: string {

	case EMERGENCY = 'emergency';
	case ALERT     = 'alert';
	case CRITICAL  = 'critical';
	case ERROR     = 'error';
	case WARNING   = 'warning';
	case NOTICE    = 'notice';
	case INFO      = 'info';
	case DEBUG     = 'debug';
}
