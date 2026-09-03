<?php
/**
 * Notification value object.
 *
 * Represents a single notification message with type and content.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Notifications;

/**
 * Represents a notification message.
 */
class Notification {

	/**
	 * Supported notification types.
	 *
	 * @var list<string>
	 */
	public static array $types = array( 'success', 'warning', 'error', 'info' );

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * Notification message.
	 *
	 * @var string
	 */
	private string $message;

	/**
	 * Build a notification.
	 *
	 * @param string $type    Notification type.
	 * @param string $message Notification message.
	 */
	public function __construct( string $type, string $message ) {
		$this->type    = $type;
		$this->message = $message;
	}

	/**
	 * Notification type.
	 *
	 * @return string
	 */
	public function type(): string {
		return $this->type;
	}

	/**
	 * Notification message.
	 *
	 * @return string
	 */
	public function message(): string {
		return $this->message;
	}
}
