<?php
/**
 * Validation result.
 *
 * Holds the outcome of a validation pass, including errors and sanitized data.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Validation;

/**
 * Represents the outcome of a validation pass.
 */
class ValidationResult implements ValidationResultInterface {

	/**
	 * Validation errors keyed by field name.
	 *
	 * @var array<string, list<string>>
	 */
	private array $errors;

	/**
	 * Sanitized and validated data.
	 *
	 * @var array<string, mixed>
	 */
	private array $data;

	/**
	 * Build a validation result.
	 *
	 * @param array<string, list<string>> $errors Validation errors.
	 * @param array<string, mixed>        $data   Sanitized data.
	 */
	public function __construct( array $errors, array $data ) {
		$this->errors = $errors;
		$this->data   = $data;
	}

	/**
	 * Whether validation failed.
	 *
	 * @return bool
	 */
	public function fails(): bool {
		return ! empty( $this->errors );
	}

	/**
	 * Whether validation passed.
	 *
	 * @return bool
	 */
	public function passes(): bool {
		return empty( $this->errors );
	}

	/**
	 * Validation errors keyed by field name.
	 *
	 * @return array<string, list<string>>
	 */
	public function errors(): array {
		return $this->errors;
	}

	/**
	 * Sanitized and validated data.
	 *
	 * @return array<string, mixed>
	 */
	public function validated(): array {
		return $this->data;
	}
}
