<?php

// Output on classic pages
function thinkup_panels_setup_classic( $hook ) {

    // Do not load on Gutenberg
    if ( function_exists( 'wp_should_load_block_editor_scripts_and_styles' ) ) {
        if ( wp_should_load_block_editor_scripts_and_styles() ) {
            return;
        }
    }

    // Only load on classic editor post/page edit screens
    if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
        return;
    }

	// Get theme data
	$theme_data = wp_get_theme();

	// Get theme name is exists
	try {

		// Get name of parent theme
		if ( is_child_theme() ) {
			$theme_name    = trim( strtolower( str_replace( ' (Lite)', '', $theme_data->parent()->get( 'Name' ) ) ) );
			$theme_slug    = trim( strtolower( str_replace( ' (Lite)', '-lite', $theme_data->parent()->get( 'Name' ) ) ) );
			$theme_version = $theme_data->parent()->get( 'Version' );
		} else {
			$theme_name    = trim( strtolower( str_replace( ' (Lite)', '', $theme_data->get( 'Name' ) ) ) );
			$theme_slug    = trim( strtolower( str_replace( ' (Lite)', '-lite', $theme_data->get( 'Name' ) ) ) );
			$theme_version = $theme_data->get( 'Version' );
		}

	} catch (\Throwable $th) {

		// Exit early on error
		return;
	}

    // Add theme stylesheets
    wp_enqueue_style(
        'thinkup-panels-classic',
        get_template_directory_uri() . '/admin/main-panels/assets/css/panels-classic.css',
        array(),
        $theme_version
    );

    // Add theme scripts
    wp_enqueue_script(
        'thinkup-panels-classic',
        get_template_directory_uri() . '/admin/main-panels/assets/js/panels-classic.js',
        array( 'jquery' ),
        $theme_version,
        true
    );

    // Localized data for your JS file
    wp_localize_script(
        'thinkup-panels-classic',
        'thinkupData',
        array(
            'themeName' => $theme_name
        )
    );

}
add_action( 'admin_enqueue_scripts', 'thinkup_panels_setup_classic' );


// Output on Gutenberg pages
function thinkup_panels_setup_gutenberg() {

	// Get theme data
	$theme_data = wp_get_theme();

	// Get theme name is exists
	try {

		// Get name of parent theme
		if ( is_child_theme() ) {
			$theme_name    = trim( strtolower( str_replace( ' (Lite)', '', $theme_data->parent()->get( 'Name' ) ) ) );
			$theme_slug    = trim( strtolower( str_replace( ' (Lite)', '-lite', $theme_data->parent()->get( 'Name' ) ) ) );
			$theme_version = $theme_data->parent()->get( 'Version' );
		} else {
			$theme_name    = trim( strtolower( str_replace( ' (Lite)', '', $theme_data->get( 'Name' ) ) ) );
			$theme_slug    = trim( strtolower( str_replace( ' (Lite)', '-lite', $theme_data->get( 'Name' ) ) ) );
			$theme_version = $theme_data->get( 'Version' );
		}

	} catch (\Throwable $th) {

		// Exit early on error
		return;
	}

    // Enqueue CSS for the editor shell
    wp_enqueue_style(
        'thinkup-panels-gutenberg',
        get_template_directory_uri() . '/admin/main-panels/assets/css/panels-gutenberg.css',
        array(),
        $theme_version
    );

    // Enqueue JS for the editor shell
    wp_enqueue_script(
        'thinkup-panels-gutenberg',
        get_template_directory_uri() . '/admin/main-panels/assets/js/panels-gutenberg.js',
        array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' ),
        $theme_version,
        true
    );

    // Localized data for your JS file
    wp_localize_script(
        'thinkup-panels-gutenberg',
        'thinkupData',
        array(
            'themeName' => $theme_name
        )
    );
}
add_action( 'enqueue_block_editor_assets', 'thinkup_panels_setup_gutenberg' );

