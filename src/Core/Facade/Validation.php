<?php
/**
 * Validation facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\Validation\ValidatorInterface;

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
