<?php
/**
 * Asset manager contract.
 *
 * Defines the public API for registering and enqueueing scripts and styles
 * using the WordPress Assets API.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Assets;

/**
 * Contract for asset managers.
 */
interface AssetManagerInterface {

	/**
	 * Register a script.
	 *
	 * @param string $handle        Script handle.
	 * @param string $src           Script source URL.
	 * @param array  $dependencies Script dependencies.
	 * @param bool   $enqueue       Whether to enqueue immediately.
	 * @return void
	 */
	public function register_script( string $handle, string $src, array $dependencies = array(), bool $enqueue = true ): void;

	/**
	 * Register a style.
	 *
	 * @param string $handle        Style handle.
	 * @param string $src           Style source URL.
	 * @param array  $dependencies Style dependencies.
	 * @param bool   $enqueue       Whether to enqueue immediately.
	 * @return void
	 */
	public function register_style( string $handle, string $src, array $dependencies = array(), bool $enqueue = true ): void;

	/**
	 * Enqueue a script.
	 *
	 * @param string $handle Script handle.
	 * @return void
	 */
	public function enqueue_script( string $handle ): void;

	/**
	 * Enqueue a style.
	 *
	 * @param string $handle Style handle.
	 * @return void
	 */
	public function enqueue_style( string $handle ): void;

	/**
	 * Localize a script.
	 *
	 * @param string $handle    Script handle.
	 * @param string $name      JavaScript object name.
	 * @param array  $l10n      Localization data.
	 * @return void
	 */
	public function localize_script( string $handle, string $name, array $l10n ): void;

	/**
	 * Generate a URL for a plugin asset.
	 *
	 * @param string $path Relative path within the plugin assets directory.
	 * @return string
	 */
	public function asset_url( string $path ): string;
}
