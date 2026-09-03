<?php
/**
 * View contract.
 *
 * Defines the public API for rendering PHP templates.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\View;

/**
 * Contract for view services.
 */
interface ViewInterface {

	/**
	 * Render a view file.
	 *
	 * @param string $view View name in dot notation (e.g. 'Admin.settings').
	 * @param array  $data Data to pass to the view.
	 * @return void
	 * @throws ViewException When the view file does not exist.
	 */
	public function render( string $view, array $data = array() ): void;

	/**
	 * Whether a view file exists.
	 *
	 * @param string $view View name in dot notation.
	 * @return bool
	 */
	public function exists( string $view ): bool;

	/**
	 * Share a variable with all views.
	 *
	 * @param string $key   Variable name.
	 * @param mixed  $value Variable value.
	 * @return void
	 */
	public function share( string $key, mixed $value ): void;
}
