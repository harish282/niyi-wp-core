<?php
/**
 * WordPress asset manager.
 *
 * Provides a thin wrapper around the native WordPress asset functions.
 * Automatically applies plugin version and generates asset URLs from the
 * plugin directory.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Assets;

use NiyiWPCore\Core\Assets\AssetManagerInterface;

/**
 * Wraps the WordPress Assets API.
 */
class AssetManager implements AssetManagerInterface {

	/**
	 * Plugin file path used to derive URLs.
	 *
	 * @var string
	 */
	private string $plugin_file;

	/**
	 * Plugin version applied to registered assets.
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Build the asset manager.
	 *
	 * @param string $plugin_file Plugin main file path.
	 * @param string $version     Plugin version.
	 */
	public function __construct( string $plugin_file, string $version ) {
		$this->plugin_file = $plugin_file;
		$this->version     = $version;
	}

	/**
	 * Register a script.
	 *
	 * @param string $handle        Script handle.
	 * @param string $src           Script source URL.
	 * @param array  $dependencies Script dependencies.
	 * @param bool   $enqueue       Whether to enqueue immediately.
	 * @return void
	 */
	public function register_script( string $handle, string $src, array $dependencies = array(), bool $enqueue = true ): void {
		wp_register_script( $handle, $src, $dependencies, $this->version, true );

		if ( $enqueue ) {
			wp_enqueue_script( $handle );
		}
	}

	/**
	 * Register a style.
	 *
	 * @param string $handle        Style handle.
	 * @param string $src           Style source URL.
	 * @param array  $dependencies Style dependencies.
	 * @param bool   $enqueue       Whether to enqueue immediately.
	 * @return void
	 */
	public function register_style( string $handle, string $src, array $dependencies = array(), bool $enqueue = true ): void {
		wp_register_style( $handle, $src, $dependencies, $this->version );

		if ( $enqueue ) {
			wp_enqueue_style( $handle );
		}
	}

	/**
	 * Enqueue a script.
	 *
	 * @param string $handle Script handle.
	 * @return void
	 */
	public function enqueue_script( string $handle ): void {
		wp_enqueue_script( $handle );
	}

	/**
	 * Enqueue a style.
	 *
	 * @param string $handle Style handle.
	 * @return void
	 */
	public function enqueue_style( string $handle ): void {
		wp_enqueue_style( $handle );
	}

	/**
	 * Localize a script.
	 *
	 * @param string $handle Script handle.
	 * @param string $name   JavaScript object name.
	 * @param array  $l10n   Localization data.
	 * @return void
	 */
	public function localize_script( string $handle, string $name, array $l10n ): void {
		wp_localize_script( $handle, $name, $l10n );
	}

	/**
	 * Generate a URL for a plugin asset.
	 *
	 * @param string $path Relative path within the plugin assets directory.
	 * @return string
	 */
	public function asset_url( string $path ): string {
		return plugin_dir_url( $this->plugin_file ) . 'assets/' . ltrim( $path, '/' );
	}
}
