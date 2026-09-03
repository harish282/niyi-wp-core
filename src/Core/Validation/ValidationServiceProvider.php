<?php
/**
 * Validation service provider.
 *
 * Registers the validator into the container as a singleton.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Validation;

use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\Contracts\ServiceProviderInterface;
use NiyiWPCore\Core\Providers\AbstractServiceProvider;

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
