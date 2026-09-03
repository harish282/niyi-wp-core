<?php
/**
 * Hooks facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

/**
 * Static proxy for HookManagerInterface.
 */
class Hooks extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return \NiyiWooSmartUpsells\Core\Hooks\HookManagerInterface::class;
	}
}
