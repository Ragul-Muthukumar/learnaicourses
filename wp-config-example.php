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

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'FS_METHOD', 'direct' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
