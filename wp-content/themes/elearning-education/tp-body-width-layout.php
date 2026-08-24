<?php

	$elearning_education_tp_theme_css = "";

	$elearning_education_theme_lay = get_theme_mod( 'elearning_education_tp_body_layout_settings','Full');
    if($elearning_education_theme_lay == 'Container'){
		$elearning_education_tp_theme_css .='body{';
			$elearning_education_tp_theme_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$elearning_education_tp_theme_css .='}';

		$elearning_education_tp_theme_css .='@media screen and (min-width:1367px){';
		$elearning_education_tp_theme_css .='body{';
			$elearning_education_tp_theme_css .='max-width: 1320px;';
		$elearning_education_tp_theme_css .='} }';

		$elearning_education_tp_theme_css .='@media screen and (max-width:575px){';
		$elearning_education_tp_theme_css .='body{';
			$elearning_education_tp_theme_css .='max-width: 100%; padding-right:0px; padding-left: 0px';
		$elearning_education_tp_theme_css .='} }';
		$elearning_education_tp_theme_css .='.scrolled{';
			$elearning_education_tp_theme_css .='width: auto; left:0; right:0;';
		$elearning_education_tp_theme_css .='}';
	}else if($elearning_education_theme_lay == 'Container Fluid'){
		$elearning_education_tp_theme_css .='body{';
			$elearning_education_tp_theme_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$elearning_education_tp_theme_css .='}';
		$elearning_education_tp_theme_css .='.page-template-front-page .menubar{';
			$elearning_education_tp_theme_css .='width: 99%';
		$elearning_education_tp_theme_css .='}';
		$elearning_education_tp_theme_css .='@media screen and (max-width:575px){';
		$elearning_education_tp_theme_css .='body{';
			$elearning_education_tp_theme_css .='max-width: 100%; padding-right:0px; padding-left:0px';
		$elearning_education_tp_theme_css .='} }';
		$elearning_education_tp_theme_css .='.scrolled{';
			$elearning_education_tp_theme_css .='width: auto; left:0; right:0;';
		$elearning_education_tp_theme_css .='}';
	}else if($elearning_education_theme_lay == 'Full'){
		$elearning_education_tp_theme_css .='body{';
			$elearning_education_tp_theme_css .='max-width: 100%;';
		$elearning_education_tp_theme_css .='}';
	}

    $elearning_education_scroll_position = get_theme_mod( 'elearning_education_scroll_top_position','Right');
    if($elearning_education_scroll_position == 'Right'){
        $elearning_education_tp_theme_css .='#return-to-top{';
            $elearning_education_tp_theme_css .='right: 20px;';
        $elearning_education_tp_theme_css .='}';
    }else if($elearning_education_scroll_position == 'Left'){
        $elearning_education_tp_theme_css .='#return-to-top{';
            $elearning_education_tp_theme_css .='left: 20px;';
        $elearning_education_tp_theme_css .='}';
    }else if($elearning_education_scroll_position == 'Center'){
        $elearning_education_tp_theme_css .='#return-to-top{';
            $elearning_education_tp_theme_css .='right: 50%;left: 50%;';
        $elearning_education_tp_theme_css .='}';
    }

		//Social icon Font size
$elearning_education_social_icon_fontsize = get_theme_mod('elearning_education_social_icon_fontsize');
			$elearning_education_tp_theme_css .='.media-links a i{';
$elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_social_icon_fontsize).'px;';
			$elearning_education_tp_theme_css .='}';

// site title and tagline font size option
$elearning_education_site_title_font_size = get_theme_mod('elearning_education_site_title_font_size', 30);{
			$elearning_education_tp_theme_css .='.logo h1 a, .logo p a{';
$elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_site_title_font_size).'px;';
			$elearning_education_tp_theme_css .='}';
}

$elearning_education_site_tagline_font_size = get_theme_mod('elearning_education_site_tagline_font_size', 15);{
			$elearning_education_tp_theme_css .='.logo p{';
$elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_site_tagline_font_size).'px;';
			$elearning_education_tp_theme_css .='}';
}


// related post
$elearning_education_related_post_mob = get_theme_mod('elearning_education_related_post_mob', true);
$elearning_education_related_post = get_theme_mod('elearning_education_remove_related_post', true);
$elearning_education_tp_theme_css .= '.related-post-block {';
if ($elearning_education_related_post == false) {
    $elearning_education_tp_theme_css .= 'display: none;';
}
$elearning_education_tp_theme_css .= '}';
$elearning_education_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($elearning_education_related_post == false || $elearning_education_related_post_mob == false) {
    $elearning_education_tp_theme_css .= '.related-post-block { display: none; }';
}
$elearning_education_tp_theme_css .= '}';

// slider btn
$elearning_education_slider_buttom_mob = get_theme_mod('elearning_education_slider_buttom_mob', true);
$elearning_education_slider_button = get_theme_mod('elearning_education_slider_button', true);
$elearning_education_tp_theme_css .= '#slider .more-btn {';
if ($elearning_education_slider_button == false) {
    $elearning_education_tp_theme_css .= 'display: none;';
}
$elearning_education_tp_theme_css .= '}';
$elearning_education_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($elearning_education_slider_button == false || $elearning_education_slider_buttom_mob == false) {
    $elearning_education_tp_theme_css .= '#slider .more-btn { display: none; }';
}
$elearning_education_tp_theme_css .= '}';

//return to header mobile				
$elearning_education_return_to_header_mob = get_theme_mod('elearning_education_return_to_header_mob', true);
$elearning_education_return_to_header = get_theme_mod('elearning_education_return_to_header', true);
$elearning_education_tp_theme_css .= '.return-to-header{';
if ($elearning_education_return_to_header == false) {
    $elearning_education_tp_theme_css .= 'display: none;';
}
$elearning_education_tp_theme_css .= '}';
$elearning_education_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($elearning_education_return_to_header == false || $elearning_education_return_to_header_mob == false) {
    $elearning_education_tp_theme_css .= '.return-to-header{ display: none; }';
}
$elearning_education_tp_theme_css .= '}';


$elearning_education_footer_widget_image = get_theme_mod('elearning_education_footer_widget_image');
if($elearning_education_footer_widget_image != false){
	$elearning_education_tp_theme_css .='#footer{';
		$elearning_education_tp_theme_css .='background: url('.esc_attr($elearning_education_footer_widget_image).');';
	$elearning_education_tp_theme_css .='}';
}

$elearning_education_related_product = get_theme_mod('elearning_education_related_product',true);
if($elearning_education_related_product == false){
	$elearning_education_tp_theme_css .='.related.products{';
		$elearning_education_tp_theme_css .='display: none;';
	$elearning_education_tp_theme_css .='}';
}

//blog description              
$elearning_education_mobile_blog_description = get_theme_mod('elearning_education_mobile_blog_description', true);
$elearning_education_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($elearning_education_mobile_blog_description == false) {
    $elearning_education_tp_theme_css .= '.blog-description{ display: none; }';
}
$elearning_education_tp_theme_css .= '}';

//======================= MENU TYPOGRAPHY ===================== //


$elearning_education_menu_font_size = get_theme_mod('elearning_education_menu_font_size', '');{
$elearning_education_tp_theme_css .='.main-navigation a, .main-navigation li.page_item_has_children:after,.main-navigation li.menu-item-has-children:after{';
$elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_menu_font_size).'px;';
	$elearning_education_tp_theme_css .='}';
}

$elearning_education_menu_text_tranform = get_theme_mod( 'elearning_education_menu_text_tranform','');
    if($elearning_education_menu_text_tranform == 'Uppercase'){
		$elearning_education_tp_theme_css .='.main-navigation a {';
			$elearning_education_tp_theme_css .='text-transform: uppercase;';
		$elearning_education_tp_theme_css .='}';
	}else if($elearning_education_menu_text_tranform == 'Lowercase'){
		$elearning_education_tp_theme_css .='.main-navigation a {';
			$elearning_education_tp_theme_css .='text-transform: lowercase;';
		$elearning_education_tp_theme_css .='}';
	}
	else if($elearning_education_menu_text_tranform == 'Capitalize'){
		$elearning_education_tp_theme_css .='.main-navigation a {';
			$elearning_education_tp_theme_css .='text-transform: capitalize;';
		$elearning_education_tp_theme_css .='}';
	}

//======================= slider Content layout ===================== //

$elearning_education_slider_content_layout = get_theme_mod('elearning_education_slider_content_layout', ''); 
$elearning_education_tp_theme_css .= '#slider .carousel-caption{';
switch ($elearning_education_slider_content_layout) {
    case 'LEFT-ALIGN':
        $elearning_education_tp_theme_css .= 'text-align:left; right: 50%; left: 15%';
        break;
    case 'CENTER-ALIGN':
        $elearning_education_tp_theme_css .= 'text-align:center; right: 50%; left: 15%';
        break;
    case 'RIGHT-ALIGN':
        $elearning_education_tp_theme_css .= 'text-align:right; right: 50%; left: 15%';
        break;
    default:
        $elearning_education_tp_theme_css .= 'text-align:center; right: 50%; left: 15%';
        break;
}
$elearning_education_tp_theme_css .= '}';

// Sale Tag Position (Fixed variable name conflict)
$elearning_education_sale_position = get_theme_mod( 'elearning_education_sale_tag_position', 'right' );
if ( $elearning_education_sale_position == 'right' ) {
    $elearning_education_tp_theme_css .= '.woocommerce ul.products li.product .onsale {';
    $elearning_education_tp_theme_css .= 'right: 25px !important; left: auto !important;';
    $elearning_education_tp_theme_css .= '}';
} elseif ( $elearning_education_sale_position == 'left' ) {
    $elearning_education_tp_theme_css .= '.woocommerce ul.products li.product .onsale {';
    $elearning_education_tp_theme_css .= 'left: 25px !important; right:auto !important;';
    $elearning_education_tp_theme_css .= '}';
}

$elearning_education_woocommerce_sale_font_size = get_theme_mod('elearning_education_woocommerce_sale_font_size');
if($elearning_education_woocommerce_sale_font_size != false){
    $elearning_education_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_woocommerce_sale_font_size).'px;';
    $elearning_education_tp_theme_css .='}';
}

$elearning_education_woocommerce_sale_padding_top_bottom = get_theme_mod('elearning_education_woocommerce_sale_padding_top_bottom');
if($elearning_education_woocommerce_sale_padding_top_bottom != false){
    $elearning_education_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $elearning_education_tp_theme_css .='padding-top: '.esc_attr($elearning_education_woocommerce_sale_padding_top_bottom).'px; padding-bottom: '.esc_attr($elearning_education_woocommerce_sale_padding_top_bottom).'px;';
    $elearning_education_tp_theme_css .='}';
}

$elearning_education_woocommerce_sale_padding_left_right = get_theme_mod('elearning_education_woocommerce_sale_padding_left_right');
if($elearning_education_woocommerce_sale_padding_left_right != false){
    $elearning_education_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $elearning_education_tp_theme_css .='padding-left: '.esc_attr($elearning_education_woocommerce_sale_padding_left_right).'px !Important; padding-right: '.esc_attr($elearning_education_woocommerce_sale_padding_left_right).'px !important;';
    $elearning_education_tp_theme_css .='}';
}

$elearning_education_woocommerce_sale_border_radius = get_theme_mod('elearning_education_woocommerce_sale_border_radius', 100);
if($elearning_education_woocommerce_sale_border_radius != false){
    $elearning_education_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $elearning_education_tp_theme_css .='border-radius: '.esc_attr($elearning_education_woocommerce_sale_border_radius).'% !important;';
    $elearning_education_tp_theme_css .='}';
}


//Font Weight
$elearning_education_menu_font_weight = get_theme_mod( 'elearning_education_menu_font_weight','');
if($elearning_education_menu_font_weight == '100'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 100;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '200'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 200;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '300'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 300;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '400'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 400;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '500'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 500;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '600'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 600;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '700'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 700;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '800'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 800;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_menu_font_weight == '900'){
$elearning_education_tp_theme_css .='.main-navigation a{';
    $elearning_education_tp_theme_css .='font-weight: 900;';
$elearning_education_tp_theme_css .='}';
}

/*------------- Blog Page------------------*/
$elearning_education_post_image_round = get_theme_mod('elearning_education_post_image_round', 0);
if($elearning_education_post_image_round != false){
	$elearning_education_tp_theme_css .='.blog .box-image img{';
		$elearning_education_tp_theme_css .='border-radius: '.esc_attr($elearning_education_post_image_round).'px;';
	$elearning_education_tp_theme_css .='}';
}

$elearning_education_post_image_width = get_theme_mod('elearning_education_post_image_width', '');
if($elearning_education_post_image_width != false){
	$elearning_education_tp_theme_css .='.blog .box-image img{';
		$elearning_education_tp_theme_css .='Width: '.esc_attr($elearning_education_post_image_width).'px;';
	$elearning_education_tp_theme_css .='}';
}

$elearning_education_post_image_length = get_theme_mod('elearning_education_post_image_length', '');
if($elearning_education_post_image_length != false){
	$elearning_education_tp_theme_css .='.blog .box-image img{';
		$elearning_education_tp_theme_css .='height: '.esc_attr($elearning_education_post_image_length).'px;';
	$elearning_education_tp_theme_css .='}';
}

// footer widget title font size
	$elearning_education_footer_widget_title_font_size = get_theme_mod('elearning_education_footer_widget_title_font_size', '');{
	$elearning_education_tp_theme_css .='#footer h3{';
		$elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_footer_widget_title_font_size).'px;';
	$elearning_education_tp_theme_css .='}';
	}

	// Copyright text font size
	$elearning_education_footer_copyright_font_size = get_theme_mod('elearning_education_footer_copyright_font_size', '');{
	$elearning_education_tp_theme_css .='#footer .site-info p{';
		$elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_footer_copyright_font_size).'px;';
	$elearning_education_tp_theme_css .='}';
	}

	// copyright padding
	$elearning_education_footer_copyright_top_bottom_padding = get_theme_mod('elearning_education_footer_copyright_top_bottom_padding', '');
	if ($elearning_education_footer_copyright_top_bottom_padding !== '') { 
	    $elearning_education_tp_theme_css .= '.site-info {';
	    $elearning_education_tp_theme_css .= 'padding-top: ' . esc_attr($elearning_education_footer_copyright_top_bottom_padding) . 'px;';
	    $elearning_education_tp_theme_css .= 'padding-bottom: ' . esc_attr($elearning_education_footer_copyright_top_bottom_padding) . 'px;';
	    $elearning_education_tp_theme_css .= '}';
	}

	// copyright position
	$elearning_education_copyright_text_position = get_theme_mod( 'elearning_education_copyright_text_position','Center');
	if($elearning_education_copyright_text_position == 'Center'){
	$elearning_education_tp_theme_css .='#footer .site-info p{';
	$elearning_education_tp_theme_css .='text-align:center;';
	$elearning_education_tp_theme_css .='}';
	}else if($elearning_education_copyright_text_position == 'Left'){
	$elearning_education_tp_theme_css .='#footer .site-info p{';
	$elearning_education_tp_theme_css .='text-align:left;';
	$elearning_education_tp_theme_css .='}';
	}else if($elearning_education_copyright_text_position == 'Right'){
	$elearning_education_tp_theme_css .='#footer .site-info p{';
	$elearning_education_tp_theme_css .='text-align:right;';
	$elearning_education_tp_theme_css .='}';
}

// Header Image title font size
$elearning_education_header_image_title_font_size = get_theme_mod('elearning_education_header_image_title_font_size', '32');{
$elearning_education_tp_theme_css .='.box-text h2{';
    $elearning_education_tp_theme_css .='font-size: '.esc_attr($elearning_education_header_image_title_font_size).'px;';
$elearning_education_tp_theme_css .='}';
}

/*--------------------------- banner image Opacity -------------------*/
    $elearning_education_theme_lay = get_theme_mod( 'elearning_education_header_banner_opacity_color','0.7');
        if($elearning_education_theme_lay == '0'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.1'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.1';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.2'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.2';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.3'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.3';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.4'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.4';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.5'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.5';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.6'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.6';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.7'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.7';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.8'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.8';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '0.9'){
            $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
                $elearning_education_tp_theme_css .='opacity:0.9';
            $elearning_education_tp_theme_css .='}';
        }else if($elearning_education_theme_lay == '1'){
            $elearning_education_tp_theme_css .='#slider img{';
                $elearning_education_tp_theme_css .='opacity:1';
            $elearning_education_tp_theme_css .='}';
        }

    $elearning_education_header_banner_image_overlay = get_theme_mod('elearning_education_header_banner_image_overlay', true);
    if($elearning_education_header_banner_image_overlay == false){
        $elearning_education_tp_theme_css .='.single-page-img, .featured-image{';
            $elearning_education_tp_theme_css .='opacity:1;';
        $elearning_education_tp_theme_css .='}';
    }

    $elearning_education_header_banner_image_ooverlay_color = get_theme_mod('elearning_education_header_banner_image_ooverlay_color', true);
    if($elearning_education_header_banner_image_ooverlay_color != false){
        $elearning_education_tp_theme_css .='.box-image-page{';
            $elearning_education_tp_theme_css .='background-color: '.esc_attr($elearning_education_header_banner_image_ooverlay_color).';';
        $elearning_education_tp_theme_css .='}';
    }

    /*------------------ Slider CSS -------------------*/
    $elearning_education_slider_opacity_setting = get_theme_mod('elearning_education_slider_opacity_setting', true);
    $elearning_education_image_opacity_color    = get_theme_mod('elearning_education_image_opacity_color', '');
    $elearning_education_slider_opacity         = get_theme_mod('elearning_education_slider_opacity', '1');

    if ($elearning_education_slider_opacity_setting) {
        // Apply opacity value to slider image
        if ($elearning_education_slider_opacity !== '') {
            $elearning_education_tp_theme_css .= '#slider img {';
            $elearning_education_tp_theme_css .= 'opacity: ' . esc_attr($elearning_education_slider_opacity) . ';';
            $elearning_education_tp_theme_css .= '}';
        }

        // Apply background color to slider if defined
        if ($elearning_education_image_opacity_color !== '') {
            $elearning_education_tp_theme_css .= '#slider {';
            $elearning_education_tp_theme_css .= 'background-color: ' . esc_attr($elearning_education_image_opacity_color) . ';';
            $elearning_education_tp_theme_css .= '}';
        }
    } else {
        // If setting is disabled, force full opacity
        $elearning_education_tp_theme_css .= '#slider img {';
        $elearning_education_tp_theme_css .= 'opacity: 1;';
        $elearning_education_tp_theme_css .= '}';
    }

    // Slider Height
    $elearning_education_slider_img_height      = get_theme_mod('elearning_education_slider_img_height');
    $elearning_education_slider_img_height_resp = get_theme_mod('elearning_education_slider_img_height_responsive');

    // Desktop height
    $elearning_education_tp_theme_css .= '@media screen and (min-width: 768px) {';
    $elearning_education_tp_theme_css .= '#slider img {';
    if ( $elearning_education_slider_img_height ) {
        $elearning_education_tp_theme_css .= 'height: ' . esc_attr( $elearning_education_slider_img_height ) . ';';
    }
    $elearning_education_tp_theme_css .= 'width: 100%; object-fit: cover;';
    $elearning_education_tp_theme_css .= '}';
    $elearning_education_tp_theme_css .= '}';

    // Mobile height
    $elearning_education_tp_theme_css .= '@media screen and (max-width: 767px) {';
    $elearning_education_tp_theme_css .= '#slider img {';
    if ( $elearning_education_slider_img_height_resp ) {
        $elearning_education_tp_theme_css .= 'height: ' . esc_attr( $elearning_education_slider_img_height_resp ) . ' !important;';
    }
    $elearning_education_tp_theme_css .= 'width: 100%; object-fit: cover;';
    $elearning_education_tp_theme_css .= '}';
    $elearning_education_tp_theme_css .= '}';

    //First Cap ( Blog Post )
    $elearning_education_show_first_caps = get_theme_mod('elearning_education_show_first_caps', 'false');
    if($elearning_education_show_first_caps == 'true' ){
    $elearning_education_tp_theme_css .='.blog .page-box p:nth-of-type(1)::first-letter{';
    $elearning_education_tp_theme_css .=' font-size: 55px; font-weight: 600;';
    $elearning_education_tp_theme_css .=' margin-right: 6px;';
    $elearning_education_tp_theme_css .=' line-height: 1;';
    $elearning_education_tp_theme_css .='}';
    }elseif($elearning_education_show_first_caps == 'false' ){
    $elearning_education_tp_theme_css .='.blog .page-box p:nth-of-type(1)::first-letter {';
    $elearning_education_tp_theme_css .='display: none;';
    $elearning_education_tp_theme_css .='}';
    }

    // Menu hover effect
    $elearning_education_menus_item = get_theme_mod( 'elearning_education_menus_item_style','None');
    if($elearning_education_menus_item == 'None'){
        $elearning_education_tp_theme_css .='.main-navigation a:hover{';
            $elearning_education_tp_theme_css .='';
        $elearning_education_tp_theme_css .='}';
    }else if($elearning_education_menus_item == 'Zoom In'){
        $elearning_education_tp_theme_css .='.main-navigation a:hover{';
            $elearning_education_tp_theme_css .='transition: all 0.3s ease-in-out !important; transform: scale(1.2) !important;';
        $elearning_education_tp_theme_css .='}';
    }

// footer widget letter case
$elearning_education_footer_widget_title_text_tranform = get_theme_mod( 'elearning_education_footer_widget_title_text_tranform','');
if($elearning_education_footer_widget_title_text_tranform == 'Uppercase'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='text-transform: uppercase;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_text_tranform == 'Lowercase'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='text-transform: lowercase;';
$elearning_education_tp_theme_css .='}';
}
else if($elearning_education_footer_widget_title_text_tranform == 'Capitalize'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='text-transform: capitalize;';
$elearning_education_tp_theme_css .='}';
}

//Footer Font Weight
$elearning_education_footer_widget_title_font_weight = get_theme_mod( 'elearning_education_footer_widget_title_font_weight','');
if($elearning_education_footer_widget_title_font_weight == '100'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 100;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '200'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 200;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '300'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 300;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '400'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 400;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '500'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 500;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '600'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 600;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '700'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 700;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '800'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 800;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_font_weight == '900'){
$elearning_education_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $elearning_education_tp_theme_css .='font-weight: 900;';
$elearning_education_tp_theme_css .='}';
}

// footer widget position
$elearning_education_footer_widget_title_position = get_theme_mod( 'elearning_education_footer_widget_title_position','');
if($elearning_education_footer_widget_title_position == 'Right'){
$elearning_education_tp_theme_css .='#footer aside.widget-area{';
$elearning_education_tp_theme_css .='text-align: right;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_position == 'Left'){
$elearning_education_tp_theme_css .='#footer aside.widget-area{';
$elearning_education_tp_theme_css .='text-align: left;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_widget_title_position == 'Center'){
$elearning_education_tp_theme_css .='#footer aside.widget-area{';
$elearning_education_tp_theme_css .='text-align: center;';
$elearning_education_tp_theme_css .='}';
}