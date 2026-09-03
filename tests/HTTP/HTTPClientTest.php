<?php
/**
 * Tests for HTTPClient service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\HTTP;

use NiyiWPCore\Core\HTTP\HTTPClient;
use NiyiWPCore\Tests\TestCase;

/**
 * HTTP client tests.
 */
class HTTPClientTest extends TestCase {

	/**
	 * Test that request method signature accepts required parameters.
	 *
	 * @return void
	 */
	public function test_request_method_exists(): void {
		$client = new HTTPClient();

		$this->assertTrue( method_exists( $client, 'request' ) );
	}

	/**
	 * Test that get method exists.
	 *
	 * @return void
	 */
	public function test_get_method_exists(): void {
		$client = new HTTPClient();

		$this->assertTrue( method_exists( $client, 'get' ) );
	}

	/**
	 * Test that post method exists.
	 *
	 * @return void
	 */
	public function test_post_method_exists(): void {
		$client = new HTTPClient();

		$this->assertTrue( method_exists( $client, 'post' ) );
	}

	/**
	 * Test that put method exists.
	 *
	 * @return void
	 */
	public function test_put_method_exists(): void {
		$client = new HTTPClient();

		$this->assertTrue( method_exists( $client, 'put' ) );
	}

	/**
	 * Test that delete method exists.
	 *
	 * @return void
	 */
	public function test_delete_method_exists(): void {
		$client = new HTTPClient();

		$this->assertTrue( method_exists( $client, 'delete' ) );
	}
}
