<?php
/**
 * Test bootstrap.
 *
 * Loads the Composer autoloader and sets up a minimal WordPress function
 * environment so the framework-agnostic core library can be unit tested
 * without bootstrapping WordPress itself.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

require_once __DIR__ . '/../vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../' );
}

// ---------------------------------------------------------------------------
// In-memory WordPress option/transient stores used by settings & cache stubs.
// ---------------------------------------------------------------------------

if ( ! isset( $GLOBALS['niyi_core_test_options'] ) ) {
	$GLOBALS['niyi_core_test_options'] = array();
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( string $option, $default = false ) {
		return $GLOBALS['niyi_core_test_options'][ $option ] ?? $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( string $option, $value, $autoload = 'yes' ) {
		$GLOBALS['niyi_core_test_options'][ $option ] = $value;

		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( string $option ) {
		if ( isset( $GLOBALS['niyi_core_test_options'][ $option ] ) ) {
			unset( $GLOBALS['niyi_core_test_options'][ $option ] );

			return true;
		}

		return false;
	}
}

if ( ! class_exists( 'wpdb' ) ) {
	/**
	 * Minimal WordPress database abstraction stub.
	 *
	 * The FakeWpdb in tests/Support overrides the query surface; this base
	 * provides only the shared members the queue schema relies on.
	 */
	class wpdb {

		/** @var string */
		public $prefix = 'wp_';

		/**
		 * Get the charset collate clause.
		 *
		 * @return string
		 */
		public function get_charset_collate() {
			return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
		}
	}
}

if ( ! function_exists( 'is_email' ) ) {
	function is_email( $email ) {
		return false;
	}
}

if ( ! function_exists( 'current_datetime' ) ) {
	function current_datetime() {
		if ( ! isset( $GLOBALS['niyi_core_test_now'] ) ) {
			$GLOBALS['niyi_core_test_now'] = new DateTimeImmutable( 'now' );
		}

		return $GLOBALS['niyi_core_test_now'];
	}
}

if ( ! function_exists( 'dbDelta' ) ) {
	function dbDelta( $queries = '' ) {
		return true;
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
	}
}

if ( ! function_exists( 'remove_action' ) ) {
	function remove_action( $tag, $function_to_remove, $priority = 10 ) {
		return true;
	}
}

if ( ! function_exists( 'remove_filter' ) ) {
	function remove_filter( $tag, $function_to_remove, $priority = 10 ) {
		return true;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		if ( ! is_string( $str ) ) {
			return '';
		}

		$text = wp_kses( $str, array() );
		$text = preg_replace( '/\s+/', ' ', $text );

		return trim( $text );
	}
}

if ( ! function_exists( 'wp_kses' ) ) {
	function wp_kses( $content, $allowed_html, $allowed_protocols = array() ) {
		if ( ! is_string( $content ) ) {
			return '';
		}

		if ( empty( $allowed_protocols ) ) {
			$allowed_protocols = array( 'http', 'https', 'mailto', 'tel' );
		}

		return strip_tags( $content );
	}
}

if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $email ) {
		return is_string( $email ) ? $email : '';
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return $key;
	}
}

// ---------------------------------------------------------------------------
// Escaping / encoding helpers.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $content ) {
		return is_string( $content ) ? $content : '';
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		return filter_var( (string) $url, FILTER_SANITIZE_URL );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return filter_var( (string) $url, FILTER_SANITIZE_URL );
	}
}

// ---------------------------------------------------------------------------
// Hooks.
// ---------------------------------------------------------------------------

if ( ! isset( $GLOBALS['niyi_test_actions'] ) ) {
	$GLOBALS['niyi_test_actions'] = array();
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $tag, ...$args ) {
		$GLOBALS['niyi_test_actions'][] = array(
			'hook' => (string) $tag,
			'args' => $args,
		);
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

// ---------------------------------------------------------------------------
// Object cache stubs.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file ) {
		return 'http://example.com/wp-content/plugins/niyi-wp-core/';
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return '/srv/wp-content/plugins/niyi-wp-core/';
	}
}

// ---------------------------------------------------------------------------
// Transient stubs.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'get_transient' ) ) {
	function get_transient( $key ) {
		$store = $GLOBALS['niyi_core_test_transients'] ?? array();

		if ( ! isset( $store[ $key ] ) || $store[ $key ]['expiry'] < time() ) {
			return false;
		}

		return $store[ $key ]['value'];
	}
}

if ( ! function_exists( 'set_transient' ) ) {
	function set_transient( $key, $value, $expiration = 0 ) {
		if ( ! isset( $GLOBALS['niyi_core_test_transients'] ) ) {
			$GLOBALS['niyi_core_test_transients'] = array();
		}

		$GLOBALS['niyi_core_test_transients'][ $key ] = array(
			'value'  => $value,
			'expiry' => $expiration > 0 ? time() + $expiration : PHP_INT_MAX,
		);

		return true;
	}
}

if ( ! function_exists( 'delete_transient' ) ) {
	function delete_transient( $key ) {
		unset( $GLOBALS['niyi_core_test_transients'][ $key ] );

		return true;
	}
}

if ( ! function_exists( 'wp_cache_get' ) ) {
	function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
		$store = $GLOBALS['niyi_core_test_cache'] ?? array();
		$path  = $group . '::' . $key;

		$found = array_key_exists( $path, $store );

		return $found ? $store[ $path ] : false;
	}
}

if ( ! function_exists( 'wp_cache_set' ) ) {
	function wp_cache_set( $key, $data, $group = '', $expire = 0 ) {
		$GLOBALS['niyi_core_test_cache'][ $group . '::' . $key ] = $data;

		return true;
	}
}

if ( ! function_exists( 'wp_cache_delete' ) ) {
	function wp_cache_delete( $key, $group = '', $deprecated = null ) {
		unset( $GLOBALS['niyi_core_test_cache'][ $group . '::' . $key ] );

		return true;
	}
}

if ( ! function_exists( 'wp_cache_flush' ) ) {
	function wp_cache_flush() {
		$GLOBALS['niyi_core_test_cache'] = array();

		return true;
	}
}

if ( ! function_exists( 'wp_cache_add_non_persistent_groups' ) ) {
	function wp_cache_add_non_persistent_groups( $groups ) {
	}
}

// ---------------------------------------------------------------------------
// Cron stubs.
// ---------------------------------------------------------------------------

if ( ! isset( $GLOBALS['niyi_test_scheduled'] ) ) {
	$GLOBALS['niyi_test_scheduled'] = array();
}

if ( ! isset( $GLOBALS['niyi_test_single_events'] ) ) {
	$GLOBALS['niyi_test_single_events'] = array();
}

if ( ! function_exists( 'wp_schedule_event' ) ) {
	function wp_schedule_event( $timestamp, $interval, $hook, $args = array() ) {
		$GLOBALS['niyi_test_scheduled'][ (string) $hook ] = array(
			'timestamp' => $timestamp,
			'interval'  => $interval,
			'args'      => $args,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_schedule_single_event' ) ) {
	function wp_schedule_single_event( $timestamp, $hook, $args = array(), $wp_error = false ) {
		$GLOBALS['niyi_test_single_events'][ (string) $hook ] = array(
			'timestamp' => $timestamp,
			'args'      => $args,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_next_scheduled' ) ) {
	function wp_next_scheduled( $hook, $args = array() ) {
		$hook = (string) $hook;

		if ( isset( $GLOBALS['niyi_test_scheduled'][ $hook ] ) ) {
			return $GLOBALS['niyi_test_scheduled'][ $hook ]['timestamp'];
		}

		if ( isset( $GLOBALS['niyi_test_single_events'][ $hook ] ) ) {
			return $GLOBALS['niyi_test_single_events'][ $hook ]['timestamp'];
		}

		return false;
	}
}

if ( ! function_exists( 'wp_clear_scheduled_hook' ) ) {
	function wp_clear_scheduled_hook( $hook, $args = array(), $wp_error = false ) {
		unset( $GLOBALS['niyi_test_scheduled'][ (string) $hook ] );
		unset( $GLOBALS['niyi_test_single_events'][ (string) $hook ] );

		return true;
	}
}

if ( ! function_exists( 'wp_get_schedules' ) ) {
	function wp_get_schedules() {
		return array_merge(
			array(
				'hourly'     => array( 'interval' => 3600, 'display' => 'Once Hourly' ),
				'twicedaily' => array( 'interval' => 43200, 'display' => 'Twice Daily' ),
				'daily'      => array( 'interval' => 86400, 'display' => 'Once Daily' ),
			),
			$GLOBALS['niyi_test_schedules'] ?? array()
		);
	}
}

// ---------------------------------------------------------------------------
// Assets stubs.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'wp_register_script' ) ) {
	function wp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
		return true;
	}
}

if ( ! function_exists( 'wp_register_style' ) ) {
	function wp_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		return true;
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		return true;
	}
}

// ---------------------------------------------------------------------------
// HTTP stubs.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'wp_remote_request' ) ) {
	function wp_remote_request( $url, $args = array() ) {
		if ( isset( $GLOBALS['niyi_core_test_http'] ) ) {
			$handler = $GLOBALS['niyi_core_test_http'];
			unset( $GLOBALS['niyi_core_test_http'] );

			return is_callable( $handler ) ? $handler( $url, $args ) : $handler;
		}

		return new WP_Error( 'http_request_failed', 'No HTTP stub configured.' );
	}
}

if ( ! function_exists( 'wc_get_logger' ) ) {
	function wc_get_logger() {
		return $GLOBALS['niyi_test_wc_logger'] ?? null;
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {

		/** @var string */
		public $error_code = '';

		/** @var string */
		public $error_message = '';

		/** @var array */
		public $error_data = array();

		public function __construct( $code = '', $message = '', $data = array() ) {
			$this->error_code    = $code;
			$this->error_message = $message;
			$this->error_data    = $data;
		}

		public function get_error_code() {
			return $this->error_code;
		}

		public function get_error_message() {
			return $this->error_message;
		}

		public function get_error_data( $code = '' ) {
			return $this->error_data;
		}

		public function has_errors() {
			return '' !== $this->error_code;
		}
	}
}

// ---------------------------------------------------------------------------
// Reset helper: called from TestCase::setUp() so every test starts clean.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'niyi_test_reset_globals' ) ) {
	/**
	 * Reset the in-memory WordPress state shared by tests.
	 *
	 * @return void
	 */
	function niyi_test_reset_globals() {
		$GLOBALS['niyi_test_options']           = array();
		$GLOBALS['niyi_core_test_transients']   = array();
		$GLOBALS['niyi_test_scheduled']         = array();
		$GLOBALS['niyi_test_single_events']     = array();
		$GLOBALS['niyi_test_schedules']         = array();
		$GLOBALS['niyi_test_actions']           = array();
		$GLOBALS['niyi_core_test_cache']        = array();
		$GLOBALS['niyi_core_test_now']          = new DateTimeImmutable( 'now' );
		$GLOBALS['niyi_test_wc_logger']         = null;
	}
}

niyi_test_reset_globals();
