<?php
/**
 * Logger facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

/**
 * Static proxy for LoggerInterface.
 */
class Log extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWooSmartUpsells\Contracts\LoggerInterface::class;
	}
}
