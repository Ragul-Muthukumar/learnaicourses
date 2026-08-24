<?php require get_template_directory() . '/theme-wizard/tgm/class-tgm-plugin-activation.php';

function ai_engine_register_recommended_plugins() {
	$plugins = array(
		array(
			'name'             => __( 'Classic Widgets', 'ai-engine' ),
			'slug'             => 'classic-widgets',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
		array(
			'name'             => __( 'Posts Like Dislike', 'ai-engine' ),
			'slug'             => 'posts-like-dislike',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
	);
	$config = array();
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'ai_engine_register_recommended_plugins' );