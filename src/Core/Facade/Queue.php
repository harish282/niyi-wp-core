<?php
/**
 * Queue facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\Queue\QueueInterface;

/**
 * Static proxy for QueueInterface.
 */
class Queue extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return QueueInterface::class;
	}
}
