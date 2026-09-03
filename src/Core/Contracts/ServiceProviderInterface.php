<?php
/**
 * Service provider contract.
 *
 * Defines the interface that all service providers must implement to
 * register and bootstrap services in the container.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Contracts;

/**
 * Contract for service providers.
 */
interface ServiceProviderInterface {

	/**
	 * Register services into the container.
	 *
	 * Should only register bindings. Must not execute business logic,
	 * schedule jobs, or run database migrations.
	 *
	 * @return void
	 */
	public function register(): void;

	/**
	 * Bootstrap services after all providers have been registered.
	 *
	 * Used for event registration, hook registration, and final
	 * initialization. Must not create services.
	 *
	 * @return void
	 */
	public function boot(): void;
}
