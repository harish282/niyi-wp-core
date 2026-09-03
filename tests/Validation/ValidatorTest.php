<?php
/**
 * Tests for Validator service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Validation;

use NiyiWPCore\Core\Validation\Validator;
use NiyiWPCore\Tests\TestCase;

/**
 * Validator tests.
 */
class ValidatorTest extends TestCase {

	/**
	 * Test required rule passes for non-empty string.
	 *
	 * @return void
	 */
	public function test_required_passes_for_non_empty_string(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'name' => 'test' ), array( 'name' => 'required' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test required rule fails for empty string.
	 *
	 * @return void
	 */
	public function test_required_fails_for_empty_string(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'name' => '' ), array( 'name' => 'required' ) );

		$this->assertTrue( $result->fails() );
		$this->assertArrayHasKey( 'name', $result->errors() );
	}

	/**
	 * Test required rule fails for null.
	 *
	 * @return void
	 */
	public function test_required_fails_for_null(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'name' => null ), array( 'name' => 'required' ) );

		$this->assertTrue( $result->fails() );
	}

	/**
	 * Test string rule passes for strings.
	 *
	 * @return void
	 */
	public function test_string_passes_for_strings(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'name' => 'test' ), array( 'name' => 'string' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test string rule fails for non-strings.
	 *
	 * @return void
	 */
	public function test_string_fails_for_non_strings(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'name' => 123 ), array( 'name' => 'string' ) );

		$this->assertTrue( $result->fails() );
	}

	/**
	 * Test integer rule passes for integers.
	 *
	 * @return void
	 */
	public function test_integer_passes_for_integers(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'count' => 5 ), array( 'count' => 'integer' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test integer rule passes for numeric strings.
	 *
	 * @return void
	 */
	public function test_integer_passes_for_numeric_strings(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'count' => '5' ), array( 'count' => 'integer' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test min rule for numbers.
	 *
	 * @return void
	 */
	public function test_min_passes_for_number_above_minimum(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'count' => 5 ), array( 'count' => 'min:1' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test min rule fails for numbers below minimum.
	 *
	 * @return void
	 */
	public function test_min_fails_for_number_below_minimum(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'count' => 0 ), array( 'count' => 'min:1' ) );

		$this->assertTrue( $result->fails() );
	}

	/**
	 * Test max rule for numbers.
	 *
	 * @return void
	 */
	public function test_max_passes_for_number_below_maximum(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'count' => 5 ), array( 'count' => 'max:10' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test max rule fails for numbers above maximum.
	 *
	 * @return void
	 */
	public function test_max_fails_for_number_above_maximum(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'count' => 15 ), array( 'count' => 'max:10' ) );

		$this->assertTrue( $result->fails() );
	}

	/**
	 * Test in rule passes for allowed value.
	 *
	 * @return void
	 */
	public function test_in_passes_for_allowed_value(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'color' => 'red' ), array( 'color' => 'in:red,green,blue' ) );

		$this->assertTrue( $result->passes() );
	}

	/**
	 * Test in rule fails for disallowed value.
	 *
	 * @return void
	 */
	public function test_in_fails_for_disallowed_value(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'color' => 'yellow' ), array( 'color' => 'in:red,green,blue' ) );

		$this->assertTrue( $result->fails() );
	}

	/**
	 * Test sanitize removes HTML tags.
	 *
	 * @return void
	 */
	public function test_sanitize_removes_html_tags(): void {
		$validator = new Validator();
		$result    = $validator->sanitize( array( 'name' => '<script>alert(1)</script>test' ) );

		$this->assertSame( 'alert(1)test', $result['name'] );
	}

	/**
	 * Test sanitize handles nested arrays.
	 *
	 * @return void
	 */
	public function test_sanitize_handles_nested_arrays(): void {
		$validator = new Validator();
		$result    = $validator->sanitize( array( 'user' => array( 'name' => '<b>test</b>' ) ) );

		$this->assertSame( 'test', $result['user']['name'] );
	}

	/**
	 * Test validated returns sanitized data.
	 *
	 * @return void
	 */
	public function test_validated_returns_sanitized_data(): void {
		$validator = new Validator();
		$result    = $validator->validate( array( 'name' => '<b>test</b>' ), array( 'name' => 'required|string' ) );

		$this->assertSame( 'test', $result->validated()['name'] );
	}
}
