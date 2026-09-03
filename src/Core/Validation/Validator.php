<?php
/**
 * Lightweight WordPress-backed validator.
 *
 * Validates and sanitizes input arrays using WordPress sanitization and
 * validation functions. Returns structured results instead of throwing
 * exceptions on validation failure.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Validation;

use NiyiWPCore\Core\Validation\ValidationResult;

/**
 * Validates and sanitizes input data.
 */
class Validator implements ValidatorInterface {

	/**
	 * Supported validation rules.
	 *
	 * @var array<string, callable>
	 */
	private static array $rules = array();

	/**
	 * Get the supported validation rules.
	 *
	 * @return array<string, callable>
	 */
	private static function rules(): array {
		if ( ! empty( self::$rules ) ) {
			return self::$rules;
		}

		self::$rules = array(
			'required' => static function ( $value, array $params = array(), array $data = array() ) {
				return null !== $value && '' !== $value && ( is_array( $value ) ? ! empty( $value ) : true );
			},
			'string'   => static function ( $value ) {
				return is_string( $value );
			},
			'integer'  => static function ( $value ) {
				return is_int( $value ) || ctype_digit( (string) $value );
			},
			'email'    => static function ( $value ) {
				return is_email( $value );
			},
			'url'      => static function ( $value ) {
				return (bool) esc_url_raw( $value );
			},
			'min'      => static function ( $value, array $params = array() ) {
				if ( is_numeric( $value ) ) {
					return $value >= $params[0];
				}

				return strlen( (string) $value ) >= $params[0];
			},
			'max'      => static function ( $value, array $params = array() ) {
				if ( is_numeric( $value ) ) {
					return $value <= $params[0];
				}

				return strlen( (string) $value ) <= $params[0];
			},
			'in'       => static function ( $value, array $params = array() ) {
				return in_array( $value, $params, true );
			},
		);

		return self::$rules;
	}

	/**
	 * Validate data against rules.
	 *
	 * @param array $data  Data to validate.
	 * @param array $rules Validation rules.
	 * @return ValidationResultInterface
	 */
	public function validate( array $data, array $rules ): ValidationResultInterface {
		$errors    = array();
		$sanitized = $this->sanitize( $data );

		foreach ( $rules as $field => $rule_string ) {
			$value       = $sanitized[ $field ] ?? null;
			$field_rules = static::parse_rules( $rule_string );

			foreach ( $field_rules as $rule ) {
				$rule_name = $rule['name'];
				$params    = $rule['params'];

				if ( ! isset( self::rules()[ $rule_name ] ) ) {
					continue;
				}

				if ( ! ( self::rules()[ $rule_name ] )( $value, $params, $sanitized ) ) {
					$errors[ $field ][] = $rule_name;
				}
			}
		}

		return new ValidationResult( $errors, $sanitized );
	}

	/**
	 * Sanitize data using WordPress sanitization functions.
	 *
	 * @param array $data Data to sanitize.
	 * @return array
	 */
	public function sanitize( array $data ): array {
		$sanitized = array();

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$sanitized[ $key ] = $this->sanitize( $value );
				continue;
			}

			$sanitized[ $key ] = static::sanitize_value( $value );
		}

		return $sanitized;
	}

	/**
	 * Sanitize a single value using WordPress sanitization functions.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed
	 */
	private static function sanitize_value( mixed $value ): mixed {
		if ( is_email( $value ) ) {
			return sanitize_email( $value );
		}

		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return esc_url_raw( $value );
		}

		if ( is_int( $value ) || is_float( $value ) ) {
			return $value;
		}

		return sanitize_text_field( (string) $value );
	}

	/**
	 * Parse a rule string into structured rules.
	 *
	 * @param string $rule_string Rule string, e.g. "required|string|min:1|max:100".
	 * @return list<array{name: string, params: list<string>}>
	 */
	private static function parse_rules( string $rule_string ): array {
		$parsed = array();

		foreach ( explode( '|', $rule_string ) as $rule ) {
			if ( str_contains( $rule, ':' ) ) {
				[ $name, $params ] = explode( ':', $rule, 2 );
				$parsed[]          = array(
					'name'   => $name,
					'params' => explode( ',', $params ),
				);
			} else {
				$parsed[] = array(
					'name'   => $rule,
					'params' => array(),
				);
			}
		}

		return $parsed;
	}
}
