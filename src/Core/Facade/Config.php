<?php
/**
 * Settings facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\Settings\SettingsInterface;

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
