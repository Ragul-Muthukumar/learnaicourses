<?php
/**
 * One-time admin recovery for Learn AI Courses (Hostinger).
 *
 * What this file does:
 * 1) Loads WordPress.
 * 2) Renames lac_capabilities / lac_user_level to wc_* (matches $table_prefix).
 * 3) Forces the admin user to the administrator role.
 * 4) Prints success, then tells you to delete this file.
 *
 * How to use:
 * 1) Upload this file to the WordPress root (same folder as wp-config.php).
 * 2) Visit: https://learnaicourses.com/lac-fix-admin.php
 * 3) Delete this file from File Manager immediately after it succeeds.
 */

 // Load WordPress core from the site root.
require __DIR__ . '/wp-load.php';

 // Only allow this script when run in a browser with a secret query key.
$secret_key = isset( $_GET['key'] ) ? (string) $_GET['key'] : '';
if ( 'learnaifix2026' !== $secret_key ) {
	status_header( 403 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo "Forbidden. Open with ?key=learnaifix2026\n";
	exit;
}

global $wpdb;

 // Detect the active table prefix from wp-config.
$table_prefix = $wpdb->prefix;
$cap_key      = $table_prefix . 'capabilities';
$level_key    = $table_prefix . 'user_level';

header( 'Content-Type: text/plain; charset=utf-8' );
echo "Table prefix: {$table_prefix}\n";
echo "Looking for capability key: {$cap_key}\n\n";

 // Rename wrong local-prefix capability keys to the live prefix.
$renamed_caps = $wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->usermeta} SET meta_key = %s WHERE meta_key = %s",
		$cap_key,
		'lac_capabilities'
	)
);
$renamed_level = $wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->usermeta} SET meta_key = %s WHERE meta_key = %s",
		$level_key,
		'lac_user_level'
	)
);

echo 'Renamed lac_capabilities rows: ' . ( false === $renamed_caps ? 'ERROR' : (string) $renamed_caps ) . "\n";
echo 'Renamed lac_user_level rows: ' . ( false === $renamed_level ? 'ERROR' : (string) $renamed_level ) . "\n";

 // Load the admin account by login name.
$admin_user = get_user_by( 'login', 'admin' );
if ( ! $admin_user ) {
	 // Fall back to user id 1 when the login name differs.
	$admin_user = get_user_by( 'id', 1 );
}

if ( ! $admin_user ) {
	echo "ERROR: Could not find user 'admin' or user id 1.\n";
	exit;
}

 // Force administrator role and level 10.
$admin_user->set_role( 'administrator' );
update_user_meta( $admin_user->ID, $level_key, '10' );

 // Clear caches that can keep an old capability map in memory.
wp_cache_delete( $admin_user->ID, 'users' );
wp_cache_delete( $admin_user->ID, 'user_meta' );
clean_user_cache( $admin_user->ID );

 // Re-read capabilities to confirm.
$fresh_user = new WP_User( $admin_user->ID );
$is_admin   = user_can( $fresh_user, 'manage_options' );

echo 'User id: ' . (string) $admin_user->ID . "\n";
echo 'User login: ' . $admin_user->user_login . "\n";
echo 'Roles: ' . implode( ', ', $fresh_user->roles ) . "\n";
echo 'Can manage_options: ' . ( $is_admin ? 'YES' : 'NO' ) . "\n\n";

if ( $is_admin ) {
	echo "SUCCESS. Now:\n";
	echo "1) DELETE this file (lac-fix-admin.php) from File Manager.\n";
	echo "2) Log out, then open https://learnaicourses.com/wp-login.php\n";
	echo "3) Log in as admin, then open https://learnaicourses.com/wp-admin/\n";
} else {
	echo "FAILED. In phpMyAdmin run:\n";
	echo "SELECT user_id, meta_key, meta_value FROM {$table_prefix}usermeta WHERE meta_key LIKE '%capabilities%';\n";
}
