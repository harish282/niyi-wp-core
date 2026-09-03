<?php
/**
 * Tests for EventDispatcher service.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Events;

use NiyiWPCore\Core\Events\Event;
use NiyiWPCore\Core\Events\EventDispatcher;
use NiyiWPCore\Tests\TestCase;

/**
 * Event dispatcher tests.
 */
class EventDispatcherTest extends TestCase {

	/**
	 * Test dispatching an event with no listeners.
	 *
	 * @return void
	 */
	public function test_dispatch_with_no_listeners_does_not_throw(): void {
		$dispatcher = new EventDispatcher();
		$event      = new class extends Event {
			public function __construct() {
				parent::__construct( array( 'id' => 1 ) );
			}
		};

		$dispatcher->dispatch( $event );

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Test registering and dispatching a listener.
	 *
	 * @return void
	 */
	public function test_dispatch_calls_listener(): void {
		$dispatcher = new EventDispatcher();
		$called     = false;

		$dispatcher->listen( Event::class, function () use ( &$called ) {
			$called = true;
		} );

		$event = new Event();
		$dispatcher->dispatch( $event );

		$this->assertTrue( $called );
	}

	/**
	 * Test that event payload is passed to listener.
	 *
	 * @return void
	 */
	public function test_dispatch_passes_event_to_listener(): void {
		$dispatcher = new EventDispatcher();
		$payload    = null;

		$dispatcher->listen( Event::class, function ( $event ) use ( &$payload ) {
			$payload = $event->payload();
		} );

		$event = new Event( array( 'id' => 1 ) );
		$dispatcher->dispatch( $event );

		$this->assertSame( array( 'id' => 1 ), $payload );
	}

	/**
	 * Test forgetting listeners.
	 *
	 * @return void
	 */
	public function test_forget_removes_listeners(): void {
		$dispatcher = new EventDispatcher();
		$called     = false;

		$dispatcher->listen( Event::class, function () use ( &$called ) {
			$called = true;
		} );

		$dispatcher->forget( Event::class );

		$event = new Event();
		$dispatcher->dispatch( $event );

		$this->assertFalse( $called );
	}

	/**
	 * Test checking if event has listeners.
	 *
	 * @return void
	 */
	public function test_has_listeners_returns_correct_status(): void {
		$dispatcher = new EventDispatcher();

		$this->assertFalse( $dispatcher->has_listeners( Event::class ) );

		$dispatcher->listen( Event::class, function () {} );

		$this->assertTrue( $dispatcher->has_listeners( Event::class ) );
	}

	/**
	 * Test dispatching to multiple listeners.
	 *
	 * @return void
	 */
	public function test_dispatch_calls_multiple_listeners(): void {
		$dispatcher = new EventDispatcher();
		$calls      = 0;

		$dispatcher->listen( Event::class, function () use ( &$calls ) {
			$calls++;
		} );

		$dispatcher->listen( Event::class, function () use ( &$calls ) {
			$calls++;
		} );

		$event = new Event();
		$dispatcher->dispatch( $event );

		$this->assertSame( 2, $calls );
	}
}
