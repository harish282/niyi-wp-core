<?php
/**
 * Cache item.
 *
 * Value object that represents a single cache entry.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Cache;

/**
 * Represents a cache entry.
 */
class CacheItem {

	/**
	 * Cache key.
	 *
	 * @var string
	 */
	private string $key;

	/**
	 * Cached value.
	 *
	 * @var mixed
	 */
	private mixed $value;

	/**
	 * Time-to-live in seconds.
	 *
	 * @var int
	 */
	private int $ttl;

	/**
	 * Whether the item was found in the cache.
	 *
	 * @var bool
	 */
	private bool $is_hit;

	/**
	 * Build a cache item.
	 *
	 * @param string $key    Cache key.
	 * @param mixed  $value  Cached value.
	 * @param int    $ttl    Time-to-live in seconds.
	 * @param bool   $is_hit Whether the item was found in cache.
	 */
	public function __construct( string $key, mixed $value, int $ttl, bool $is_hit ) {
		$this->key    = $key;
		$this->value  = $value;
		$this->ttl    = $ttl;
		$this->is_hit = $is_hit;
	}

	/**
	 * Cache key.
	 *
	 * @return string
	 */
	public function key(): string {
		return $this->key;
	}

	/**
	 * Cached value.
	 *
	 * @return mixed
	 */
	public function value(): mixed {
		return $this->value;
	}

	/**
	 * Time-to-live in seconds.
	 *
	 * @return int
	 */
	public function ttl(): int {
		return $this->ttl;
	}

	/**
	 * Whether the item was found in the cache.
	 *
	 * @return bool
	 */
	public function is_hit(): bool {
		return $this->is_hit;
	}
}
