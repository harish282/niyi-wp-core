<?php
/**
 * HTTP exception.
 *
 * Thrown when an outbound HTTP request cannot be completed or when the
 * WordPress HTTP API returns a transport error.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\HTTP;

/**
 * Exception thrown for HTTP transport errors.
 */
class HTTPException extends \RuntimeException {
}
