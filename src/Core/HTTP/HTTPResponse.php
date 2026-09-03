<?php
/**
 * HTTP response wrapper.
 *
 * Normalizes WordPress HTTP API responses into a consistent, framework-agnostic
 * object. Avoids leaking raw WordPress arrays outside this layer.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\HTTP;

/**
 * Lightweight HTTP response object.
 */
class HTTPResponse {

	/**
	 * Raw WordPress response data.
	 *
	 * @var array<string, mixed>
	 */
	private array $data;

	/**
	 * Build the response wrapper.
	 *
	 * @param array<string, mixed> $data Raw WordPress response.
	 */
	public function __construct( array $data ) {
		$this->data = $data;
	}

	/**
	 * HTTP status code.
	 *
	 * @return int
	 */
	public function status(): int {
		return (int) ( $this->data['response']['code'] ?? 0 );
	}

	/**
	 * Response body as a string.
	 *
	 * @return string
	 */
	public function body(): string {
		return (string) ( $this->data['body'] ?? '' );
	}

	/**
	 * Decode the response body as JSON.
	 *
	 * @return array<string, mixed>
	 * @throws HTTPException When the body is not valid JSON.
	 */
	public function json(): array {
		$decoded = json_decode( $this->body(), true );

		if ( ! is_array( $decoded ) ) {
			throw new HTTPException(
				wp_kses_post( sprintf( 'Invalid JSON response received (HTTP %d).', $this->status() ) )
			);
		}

		return $decoded;
	}

	/**
	 * Response headers.
	 *
	 * @return array<string, string>
	 */
	public function headers(): array {
		$headers = $this->data['headers'] ?? array();

		if ( ! is_array( $headers ) ) {
			return array();
		}

		return array_map(
			static fn ( $value ) => (string) $value,
			$headers
		);
	}

	/**
	 * Whether the request was successful (status 200-299).
	 *
	 * @return bool
	 */
	public function successful(): bool {
		return $this->status() >= 200 && $this->status() < 300;
	}

	/**
	 * Whether the request failed (status outside 200-299).
	 *
	 * @return bool
	 */
	public function failed(): bool {
		return ! $this->successful();
	}

	/**
	 * Whether the response is a client error (400-499).
	 *
	 * @return bool
	 */
	public function clientError(): bool {
		return $this->status() >= 400 && $this->status() < 500;
	}

	/**
	 * Whether the response is a server error (500-599).
	 *
	 * @return bool
	 */
	public function serverError(): bool {
		return $this->status() >= 500 && $this->status() < 600;
	}
}
