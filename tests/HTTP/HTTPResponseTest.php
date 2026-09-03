<?php
/**
 * Tests for HTTPResponse service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\HTTP;

use NiyiWPCore\Core\HTTP\HTTPResponse;
use NiyiWPCore\Tests\TestCase;

/**
 * HTTP response tests.
 */
class HTTPResponseTest extends TestCase {

	/**
	 * Test status code retrieval.
	 *
	 * @return void
	 */
	public function test_status_returns_code(): void {
		$response = new HTTPResponse( array( 'response' => array( 'code' => 200 ) ) );

		$this->assertSame( 200, $response->status() );
	}

	/**
	 * Test status code defaults to 0.
	 *
	 * @return void
	 */
	public function test_status_returns_zero_when_missing(): void {
		$response = new HTTPResponse( array() );

		$this->assertSame( 0, $response->status() );
	}

	/**
	 * Test body retrieval.
	 *
	 * @return void
	 */
	public function test_body_returns_string(): void {
		$response = new HTTPResponse( array( 'body' => 'test body' ) );

		$this->assertSame( 'test body', $response->body() );
	}

	/**
	 * Test successful response detection.
	 *
	 * @return void
	 */
	public function test_successful_returns_true_for_2xx(): void {
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 200 ) ) ) )->successful() );
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 201 ) ) ) )->successful() );
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 299 ) ) ) )->successful() );
	}

	/**
	 * Test failed response detection.
	 *
	 * @return void
	 */
	public function test_failed_returns_true_for_non_2xx(): void {
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 404 ) ) ) )->failed() );
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 500 ) ) ) )->failed() );
	}

	/**
	 * Test client error detection.
	 *
	 * @return void
	 */
	public function test_client_error_returns_true_for_4xx(): void {
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 400 ) ) ) )->clientError() );
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 404 ) ) ) )->clientError() );
		$this->assertFalse( ( new HTTPResponse( array( 'response' => array( 'code' => 500 ) ) ) )->clientError() );
	}

	/**
	 * Test server error detection.
	 *
	 * @return void
	 */
	public function test_server_error_returns_true_for_5xx(): void {
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 500 ) ) ) )->serverError() );
		$this->assertTrue( ( new HTTPResponse( array( 'response' => array( 'code' => 503 ) ) ) )->serverError() );
		$this->assertFalse( ( new HTTPResponse( array( 'response' => array( 'code' => 404 ) ) ) )->serverError() );
	}

	/**
	 * Test JSON decoding.
	 *
	 * @return void
	 */
	public function test_json_decodes_valid_json(): void {
		$response = new HTTPResponse( array( 'body' => '{"key":"value"}' ) );

		$this->assertSame( array( 'key' => 'value' ), $response->json() );
	}

	/**
	 * Test JSON decoding throws exception for invalid JSON.
	 *
	 * @return void
	 */
	public function test_json_throws_exception_for_invalid_json(): void {
		$this->expectException( \NiyiWPCore\Core\HTTP\HTTPException::class );

		$response = new HTTPResponse( array( 'body' => 'not json' ) );
		$response->json();
	}

	/**
	 * Test headers retrieval.
	 *
	 * @return void
	 */
	public function test_headers_returns_array(): void {
		$response = new HTTPResponse( array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
		) );

		$this->assertSame( array( 'Content-Type' => 'application/json' ), $response->headers() );
	}

	/**
	 * Test headers returns empty array when missing.
	 *
	 * @return void
	 */
	public function test_headers_returns_empty_array_when_missing(): void {
		$response = new HTTPResponse( array() );

		$this->assertSame( array(), $response->headers() );
	}
}
