<?php
/**
 * HTTP facade.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Facade;

use NiyiWooSmartUpsells\Core\HTTP\HTTPClientInterface;

/**
 * Static proxy for HTTPClientInterface.
 */
class Http extends Facade {

	/**
	 * {@inheritdoc}
	 */
	protected static function getFacadeAccessor(): string {
		return HTTPClientInterface::class;
	}
}
