<?php
/**
 * Astra child theme for Learn AI Courses.
 *
 * What this file does:
 * - Enqueues LMS layout CSS on top of Astra.
 * - Forces a full-width, no-sidebar layout on course screens.
 * - Assigns the Primary menu to Astra header locations after activation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the child stylesheet after Astra.
 *
 * @return void
 */
function lac_astra_child_enqueue_assets() {
	wp_enqueue_style(
		'astra-learnaicourses-lms',
		get_stylesheet_directory_uri() . '/assets/css/lms.css',
		array( 'astra-theme-css' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'lac_astra_child_enqueue_assets', 20 );

/**
 * Mark the body so LMS layout CSS can target this child theme.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function lac_astra_child_body_class( $classes ) {
	$classes[] = 'lac-astra-lms';
	return $classes;
}
add_filter( 'body_class', 'lac_astra_child_body_class' );

/**
 * Hide Astra’s default page title on LMS templates that already print an H1.
 *
 * @param bool $enabled Whether Astra should print the title.
 * @return bool
 */
function lac_astra_child_title_enabled( $enabled ) {
	if ( is_front_page() || is_post_type_archive( 'lac_course' ) || is_singular( array( 'lac_course', 'lac_lesson' ) ) ) {
		return false;
	}
	return $enabled;
}
add_filter( 'astra_the_title_enabled', 'lac_astra_child_title_enabled' );

/**
 * Use a no-sidebar layout on catalog and learning screens.
 *
 * @param string $layout Astra sidebar layout slug.
 * @return string
 */
function lac_astra_child_page_layout( $layout ) {
	if ( is_front_page() || is_post_type_archive( 'lac_course' ) || is_singular( array( 'lac_course', 'lac_lesson' ) ) ) {
		return 'no-sidebar';
	}
	return $layout;
}
add_filter( 'astra_page_layout', 'lac_astra_child_page_layout' );

/**
 * Show a full catalog page of courses instead of the default blog page size.
 *
 * @param WP_Query $query Main query instance.
 * @return void
 */
function lac_astra_child_course_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'lac_course' ) ) {
		$query->set( 'posts_per_page', 12 );
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'lac_astra_child_course_archive_query' );

/**
 * Configure Astra options and menu locations when this child is activated.
 *
 * @return void
 */
function lac_astra_child_on_switch() {
	if ( function_exists( 'astra_update_option' ) ) {
		astra_update_option( 'site-sidebar-layout', 'no-sidebar' );
		astra_update_option( 'single-page-sidebar-layout', 'no-sidebar' );
		astra_update_option( 'archive-post-sidebar-layout', 'no-sidebar' );
		astra_update_option( 'blog-single-sidebar-layout', 'no-sidebar' );
		astra_update_option( 'page-sidebar-layout', 'no-sidebar' );
		astra_update_option( 'site-content-width', 1120 );
		astra_update_option( 'display-site-title', 1 );
		astra_update_option( 'header-main-sep', 1 );
	}

	$primary_menu = wp_get_nav_menu_object( 'Primary' );
	if ( $primary_menu ) {
		$locations                 = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary']      = (int) $primary_menu->term_id;
		$locations['mobile_menu']  = (int) $primary_menu->term_id;
		$locations['footer_menu']  = (int) $primary_menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
add_action( 'after_switch_theme', 'lac_astra_child_on_switch' );
