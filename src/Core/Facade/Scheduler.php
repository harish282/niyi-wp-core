<?php
/**
 * Scheduler facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\Scheduler\SchedulerInterface;

/**
 * Static proxy for SchedulerInterface.
 */
class Scheduler extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return SchedulerInterface::class;
	}
}
