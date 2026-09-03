<?php
/**
 * Queue facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\Queue\QueueInterface;

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
