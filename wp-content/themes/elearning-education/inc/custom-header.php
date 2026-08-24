<?php
/**
 * Custom header implementation
 *
 * @link https://codex.wordpress.org/Custom_Headers
 *
 * @package eLearning Education
 * @subpackage elearning_education
 */

function elearning_education_custom_header_setup() {
    register_default_headers( array(
        'default-image' => array(
            'url'           => get_template_directory_uri() . '/assets/images/sliderimage.png',
            'thumbnail_url' => get_template_directory_uri() . '/assets/images/sliderimage.png',
            'description'   => __( 'Default Header Image', 'elearning-education' ),
        ),
    ) );
}
add_action( 'after_setup_theme', 'elearning_education_custom_header_setup' );

/**
 * Styles the header image based on Customizer settings.
 */
function elearning_education_header_style() {
    $elearning_education_header_image = get_header_image() ? get_header_image() : get_template_directory_uri() . '/assets/images/sliderimage.png';

    $elearning_education_height     = get_theme_mod( 'elearning_education_header_image_height', 350 );
    $elearning_education_position   = get_theme_mod( 'elearning_education_header_background_position', 'center' );
    $elearning_education_attachment = get_theme_mod( 'elearning_education_header_background_attachment', 1 ) ? 'fixed' : 'scroll';

    $elearning_education_custom_css = "
        .header-img, .single-page-img, .external-div .box-image img, .external-div {
            background-image: url('" . esc_url( $elearning_education_header_image ) . "');
            background-size: cover;
            height: " . esc_attr( $elearning_education_height ) . "px;
            background-position: " . esc_attr( $elearning_education_position ) . ";
            background-attachment: " . esc_attr( $elearning_education_attachment ) . ";
        }

        @media (max-width: 1000px) {
            .header-img, .single-page-img, .external-div .box-image-page img,.external-div,.featured-image{
                height: 250px !important;
            }
            .box-text h2{
                font-size: 27px;
            }
        }
    ";

    wp_add_inline_style( 'elearning-education-style', $elearning_education_custom_css );
}
add_action( 'wp_enqueue_scripts', 'elearning_education_header_style' );

/**
 * Enqueue the main theme stylesheet.
 */
function elearning_education_enqueue_styles() {
    wp_enqueue_style( 'elearning-education-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'elearning_education_enqueue_styles' );