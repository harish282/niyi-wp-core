<?php
/**
 * Validator contract.
 *
 * Defines the public API for validating and sanitizing data.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Validation;

/**
 * Contract for validators.
 */
interface ValidatorInterface {

	/**
	 * Validate data against rules.
	 *
	 * @param array $data  Data to validate.
	 * @param array $rules Validation rules.
	 * @return ValidationResultInterface
	 */
	public function validate( array $data, array $rules ): ValidationResultInterface;

	/**
	 * Sanitize data using WordPress sanitization functions.
	 *
	 * @param array $data Data to sanitize.
	 * @return array
	 */
	public function sanitize( array $data ): array;
}
