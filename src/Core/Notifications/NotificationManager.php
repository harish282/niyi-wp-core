<?php
/**
 * WordPress notification manager.
 *
 * Provides a unified API for displaying WordPress admin notifications.
 * Supports success, warning, error, and info types. Notices can be queued
 * for the next request using WordPress options.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Notifications;

use NiyiWPCore\Core\Notifications\Notification;

/**
 * Displays WordPress admin notifications.
 */
class NotificationManager implements NotificationManagerInterface {

	/**
	 * Option key for storing queued notifications.
	 *
	 * @var string
	 */
	private const OPTION_KEY = 'niyi_wsu_notifications';

	/**
	 * Display a success notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function success( string $message ): void {
		$this->enqueue( 'success', $message );
	}

	/**
	 * Display a warning notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function warning( string $message ): void {
		$this->enqueue( 'warning', $message );
	}

	/**
	 * Display an error notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function error( string $message ): void {
		$this->enqueue( 'error', $message );
	}

	/**
	 * Display an informational notification.
	 *
	 * @param string $message Notification message.
	 * @return void
	 */
	public function info( string $message ): void {
		$this->enqueue( 'info', $message );
	}

	/**
	 * Store a notification for display.
	 *
	 * @param string $type    Notification type.
	 * @param string $message Notification message.
	 * @return void
	 */
	private function enqueue( string $type, string $message ): void {
		$stored = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$stored[] = array(
			'type'    => $type,
			'message' => $message,
		);

		update_option( self::OPTION_KEY, $stored );
	}

	/**
	 * Retrieve all queued notifications.
	 *
	 * @return list<Notification>
	 */
	private function all(): array {
		$stored = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			return array();
		}

		$notifications = array();

		foreach ( $stored as $item ) {
			if ( is_array( $item ) && isset( $item['type'], $item['message'] ) ) {
				$notifications[] = new Notification( $item['type'], $item['message'] );
			} elseif ( is_object( $item ) && method_exists( $item, 'type' ) && method_exists( $item, 'message' ) ) {
				$notifications[] = new Notification( $item->type(), $item->message() );
			}
		}

		return $notifications;
	}

	/**
	 * Flush all queued notifications.
	 *
	 * @return void
	 */
	public function flush(): void {
		delete_option( self::OPTION_KEY );
	}

	/**
	 * Render all queued notifications as admin notices.
	 *
	 * @return void
	 */
	public function render(): void {
		$notifications = $this->all();

		if ( empty( $notifications ) ) {
			return;
		}

		foreach ( $notifications as $notification ) {
			$this->render_notice( $notification );
		}

		$this->flush();
	}

	/**
	 * Render a single notification as an admin notice.
	 *
	 * @param Notification $notification Notification to render.
	 * @return void
	 */
	private function render_notice( Notification $notification ): void {
		$type    = esc_html( $notification->type() );
		$message = esc_html( $notification->message() );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $message . '</p></div>';
	}
}
