<?php
/**
 * View facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

/**
 * Static proxy for ViewInterface.
 */
class View extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWooSmartUpsells\Core\View\ViewInterface::class;
	}
}
