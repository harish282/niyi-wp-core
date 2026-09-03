<?php
/**
 * Scheduler facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\Scheduler\SchedulerInterface;

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
