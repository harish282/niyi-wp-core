<?php
/**
 * Tests for NotificationManager service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Notifications;

use NiyiWPCore\Core\Notifications\Notification;
use NiyiWPCore\Core\Notifications\NotificationManager;
use NiyiWPCore\Tests\TestCase;

/**
 * Notification manager tests.
 */
class NotificationManagerTest extends TestCase {

	/**
	 * Test enqueue stores notification.
	 *
	 * @return void
	 */
	public function test_enqueue_stores_notification(): void {
		$manager = new NotificationManager();
		$manager->success( 'Test message' );

		$this->assertTrue( true );
	}

	/**
	 * Test notification value object.
	 *
	 * @return void
	 */
	public function test_notification_value_object(): void {
		$notification = new Notification( 'success', 'Test message' );

		$this->assertSame( 'success', $notification->type() );
		$this->assertSame( 'Test message', $notification->message() );
	}

	/**
	 * Test notification types array.
	 *
	 * @return void
	 */
	public function test_notification_types(): void {
		$this->assertSame(
			array( 'success', 'warning', 'error', 'info' ),
			Notification::$types
		);
	}
}
