<?php
/**
 * WordPress cache wrapper.
 *
 * Provides a consistent caching API over the WordPress Object Cache and
 * Transients APIs. Values without TTL are stored in the object cache;
 * values with TTL are stored as transients for persistent expiration.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Cache;

use NiyiWooSmartUpsells\Core\Cache\CacheException;

/**
 * Wraps the WordPress caching APIs.
 */
class Cache implements CacheInterface {

	/**
	 * Retrieve a cached value.
	 *
	 * @param string $key     Cache key.
	 * @param mixed  $default Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed {
		$value = wp_cache_get( $key );

		if ( false === $value ) {
			return $default;
		}

		return $value;
	}

	/**
	 * Store a value in the cache.
	 *
	 * Uses transients when TTL is provided, otherwise uses the object cache.
	 *
	 * @param string $key  Cache key.
	 * @param mixed  $value Value to cache.
	 * @param int    $ttl   Time-to-live in seconds. 0 means no expiration.
	 * @return bool
	 */
	public function set( string $key, mixed $value, int $ttl = 0 ): bool {
		if ( $ttl > 0 ) {
			return set_transient( $key, $value, $ttl );
		}

		return wp_cache_set( $key, $value );
	}

	/**
	 * Retrieve a cached value or execute a callback to generate it.
	 *
	 * @param string   $key      Cache key.
	 * @param \Closure $callback Callback that returns the value on cache miss.
	 * @param int      $ttl      Time-to-live in seconds. 0 means no expiration.
	 * @return mixed
	 * @throws CacheException When the callback does not return a value.
	 */
	public function remember( string $key, \Closure $callback, int $ttl = 0 ): mixed {
		if ( $this->has( $key ) ) {
			return $this->get( $key );
		}

		$value = $callback();

		if ( null === $value && ! $this->has( $key ) ) {
			throw new CacheException(
				wp_kses_post( sprintf( 'Cache callback for key "%s" did not return a value.', $key ) )
			);
		}

		$this->set( $key, $value, $ttl );

		return $value;
	}

	/**
	 * Whether a key exists in the cache.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public function has( string $key ): bool {
		return wp_cache_get( $key ) !== false || false !== get_transient( $key );
	}

	/**
	 * Remove a key from the cache.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public function forget( string $key ): bool {
		wp_cache_delete( $key );
		delete_transient( $key );

		return true;
	}

	/**
	 * Remove all cached values managed by this service.
	 *
	 * Note: This flushes the entire WordPress object cache, which may affect
	 * other plugins. Use with caution.
	 *
	 * @return bool
	 */
	public function flush(): bool {
		return wp_cache_flush();
	}
}
