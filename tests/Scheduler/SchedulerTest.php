<?php
/**
 * Tests for the WordPress cron scheduler.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Scheduler;

use NiyiWPCore\Core\Scheduler\Scheduler;
use NiyiWPCore\Core\Scheduler\SchedulerException;
use NiyiWPCore\Tests\Support\SpyLogger;
use NiyiWPCore\Tests\TestCase;

/**
 * Scheduler tests.
 */
class SchedulerTest extends TestCase {

	/**
	 * Logger spy.
	 *
	 * @var SpyLogger
	 */
	private SpyLogger $logger;

	/**
	 * Scheduler under test.
	 *
	 * @var Scheduler
	 */
	private Scheduler $scheduler;

	/**
	 * Build a fresh scheduler.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->logger    = new SpyLogger();
		$this->scheduler = new Scheduler( $this->logger );
	}

	/**
	 * Test schedule() registers a recurring event.
	 *
	 * @return void
	 */
	public function test_schedule_registers_a_recurring_event(): void {
		$result = $this->scheduler->schedule( 'my_recurring_hook', 'hourly', array( 'product_id' => 42 ) );

		$this->assertTrue( $result );
		$this->assertTrue( $this->scheduler->is_scheduled( 'my_recurring_hook' ) );

		$event = $GLOBALS['niyi_test_scheduled']['my_recurring_hook'];

		$this->assertSame( 'hourly', $event['interval'] );
		$this->assertSame( array( 'product_id' => 42 ), $event['args'] );
	}

	/**
	 * Test schedule() declines a duplicate registration.
	 *
	 * @return void
	 */
	public function test_schedule_returns_false_when_already_scheduled(): void {
		$this->scheduler->schedule( 'my_recurring_hook', 'hourly' );

		$this->assertFalse( $this->scheduler->schedule( 'my_recurring_hook', 'daily' ) );
	}

	/**
	 * Test schedule() accepts custom intervals from wp_get_schedules().
	 *
	 * @return void
	 */
	public function test_schedule_accepts_custom_intervals(): void {
		$GLOBALS['niyi_test_schedules'] = array( 'every_minute' => array( 'interval' => 60 ) );

		$this->assertTrue( $this->scheduler->schedule( 'custom_hook', 'every_minute' ) );
	}

	/**
	 * Test schedule() throws for an invalid interval.
	 *
	 * @return void
	 */
	public function test_schedule_throws_for_invalid_interval(): void {
		$this->expectException( SchedulerException::class );
		$this->expectExceptionMessage( 'Invalid cron interval' );

		$this->scheduler->schedule( 'my_recurring_hook', 'not_a_real_interval' );
	}

	/**
	 * Test schedule_once() registers a single-run event at the timestamp.
	 *
	 * @return void
	 */
	public function test_schedule_once_registers_a_single_event(): void {
		$timestamp = 1750000000;

		$result = $this->scheduler->schedule_once( 'my_single_hook', $timestamp, array( 'x' => 1 ) );

		$this->assertTrue( $result );
		$this->assertSame( $timestamp, wp_next_scheduled( 'my_single_hook' ) );
		$this->assertSame( array( 'x' => 1 ), $GLOBALS['niyi_test_single_events']['my_single_hook']['args'] );
	}

	/**
	 * Test schedule_once() declines a duplicate registration.
	 *
	 * @return void
	 */
	public function test_schedule_once_returns_false_when_already_scheduled(): void {
		$this->scheduler->schedule_once( 'my_single_hook', 1750000000 );

		$this->assertFalse( $this->scheduler->schedule_once( 'my_single_hook', 1750000001 ) );
	}

	/**
	 * Test unschedule() removes a scheduled event.
	 *
	 * @return void
	 */
	public function test_unschedule_clears_the_hook(): void {
		$this->scheduler->schedule( 'my_recurring_hook', 'hourly' );
		$this->assertTrue( $this->scheduler->is_scheduled( 'my_recurring_hook' ) );

		$this->assertTrue( $this->scheduler->unschedule( 'my_recurring_hook' ) );
		$this->assertFalse( $this->scheduler->is_scheduled( 'my_recurring_hook' ) );
	}

	/**
	 * Test is_scheduled() reflects the cron state.
	 *
	 * @return void
	 */
	public function test_is_scheduled_reflects_state(): void {
		$this->assertFalse( $this->scheduler->is_scheduled( 'never_scheduled_hook' ) );

		$this->scheduler->schedule( 'my_recurring_hook', 'daily' );

		$this->assertTrue( $this->scheduler->is_scheduled( 'my_recurring_hook' ) );
	}

	/**
	 * Test run_now() fires the hook with its arguments.
	 *
	 * @return void
	 */
	public function test_run_now_fires_the_hook(): void {
		$this->scheduler->run_now( 'my_manual_hook', array( 'a', 'b' ) );

		$actions = $GLOBALS['niyi_test_actions'];
		$this->assertCount( 1, $actions );
		$this->assertSame( 'my_manual_hook', $actions[0]['hook'] );
		$this->assertSame( array( 'a', 'b' ), $actions[0]['args'] );
	}

	/**
	 * Test run_now() logs an info entry.
	 *
	 * @return void
	 */
	public function test_run_now_logs_an_entry(): void {
		$this->scheduler->run_now( 'my_manual_hook' );

		$this->assertContains( 'Scheduler: running hook "my_manual_hook" immediately.', $this->logger->messages_at( 'info' ) );
	}
}
