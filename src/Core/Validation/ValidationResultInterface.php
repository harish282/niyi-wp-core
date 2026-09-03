<?php
/**
 * Validation result contract.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Validation;

/**
 * Contract for validation results.
 */
interface ValidationResultInterface {

	/**
	 * Whether validation failed.
	 *
	 * @return bool
	 */
	public function fails(): bool;

	/**
	 * Whether validation passed.
	 *
	 * @return bool
	 */
	public function passes(): bool;

	/**
	 * Validation errors keyed by field name.
	 *
	 * @return array<string, list<string>>
	 */
	public function errors(): array;

	/**
	 * Sanitized and validated data.
	 *
	 * @return array<string, mixed>
	 */
	public function validated(): array;
}
