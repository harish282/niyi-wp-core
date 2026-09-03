<?php
/**
 * View facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

/**
 * Static proxy for ViewInterface.
 */
class View extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWPCore\Core\View\ViewInterface::class;
	}
}
