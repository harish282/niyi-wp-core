<?php
/**
 * HTTP client contract.
 *
 * Defines the public API for making outbound HTTP requests. Implementations
 * must remain framework-agnostic except for delegating to the WordPress HTTP
 * API internally.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\HTTP;

use NiyiWooSmartUpsells\Core\HTTP\HTTPResponse;

/**
 * Contract for HTTP clients.
 */
interface HTTPClientInterface {

	/**
	 * Send a GET request.
	 *
	 * @param string $url     Request URL.
	 * @param array  $options Request options.
	 * @return HTTPResponse
	 */
	public function get( string $url, array $options = array() ): HTTPResponse;

	/**
	 * Send a POST request.
	 *
	 * @param string       $url     Request URL.
	 * @param array|string $body    Request body.
	 * @param array        $options Request options.
	 * @return HTTPResponse
	 */
	public function post( string $url, array|string $body = array(), array $options = array() ): HTTPResponse;

	/**
	 * Send a PUT request.
	 *
	 * @param string       $url     Request URL.
	 * @param array|string $body    Request body.
	 * @param array        $options Request options.
	 * @return HTTPResponse
	 */
	public function put( string $url, array|string $body = array(), array $options = array() ): HTTPResponse;

	/**
	 * Send a DELETE request.
	 *
	 * @param string $url     Request URL.
	 * @param array  $options Request options.
	 * @return HTTPResponse
	 */
	public function delete( string $url, array $options = array() ): HTTPResponse;

	/**
	 * Send a request with a custom method.
	 *
	 * @param string $method  HTTP method.
	 * @param string $url     Request URL.
	 * @param array  $options Request options.
	 * @return HTTPResponse
	 */
	public function request( string $method, string $url, array $options = array() ): HTTPResponse;
}
