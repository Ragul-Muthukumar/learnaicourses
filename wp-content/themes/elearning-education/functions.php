<?php
/**
 * eLearning Education functions and definitions
 *
 * @package eLearning Education
 * @subpackage elearning_education
 */

function elearning_education_setup() {

	load_theme_textdomain( 'elearning-education', get_template_directory() . '/language' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'title-tag' );
	add_theme_support( "responsive-embeds" );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'elearning-education-featured-image', 2000, 1200, true );
	add_image_size( 'elearning-education-thumbnail-avatar', 100, 100, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary-menu'    => __( 'Primary Menu', 'elearning-education' ),
	) );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
		'flex-height'  => true,
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array('image','video','gallery','audio',) );

	add_theme_support( 'html5', array('comment-form','comment-list','gallery','caption',) );

	add_theme_support( 'custom-header', apply_filters( 'elearning_education_custom_header_args', array(
        'default-text-color' => 'fff',
        'header-text'        => false,
        'width'              => 1600,
        'height'             => 350,
        'flex-width'         => true,
        'flex-height'        => true,
        'wp-head-callback'   => 'elearning_education_header_style',
        'default-image'      => get_template_directory_uri() . '/assets/images/sliderimage.png',
    ) ) );

	/**
	 * Implement the Custom Header feature.
	 */
	require get_parent_theme_file_path( '/inc/custom-header.php' );

}
add_action( 'after_setup_theme', 'elearning_education_setup' );

// Add function after setup:
function elearning_education_conditional_editor_styles() {

	add_editor_style( array( 'assets/css/editor-style.css', elearning_education_fonts_url() ) );
}
add_action( 'after_setup_theme', 'elearning_education_conditional_editor_styles', 11 );

/**
 * Register custom fonts.
 */
function elearning_education_fonts_url(){
	$elearning_education_font_url = '';
	$elearning_education_font_family = array();
	$elearning_education_font_family[] = 'Libre Baskerville:ital,wght@0,400;0,700;1,400';
	$elearning_education_font_family[] = 'Manrope:wght@200;300;400;500;600;700;800';
	$elearning_education_font_family[] = 'Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900';

	$elearning_education_font_family[] = 'Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Bad Script';
		$elearning_education_font_family[] = 'Bebas Neue';
		$elearning_education_font_family[] = 'Fjalla One';
		$elearning_education_font_family[] = 'PT Sans:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'PT Serif:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900';
		$elearning_education_font_family[] = 'Roboto Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700';
		$elearning_education_font_family[] = 'Alex Brush';
		$elearning_education_font_family[] = 'Overpass:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Playball';
		$elearning_education_font_family[] = 'Alegreya:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Julius Sans One';
		$elearning_education_font_family[] = 'Arsenal:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Slabo 13px';
		$elearning_education_font_family[] = 'Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900';
		$elearning_education_font_family[] = 'Overpass Mono:wght@300;400;500;600;700';
		$elearning_education_font_family[] = 'Source Sans Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900';
		$elearning_education_font_family[] = 'Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900';
		$elearning_education_font_family[] = 'Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
		$elearning_education_font_family[] = 'Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700';
		$elearning_education_font_family[] = 'Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
		$elearning_education_font_family[] = 'Arimo:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
		$elearning_education_font_family[] = 'Playfair Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Quicksand:wght@300;400;500;600;700';
		$elearning_education_font_family[] = 'Padauk:wght@400;700';
		$elearning_education_font_family[] = 'Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000';
		$elearning_education_font_family[] = 'Inconsolata:wght@200;300;400;500;600;700;800;900&family=Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000';
		$elearning_education_font_family[] = 'Bitter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000';
		$elearning_education_font_family[] = 'Pacifico';
		$elearning_education_font_family[] = 'Indie Flower';
		$elearning_education_font_family[] = 'VT323';
		$elearning_education_font_family[] = 'Dosis:wght@200;300;400;500;600;700;800';
		$elearning_education_font_family[] = 'Frank Ruhl Libre:wght@300;400;500;700;900';
		$elearning_education_font_family[] = 'Fjalla One';
		$elearning_education_font_family[] = 'Figtree:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Oxygen:wght@300;400;700';
		$elearning_education_font_family[] = 'Arvo:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Noto Serif:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Lobster';
		$elearning_education_font_family[] = 'Crimson Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700';
		$elearning_education_font_family[] = 'Yanone Kaffeesatz:wght@200;300;400;500;600;700';
		$elearning_education_font_family[] = 'Anton';
		$elearning_education_font_family[] = 'Libre Baskerville:ital,wght@0,400;0,700;1,400';
		$elearning_education_font_family[] = 'Bree Serif';
		$elearning_education_font_family[] = 'Gloria Hallelujah';
		$elearning_education_font_family[] = 'Abril Fatface';
		$elearning_education_font_family[] = 'Varela Round';
		$elearning_education_font_family[] = 'Vampiro One';
		$elearning_education_font_family[] = 'Shadows Into Light';
		$elearning_education_font_family[] = 'Cuprum:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
		$elearning_education_font_family[] = 'Rokkitt:wght@100;200;300;400;500;600;700;800;900';
		$elearning_education_font_family[] = 'Vollkorn:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Francois One';
		$elearning_education_font_family[] = 'Orbitron:wght@400;500;600;700;800;900';
		$elearning_education_font_family[] = 'Patua One';
		$elearning_education_font_family[] = 'Acme';
		$elearning_education_font_family[] = 'Satisfy';
		$elearning_education_font_family[] = 'Josefin Slab:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700';
		$elearning_education_font_family[] = 'Quattrocento Sans:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Architects Daughter';
		$elearning_education_font_family[] = 'Russo One';
		$elearning_education_font_family[] = 'Monda:wght@400;700';
		$elearning_education_font_family[] = 'Righteous';
		$elearning_education_font_family[] = 'Lobster Two:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Hammersmith One';
		$elearning_education_font_family[] = 'Courgette';
		$elearning_education_font_family[] = 'Permanent Marke';
		$elearning_education_font_family[] = 'Cherry Swash:wght@400;700';
		$elearning_education_font_family[] = 'Cormorant Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700';
		$elearning_education_font_family[] = 'Poiret One';
		$elearning_education_font_family[] = 'BenchNine:wght@300;400;700';
		$elearning_education_font_family[] = 'Economica:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Handlee';
		$elearning_education_font_family[] = 'Cardo:ital,wght@0,400;0,700;1,400';
		$elearning_education_font_family[] = 'Alfa Slab One';
		$elearning_education_font_family[] = 'Averia Serif Libre:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700';
		$elearning_education_font_family[] = 'Cookie';
		$elearning_education_font_family[] = 'Chewy';
		$elearning_education_font_family[] = 'Great Vibes';
		$elearning_education_font_family[] = 'Coming Soon';
		$elearning_education_font_family[] = 'Philosopher:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Days One';
		$elearning_education_font_family[] = 'Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Shrikhand';
		$elearning_education_font_family[] = 'Tangerine:wght@400;700';
		$elearning_education_font_family[] = 'IM Fell English SC';
		$elearning_education_font_family[] = 'Boogaloo';
		$elearning_education_font_family[] = 'Bangers';
		$elearning_education_font_family[] = 'Fredoka One';
		$elearning_education_font_family[] = 'Volkhov:ital,wght@0,400;0,700;1,400;1,700';
		$elearning_education_font_family[] = 'Shadows Into Light Two';
		$elearning_education_font_family[] = 'Marck Script';
		$elearning_education_font_family[] = 'Sacramento';
		$elearning_education_font_family[] = 'Unica One';
		$elearning_education_font_family[] = 'Dancing Script:wght@400;500;600;700';
		$elearning_education_font_family[] = 'Exo 2:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Archivo:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
		$elearning_education_font_family[] = 'DM Serif Display:ital@0;1';
		$elearning_education_font_family[] = 'Open Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800';

	$elearning_education_query_args = array(
		'family'	=> rawurlencode(implode('|',$elearning_education_font_family)),
	);
	$elearning_education_font_url = add_query_arg($elearning_education_query_args,'//fonts.googleapis.com/css');
	return $elearning_education_font_url;
	$contents = elearning_education_wptt_get_web_font_url( esc_url_raw( $elearning_education_font_url ) );
}

/**
 * Register widget area.
 */
function elearning_education_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'elearning-education' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'elearning-education' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your sidebar on pages.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar 3', 'elearning-education' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'elearning-education' ),
		'id'            => 'footer-1',
		'description'   => __( 'Add widgets here to appear in your footer.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'elearning-education' ),
		'id'            => 'footer-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 3', 'elearning-education' ),
		'id'            => 'footer-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 4', 'elearning-education' ),
		'id'            => 'footer-4',
		'description'   => __( 'Add widgets here to appear in your footer.', 'elearning-education' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'elearning_education_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function elearning_education_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'elearning-education-fonts', elearning_education_fonts_url(), array(), null );

	// Bootstrap
	wp_enqueue_style( 'bootstrap-css', get_theme_file_uri( '/assets/css/bootstrap.css' ) );

	// Theme stylesheet.
	wp_enqueue_style( 'elearning-education-style', get_stylesheet_uri() );
	require get_parent_theme_file_path( '/tp-theme-color.php' );
	wp_add_inline_style( 'elearning-education-style',$elearning_education_tp_theme_css );
	require get_parent_theme_file_path( '/tp-body-width-layout.php' );
	wp_add_inline_style( 'elearning-education-style',$elearning_education_tp_theme_css );
	wp_style_add_data('elearning-education-style', 'rtl', 'replace');

	// owl-carousel
	wp_enqueue_style( 'owl-carousel-css', get_theme_file_uri( '/assets/css/owl.carousel.css' ) );

	// Theme block stylesheet.
	wp_enqueue_style( 'elearning-education-block-style', get_theme_file_uri( '/assets/css/blocks.css' ), array( 'elearning-education-style' ), '1.0' );

	// Fontawesome
	wp_enqueue_style( 'fontawesome-css', get_theme_file_uri( '/assets/css/fontawesome-all.css' ) );

	wp_enqueue_script( 'bootstrap-js', get_theme_file_uri( '/assets/js/bootstrap.js' ), array( 'jquery' ), true );
	wp_enqueue_script( 'owl-carousel-js', get_theme_file_uri( '/assets/js/owl.carousel.js' ), array( 'jquery' ), true );
	wp_enqueue_script( 'elearning-education-custom-scripts', get_template_directory_uri() . '/assets/js/elearning-education-custom.js', array('jquery'), true);

	wp_enqueue_script( 'elearning-education-focus-nav', get_template_directory_uri() . '/assets/js/focus-nav.js', array('jquery'), true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$elearning_education_body_font_family = get_theme_mod('elearning_education_body_font_family', '');

	$elearning_education_heading_font_family = get_theme_mod('elearning_education_heading_font_family', '');

	$elearning_education_tp_theme_css = '
		body{
		    font-family: '.esc_html($elearning_education_body_font_family).';
		}
		h1{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
		h2{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
		h3{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
		h4{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
		h5{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
		h6{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
		#theme-sidebar .wp-block-search .wp-block-search__label{
		    font-family: '.esc_html($elearning_education_heading_font_family).';
		}
	';
	wp_add_inline_style('elearning-education-style', $elearning_education_tp_theme_css);

}
add_action( 'wp_enqueue_scripts', 'elearning_education_scripts' );

/*radio button sanitization*/
function elearning_education_sanitize_choices( $input, $setting ) {
    global $wp_customize;
    $control = $wp_customize->get_control( $setting->id );
    if ( array_key_exists( $input, $control->choices ) ) {
        return $input;
    } else {
        return $setting->default;
    }
}

/* Excerpt Limit Begin */
function elearning_education_excerpt_function($excerpt_count = 35) {
    $elearning_education_excerpt = get_the_excerpt();

    $ELEARNING_EDUCATION_TEXT_excerpt = wp_strip_all_tags($elearning_education_excerpt);

    $elearning_education_excerpt_limit = esc_attr(get_theme_mod('elearning_education_excerpt_count', $excerpt_count));

    $elearning_education_theme_excerpt = implode(' ', array_slice(explode(' ', $ELEARNING_EDUCATION_TEXT_excerpt), 0, $elearning_education_excerpt_limit));

    return $elearning_education_theme_excerpt;
}

function elearning_education_sanitize_dropdown_pages( $page_id, $setting ) {
  // Ensure $input is an absolute integer.
  $page_id = absint( $page_id );
  // If $page_id is an ID of a published page, return it; otherwise, return the default.
  return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
}

// Sanitize Sortable control.
function elearning_education_sanitize_sortable( $val, $setting ) {
	if ( is_string( $val ) || is_numeric( $val ) ) {
		return array(
			esc_attr( $val ),
		);
	}
	$sanitized_value = array();
	foreach ( $val as $item ) {
		if ( isset( $setting->manager->get_control( $setting->id )->choices[ $item ] ) ) {
			$sanitized_value[] = esc_attr( $item );
		}
	}
	return $sanitized_value;
}

// Change number or products per row to 3
add_filter('loop_shop_columns', 'elearning_education_loop_columns');
if (!function_exists('elearning_education_loop_columns')) {
	function elearning_education_loop_columns() {
		$columns = get_theme_mod( 'elearning_education_per_columns', 3 );
		return $columns;
	}
}

//Change number of products that are displayed per page (shop page)
add_filter( 'loop_shop_per_page', 'elearning_education_per_page', 20 );
function elearning_education_per_page( $elearning_education_cols ) {
  	$elearning_education_cols = get_theme_mod( 'elearning_education_product_per_page', 9 );
	return $elearning_education_cols;
}
// Category count 
function elearning_education_display_post_category_count() {
    $elearning_education_category = get_the_category();
    $elearning_education_category_count = ($elearning_education_category) ? count($elearning_education_category) : 0;
    $elearning_education_category_text = ($elearning_education_category_count === 1) ? 'category' : 'categories'; // Check for pluralization
    echo $elearning_education_category_count . ' ' . $elearning_education_category_text;
}

//post tag
function custom_tags_filter($elearning_education_tag_list) {
    // Replace the comma (,) with an empty string
    $elearning_education_tag_list = str_replace(', ', '', $elearning_education_tag_list);

    return $elearning_education_tag_list;
}
add_filter('the_tags', 'custom_tags_filter');

function custom_output_tags() {
    $elearning_education_tags = get_the_tags();

    if ($elearning_education_tags) {
        $elearning_education_tags_output = '<div class="post_tag">Tags: ';

        $elearning_education_first_tag = reset($elearning_education_tags);

        foreach ($elearning_education_tags as $tag) {
            $elearning_education_tags_output .= '<a href="' . esc_url(get_tag_link($tag)) . '" rel="tag" class="me-2">' . esc_html($tag->name) . '</a>';
            if ($tag !== $elearning_education_first_tag) {
                $elearning_education_tags_output .= ' ';
            }
        }

        $elearning_education_tags_output .= '</div>';

        echo $elearning_education_tags_output;
    }
}

function elearning_education_sanitize_phone_number( $phone ) {
	return preg_replace( '/[^\d+]/', '', $phone );
}

function elearning_education_sanitize_number_range( $number, $setting ) {

	// Ensure input is an absolute integer.
	$number = absint( $number );

	// Get the input attributes associated with the setting.
	$atts = $setting->manager->get_control( $setting->id )->input_attrs;

	// Get minimum number in the range.
	$min = ( isset( $atts['min'] ) ? $atts['min'] : $number );

	// Get maximum number in the range.
	$max = ( isset( $atts['max'] ) ? $atts['max'] : $number );

	// Get step.
	$step = ( isset( $atts['step'] ) ? $atts['step'] : 1 );

	// If the number is within the valid range, return it; otherwise, return the default
	return ( $min <= $number && $number <= $max && is_int( $number / $step ) ? $number : $setting->default );
}

function elearning_education_sanitize_checkbox( $input ) {
	// Boolean check
	return ( ( isset( $input ) && true == $input ) ? true : false );
}

function elearning_education_sanitize_number_absint( $number, $setting ) {
	// Ensure $number is an absolute integer (whole number, zero or greater).
	$number = absint( $number );

	// If the input is an absolute integer, return it; otherwise, return the default
	return ( $number ? $number : $setting->default );
}

function elearning_education_sanitize_select( $input, $setting ){
	//input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
	$input = sanitize_key($input);

	//get the list of possible select options
	$choices = $setting->manager->get_control( $setting->id )->choices;

	//return input if valid or return default option
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

function elearning_education_string_limit_words($string, $word_limit) {
    $words = explode(' ', $string);
    return implode(' ', array_slice($words, 0, $word_limit));
}

/**
 * Use front-page.php when Front page displays is set to a static page.
 */
function elearning_education_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template','elearning_education_front_page_template' );

function elearning_education_theme_setup() {

	define('ELEARNING_EDUCATION_CREDIT',__('https://www.themespride.com/products/free-elearning-education-wordpress-theme','elearning-education') );
	if ( ! function_exists( 'elearning_education_credit' ) ) {
		function elearning_education_credit(){
			echo "<a href=".esc_url(ELEARNING_EDUCATION_CREDIT)." target='_blank'>".esc_html__(get_theme_mod('elearning_education_footer_text',__('eLearning Education WordPress Theme','elearning-education')))."</a>";
		}
	}

	/**
	 * Custom template tags for this theme.
	 */
	require get_parent_theme_file_path( '/inc/template-tags.php' );

	/**
	 * Additional features to allow styling of the templates.
	 */
	require get_parent_theme_file_path( '/inc/template-functions.php' );

	/**
	 * Customizer additions.
	 */
	require get_parent_theme_file_path( '/inc/customizer.php' );

	/**
	 * About Theme.
	 */
	require get_parent_theme_file_path( '/inc/about-theme.php' );

	/**
	 * Load Theme Web File
	 */
	require get_parent_theme_file_path('/inc/wptt-webfont-loader.php' );
	/**
	 * Load Toggle file
	 */
	require get_parent_theme_file_path( '/inc/controls/customize-control-toggle.php' );

	/**
	 * load sortable file
	 */
	require get_parent_theme_file_path( '/inc/controls/sortable-control.php' );

	/**
	 * TGM Recommendation
	 */
	require get_parent_theme_file_path( '/inc/TGM/tgm.php' );

}
add_action( 'after_setup_theme', 'elearning_education_theme_setup' );

/**
 * Logo Custamization.
 */

function elearning_education_logo_width(){

	$elearning_education_logo_width   = get_theme_mod( 'elearning_education_logo_width', 100 );

	echo "<style type='text/css' media='all'>"; ?>
		img.custom-logo{
		    width: <?php echo absint( $elearning_education_logo_width ); ?>px;
		    max-width: 100%;
	}
	<?php echo "</style>";
}

add_action( 'wp_head', 'elearning_education_logo_width' );

add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );


//Admin Enqueue for Admin
function elearning_education_admin_enqueue_scripts(){
	wp_enqueue_style('elearning-education-admin-style',( get_template_directory_uri() ) . '/assets/css/admin.css');
	wp_enqueue_script( 'elearning-education-custom-scripts',( get_template_directory_uri() ). '/assets/js/elearning-education-custom.js', array('jquery'), true);
	wp_register_script( 'elearning-education-admin-script', get_template_directory_uri() . '/assets/js/elearning-education-admin.js', array( 'jquery' ), '', true );

	wp_localize_script(
		'elearning-education-admin-script',
		'elearning_education',
		array(
			'admin_ajax'	=>	admin_url('admin-ajax.php'),
			'wpnonce'			=>	wp_create_nonce('elearning_education_dismissed_notice_nonce')
		)
	);
	wp_enqueue_script('elearning-education-admin-script');

    wp_localize_script( 'elearning-education-admin-script', 'elearning_education_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
    );
}
add_action( 'admin_enqueue_scripts', 'elearning_education_admin_enqueue_scripts' );


// get started
add_action( 'wp_ajax_elearning_education_dismissed_notice_handler', 'elearning_education_ajax_notice_handler' );

function elearning_education_ajax_notice_handler() {
	if (!wp_verify_nonce($_POST['wpnonce'], 'elearning_education_dismissed_notice_nonce')) {
		exit;
	}
    if ( isset( $_POST['type'] ) ) {
        $type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
        update_option( 'dismissed-' . $type, TRUE );
    }
}

function elearning_education_activation_notice() { 

	if ( ! get_option('dismissed-get_started', FALSE ) ) { ?>

    <div class="elearning-education-notice-wrapper updated notice notice-get-started-class is-dismissible" data-notice="get_started">
        <div class="elearning-education-getting-started-notice clearfix">
        	<div class="row-top">
	            <div class="elearning-education-theme-notice-content">
	                <h2 class="elearning-education-notice-h2">
	                    <?php
	                printf(
	                /* translators: 1: welcome page link starting html tag, 2: welcome page link ending html tag. */
	                    esc_html__( 'Install the Demo Import Plugin now to instantly set up your site like the live preview.', 'elearning-education' ), '<strong>'. wp_get_theme()->get('Name'). '</strong>' );
	                ?>
	                </h2>
	                <?php 
					$elearning_education_theme = wp_get_theme();
					?>
					<a class="elearning-education-btn-get-started button button-primary button-hero elearning-education-button-padding" href="<?php echo esc_url( admin_url( 'themes.php?page=elearning-education-about' ) ); ?>">
					    <?php 
					    echo sprintf(
					        esc_html__( 'Get Started with %s Theme', 'elearning-education' ),
					        esc_html( $elearning_education_theme->get( 'Name' ) )
					    ); 
					    ?>
					</a>
	            </div>
	            <div class="image-box">
			    	<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/theme-notice.png' ); ?>" alt="<?php echo esc_attr__( 'eLearning Education', 'elearning-education' ); ?>" />
				</div>
	        </div>
        </div>
    </div>
<?php }

}
add_action( 'admin_notices', 'elearning_education_activation_notice' );

add_action('after_switch_theme', 'elearning_education_setup_options');
function elearning_education_setup_options () {
    update_option('dismissed-get_started', FALSE );
}

// Get Started Detail Notice - Dismiss permanently
function elearning_education_dismissed_get_started_detail_notice() {
    update_option( 'dismissed-get_started-detail', true );
    wp_send_json_success();
}
add_action( 'wp_ajax_elearning_education_dismissed_get_started_detail_notice', 'elearning_education_dismissed_get_started_detail_notice' );
add_action( 'wp_ajax_nopriv_elearning_education_dismissed_get_started_detail_notice', 'elearning_education_dismissed_get_started_detail_notice' );

// Reset on theme switch
add_action('after_switch_theme', 'elearning_education_setup_settings');
function elearning_education_setup_settings() {
    update_option('dismissed-get_started', false );
    update_option('dismissed-get_started-detail', false );
}

add_action( 'wp_ajax_elearning_education_popup_done', 'elearning_education_popup_done' );
function elearning_education_popup_done() {
	$theme_slug = get_stylesheet();
	update_option( $theme_slug . '_demo_popup_shown', true );
	wp_die();
}