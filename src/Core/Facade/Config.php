<?php
/**
 * Settings facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\Settings\SettingsInterface;

/**
 * Static proxy for SettingsInterface.
 */
class Config extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return SettingsInterface::class;
	}
}
