<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * <?php the_header_image_tag(); 
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package AI Engine
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses ai_engine_header_style()
 */
function ai_engine_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'ai_engine_custom_header_args', array(
		'default-image'          => '',
		'width'                  => 1200,
		'height'                 => 100,
		'flex-height'            => true,
		'flex-width'             => true,
		'wp-head-callback'       => 'ai_engine_header_style',
	) ) );

	// Register default headers.
	register_default_headers( array(
		'default-banner' => array(
			'url'           => '%s/images/default.png',
			'thumbnail_url' => '%s/images/default.png',
			'description'   => esc_html_x( 'Default Banner', 'header image description', 'ai-engine' ),
		),

	) );
}
add_action( 'after_setup_theme', 'ai_engine_custom_header_setup' );

function ai_engine_header_style() {

	$ai_engine_header_text_color = get_header_textcolor();

	if ( get_theme_support( 'custom-header', 'default-text-color' ) === $ai_engine_header_text_color ) {
		return;
	}

	if ( ! display_header_text() ) :

		echo '<style>
		.site-title,
		.site-description{
			position:absolute;
			clip:rect(1px,1px,1px,1px);
		}
		</style>';

	else :

		echo '<style>
		.site-title a,
		.site-description{
			color:#' . esc_attr( $ai_engine_header_text_color ) . ' !important;
		}
		</style>';

	endif;
}