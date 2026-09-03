<?php
/**
 * Base event class.
 *
 * Extend this class to create typed events that carry data about something
 * that happened in the application.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Events;

/**
 * Base event object.
 */
class Event {

	/**
	 * Event payload.
	 *
	 * @var array<string, mixed>
	 */
	protected array $payload = array();

	/**
	 * Build the event with optional payload data.
	 *
	 * @param array<string, mixed> $payload Event payload.
	 */
	public function __construct( array $payload = array() ) {
		$this->payload = $payload;
	}

	/**
	 * Retrieve a payload value.
	 *
	 * @param string $key     Payload key.
	 * @param mixed  $default Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed {
		return $this->payload[ $key ] ?? $default;
	}

	/**
	 * Retrieve the full payload.
	 *
	 * @return array<string, mixed>
	 */
	public function payload(): array {
		return $this->payload;
	}
}
