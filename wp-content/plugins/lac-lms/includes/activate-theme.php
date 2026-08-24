<?php
/**
 * Ensure the Learn AI Courses eLearning child theme is active.
 *
 * What this file does:
 * - Theme files in git do not change the UI until WordPress activates them.
 * - On this branch, switch once from Kadence (or another theme) to elearning-education-lac.
 * Process:
 * 1) Skip when already on the preferred child theme.
 * 2) Skip when the one-time flag is set (admin can change themes afterward).
 * 3) Validate the child theme exists, then switch_theme().
 */

 // Guard against direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preferred active stylesheet for the new-theme branch UI.
 *
 * @return string Theme stylesheet slug.
 */
function lac_preferred_theme_stylesheet() {
	 // Child theme that ships LMS catalog/detail templates on this branch.
	return 'elearning-education-lac';
}

/**
 * Activate the preferred LMS theme once when another theme is still selected.
 *
 * @return void
 */
function lac_maybe_activate_preferred_theme() {
	 // Preferred child theme slug for this product branch.
	$preferred_stylesheet = lac_preferred_theme_stylesheet();
	 // Exit early when WordPress is already using the preferred theme.
	if ( get_option( 'stylesheet' ) === $preferred_stylesheet ) {
		 // Still mark the flag so future switches by admins are respected.
		if ( ! get_option( 'lac_ee_theme_auto_activated' ) ) {
			update_option( 'lac_ee_theme_auto_activated', 1, true );
		}
		return;
	}
	 // Respect a prior auto-activation so operators can pick another theme later.
	if ( get_option( 'lac_ee_theme_auto_activated' ) ) {
		return;
	}
	 // Load the preferred theme object and confirm it is installable.
	$theme = wp_get_theme( $preferred_stylesheet );
	if ( ! $theme->exists() || $theme->errors() ) {
		return;
	}
	 // Switch active theme so homepage/catalog/course templates take effect.
	switch_theme( $preferred_stylesheet );
	 // Persist the one-time activation marker.
	update_option( 'lac_ee_theme_auto_activated', 1, true );
	 // Flush rewrites after the theme change.
	flush_rewrite_rules( false );
}

 // Run after plugins load so theme APIs are available.
add_action( 'plugins_loaded', 'lac_maybe_activate_preferred_theme', 5 );
