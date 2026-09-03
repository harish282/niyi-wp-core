<?php
/**
 * Cache contract.
 *
 * Defines the public API for caching values using the WordPress caching
 * system. Implementations hide the underlying storage mechanism from
 * application code.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Cache;

/**
 * Contract for cache services.
 */
interface CacheInterface {

	/**
	 * Retrieve a cached value.
	 *
	 * @param string $key     Cache key.
	 * @param mixed  $default Fallback when the key is absent or expired.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed;

	/**
	 * Store a value in the cache.
	 *
	 * @param string $key  Cache key.
	 * @param mixed  $value Value to cache.
	 * @param int    $ttl   Time-to-live in seconds. 0 means no expiration.
	 * @return bool
	 */
	public function set( string $key, mixed $value, int $ttl = 0 ): bool;

	/**
	 * Retrieve a cached value or execute a callback to generate it.
	 *
	 * @param string   $key      Cache key.
	 * @param \Closure $callback Callback that returns the value on cache miss.
	 * @param int      $ttl      Time-to-live in seconds. 0 means no expiration.
	 * @return mixed
	 */
	public function remember( string $key, \Closure $callback, int $ttl = 0 ): mixed;

	/**
	 * Whether a key exists in the cache and has not expired.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public function has( string $key ): bool;

	/**
	 * Remove a key from the cache.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public function forget( string $key ): bool;

	/**
	 * Remove all cached values.
	 *
	 * @return bool
	 */
	public function flush(): bool;
}
