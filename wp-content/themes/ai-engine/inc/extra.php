<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ai_engine
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function ai_engine_body_classes( $classes ) {
  global $ai_engine_post;
  
    if( !is_page_template( 'template-home.php' ) ){
        $classes[] = 'inner';
        // Adds a class of group-blog to blogs with more than 1 published author.
    }

    if ( is_multi_author() ) {
        $classes[] = 'group-blog ';
    }

    // Adds a class of custom-background-image to sites with a custom background image.
    if ( get_background_image() ) {
        $classes[] = 'custom-background-image';
    }
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
        $classes[] = 'custom-background-color';
    }
    

    if( ai_engine_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() || 'product' === get_post_type() ) && ! is_active_sidebar( 'shop-sidebar' ) ){
        $classes[] = 'full-width';
    }    

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_page() ) {
        $classes[] = 'hfeed ';
    }
  
    if( is_404() ||  is_search() ){
        $classes[] = 'full-width';
    }
  
    if( ! is_active_sidebar( 'right-sidebar' ) ) {
        $classes[] = 'full-width'; 
    }

    return $classes;
}
add_filter( 'body_class', 'ai_engine_body_classes' );

 /**
 * 
 * @link http://www.altafweb.com/2011/12/remove-specific-tag-from-php-string.html
 */
function ai_engine_strip_single( $tag, $string ){
    $string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
    $string=preg_replace('/<\/'.$tag.'>/i', '', $string);
    return $string;
}

if ( ! function_exists( 'ai_engine_excerpt_more' ) ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
 */
function ai_engine_excerpt_more($more) {
  return is_admin() ? $more : ' &hellip; ';
}
endif;
add_filter( 'excerpt_more', 'ai_engine_excerpt_more' );


if( ! function_exists( 'ai_engine_footer_credit' ) ):
/**
 * Footer Credits
*/
function ai_engine_footer_credit() {

    // Check if footer copyright is enabled
    $ai_engine_show_footer_copyright = get_theme_mod( 'ai_engine_footer_setting', true );

    if ( ! $ai_engine_show_footer_copyright ) {
        return; 
    }

    $ai_engine_copyright_text = get_theme_mod('ai_engine_footer_copyright_text');

    $ai_engine_text = '<div class="site-info"><div class="container"><span class="copyright">';
    if ($ai_engine_copyright_text) {
        $ai_engine_text .= wp_kses_post($ai_engine_copyright_text); 
    } else {
        $ai_engine_text .= esc_html__('&copy; ', 'ai-engine') . date_i18n(esc_html__('Y', 'ai-engine')); 
        $ai_engine_text .= ' <a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a>' . esc_html__('. All Rights Reserved.', 'ai-engine');
    }
    $ai_engine_text .= '</span>';
    $ai_engine_text .= '<span class="by"> <a href="' . esc_url('https://www.themeignite.com/products/ai-engine') . '" rel="nofollow" target="_blank">' . AI_ENGINE_THEME_NAME . '</a>' . esc_html__(' By ', 'ai-engine') . '<a href="' . esc_url('https://themeignite.com/') . '" rel="nofollow" target="_blank">' . esc_html__('Themeignite', 'ai-engine') . '</a>.';
    /* translators: %s: link to WordPress.org */
    $ai_engine_text .= sprintf(esc_html__(' Powered By %s', 'ai-engine'), '<a href="' . esc_url(__('https://wordpress.org/', 'ai-engine')) . '" target="_blank">WordPress</a>.');
    if (function_exists('the_privacy_policy_link')) {
        $ai_engine_text .= get_the_privacy_policy_link();
    }
    $ai_engine_text .= '</span></div></div>';
    echo apply_filters('ai_engine_footer_text', $ai_engine_text);
}
add_action('ai_engine_footer', 'ai_engine_footer_credit');
endif;

/**
 * Is Woocommerce activated
*/
if ( ! function_exists( 'ai_engine_woocommerce_activated' ) ) {
  function ai_engine_woocommerce_activated() {
    if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
  }
}

if( ! function_exists( 'ai_engine_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function ai_engine_change_comment_form_default_fields( $fields ){    
    // get the current commenter if available
    $ai_engine_commenter = wp_get_current_commenter();
 
    // core functionality
    $req      = get_option( 'require_name_email' );
    $ai_engine_aria_req = ( $req ? " aria-required='true'" : '' );
    $ai_engine_required = ( $req ? " required" : '' );
    $ai_engine_author   = ( $req ? __( 'Name*', 'ai-engine' ) : __( 'Name', 'ai-engine' ) );
    $ai_engine_email    = ( $req ? __( 'Email*', 'ai-engine' ) : __( 'Email', 'ai-engine' ) );
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><label class="screen-reader-text" for="author">' . esc_html__( 'Name', 'ai-engine' ) . '<span class="required">*</span></label><input id="author" name="author" placeholder="' . esc_attr( $ai_engine_author ) . '" type="text" value="' . esc_attr( $ai_engine_commenter['comment_author'] ) . '" size="30"' . $ai_engine_aria_req . $ai_engine_required . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><label class="screen-reader-text" for="email">' . esc_html__( 'Email', 'ai-engine' ) . '<span class="required">*</span></label><input id="email" name="email" placeholder="' . esc_attr( $ai_engine_email ) . '" type="text" value="' . esc_attr(  $ai_engine_commenter['comment_author_email'] ) . '" size="30"' . $ai_engine_aria_req . $ai_engine_required. ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label class="screen-reader-text" for="url">' . esc_html__( 'Website', 'ai-engine' ) . '</label><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'ai-engine' ) . '" type="text" value="' . esc_attr( $ai_engine_commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;    
}
endif;
add_filter( 'comment_form_default_fields', 'ai_engine_change_comment_form_default_fields' );

if( ! function_exists( 'ai_engine_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function ai_engine_change_comment_form_defaults( $defaults ){    
    $defaults['comment_field'] = '<p class="comment-form-comment"><label class="screen-reader-text" for="comment">' . esc_html__( 'Comment', 'ai-engine' ) . '</label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'ai-engine' ) . '" cols="45" rows="8" aria-required="true" required></textarea></p>';
    
    return $defaults;    
}
endif;
add_filter( 'comment_form_defaults', 'ai_engine_change_comment_form_defaults' );

if( ! function_exists( 'ai_engine_escape_text_tags' ) ) :
/**
 * Remove new line tags from string
 *
 * @param $text
 * @return string
 */
function ai_engine_escape_text_tags( $text ) {
    return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}
endif;

if( ! function_exists( 'wp_body_open' ) ) :
/**
 * Fire the wp_body_open action.
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
*/
function wp_body_open() {
    /**
     * Triggered after the opening <body> tag.
    */
    do_action( 'wp_body_open' );
}
endif;

if ( ! function_exists( 'ai_engine_get_fallback_svg' ) ) :    
/**
 * Get Fallback SVG
*/
function ai_engine_get_fallback_svg( $ai_engine_post_thumbnail ) {
    if( ! $ai_engine_post_thumbnail ){
        return;
    }
    
    $ai_engine_image_size = ai_engine_get_image_sizes( $ai_engine_post_thumbnail );
     
    if( $ai_engine_image_size ){ ?>
        <div class="svg-holder">
             <svg class="fallback-svg" viewBox="0 0 <?php echo esc_attr( $ai_engine_image_size['width'] ); ?> <?php echo esc_attr( $ai_engine_image_size['height'] ); ?>" preserveAspectRatio="none">
                    <rect width="<?php echo esc_attr( $ai_engine_image_size['width'] ); ?>" height="<?php echo esc_attr( $ai_engine_image_size['height'] ); ?>" style="fill:#dedddd;"></rect>
            </svg>
        </div>
        <?php
    }
}
endif;

function ai_engine_enqueue_google_fonts() {

    require get_template_directory() . '/inc/wptt-webfont-loader.php';

    wp_enqueue_style(
        'google-fonts-Josefin+Sans',
        ai_engine_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap' ),
        array(),
        '1.0'
    );
}
add_action( 'wp_enqueue_scripts', 'ai_engine_enqueue_google_fonts' );


if( ! function_exists( 'ai_engine_site_branding' ) ) :
/**
 * Site Branding
*/
function ai_engine_site_branding(){
    $ai_engine_logo_site_title = get_theme_mod( 'header_site_title', 1 );
    $ai_engine_tagline = get_theme_mod( 'header_tagline', false );
    $ai_engine_logo_width = get_theme_mod('logo_width', 100); // Retrieve the logo width setting

    ?>
    <div class="site-branding" style="max-width: <?php echo esc_attr(get_theme_mod('logo_width', '-1'))?>px;">
        <?php 
        // Check if custom logo is set and display it
        if (function_exists('has_custom_logo') && has_custom_logo()) {
            the_custom_logo();
        }
        if ($ai_engine_logo_site_title):
             if (is_front_page()): ?>
            <h1 class="site-title" style="font-size: <?php echo esc_attr(get_theme_mod('ai_engine_site_title_size', '24')); ?>px;">
            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
          </h1>
            <?php else: ?>
                <p class="site-title" itemprop="name">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                </p>
            <?php endif; ?>
        <?php endif; 
    
        if ($ai_engine_tagline) :
            $ai_engine_description = get_bloginfo('description', 'display');
            if ($ai_engine_description || is_customize_preview()) :
        ?>
                <p class="site-description" itemprop="description"><?php echo $ai_engine_description; ?></p>
            <?php endif;
        endif;
        ?>
    </div>
    <?php
}
endif;
if( ! function_exists( 'ai_engine_navigation' ) ) :
    /**
     * Site Navigation
    */
    function ai_engine_navigation(){
        ?>
        <nav class="main-navigation" id="site-navigation" role="navigation">
            <?php 
            wp_nav_menu( array( 
                'theme_location' => 'primary', 
                'menu_id' => 'primary-menu' 
            ) ); 
            ?>
        </nav>
        <?php
    }
endif;

if( ! function_exists( 'ai_engine_top_header' ) ) :
/**
 * Header Start
*/
function ai_engine_top_header(){
    $ai_engine_header_setting     = get_theme_mod( 'ai_engine_header_setting', false );
    $ai_engine_social_icon  = get_theme_mod( 'ai_engine_social_icon_setting', false);
    $ai_engine_header_email    = get_theme_mod('ai_engine_header_email');
    $ai_engine_header_phone    = get_theme_mod('ai_engine_header_phone');
    ?>
    <?php if ( $ai_engine_header_setting ){?>
        <div class="top-header">
            <div class="container">
                <div class="row">                      
                    <div class="col-lg-8 col-md-12 col-12 top-info">
                        <!-- PHONE -->
                        <?php if ($ai_engine_header_phone) : ?>
                            <div class="location">
                                <a href="tel:<?php echo esc_attr($ai_engine_header_phone); ?>">
                                    <span class="contact-icon">
                                        <i class="<?php echo esc_attr(get_theme_mod('ai_engine_phone_icon','fas fa-phone')); ?>"></i>
                                    </span>
                                    <span class="contact-box">
                                        <span class="location-text">
                                            <span class="main-text"><?php echo esc_html($ai_engine_header_phone); ?></span>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <!-- EMAIL -->
                        <?php if ($ai_engine_header_email) : ?>
                            <div class="location">
                                <a href="mailto:<?php echo esc_attr($ai_engine_header_email); ?>">
                                    <span class="contact-icon">                                     
                                        <i class="<?php echo esc_attr(get_theme_mod('ai_engine_mail_icon','fa-regular fa-envelope')); ?>"></i>                                      
                                    </span>
                                    <span class="contact-box">
                                        <span class="location-text">
                                            <span class="main-text"><?php echo esc_html($ai_engine_header_email); ?></span>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col-md-12 col-12 text-lg-end text-md-center align-self-center d-flex topbar-info">
                        <?php if ( $ai_engine_social_icon ){?>
                            <span class="social-links">
                                <?php 
                                    $ai_engine_social_link1 = get_theme_mod( 'ai_engine_social_link_1' );
                                    $ai_engine_social_link3 = get_theme_mod( 'ai_engine_social_link_3' );
                                    $ai_engine_social_link4 = get_theme_mod( 'ai_engine_social_link_4' );
                                    $ai_engine_social_link2 = get_theme_mod( 'ai_engine_social_link_2' );
                                    $ai_engine_social_link5 = get_theme_mod( 'ai_engine_social_link_5' );

                                    if ( ! empty( $ai_engine_social_link1 ) ) {
                                    echo '<a class="social1" href="' . esc_url( $ai_engine_social_link1 ) . '" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                                    }
                                    if ( ! empty( $ai_engine_social_link3 ) ) {
                                    echo '<a class="social3" href="' . esc_url( $ai_engine_social_link3 ) . '" target="_blank"><i class="fab fa-instagram"></i></a>';
                                    }
                                    if ( ! empty( $ai_engine_social_link4 ) ) {
                                    echo '<a class="social4" href="' . esc_url( $ai_engine_social_link4 ) . '" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                                    }
                                    if ( ! empty( $ai_engine_social_link2 ) ) {
                                    echo '<a class="social2" href="' . esc_url( $ai_engine_social_link2 ) . '" target="_blank"><i class="fab fa-twitter"></i></a>';
                                    } 
                                    if ( ! empty( $ai_engine_social_link5 ) ) {
                                    echo '<a class="social5" href="' . esc_url( $ai_engine_social_link5 ) . '" target="_blank"><i class="fab fa-behance"></i></a>';
                                    } 
                                ?>
                            </span>
                        <?php } ?>                       
                    </div>
                </div>              
            </div>
        </div>
    <?php } ?>
    <?php
}
endif;
add_action( 'ai_engine_top_header', 'ai_engine_top_header', 20 );


if( ! function_exists( 'ai_engine_header' ) ) :
/**
 * Header Start
*/
function ai_engine_header(){
    $ai_engine_header_image = get_header_image();
    $ai_engine_sticky_header = get_theme_mod('ai_engine_sticky_header');
    $ai_engine_header_btn_url  = get_theme_mod( 'ai_engine_header_btn_url' );
    $ai_engine_header_btn_text = get_theme_mod( 'ai_engine_header_btn_text' );
    $ai_engine_header_login_btn_text = get_theme_mod( 'ai_engine_header_login_btn_text' );
    $ai_engine_header_btn_url  = get_theme_mod( 'ai_engine_header_btn_url' );
    $ai_engine_header_login_btn_url = get_theme_mod( 'ai_engine_header_login_btn_url' );
    ?>
<div id="page-site-header">
    <header id="masthead" class="site-header header-inner" role="banner">
        <div class="theme-menu head_bg"
            <?php if ( ! empty( $ai_engine_header_image ) ) : ?>
                style="background-image:url('<?php echo esc_url( $ai_engine_header_image ); ?>');background-repeat:no-repeat;background-size:cover;"
            <?php endif; ?>
            data-sticky="<?php echo esc_attr( $ai_engine_sticky_header ?: '' ); ?>">

            <div class="topbar-header-2" data-sticky="<?php echo $ai_engine_sticky_header; ?>">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-12 col-12 align-self-center acc-order">
                            <?php ai_engine_site_branding(); ?>
                        </div>
                        <div class="col-lg-6 col-md-12 col-12 align-self-center py-2 mail-box">
                            <div class="middle-header">
                                <?php ai_engine_navigation(); ?>
                            </div>
                        </div> 
                        <div class="col-lg-3 col-md-12 col-12 align-self-center text-lg-end">
                            <div class="header-buttons align-self-center d-flex flex-wrap">
                                <div class="slide-btn-green login-btn">
                                    <?php if ($ai_engine_header_login_btn_text ) : ?>
                                    <a href="<?php echo esc_url( $ai_engine_header_login_btn_url ); ?>">                   
                                        <?php echo esc_html( $ai_engine_header_login_btn_text ); ?>
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <div class="slide-btn-green">
                                    <?php if ($ai_engine_header_btn_text ) : ?>
                                    <a href="<?php echo esc_url( $ai_engine_header_btn_url ); ?>">                   
                                        <?php echo esc_html( $ai_engine_header_btn_text ); ?>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </header>
</div>
    <?php
}
endif;
add_action( 'ai_engine_header', 'ai_engine_header', 20 );
