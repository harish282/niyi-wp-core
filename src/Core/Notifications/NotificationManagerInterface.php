<?php
/**
 * Notification manager contract.
 *
 * Defines the public API for displaying WordPress notifications.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Notifications;

/**
 * Contract for notification managers.
 */
interface NotificationManagerInterface {

	/**
	 * Display a success notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function success( string $message ): void;

	/**
	 * Display a warning notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function warning( string $message ): void;

	/**
	 * Display an error notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function error( string $message ): void;

	/**
	 * Display an informational notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function info( string $message ): void;
}
