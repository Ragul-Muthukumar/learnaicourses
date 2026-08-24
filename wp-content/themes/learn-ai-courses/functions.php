<?php
/**
 * Learn AI Courses theme bootstrap.
 *
 * What this file does:
 * - Registers theme supports, menus, and front-end assets.
 * Process:
 * 1) Enable featured images, title tag, and HTML5 markup.
 * 2) Register primary navigation.
 * 3) Enqueue Google fonts, main.css, and main.js.
 */

 // Abort when loaded outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configure theme features after setup.
 *
 * @return void
 */
function lac_theme_setup() {
	 // Let WordPress manage the document title tag.
	add_theme_support( 'title-tag' );
	 // Enable featured images for courses and pages.
	add_theme_support( 'post-thumbnails' );
	 // Use HTML5 markup for core forms and galleries.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	 // Register the primary header menu location.
	register_nav_menus(
		array(
			'primary' => 'Primary Menu',
		)
	);
}

 // Hook theme supports into after_setup_theme.
add_action( 'after_setup_theme', 'lac_theme_setup' );

/**
 * Enqueue theme styles and scripts.
 *
 * @return void
 */
function lac_theme_enqueue_assets() {
	 // Load expressive display + body fonts (avoid default Inter/Roboto/Arial stacks).
	wp_enqueue_style(
		'lac-theme-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Sora:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	 // Load the main atmospheric stylesheet.
	wp_enqueue_style(
		'lac-theme-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'lac-theme-fonts' ),
		wp_get_theme()->get( 'Version' )
	);
	 // Load motion and mobile nav behavior.
	wp_enqueue_script(
		'lac-theme-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

 // Hook assets into the public enqueue phase.
add_action( 'wp_enqueue_scripts', 'lac_theme_enqueue_assets' );

/**
 * Fallback menu when no menu is assigned yet.
 *
 * @return void
 */
function lac_theme_fallback_menu() {
	 // Print a minimal Courses link so navigation is never empty.
	echo '<ul class="site-nav__list">';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'lac_course' ) ) . '">Courses</a></li>';
	echo '</ul>';
}
