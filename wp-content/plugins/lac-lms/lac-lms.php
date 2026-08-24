<?php
/**
 * Plugin Name: LAC LMS
 * Plugin URI:  http://localhost/learnaicourses
 * Description: Learn AI Courses LMS — courses, lessons, enrollment, and REST APIs.
 * Version:     1.0.0
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
 * 3) Require common, db, validation, CPT, enrollment, REST, and seed modules.
 * 4) Hook activation to create enrollment tables and seed demo courses once.
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
define( 'LAC_LMS_VERSION', '1.0.0' );

 // Shared helpers: logging, id hashing, and response shaping.
require_once LAC_LMS_PATH . 'includes/common.php';

 // Named database query functions (no inline SQL in feature files).
require_once LAC_LMS_PATH . 'includes/db.php';

 // Input validation helpers for enrollment and REST payloads.
require_once LAC_LMS_PATH . 'includes/validation.php';

 // Course custom post type and meta boxes.
require_once LAC_LMS_PATH . 'includes/cpt-course.php';

 // Lesson custom post type linked to parent courses.
require_once LAC_LMS_PATH . 'includes/cpt-lesson.php';

 // Enrollment actions for logged-in learners.
require_once LAC_LMS_PATH . 'includes/enrollment.php';

 // Public REST routes that return encrypted ids only.
require_once LAC_LMS_PATH . 'includes/rest-api.php';

 // One-time demo content seeder for local development.
require_once LAC_LMS_PATH . 'includes/seed.php';

/**
 * Runs on plugin activation: tables, rewrite flush, optional seed.
 *
 * @return void
 */
function lac_lms_activate() {
	// Create or upgrade the enrollments table schema.
	lac_db_create_enrollment_table();
	// Register CPTs so rewrite rules know about them before flushing.
	lac_register_course_post_type();
	lac_register_lesson_post_type();
	// Flush permalinks so /courses/ and /lessons/ resolve immediately.
	flush_rewrite_rules();
	 // Seed demo AI courses only once on a fresh install.
	lac_seed_demo_content_if_needed();
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
 * Enqueue front-end LMS assets on relevant course templates.
 *
 * @return void
 */
function lac_lms_enqueue_assets() {
	 // Load LMS styles for course archives and single course views.
	if ( is_post_type_archive( 'lac_course' ) || is_singular( array( 'lac_course', 'lac_lesson' ) ) || is_front_page() ) {
		 // Register the LMS stylesheet with a version for cache busting.
		wp_enqueue_style(
			'lac-lms-style',
			LAC_LMS_URL . 'assets/css/lac-lms.css',
			array(),
			LAC_LMS_VERSION
		);
		 // Register the shared front-end script that posts enrollments.
		wp_enqueue_script(
			'lac-lms-common',
			LAC_LMS_URL . 'assets/js/common.js',
			array(),
			LAC_LMS_VERSION,
			true
		);
		 // Expose REST nonce and root URL to the browser script.
		wp_localize_script(
			'lac-lms-common',
			'lac_lms_config',
			array(
				'rest_url'   => esc_url_raw( rest_url( 'lac-lms/v1/' ) ),
				'rest_nonce' => wp_create_nonce( 'wp_rest' ),
				'is_logged_in' => is_user_logged_in() ? 1 : 0,
				'login_url'  => wp_login_url( get_permalink() ),
			)
		);
	}
}

 // Hook asset loading into the public enqueue phase.
add_action( 'wp_enqueue_scripts', 'lac_lms_enqueue_assets' );
