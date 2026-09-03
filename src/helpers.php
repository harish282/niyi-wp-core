<?php
/**
 * Global core helper functions.
 *
 * Loaded in the global scope. Provide ergonomic access to core services.
 * Do not namespace these functions.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

use NiyiWPCore\Core\Plugin;
use NiyiWPCore\Core\View\ViewInterface;

if ( ! function_exists( 'niyi_view' ) ) {
	/**
	 * Global accessor for the View service.
	 *
	 * @param string               $view   View name in dot notation.
	 * @param array<string, mixed> $data   Data to pass to the view.
	 * @return ViewInterface|void|null
	 */
	function niyi_view( string $view = '', array $data = array() ) {
		$container = Plugin::container_instance();

		if ( ! $container ) {
			return null;
		}

		$view_service = $container->get( ViewInterface::class );

		if ( '' === $view ) {
			return $view_service;
		}

		$view_service->render( $view, $data );
	}
}