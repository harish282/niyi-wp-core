<?php
/**
 * Logger facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

/**
 * Static proxy for LoggerInterface.
 */
class Log extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWPCore\Core\Logging\LoggerInterface::class;
	}
}
