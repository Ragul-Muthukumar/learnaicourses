<?php
/**
 * Plugin Name: Learn AI Courses Lockdown
 * Description: Locks administrator to one user and blocks new plugins and themes. The rule is stored in the learnaicourses_guard table, not in wp-config.php.
 * Version:     2.0.0
 *
 * Must-use, and named so it sorts first. WordPress includes every mu-plugin from a list built before this file runs, so a foreign mu-plugin is emptied before that include.
 *
 * @package LearnAICourses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seal secret. This lives in this file on purpose.
 * A define() in wp-config.php could be set by whoever edits that file, and would let them forge a valid seal.
 *
 * @return string
 */
function lac_guard_pepper() {
	return 'efd55237ba70eeadda18b8c51c218412a927e6401de7ecc5de3ebf718e4ad06d';
}

/**
 * The only policy this site will keep. A database row that disagrees is rewritten.
 *
 * owner_user_id is the one account allowed to stay an administrator.
 * The two block flags stop new plugins and new themes.
 *
 * @return array<string, string>
 */
function lac_guard_canonical() {
	return array(
		'owner_user_id'        => '1',
		'block_new_admins'     => '1',
		'block_plugin_install' => '1',
		'block_theme_install'  => '1',
	);
}

/**
 * Plugins the dashboard is allowed to keep active.
 * Anything else is removed from the active list and its folder is moved aside.
 *
 * @return string[]
 */
function lac_guard_allowed_plugins() {
	// Folders already in this repo. A plugin dropped beside these is quarantined.
	return array(
		'lac-lms/lac-lms.php',
		'bgcheckout/bgcheckout.php',
		'woocommerce/woocommerce.php',
		'akismet/akismet.php',
	);
}

/**
 * Plugin directories that match the allow list.
 *
 * @return string[]
 */
function lac_guard_allowed_plugin_dirs() {
	$dirs = array();
	foreach ( lac_guard_allowed_plugins() as $plugin ) {
		$dirs[] = dirname( $plugin );
	}
	return $dirs;
}

/**
 * Hostinger ships its own must-use files. Those stay. Every other mu-plugin is treated as foreign.
 *
 * @param string $basename File name.
 * @return bool
 */
function lac_guard_mu_allowed( $basename ) {
	if ( '0-learnaicourses-lockdown.php' === $basename ) {
		return true;
	}
	// Hostinger's panel writes these. They are not the WordPress file manager.
	if ( str_starts_with( $basename, 'hostinger-' ) && str_ends_with( $basename, '.php' ) ) {
		return true;
	}
	return false;
}

/**
 * HMAC for one guard row. A changed value without this seal is rejected.
 *
 * @param string $key   Row key.
 * @param string $value Row value.
 * @return string
 */
function lac_guard_seal( $key, $value ) {
	return hash_hmac( 'sha256', $key . '=' . $value, lac_guard_pepper() );
}

/**
 * Table name. Uses the site prefix, so local lac_ becomes lac_learnaicourses_guard
 * and the live Hostinger prefix wc_ becomes wc_learnaicourses_guard.
 *
 * @return string
 */
function lac_guard_table() {
	global $wpdb;
	return $wpdb->prefix . 'learnaicourses_guard';
}

/**
 * Create the guard table when it is missing, then make every row match the canonical policy.
 *
 * wp-config.php is not read here. Editing LAC_LOCK_ADMIN_ID in that file does not move the lock.
 *
 * @return void
 */
function lac_guard_ensure() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$canonical = lac_guard_canonical();
	// Fail closed if the database cannot be read. The site stays locked.
	$GLOBALS['lac_guard_policy'] = $canonical;

	global $wpdb;
	if ( ! isset( $wpdb ) || ! is_object( $wpdb ) ) {
		return;
	}

	$table   = lac_guard_table();
	$charset = $wpdb->get_charset_collate();
	// Own table, not wp_options. Options are writable from many plugins; this table is checked against the seal.
	$wpdb->query(
		"CREATE TABLE IF NOT EXISTS {$table} (
			guard_key varchar(64) NOT NULL,
			guard_value varchar(255) NOT NULL,
			guard_seal char(64) NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY (guard_key)
		) {$charset}"
	);

	$policy = array();
	foreach ( $canonical as $key => $value ) {
		$row  = $wpdb->get_row(
			$wpdb->prepare( "SELECT guard_value, guard_seal FROM {$table} WHERE guard_key = %s", $key ),
			ARRAY_A
		);
		$seal = lac_guard_seal( $key, $value );
		$valid = is_array( $row )
			&& hash_equals( $seal, (string) $row['guard_seal'] )
			&& (string) $row['guard_value'] === (string) $value;

		// A missing row, a bad seal, or a value other than the locked one is written back.
		if ( ! $valid ) {
			$wpdb->replace(
				$table,
				array(
					'guard_key'   => $key,
					'guard_value' => $value,
					'guard_seal'  => $seal,
					'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
				),
				array( '%s', '%s', '%s', '%s' )
			);
		}
		$policy[ $key ] = (string) $value;
	}

	$GLOBALS['lac_guard_policy'] = $policy;
}

/**
 * Policy loaded from the guard table (after repair).
 *
 * @return array<string, string>
 */
function lac_guard_policy() {
	if ( empty( $GLOBALS['lac_guard_policy'] ) || ! is_array( $GLOBALS['lac_guard_policy'] ) ) {
		lac_guard_ensure();
	}
	return $GLOBALS['lac_guard_policy'];
}

/**
 * @param string $key Policy key.
 * @return bool
 */
function lac_guard_flag( $key ) {
	$policy = lac_guard_policy();
	return isset( $policy[ $key ] ) && '1' === (string) $policy[ $key ];
}

/**
 * User ID allowed to hold administrator. Comes from the guard table.
 *
 * @return int
 */
function lac_lock_owner_id() {
	$policy = lac_guard_policy();
	$owner  = isset( $policy['owner_user_id'] ) ? (int) $policy['owner_user_id'] : 1;
	// A zero or negative id would lock nobody and also lock out the real owner. Fall back to user 1.
	if ( $owner < 1 ) {
		return 1;
	}
	return $owner;
}

/**
 * Whether this account is the locked owner.
 *
 * @param int $user_id User ID.
 * @return bool
 */
function lac_lock_is_owner( $user_id ) {
	return (int) $user_id === lac_lock_owner_id();
}

/**
 * Capabilities that add or remove plugin and theme files.
 * Updates of plugins that are already installed stay allowed.
 *
 * @return string[]
 */
function lac_lock_blocked_file_caps() {
	$caps = array();
	if ( lac_guard_flag( 'block_plugin_install' ) ) {
		$caps[] = 'install_plugins';
		$caps[] = 'upload_plugins';
		$caps[] = 'delete_plugins';
		$caps[] = 'edit_plugins';
	}
	if ( lac_guard_flag( 'block_theme_install' ) ) {
		$caps[] = 'install_themes';
		$caps[] = 'upload_themes';
		$caps[] = 'delete_themes';
		$caps[] = 'edit_themes';
	}
	return $caps;
}

/**
 * Capabilities that mean "administrator" even when the role name is absent.
 *
 * Shop managers keep level_9, list_users, and manage_woocommerce. Those are not in this list.
 * A direct usermeta insert often copies these keys instead of the administrator role.
 *
 * @return string[]
 */
function lac_lock_privileged_caps() {
	return array(
		'manage_options',
		'edit_users',
		'create_users',
		'delete_users',
		'promote_users',
		'remove_users',
		'activate_plugins',
		'update_plugins',
		'install_plugins',
		'edit_plugins',
		'delete_plugins',
		'upload_plugins',
		'edit_themes',
		'install_themes',
		'update_themes',
		'delete_themes',
		'upload_themes',
		'switch_themes',
		'update_core',
		'edit_files',
		'edit_dashboard',
		'unfiltered_html',
		'unfiltered_upload',
		'level_10',
		'manage_network',
		'manage_network_users',
		'manage_network_plugins',
		'manage_network_themes',
		'manage_network_options',
		'manage_sites',
	);
}

/**
 * Stored capability map with administrator power removed for everyone except the owner.
 *
 * Role names such as customer, subscriber, and shop_manager stay. Loose capability keys do not,
 * because that is how a raw INSERT grants admin without writing the role name.
 *
 * @param array<string, mixed> $meta_value Capabilities meta.
 * @param int                   $user_id    User ID.
 * @return array<string, mixed>
 */
function lac_lock_sanitize_stored_caps( $meta_value, $user_id ) {
	if ( lac_lock_is_owner( $user_id ) ) {
		if ( empty( $meta_value['administrator'] ) ) {
			$meta_value['administrator'] = true;
		}
		return $meta_value;
	}

	$wp_roles = function_exists( 'wp_roles' ) ? wp_roles() : null;
	$clean    = array();

	foreach ( $meta_value as $key => $granted ) {
		if ( ! is_string( $key ) ) {
			continue;
		}
		// The administrator role itself, and the primitive caps that role is made of.
		if ( 'administrator' === $key || in_array( $key, lac_lock_privileged_caps(), true ) ) {
			continue;
		}
		if ( $wp_roles && $wp_roles->is_role( $key ) ) {
			$role         = $wp_roles->get_role( $key );
			$grants_admin = false;
			if ( $role ) {
				foreach ( lac_lock_privileged_caps() as $cap ) {
					if ( ! empty( $role->capabilities[ $cap ] ) ) {
						$grants_admin = true;
						break;
					}
				}
			}
			// A custom role cloned from administrator is dropped with the same sweep.
			if ( $grants_admin ) {
				continue;
			}
			$clean[ $key ] = (bool) $granted;
			continue;
		}
		// An explicit deny can stay. A granted primitive cap cannot.
		if ( empty( $granted ) ) {
			$clean[ $key ] = false;
		}
	}

	if ( empty( $clean ) ) {
		$clean['subscriber'] = true;
	}

	return $clean;
}

/**
 * Take administrator capabilities off every role except the real administrator role.
 *
 * @return void
 */
function lac_lock_scrub_foreign_roles() {
	if ( ! function_exists( 'wp_roles' ) ) {
		return;
	}
	$wp_roles = wp_roles();
	$names    = array_keys( $wp_roles->roles );
	foreach ( $names as $name ) {
		$role = $wp_roles->roles[ $name ];
		if ( 'administrator' === $name || empty( $role['capabilities'] ) || ! is_array( $role['capabilities'] ) ) {
			continue;
		}
		foreach ( lac_lock_privileged_caps() as $cap ) {
			if ( ! empty( $role['capabilities'][ $cap ] ) ) {
				$wp_roles->remove_cap( $name, $cap );
			}
		}
	}
}

/**
 * Empty foreign must-use plugins before WordPress includes the rest of the list.
 *
 * @return void
 */
function lac_guard_neuter_foreign_mu_plugins() {
	if ( ! defined( 'WPMU_PLUGIN_DIR' ) || ! is_dir( WPMU_PLUGIN_DIR ) ) {
		return;
	}

	$foreign = array();
	$paths   = glob( WPMU_PLUGIN_DIR . '/*.php' );
	if ( ! is_array( $paths ) ) {
		return;
	}

	foreach ( $paths as $path ) {
		$base = basename( $path );
		if ( lac_guard_mu_allowed( $base ) ) {
			continue;
		}
		// The include list is already built. Emptying the file makes that include return immediately.
		file_put_contents( $path, "<?php\nreturn;\n" );
		$foreign[] = $path;
	}

	if ( ! $foreign ) {
		return;
	}

	register_shutdown_function(
		static function () use ( $foreign ) {
			foreach ( $foreign as $path ) {
				if ( is_file( $path ) ) {
					$target = $path . '.off';
					if ( file_exists( $target ) ) {
						$target .= '-' . gmdate( 'YmdHis' );
					}
					@rename( $path, $target );
				}
			}
		}
	);
}

/**
 * Move plugin directories that are not on the allow list out of wp-content/plugins.
 * The folder is kept, with a .disabled suffix, so it is not loaded and not deleted unseen.
 *
 * @return void
 */
function lac_guard_quarantine_plugins() {
	if ( ! defined( 'WP_PLUGIN_DIR' ) || ! is_dir( WP_PLUGIN_DIR ) ) {
		return;
	}

	$allowed = lac_guard_allowed_plugin_dirs();
	$names   = scandir( WP_PLUGIN_DIR );
	if ( ! is_array( $names ) ) {
		return;
	}

	foreach ( $names as $name ) {
		if ( '.' === $name || '..' === $name ) {
			continue;
		}
		$path = WP_PLUGIN_DIR . '/' . $name;
		if ( ! is_dir( $path ) ) {
			continue;
		}
		// Already quarantined on a previous request.
		if ( str_ends_with( $name, '.disabled' ) || str_contains( $name, '.disabled-' ) ) {
			continue;
		}
		if ( in_array( $name, $allowed, true ) ) {
			continue;
		}
		$target = $path . '.disabled';
		if ( file_exists( $target ) ) {
			$target .= '-' . gmdate( 'YmdHis' );
		}
		@rename( $path, $target );
	}
}

/**
 * Deny PHP execution under uploads. Webshells from the November 2026 incident were dropped here.
 * php_flag is intentionally not used: on hosts that forbid it, that directive turns every upload into a 500.
 *
 * @return void
 */
function lac_guard_protect_uploads() {
	if ( ! defined( 'WP_CONTENT_DIR' ) ) {
		return;
	}
	$dir = WP_CONTENT_DIR . '/uploads';
	if ( ! is_dir( $dir ) ) {
		return;
	}
	$file  = $dir . '/.htaccess';
	$rules = "<FilesMatch \"(?i)\\.(php|phtml|php[0-9]|phar|cgi)$\">\n\tRequire all denied\n</FilesMatch>\n";
	$have  = is_file( $file ) ? (string) file_get_contents( $file ) : '';
	if ( str_contains( $have, 'Require all denied' ) && str_contains( $have, 'php' ) ) {
		return;
	}
	file_put_contents( $file, $rules );
}

/**
 * Deny plugin and theme install, upload, delete, and the file editors.
 *
 * @param string[] $caps    Required capabilities.
 * @param string   $cap     Capability being checked.
 * @param int      $user_id User ID.
 * @return string[]
 */
function lac_lock_map_meta_cap( $caps, $cap, $user_id ) {
	unset( $user_id );
	if ( in_array( $cap, lac_lock_blocked_file_caps(), true ) ) {
		return array( 'do_not_allow' );
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'lac_lock_map_meta_cap', 9999, 3 );

/**
 * Clear the same capabilities on the user object. Some callers read the cap array after map_meta_cap.
 *
 * @param array<string, bool> $allcaps All capabilities for the user.
 * @return array<string, bool>
 */
function lac_lock_user_has_cap( $allcaps, $caps = array(), $args = array(), $user = null ) {
	unset( $caps, $args );

	foreach ( lac_lock_blocked_file_caps() as $cap ) {
		$allcaps[ $cap ] = false;
	}

	// Covers a usermeta row written by SQL on this same request, before the sweep rewrites the row.
	if ( lac_guard_flag( 'block_new_admins' ) && $user instanceof WP_User && ! lac_lock_is_owner( $user->ID ) ) {
		foreach ( lac_lock_privileged_caps() as $cap ) {
			$allcaps[ $cap ] = false;
		}
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'lac_lock_user_has_cap', 9999, 4 );

/**
 * Hide the administrator role on user create and profile screens.
 *
 * @param array<string, array<string, mixed>> $roles Editable roles.
 * @return array<string, array<string, mixed>>
 */
function lac_lock_editable_roles( $roles ) {
	if ( ! lac_guard_flag( 'block_new_admins' ) ) {
		return $roles;
	}
	// No dashboard user, including the owner, may assign administrator from a screen.
	unset( $roles['administrator'] );
	return $roles;
}
add_filter( 'editable_roles', 'lac_lock_editable_roles' );

/**
 * Stop the REST API from creating or promoting an administrator.
 *
 * @param object $prepared User object about to be saved.
 * @return object
 */
function lac_lock_rest_user( $prepared ) {
	if ( ! lac_guard_flag( 'block_new_admins' ) ) {
		return $prepared;
	}
	if ( isset( $prepared->roles ) && is_array( $prepared->roles ) ) {
		$prepared->roles = array_values( array_diff( $prepared->roles, array( 'administrator' ) ) );
		if ( empty( $prepared->roles ) ) {
			$prepared->roles = array( 'subscriber' );
		}
	}
	if ( isset( $prepared->role ) && 'administrator' === $prepared->role ) {
		$prepared->role = 'subscriber';
	}
	return $prepared;
}
add_filter( 'rest_pre_insert_user', 'lac_lock_rest_user' );

/**
 * Rewrite a capabilities meta write so only the owner stays an administrator.
 *
 * Returning true skips WordPress's original write. A static guard stops the
 * follow-up update_user_meta from entering this filter again.
 *
 * @param mixed  $check      Short-circuit value.
 * @param int    $user_id    User ID.
 * @param string $meta_key   Meta key.
 * @param mixed  $meta_value Meta value.
 * @return mixed
 */
function lac_lock_filter_capabilities( $check, $user_id, $meta_key, $meta_value ) {
	static $writing = false;

	if ( $writing || null !== $check || ! is_string( $meta_key ) || ! is_array( $meta_value ) ) {
		return $check;
	}
	if ( ! lac_guard_flag( 'block_new_admins' ) ) {
		return $check;
	}

	global $wpdb;
	$cap_key = $wpdb->get_blog_prefix() . 'capabilities';
	if ( $meta_key !== $cap_key ) {
		return $check;
	}

	$clean = lac_lock_sanitize_stored_caps( $meta_value, $user_id );
	if ( $clean === $meta_value ) {
		return $check;
	}
	$meta_value = $clean;

	$writing = true;
	update_user_meta( $user_id, $meta_key, $meta_value );
	$writing = false;

	return true;
}
add_filter( 'update_user_metadata', 'lac_lock_filter_capabilities', 1, 4 );

/**
 * Drop administrator from a user that is not the owner.
 *
 * @param int $user_id User ID.
 * @return void
 */
function lac_lock_strip_admin( $user_id ) {
	static $running = false;

	if ( $running || ! lac_guard_flag( 'block_new_admins' ) || lac_lock_is_owner( $user_id ) ) {
		return;
	}

	$user = get_userdata( $user_id );
	if ( ! $user || ! in_array( 'administrator', (array) $user->roles, true ) ) {
		return;
	}

	$running = true;
	$user->remove_role( 'administrator' );
	$user = get_userdata( $user_id );
	// An account with no role left cannot sign in to the dashboard. Subscriber can.
	if ( $user && empty( $user->roles ) ) {
		$user->set_role( 'subscriber' );
	}
	$running = false;
}

/**
 * Rewrite capability rows that SQL inserted after the last request.
 *
 * The meta filter does not see a direct INSERT. This runs on init, which is before
 * wp-admin, the REST API, and the login form read the current user.
 *
 * @return void
 */
function lac_lock_sweep_admins() {
	if ( wp_installing() || ! lac_guard_flag( 'block_new_admins' ) ) {
		return;
	}

	lac_lock_scrub_foreign_roles();

	$owner_id = lac_lock_owner_id();
	$owner    = get_userdata( $owner_id );
	// Put the owner back if a query removed the role between requests.
	if ( $owner && ! in_array( 'administrator', (array) $owner->roles, true ) ) {
		$owner->add_role( 'administrator' );
	}

	global $wpdb;
	$cap_key = $wpdb->get_blog_prefix() . 'capabilities';
	$needles = array_merge( array( 'administrator' ), lac_lock_privileged_caps() );
	$likes   = array();
	$args    = array( $cap_key );
	foreach ( $needles as $needle ) {
		// One placeholder per needle. Preparing twice would treat the % in LIKE as another placeholder.
		$likes[] = 'meta_value LIKE %s';
		$args[]  = '%"' . $wpdb->esc_like( $needle ) . '"%';
	}

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s AND (" . implode( ' OR ', $likes ) . ')',
			$args
		),
		ARRAY_A
	);

	foreach ( $rows as $row ) {
		$user_id = (int) $row['user_id'];
		if ( lac_lock_is_owner( $user_id ) ) {
			continue;
		}
		$caps = maybe_unserialize( $row['meta_value'] );
		if ( ! is_array( $caps ) ) {
			continue;
		}
		$clean = lac_lock_sanitize_stored_caps( $caps, $user_id );
		if ( $clean === $caps ) {
			continue;
		}
		update_user_meta( $user_id, $cap_key, $clean );
		$cleaned = new WP_User( $user_id );
		$cleaned->update_user_level_from_caps();
	}
}
add_action( 'init', 'lac_lock_sweep_admins', 1 );

/**
 * @param int $user_id User ID.
 * @return void
 */
function lac_lock_on_role_change( $user_id ) {
	lac_lock_strip_admin( $user_id );
}
add_action( 'set_user_role', 'lac_lock_on_role_change', 999 );
add_action( 'add_user_role', 'lac_lock_on_role_change', 999 );
add_action( 'user_register', 'lac_lock_on_role_change', 999 );

/**
 * Keep unknown plugins out of the active list on read and on save.
 *
 * @param mixed $plugins Plugin list.
 * @return mixed
 */
function lac_guard_filter_active_plugins( $plugins ) {
	if ( ! is_array( $plugins ) ) {
		return $plugins;
	}
	if ( ! lac_guard_flag( 'block_plugin_install' ) ) {
		return $plugins;
	}
	$allowed = lac_guard_allowed_plugins();
	$kept    = array();
	foreach ( $plugins as $plugin ) {
		if ( is_string( $plugin ) && in_array( $plugin, $allowed, true ) ) {
			$kept[] = $plugin;
		}
	}
	return $kept;
}
add_filter( 'option_active_plugins', 'lac_guard_filter_active_plugins' );
add_filter( 'pre_update_option_active_plugins', 'lac_guard_filter_active_plugins' );

/**
 * Stop the upgrader from installing a plugin or theme. Updates of an existing plugin still pass.
 *
 * @param mixed $response   Install response or a WP_Error.
 * @param array $hook_extra Upgrader context.
 * @return mixed
 */
function lac_guard_block_install( $response, $hook_extra ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}
	$type   = isset( $hook_extra['type'] ) ? (string) $hook_extra['type'] : '';
	$action = isset( $hook_extra['action'] ) ? (string) $hook_extra['action'] : '';
	$block_plugin = ( 'plugin' === $type && lac_guard_flag( 'block_plugin_install' ) );
	$block_theme  = ( 'theme' === $type && lac_guard_flag( 'block_theme_install' ) );
	// An update of lac-lms, bgcheckout, or WooCommerce is action "update", so it is not rejected here.
	if ( 'install' === $action && ( $block_plugin || $block_theme ) ) {
		return new WP_Error( 'lac_guard', 'Installing plugins and themes is blocked by the Learn AI Courses guard table.' );
	}
	return $response;
}
add_filter( 'upgrader_pre_install', 'lac_guard_block_install', 10, 2 );

/**
 * Answer the install and editor URLs with a refusal. Capability checks already fail; this covers the screen itself.
 *
 * @return void
 */
function lac_guard_deny_plugin_screen() {
	if ( ! lac_guard_flag( 'block_plugin_install' ) ) {
		return;
	}
	wp_die( esc_html( 'Plugin installation and editing are blocked by the Learn AI Courses guard table.' ), 'Blocked', array( 'response' => 403 ) );
}

/**
 * @return void
 */
function lac_guard_deny_theme_screen() {
	if ( ! lac_guard_flag( 'block_theme_install' ) ) {
		return;
	}
	wp_die( esc_html( 'Theme installation and editing are blocked by the Learn AI Courses guard table.' ), 'Blocked', array( 'response' => 403 ) );
}

/**
 * update.php is shared by core, plugin, and theme actions. Only the install and upload actions are refused.
 *
 * @return void
 */
function lac_guard_block_update_screen() {
	$action          = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
	$blocked_actions = array( 'upload-plugin', 'upload-theme', 'install-plugin', 'install-theme' );
	if ( ! in_array( $action, $blocked_actions, true ) ) {
		return;
	}
	wp_die( esc_html( 'Installing plugins and themes is blocked by the Learn AI Courses guard table.' ), 'Blocked', array( 'response' => 403 ) );
}
add_action( 'load-update.php', 'lac_guard_block_update_screen' );
add_action( 'load-plugin-install.php', 'lac_guard_deny_plugin_screen' );
add_action( 'load-plugin-editor.php', 'lac_guard_deny_plugin_screen' );
add_action( 'load-theme-install.php', 'lac_guard_deny_theme_screen' );
add_action( 'load-theme-editor.php', 'lac_guard_deny_theme_screen' );

/**
 * Remove install and editor menu entries so the owner is not sent into a dead screen.
 *
 * @return void
 */
function lac_guard_hide_install_menus() {
	if ( lac_guard_flag( 'block_plugin_install' ) ) {
		remove_submenu_page( 'plugins.php', 'plugin-install.php' );
		remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
	}
	if ( lac_guard_flag( 'block_theme_install' ) ) {
		remove_submenu_page( 'themes.php', 'theme-install.php' );
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
	}
}
add_action( 'admin_menu', 'lac_guard_hide_install_menus', 999 );

/**
 * Tell the owner, on the plugins and users screens, that the lock is stored in the guard table.
 *
 * @return void
 */
function lac_lock_admin_notice() {
	if ( ! lac_lock_is_owner( get_current_user_id() ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, array( 'plugins', 'plugin-install', 'themes', 'users', 'user', 'user-new' ), true ) ) {
		return;
	}
	global $wpdb;
	$table = isset( $wpdb->prefix ) ? $wpdb->prefix . 'learnaicourses_guard' : 'learnaicourses_guard';
	echo '<div class="notice notice-info"><p>Learn AI Courses guard is on. The rule is in the <code>' . esc_html( $table ) . '</code> table, not in wp-config.php. Only user ID ' . (int) lac_lock_owner_id() . ' can be an administrator. New plugins and themes cannot be installed. Changing wp-config.php does not turn this off.</p></div>';
}
add_action( 'admin_notices', 'lac_lock_admin_notice' );

// Run before this file returns. Later mu-plugins are already on WordPress's include list, so they must be emptied now.
lac_guard_neuter_foreign_mu_plugins();
lac_guard_quarantine_plugins();
lac_guard_ensure();
lac_guard_protect_uploads();
