<?php
/**
 * One-time admin recovery for Learn AI Courses (Hostinger).
 *
 * What this file does:
 * 1) Loads WordPress.
 * 2) Renames lac_* role/capability keys to the live wc_* prefix.
 * 3) Ensures wc_user_roles exists (required for administrator to work).
 * 4) Forces user "admin" (id 1) to administrator and verifies manage_options.
 *
 * How to use:
 * 1) Upload to WordPress root (same folder as wp-config.php).
 * 2) Visit: https://learnaicourses.com/lac-fix-admin.php?key=learnaifix2026
 * 3) Delete this file immediately after SUCCESS.
 */

 // Load WordPress from the site root.
require __DIR__ . '/wp-load.php';

 // Require a simple secret so the script is not public.
$secret_key = isset( $_GET['key'] ) ? (string) $_GET['key'] : '';
if ( 'learnaifix2026' !== $secret_key ) {
	status_header( 403 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo "Forbidden. Open with ?key=learnaifix2026\n";
	exit;
}

global $wpdb;

header( 'Content-Type: text/plain; charset=utf-8' );

$table_prefix = $wpdb->prefix;
$cap_key      = $table_prefix . 'capabilities';
$level_key    = $table_prefix . 'user_level';
$roles_key    = $table_prefix . 'user_roles';

echo "Table prefix: {$table_prefix}\n";
echo "Capability key: {$cap_key}\n";
echo "Roles option key: {$roles_key}\n\n";

 // Rename leftover local-prefix role option to the live prefix.
$renamed_roles_option = $wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->options} SET option_name = %s WHERE option_name = %s",
		$roles_key,
		'lac_user_roles'
	)
);
echo 'Renamed lac_user_roles option rows: ' . ( false === $renamed_roles_option ? 'ERROR' : (string) $renamed_roles_option ) . "\n";

 // If wc_user_roles is still missing, rebuild default WordPress roles.
$roles_option = get_option( $roles_key );
if ( empty( $roles_option ) || ! is_array( $roles_option ) || empty( $roles_option['administrator'] ) ) {
	require_once ABSPATH . 'wp-admin/includes/schema.php';
	 // populate_roles() writes {$wpdb->prefix}user_roles.
	if ( function_exists( 'populate_roles' ) ) {
		populate_roles();
		echo "Ran populate_roles() to rebuild {$roles_key}.\n";
	} else {
		echo "ERROR: populate_roles() not available.\n";
	}
	 // Clear option cache after DB write.
	wp_cache_delete( $roles_key, 'options' );
	wp_cache_delete( 'alloptions', 'options' );
} else {
	echo "{$roles_key} already present with administrator role.\n";
}

 // Rename wrong capability meta keys if any remain.
$wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->usermeta} SET meta_key = %s WHERE meta_key = %s",
		$cap_key,
		'lac_capabilities'
	)
);
$wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->usermeta} SET meta_key = %s WHERE meta_key = %s",
		$level_key,
		'lac_user_level'
	)
);

 // Resolve the admin user.
$admin_user = get_user_by( 'login', 'admin' );
if ( ! $admin_user ) {
	$admin_user = get_user_by( 'id', 1 );
}
if ( ! $admin_user ) {
	echo "ERROR: Could not find user admin / id 1.\n";
	exit;
}

 // Remove every capabilities/level variant so only the correct keys remain.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->usermeta}
		WHERE user_id = %d
		AND meta_key IN (
			'wc_capabilities','wc_user_level',
			'lac_capabilities','lac_user_level',
			'wp_capabilities','wp_user_level',
			%s, %s
		)",
		$admin_user->ID,
		$cap_key,
		$level_key
	)
);

 // Insert clean administrator capability rows.
$admin_caps_value = serialize( array( 'administrator' => true ) );
$wpdb->insert(
	$wpdb->usermeta,
	array(
		'user_id'    => $admin_user->ID,
		'meta_key'   => $cap_key,
		'meta_value' => $admin_caps_value,
	),
	array( '%d', '%s', '%s' )
);
$wpdb->insert(
	$wpdb->usermeta,
	array(
		'user_id'    => $admin_user->ID,
		'meta_key'   => $level_key,
		'meta_value' => '10',
	),
	array( '%d', '%s', '%s' )
);
echo "Inserted clean {$cap_key} and {$level_key}.\n";

 // Flush user caches, then set role through WordPress APIs.
wp_cache_delete( $admin_user->ID, 'users' );
wp_cache_delete( $admin_user->ID, 'user_meta' );
clean_user_cache( $admin_user->ID );

$admin_user = new WP_User( $admin_user->ID );
$admin_user->set_role( 'administrator' );

 // Re-check after role assignment.
wp_cache_delete( $admin_user->ID, 'users' );
wp_cache_delete( $admin_user->ID, 'user_meta' );
clean_user_cache( $admin_user->ID );
$fresh_user = new WP_User( $admin_user->ID );
$is_admin   = user_can( $fresh_user, 'manage_options' );

 // Debug dump of capability meta + roles option presence.
$cap_row = $wpdb->get_row(
	$wpdb->prepare(
		"SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = %s",
		$fresh_user->ID,
		$cap_key
	)
);
$roles_now = get_option( $roles_key );

echo 'User id: ' . (string) $fresh_user->ID . "\n";
echo 'User login: ' . $fresh_user->user_login . "\n";
echo 'Roles: ' . implode( ', ', $fresh_user->roles ) . "\n";
echo 'Can manage_options: ' . ( $is_admin ? 'YES' : 'NO' ) . "\n";
echo 'Cap meta_value: ' . ( $cap_row ? $cap_row->meta_value : '(missing)' ) . "\n";
echo 'Roles option has administrator: ' . ( is_array( $roles_now ) && isset( $roles_now['administrator'] ) ? 'YES' : 'NO' ) . "\n\n";

if ( $is_admin ) {
	echo "SUCCESS.\n";
	echo "1) DELETE lac-fix-admin.php from File Manager now.\n";
	echo "2) Log out completely.\n";
	echo "3) Log in at https://learnaicourses.com/wp-login.php as admin\n";
	echo "4) Open https://learnaicourses.com/wp-admin/\n";
} else {
	echo "FAILED. Run this SQL in phpMyAdmin and re-open this URL:\n";
	echo "UPDATE {$wpdb->options} SET option_name = '{$roles_key}' WHERE option_name = 'lac_user_roles';\n";
}
