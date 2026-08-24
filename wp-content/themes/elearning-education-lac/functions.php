<?php
/**
 * Child of eLearning Education for Learn AI Courses.
 *
 * What this file does:
 * - Loads the parent theme stylesheet (parent enqueues get_stylesheet_uri()).
 * - Assigns the Primary menu to this theme’s primary-menu location.
 * - Turns off the stock homepage slider and uses a full-width page layout.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent CSS plus LMS layout CSS.
 *
 * @return void
 */
function lac_ee_child_enqueue_assets() {
	wp_enqueue_style(
		'elearning-education-parent',
		get_template_directory_uri() . '/style.css',
		array( 'bootstrap-css' ),
		wp_get_theme( 'elearning-education' )->get( 'Version' )
	);
	wp_enqueue_style(
		'elearning-education-lac-lms',
		get_stylesheet_directory_uri() . '/assets/css/lms.css',
		array( 'elearning-education-parent', 'elearning-education-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'lac_ee_child_enqueue_assets', 20 );

/**
 * Mark the body for LMS layout CSS.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function lac_ee_child_body_class( $classes ) {
	$classes[] = 'lac-astra-lms';
	return $classes;
}
add_filter( 'body_class', 'lac_ee_child_body_class' );

/**
 * Show twelve courses per catalog page.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function lac_ee_child_course_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'lac_course' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'lac_ee_child_course_archive_query' );

/**
 * Apply education-theme settings when this child is activated.
 *
 * @return void
 */
function lac_ee_child_on_switch() {
	set_theme_mod( 'elearning_education_slider_arrows', false );
	set_theme_mod( 'elearning_education_online_courses_enable', false );
	set_theme_mod( 'elearning_education_sidebar_page_layout', 'full' );
	set_theme_mod( 'elearning_education_sidebar_post_layout', 'full' );
	set_theme_mod( 'elearning_education_tp_color_option', '#1e4d7b' );
	set_theme_mod( 'elearning_education_tp_secoundary_color_option', '#192640' );
	set_theme_mod( 'elearning_education_site_title_text', true );
	set_theme_mod( 'elearning_education_login_button', 'Sign in' );
	set_theme_mod( 'elearning_education_login_button_link', wp_login_url() );
	set_theme_mod( 'elearning_education_register_button', 'Create account' );
	set_theme_mod( 'elearning_education_register_button_link', wp_registration_url() );
	set_theme_mod( 'elearning_education_footer_text', get_bloginfo( 'name' ) );

	$primary_menu = wp_get_nav_menu_object( 'Primary' );
	if ( $primary_menu ) {
		$locations                   = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary-menu']   = (int) $primary_menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
add_action( 'after_switch_theme', 'lac_ee_child_on_switch' );
