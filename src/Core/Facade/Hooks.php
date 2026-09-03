<?php
/**
 * Hooks facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

/**
 * Static proxy for HookManagerInterface.
 */
class Hooks extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWPCore\Core\Hooks\HookManagerInterface::class;
	}
}
