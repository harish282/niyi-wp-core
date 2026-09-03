<?php
/**
 * Settings contract.
 *
 * Defines the public API for persistent plugin settings backed by the
 * WordPress Options API. Framework-agnostic except for delegating to
 * WordPress functions internally.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Settings;

/**
 * Contract for settings services.
 */
interface SettingsInterface {

	/**
	 * Retrieve a setting value.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed;

	/**
	 * Store a setting value in memory.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Value to store.
	 * @return self
	 */
	public function set( string $key, mixed $value ): self;

	/**
	 * Whether a setting key exists.
	 *
	 * @param string $key Setting key.
	 * @return bool
	 */
	public function has( string $key ): bool;

	/**
	 * Remove a setting value from memory.
	 *
	 * @param string $key Setting key.
	 * @return bool
	 */
	public function delete( string $key ): bool;

	/**
	 * Retrieve all settings currently in memory.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array;

	/**
	 * Persist settings to the WordPress options table.
	 *
	 * @return bool
	 */
	public function save(): bool;

	/**
	 * Reload settings from the WordPress options table.
	 *
	 * @return void
	 */
	public function reload(): void;
}
