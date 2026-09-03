<?php
/**
 * WordPress options-backed settings store.
 *
 * Provides a clean API for persistent plugin settings using a single
 * WordPress option. Settings are cached in memory for the current request
 * and persisted via `save()`.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Settings;

use NiyiWPCore\Core\Settings\SettingsException;

/**
 * Wraps the WordPress Options API for plugin settings.
 */
class Settings implements SettingsInterface {

	/**
	 * WordPress option name.
	 *
	 * @var string
	 */
	private string $option;

	/**
	 * In-memory settings cache.
	 *
	 * @var array<string, mixed>
	 */
	private array $items = array();

	/**
	 * Build the settings store.
	 *
	 * @param string $option WordPress option name.
	 * @param array  $values Initial values merged over stored settings.
	 */
	public function __construct( string $option = 'niyi_woo_smart_upsells_settings', array $values = array() ) {
		$this->option = $option;

		$this->reload();
		$this->items = array_merge( $this->items, $values );
	}

	/**
	 * Retrieve a setting value.
	 *
	 * @param string $key     Setting key (dot notation supported).
	 * @param mixed  $default Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed {
		if ( ! str_contains( $key, '.' ) ) {
			return $this->items[ $key ] ?? $default;
		}

		return $this->resolveNestedGet( $this->items, $key, $default );
	}

	/**
	 * Store a setting value in memory.
	 *
	 * @param string $key   Setting key (dot notation supported).
	 * @param mixed  $value Value to store.
	 * @return self
	 */
	public function set( string $key, mixed $value ): self {
		if ( ! str_contains( $key, '.' ) ) {
			$this->items[ $key ] = $value;

			return $this;
		}

		$keys = explode( '.', $key );

		$this->resolveNestedSet( $this->items, $keys, $value );

		return $this;
	}

	/**
	 * Whether a setting key exists.
	 *
	 * @param string $key Setting key (dot notation supported).
	 * @return bool
	 */
	public function has( string $key ): bool {
		if ( ! str_contains( $key, '.' ) ) {
			return array_key_exists( $key, $this->items );
		}

		return null !== $this->get( $key, '__NIYI_MISSING__' );
	}

	/**
	 * Remove a setting value from memory.
	 *
	 * @param string $key Setting key (dot notation supported).
	 * @return bool
	 */
	public function delete( string $key ): bool {
		if ( ! $this->has( $key ) ) {
			return false;
		}

		if ( ! str_contains( $key, '.' ) ) {
			unset( $this->items[ $key ] );

			return true;
		}

		$keys = explode( '.', $key );
		$last = array_pop( $keys );

		$target = &$this->items;

		foreach ( $keys as $segment ) {
			$target = &$target[ $segment ];
		}

		unset( $target[ $last ] );

		return true;
	}

	/**
	 * Retrieve all settings currently in memory.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array {
		return $this->items;
	}

	/**
	 * Traverse the array by dot segments to retrieve a nested value.
	 *
	 * @param array  $items   Array to traverse.
	 * @param string $key     Dot-notated key.
	 * @param mixed  $default Fallback when the path is absent.
	 * @return mixed
	 */
	private function resolveNestedGet( array $items, string $key, mixed $default ): mixed {
		foreach ( explode( '.', $key ) as $segment ) {
			if ( ! is_array( $items ) || ! array_key_exists( $segment, $items ) ) {
				return $default;
			}

			$items = $items[ $segment ];
		}

		return $items;
	}

	/**
	 * Create nested array structure and set a value at the leaf.
	 *
	 * @param array    &$items Array to modify.
	 * @param string[] $keys   Segments of the dot-notated key.
	 * @param mixed    $value  Value to set.
	 * @return void
	 */
	private function resolveNestedSet( array &$items, array $keys, mixed $value ): void {
		$target = &$items;

		foreach ( $keys as $segment ) {
			if ( ! isset( $target[ $segment ] ) || ! is_array( $target[ $segment ] ) ) {
				$target[ $segment ] = array();
			}

			$target = &$target[ $segment ];
		}

		$target = $value;
	}

	/**
	 * Persist settings to the WordPress options table.
	 *
	 * @return bool
	 */
	public function save(): bool {
		return update_option( $this->option, $this->items );
	}

	/**
	 * Reload settings from the WordPress options table.
	 *
	 * @return void
	 */
	public function reload(): void {
		$stored = get_option( $this->option );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$this->items = $stored;
	}
}
