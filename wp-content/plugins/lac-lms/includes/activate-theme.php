<?php
/**
 * Ensure the Online Courses FSE child theme is active.
 *
 * What this file does:
 * - Theme files in git do not change the UI until WordPress activates them.
 * - Switch once to online-courses-fse-lac (the green “Online Courses” storefront).
 * Process:
 * 1) Skip when already on the preferred child theme.
 * 2) Skip when the one-time flag is set (admins can change themes later).
 * 3) Validate the child theme, switch_theme(), and use posts front so front-page.html loads.
 */

 // Guard against direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preferred active stylesheet for the Learn AI Courses storefront.
 *
 * @return string Theme stylesheet slug.
 */
function lac_preferred_theme_stylesheet() {
	 // FSE child that matches the Online Courses green hero design.
	return 'online-courses-fse-lac';
}

/**
 * Activate the preferred Online Courses FSE theme once when another theme is selected.
 *
 * @return void
 */
function lac_maybe_activate_preferred_theme() {
	 // Preferred child theme slug for the designed storefront.
	$preferred_stylesheet = lac_preferred_theme_stylesheet();
	 // Exit early when WordPress is already using the preferred theme.
	if ( get_option( 'stylesheet' ) === $preferred_stylesheet ) {
		 // Mark complete and keep FSE homepage behavior (front-page.html).
		if ( ! get_option( 'lac_fse_theme_auto_activated' ) ) {
			update_option( 'lac_fse_theme_auto_activated', 1, true );
			update_option( 'show_on_front', 'posts' );
		}
		return;
	}
	 // Respect a prior auto-activation so operators can pick another theme later.
	if ( get_option( 'lac_fse_theme_auto_activated' ) ) {
		return;
	}
	 // Load the preferred theme object and confirm it is installable.
	$theme = wp_get_theme( $preferred_stylesheet );
	if ( ! $theme->exists() || $theme->errors() ) {
		return;
	}
	 // Switch active theme so the Online Courses FSE UI takes effect.
	switch_theme( $preferred_stylesheet );
	 // Use the latest posts / front-page.html storefront (not a Kadence static page).
	update_option( 'show_on_front', 'posts' );
	 // Persist the one-time activation marker.
	update_option( 'lac_fse_theme_auto_activated', 1, true );
	 // Clear the older eLearning auto-activate flag if present.
	delete_option( 'lac_ee_theme_auto_activated' );
	 // Flush rewrites after the theme change.
	flush_rewrite_rules( false );
}

 // Run after plugins load so theme APIs are available.
add_action( 'plugins_loaded', 'lac_maybe_activate_preferred_theme', 5 );
