<?php
/**
 * Validation facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\Validation\ValidatorInterface;

/**
 * Static proxy for ValidatorInterface.
 */
class Validation extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return ValidatorInterface::class;
	}
}
