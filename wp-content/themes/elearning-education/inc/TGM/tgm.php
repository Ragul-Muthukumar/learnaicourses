<?php

require get_template_directory() . '/inc/TGM/class-tgm-plugin-activation.php';
/**
 * Recommended plugins.
 */
function elearning_education_register_recommended_plugins() {
	$plugins = array(
		array(
			'name'             => __( 'Learnpress', 'elearning-education' ),
			'slug'             => 'learnpress',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
		array(
			'name'             => __( 'WooCommerce', 'elearning-education' ),
			'slug'             => 'woocommerce',
			'required'         => false,
			'force_activation' => false,
		),
		array(
			'name'             => __( 'YITH WooCommerce Wishlist', 'elearning-education' ),
			'slug'             => 'yith-woocommerce-wishlist',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
		array(
            'name'             => __( 'Advanced Appointment Booking & Scheduling', 'elearning-education' ),
            'slug'             => 'advanced-appointment-booking-scheduling',
            'required'         => false,
            'force_activation' => false,
        ),
	);
	$config = array();
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'elearning_education_register_recommended_plugins' );
