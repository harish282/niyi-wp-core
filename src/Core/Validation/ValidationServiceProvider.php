<?php
/**
 * Validation service provider.
 *
 * Registers the validator into the container as a singleton.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Validation;

use NiyiWooSmartUpsells\Core\Container\ContainerInterface;
use NiyiWooSmartUpsells\Core\Contracts\ServiceProviderInterface;
use NiyiWooSmartUpsells\Core\Providers\AbstractServiceProvider;

/**
 * Registers the validator.
 */
class ValidationServiceProvider extends AbstractServiceProvider {

	/**
	 * Register validation bindings.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton(
			ValidatorInterface::class,
			fn() => new Validator()
		);
	}

	/**
	 * Bootstrap the validation service.
	 *
	 * @return void
	 */
	public function boot(): void {
	}
}
