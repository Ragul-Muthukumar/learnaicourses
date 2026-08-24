<?php
/**
 * Example wp-config for Learn AI Courses.
 * Copy to wp-config.php and fill in DB credentials / salts.
 * Local defaults match docker-compose.yml (127.0.0.1:13306).
 */
define( 'DB_NAME', 'learnaicourses' );
define( 'DB_USER', 'learnaicourses' );
define( 'DB_PASSWORD', 'learnaicourses_pass' );
define( 'DB_HOST', '127.0.0.1:13306' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/** https://api.wordpress.org/secret-key/1.1/salt/ */
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

$table_prefix = 'lac_';

/**
 * Load debug + PayPal keys from .env when present.
 */
$lac_env_path = __DIR__ . '/.env';
if ( is_readable( $lac_env_path ) ) {
	$lac_env_lines = file( $lac_env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	foreach ( $lac_env_lines as $lac_env_line ) {
		if ( str_starts_with( trim( $lac_env_line ), '#' ) || ! str_contains( $lac_env_line, '=' ) ) {
			continue;
		}
		list( $lac_env_key, $lac_env_value ) = explode( '=', $lac_env_line, 2 );
		$lac_env_key   = trim( $lac_env_key );
		$lac_env_value = trim( $lac_env_value );
		if (
			( str_starts_with( $lac_env_value, '"' ) && str_ends_with( $lac_env_value, '"' ) )
			|| ( str_starts_with( $lac_env_value, "'" ) && str_ends_with( $lac_env_value, "'" ) )
		) {
			$lac_env_value = substr( $lac_env_value, 1, -1 );
		}
		if ( in_array( $lac_env_key, array( 'WP_DEBUG', 'WP_DEBUG_LOG', 'WP_DEBUG_DISPLAY' ), true ) && ! defined( $lac_env_key ) ) {
			define( $lac_env_key, filter_var( $lac_env_value, FILTER_VALIDATE_BOOLEAN ) );
		}
		if (
			in_array( $lac_env_key, array( 'PAYPAL_CLIENT_ID', 'PAYPAL_CLIENT_SECRET', 'PAYPAL_MODE', 'PAYPAL_CURRENCY' ), true )
			&& ! defined( $lac_env_key )
		) {
			define( $lac_env_key, $lac_env_value );
		}
	}
}

if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
	define( 'WP_DEBUG_DISPLAY', false );
}
define( 'FS_METHOD', 'direct' );

/**
 * Optional hard-coded PayPal fallbacks when .env is unavailable.
 */
// define( 'PAYPAL_CLIENT_ID', 'your_sandbox_client_id' );
// define( 'PAYPAL_CLIENT_SECRET', 'your_sandbox_client_secret' );
// define( 'PAYPAL_MODE', 'sandbox' );
// define( 'PAYPAL_CURRENCY', 'USD' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
