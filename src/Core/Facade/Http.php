<?php
/**
 * HTTP facade.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Facade;

use NiyiWPCore\Core\HTTP\HTTPClientInterface;

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
