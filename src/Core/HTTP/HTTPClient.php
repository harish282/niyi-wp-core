<?php
/**
 * Lightweight HTTP client.
 *
 * Wraps the WordPress HTTP API (`wp_remote_request`) behind a clean,
 * object-oriented interface. Returns framework-agnostic response objects
 * and throws exceptions on transport errors.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\HTTP;

use NiyiWPCore\Core\HTTP\HTTPResponse;

/**
 * WordPress HTTP API wrapper.
 */
class HTTPClient implements HTTPClientInterface {

	/**
	 * Send an HTTP request.
	 *
	 * @param string $method  HTTP method.
	 * @param string $url     Request URL.
	 * @param array  $options Request options merged with method defaults.
	 * @return HTTPResponse
	 * @throws HTTPException When the WordPress HTTP API returns a transport error.
	 */
	public function request( string $method, string $url, array $options = array() ): HTTPResponse {
		$args = $this->buildArgs( $method, $options );

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new HTTPException(
				wp_kses_post(
					sprintf(
						'HTTP request to "%s" failed: %s',
						$url,
						$response->get_error_message()
					)
				)
			);
		}

		return new HTTPResponse( $response );
	}

	/**
	 * Send a GET request.
	 *
	 * @param string $url     Request URL.
	 * @param array  $options Request options.
	 * @return HTTPResponse
	 */
	public function get( string $url, array $options = array() ): HTTPResponse {
		return $this->request( 'GET', $url, $options );
	}

	/**
	 * Send a POST request.
	 *
	 * @param string       $url     Request URL.
	 * @param array|string $body    Request body.
	 * @param array        $options Request options.
	 * @return HTTPResponse
	 */
	public function post( string $url, array|string $body = array(), array $options = array() ): HTTPResponse {
		$options['body'] = $body;

		return $this->request( 'POST', $url, $options );
	}

	/**
	 * Send a PUT request.
	 *
	 * @param string       $url     Request URL.
	 * @param array|string $body    Request body.
	 * @param array        $options Request options.
	 * @return HTTPResponse
	 */
	public function put( string $url, array|string $body = array(), array $options = array() ): HTTPResponse {
		$options['body'] = $body;

		return $this->request( 'PUT', $url, $options );
	}

	/**
	 * Send a DELETE request.
	 *
	 * @param string $url     Request URL.
	 * @param array  $options Request options.
	 * @return HTTPResponse
	 */
	public function delete( string $url, array $options = array() ): HTTPResponse {
		return $this->request( 'DELETE', $url, $options );
	}

	/**
	 * Build WordPress HTTP API arguments from request options.
	 *
	 * @param string $method  HTTP method.
	 * @param array  $options Caller-supplied options.
	 * @return array<string, mixed>
	 */
	private function buildArgs( string $method, array $options ): array {
		$args = array(
			'method'  => strtoupper( $method ),
			'timeout' => $options['timeout'] ?? 15,
		);

		if ( isset( $options['headers'] ) && is_array( $options['headers'] ) ) {
			$args['headers'] = $options['headers'];
		}

		if ( array_key_exists( 'body', $options ) ) {
			$args['body'] = $options['body'];
		}

		if ( isset( $options['query'] ) && is_array( $options['query'] ) ) {
			$args['body'] = array_merge( (array) ( $args['body'] ?? array() ), $options['query'] );
		}

		return $args;
	}
}
