<?php
/**
 * Plugin front-end templates for course product pages.
 *
 * What this file does:
 * - Forces lac-lms single/archive templates even when Kadence (or another theme) is active.
 * Process:
 * 1) Detect singular lac_course or the courses archive.
 * 2) Swap template_include to the plugin PHP templates.
 */

 // Guard against direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Override theme templates for LMS course product views.
 *
 * @param string $template Path to the theme template WordPress selected.
 * @return string Path to the plugin template when applicable.
 */
function lac_lms_template_include( $template ) {
	 // Serve the product detail template for every published course.
	if ( is_singular( 'lac_course' ) ) {
		 // Resolve the plugin single-course template path.
		$plugin_template = LAC_LMS_PATH . 'templates/single-lac_course.php';
		 // Use it when the file exists on disk.
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}
	 // Serve the catalog template for the courses archive.
	if ( is_post_type_archive( 'lac_course' ) ) {
		 // Resolve the plugin archive template path.
		$plugin_template = LAC_LMS_PATH . 'templates/archive-lac_course.php';
		 // Use it when the file exists on disk.
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}
	 // Leave all other templates untouched.
	return $template;
}

 // Replace theme templates after WordPress resolves the query.
add_filter( 'template_include', 'lac_lms_template_include', 99 );

/**
 * Show more courses per page on the product catalog archive.
 *
 * @param WP_Query $query Main or secondary query object.
 * @return void
 */
function lac_lms_catalog_posts_per_page( $query ) {
	 // Only adjust the front-end main courses archive query.
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	 // Expand the catalog page size for product browsing.
	if ( $query->is_post_type_archive( 'lac_course' ) ) {
		$query->set( 'posts_per_page', 24 );
	}
}

 // Bind catalog page-size adjustment before the main query runs.
add_action( 'pre_get_posts', 'lac_lms_catalog_posts_per_page' );

/**
 * Hide Kadence's default title hero on LMS product templates.
 *
 * @return void
 */
function lac_lms_hide_theme_course_hero() {
	 // Only adjust LMS course product views.
	if ( ! is_singular( 'lac_course' ) && ! is_post_type_archive( 'lac_course' ) ) {
		return;
	}
	 // Print a small CSS override so our product hero leads.
	echo '<style id="lac-lms-product-hero-css">.entry-hero,.page-title-wrap,.lac_course-archive-hero-section{display:none!important;}.content-area,.entry-content-wrap{margin-top:0!important;padding-top:0!important;}</style>';
}

 // Inject the hide-hero CSS into the document head.
add_action( 'wp_head', 'lac_lms_hide_theme_course_hero', 40 );
