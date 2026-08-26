<?php
/**
 * Plugin Name: LAC LMS
 * Plugin URI:  http://localhost/learnaicourses
 * Description: Learn AI Courses LMS — courses, lessons, enrollment, PayPal checkout, and REST APIs.
 * Version:     1.4.9
 * Author:      Learn AI Courses
 * Text Domain: lac-lms
 * Requires at least: 6.4
 * Requires PHP: 8.1
 *
 * What this file does:
 * - Boots the LMS plugin and loads modular includes.
 * - Registers activation hooks for tables and rewrite flush.
 * Process:
 * 1) Guard against direct access.
 * 2) Define plugin path/url constants.
 * 3) Require common, theme activator, db, validation, CPT, PayPal, enrollment, REST, and seed modules.
 * 4) Create enrollment + orders tables and upgrade schema when needed.
 * 5) Enqueue enroll/PayPal front-end assets on course views.
 */

 // Abort if WordPress did not load this file through the plugin API.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 // Absolute filesystem path to this plugin directory (trailing slash).
define( 'LAC_LMS_PATH', plugin_dir_path( __FILE__ ) );

 // Public URL to this plugin directory (trailing slash).
define( 'LAC_LMS_URL', plugin_dir_url( __FILE__ ) );

 // Semantic version used for cache-busting enqueued assets.
define( 'LAC_LMS_VERSION', '1.4.9' );

 // Shared helpers: logging, id hashing, and response shaping.
require_once LAC_LMS_PATH . 'includes/common.php';

 // One-time activation of the Online Courses FSE storefront theme.
require_once LAC_LMS_PATH . 'includes/activate-theme.php';

 // Named database query functions (no inline SQL in feature files).
require_once LAC_LMS_PATH . 'includes/db.php';

 // Input validation helpers for enrollment and REST payloads.
require_once LAC_LMS_PATH . 'includes/validation.php';

 // Course custom post type and meta boxes.
require_once LAC_LMS_PATH . 'includes/cpt-course.php';

 // Lesson custom post type linked to parent courses.
require_once LAC_LMS_PATH . 'includes/cpt-lesson.php';

 // Detailed curricula, lesson HTML, and a one-time content refresh.
require_once LAC_LMS_PATH . 'includes/curriculum.php';

 // PayPal Orders API helpers for paid course checkout.
require_once LAC_LMS_PATH . 'includes/paypal.php';

 // Checkout page bootstrap, URLs, and checkout shortcode.
require_once LAC_LMS_PATH . 'includes/checkout.php';

 // Enrollment and purchase CTAs for logged-in learners.
require_once LAC_LMS_PATH . 'includes/enrollment.php';

 // Public REST routes that return encrypted ids only.
require_once LAC_LMS_PATH . 'includes/rest-api.php';

 // One-time demo content seeder for local development.
require_once LAC_LMS_PATH . 'includes/seed.php';

 // Purchasable AI course batch seeder ($1–$50).
require_once LAC_LMS_PATH . 'includes/seed-purchase-courses.php';

 // Featured image seeder for course posts.
require_once LAC_LMS_PATH . 'includes/seed-course-images.php';

/**
 * Runs on plugin activation: tables, rewrite flush, optional seed.
 *
 * @return void
 */
function lac_lms_activate() {
	 // Create or upgrade the enrollments table schema.
	lac_db_create_enrollment_table();
	 // Create or upgrade the PayPal orders table schema.
	lac_db_create_orders_table();
	 // Register CPTs so rewrite rules know about them before flushing.
	lac_register_course_post_type();
	lac_register_lesson_post_type();
	 // Flush permalinks so /courses/ and /lessons/ resolve immediately.
	flush_rewrite_rules();
	 // Seed demo AI courses only once on a fresh install.
	lac_seed_demo_content_if_needed();
	 // Ensure the checkout page exists for enroll / purchase flows.
	lac_ensure_checkout_page();
	 // Persist the schema version for future upgrades.
	update_option( 'lac_lms_db_version', '1.3.1' );
	 // Mark rewrite rules as current for this plugin version.
	update_option( 'lac_rewrite_version', LAC_LMS_VERSION, true );
	 // Record a successful activation in the debug log.
	lac_log_info( 'LAC LMS activated successfully.' );
}

 // Bind the activation callback to this plugin's main file.
register_activation_hook( __FILE__, 'lac_lms_activate' );

/**
 * Runs on plugin deactivation: flush rewrite rules only.
 *
 * @return void
 */
function lac_lms_deactivate() {
	 // Remove custom rewrite endpoints without dropping learner data.
	flush_rewrite_rules();
	 // Note the deactivation for operators reviewing logs.
	lac_log_info( 'LAC LMS deactivated.' );
}

 // Bind the deactivation callback to this plugin's main file.
register_deactivation_hook( __FILE__, 'lac_lms_deactivate' );

/**
 * Ensure newer schema exists without requiring reactivation.
 *
 * @return void
 */
function lac_lms_maybe_upgrade_schema() {
	 // Read the last applied database schema version.
	$current_version = get_option( 'lac_lms_db_version', '1.0.0' );
	 // Skip work when the schema is already current.
	if ( version_compare( $current_version, '1.2.0', '>=' ) ) {
		return;
	}
	 // Create enrollment and orders tables for upgraded installs.
	lac_db_create_enrollment_table();
	lac_db_create_orders_table();
	 // Persist the new schema version marker.
	update_option( 'lac_lms_db_version', '1.2.0' );
	 // Confirm the upgrade in the operator log.
	lac_log_info( 'LAC LMS schema upgraded to 1.2.0.' );
}

 // Run schema upgrades early on every request until current.
add_action( 'plugins_loaded', 'lac_lms_maybe_upgrade_schema' );

/**
 * Flush rewrite rules once per plugin version so /courses/ works after deploy.
 *
 * Hostinger / fresh installs often activate the theme first; without this,
 * the lac_course archive stays 404 until Permalinks are saved manually.
 *
 * @return void
 */
function lac_lms_maybe_flush_rewrites() {
	 // Skip when this version already flushed rules successfully.
	if ( get_option( 'lac_rewrite_version' ) === LAC_LMS_VERSION ) {
		return;
	}
	 // Re-register CPTs before flushing so their slugs are in the rules map.
	lac_register_course_post_type();
	lac_register_lesson_post_type();
	 // Soft flush rebuilds rules without deleting the option prematurely.
	flush_rewrite_rules( false );
	 // Ensure the checkout page exists after a file-only deploy.
	if ( function_exists( 'lac_ensure_checkout_page' ) ) {
		lac_ensure_checkout_page();
	}
	 // Persist the version marker so we only flush once per release.
	update_option( 'lac_rewrite_version', LAC_LMS_VERSION, true );
	lac_log_info( 'LAC LMS rewrite rules flushed for version ' . LAC_LMS_VERSION );
}

 // Run late on init so CPT registration callbacks have already fired.
add_action( 'init', 'lac_lms_maybe_flush_rewrites', 99 );

/**
 * Show an admin warning when Courses CPT has zero published posts.
 *
 * @return void
 */
function lac_lms_admin_missing_courses_notice() {
	 // Only show to users who can manage plugins / content.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	 // Count published LMS courses in the active database.
	$course_count = (int) wp_count_posts( 'lac_course' )->publish;
	 // Exit when at least one course exists.
	if ( $course_count > 0 ) {
		return;
	}
	 // Explain the empty catalog and the usual Hostinger import mistake.
	echo '<div class="notice notice-error"><p>';
	echo esc_html( 'LAC LMS is active but no courses were found. Import database/learnaicourses.sql (matching your table prefix in wp-config.php), or create courses under Courses in the admin. Until then /courses/ will be empty or 404.' );
	echo '</p></div>';
}

 // Surface the empty-catalog warning on every admin screen.
add_action( 'admin_notices', 'lac_lms_admin_missing_courses_notice' );

/**
 * Enqueue front-end LMS assets on relevant course templates.
 *
 * @return void
 */
function lac_lms_enqueue_assets() {
	 // Detect whether the current request is the LMS checkout page.
	$checkout_page_id = lac_get_checkout_page_id();
	$is_checkout_page = is_page( 'checkout' ) || ( $checkout_page_id > 0 && is_page( $checkout_page_id ) );
	 // Load LMS styles for course archives, single course views, and checkout.
	if ( is_post_type_archive( 'lac_course' ) || is_singular( array( 'lac_course', 'lac_lesson' ) ) || is_front_page() || $is_checkout_page ) {
		 // Register the LMS stylesheet with a version for cache busting.
		wp_enqueue_style(
			'lac-lms-style',
			LAC_LMS_URL . 'assets/css/lac-lms.css',
			array(),
			LAC_LMS_VERSION
		);
		 // Load the PayPal JS SDK on checkout when live/sandbox credentials exist.
		if ( $is_checkout_page && lac_paypal_is_configured() && ! lac_paypal_is_mock_mode() ) {
			 // Build the SDK URL with client id, currency, and intent.
			$paypal_sdk_url = add_query_arg(
				array(
					'client-id'  => lac_paypal_client_id(),
					'currency'   => lac_paypal_currency(),
					'intent'     => 'capture',
					'components' => 'buttons',
				),
				'https://www.paypal.com/sdk/js'
			);
			 // Enqueue the official PayPal Smart Buttons script.
			wp_enqueue_script(
				'lac-paypal-sdk',
				$paypal_sdk_url,
				array(),
				null,
				true
			);
		}
		 // Register checkout JS only on the checkout page where actions run.
		if ( $is_checkout_page ) {
			wp_enqueue_script(
				'lac-lms-common',
				LAC_LMS_URL . 'assets/js/common.js',
				( lac_paypal_is_configured() && ! lac_paypal_is_mock_mode() ) ? array( 'lac-paypal-sdk' ) : array(),
				LAC_LMS_VERSION,
				true
			);
			 // Expose REST nonce, login URL, and PayPal flags to the browser script.
			wp_localize_script(
				'lac-lms-common',
				'lac_lms_config',
				array(
					'rest_url'          => esc_url_raw( rest_url( 'lac-lms/v1/' ) ),
					'rest_nonce'        => wp_create_nonce( 'wp_rest' ),
					'is_logged_in'      => is_user_logged_in() ? 1 : 0,
					'login_url'         => wp_login_url( get_permalink() ),
					'paypal_configured' => lac_paypal_is_configured() ? 1 : 0,
					'paypal_mock'       => lac_paypal_is_mock_mode() ? 1 : 0,
					'paypal_currency'   => lac_paypal_currency(),
					'is_checkout_page'  => 1,
				)
			);
		}
	}
}

 // Hook asset loading into the public enqueue phase.
add_action( 'wp_enqueue_scripts', 'lac_lms_enqueue_assets' );
