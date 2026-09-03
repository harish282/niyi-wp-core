<?php
/**
 * Lightweight PHP view renderer.
 *
 * Resolves view files from dot notation, merges shared data, and renders
 * PHP templates. No template engine—just plain PHP with standard WordPress
 * escaping functions.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\View;

/**
 * Renders PHP view files.
 */
class View implements ViewInterface {

	/**
	 * Base path for view files.
	 *
	 * @var string
	 */
	private string $base_path;

	/**
	 * Shared variables available to all views.
	 *
	 * @var array<string, mixed>
	 */
	private array $shared = array();

	/**
	 * Build the view renderer.
	 *
	 * @param string $base_path Base directory for view files.
	 */
	public function __construct( string $base_path ) {
		$this->base_path = rtrim( $base_path, '/' );
	}

	/**
	 * Resolve a view name to a file path.
	 *
	 * @param string $view View name in dot notation.
	 * @return string
	 */
	private function resolve_path( string $view ): string {
		$path = str_replace( '.', '/', $view ) . '.php';

		return $this->base_path . '/' . $path;
	}

	/**
	 * Render a view file.
	 *
	 * @param string $view View name in dot notation.
	 * @param array  $data Data to pass to the view.
	 * @return void
	 * @throws ViewException When the view file does not exist.
	 */
	public function render( string $view, array $data = array() ): void {
		$path = $this->resolve_path( $view );

		if ( ! $this->exists( $view ) ) {
			throw new ViewException(
				wp_kses_post( sprintf( 'View [%s] not found at [%s].', $view, $path ) )
			);
		}

		$data = array_merge( $this->shared, $data );

		unset( $data['__path'] );
		$data['__path'] = $path;

		extract( $data, EXTR_SKIP );

		include $path;
	}

	/**
	 * Whether a view file exists.
	 *
	 * @param string $view View name in dot notation.
	 * @return bool
	 */
	public function exists( string $view ): bool {
		return file_exists( $this->resolve_path( $view ) );
	}

	/**
	 * Share a variable with all views.
	 *
	 * @param string $key   Variable name.
	 * @param mixed  $value Variable value.
	 * @return void
	 */
	public function share( string $key, mixed $value ): void {
		$this->shared[ $key ] = $value;
	}
}
