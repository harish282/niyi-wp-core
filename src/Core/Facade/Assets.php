<?php
/**
 * Assets facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\Assets\AssetManagerInterface;

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
