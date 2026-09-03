<?php
/**
 * Container exception.
 *
 * Thrown when a service cannot be resolved from the container: the identifier
 * is missing, the factory returns an unusable value, or the identifier is
 * invalid. Framework-agnostic — it must not depend on WordPress.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Container\Exceptions;

use Throwable;

/**
 * Exception raised for container resolution failures.
 */
class ContainerException extends \RuntimeException {

	/**
	 * Build the exception.
	 *
	 * @param string         $message  Failure description.
	 * @param int            $code     Optional machine code.
	 * @param Throwable|null $previous Optional chained exception.
	 */
	public function __construct( string $message = '', int $code = 0, ?Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}
