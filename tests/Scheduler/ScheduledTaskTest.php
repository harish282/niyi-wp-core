<?php
/**
 * Tests for the scheduled task value object.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Scheduler;

use NiyiWPCore\Core\Scheduler\ScheduledTask;
use NiyiWPCore\Tests\TestCase;

/**
 * Scheduled task tests.
 */
class ScheduledTaskTest extends TestCase {

	/**
	 * Test a recurring task exposes its hook, interval, and arguments.
	 *
	 * @return void
	 */
	public function test_recurring_task_exposes_its_properties(): void {
		$task = new ScheduledTask( 'my_hook', 'hourly', array( 'a' => 1 ) );

		$this->assertSame( 'my_hook', $task->hook() );
		$this->assertSame( 'hourly', $task->interval() );
		$this->assertSame( array( 'a' => 1 ), $task->arguments() );
		$this->assertNull( $task->timestamp() );
		$this->assertFalse( $task->is_once() );
	}

	/**
	 * Test a one-time task carries its timestamp.
	 *
	 * @return void
	 */
	public function test_once_task_carries_its_timestamp(): void {
		$task = ScheduledTask::once( 'my_hook', 1750000000, array( 'b' => 2 ) );

		$this->assertSame( 'my_hook', $task->hook() );
		$this->assertSame( 'once', $task->interval() );
		$this->assertSame( array( 'b' => 2 ), $task->arguments() );
		$this->assertSame( 1750000000, $task->timestamp() );
		$this->assertTrue( $task->is_once() );
	}

	/**
	 * Test the arguments default to an empty array.
	 *
	 * @return void
	 */
	public function test_arguments_default_to_empty(): void {
		$task = new ScheduledTask( 'my_hook', 'daily' );

		$this->assertSame( array(), $task->arguments() );
	}
}
