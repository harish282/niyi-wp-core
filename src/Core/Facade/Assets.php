<?php
/**
 * Assets facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\Assets\AssetManagerInterface;

/**
 * Static proxy for AssetManagerInterface.
 */
class Assets extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return AssetManagerInterface::class;
	}
}
