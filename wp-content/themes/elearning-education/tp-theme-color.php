<?php

$elearning_education_tp_theme_css = '';

//theme color
$elearning_education_tp_color_option = get_theme_mod('elearning_education_tp_color_option');

// 1st color
$elearning_education_tp_color_option = get_theme_mod('elearning_education_tp_color_option', '#59d3b1');
if ($elearning_education_tp_color_option) {
	$elearning_education_tp_theme_css .= ':root {';
	$elearning_education_tp_theme_css .= '--color-primary1: ' . esc_attr($elearning_education_tp_color_option) . ';';
	$elearning_education_tp_theme_css .= '}';
}

// 2nd color
$elearning_education_tp_secoundary_color_option = get_theme_mod('elearning_education_tp_secoundary_color_option', '#192640');
if ($elearning_education_tp_secoundary_color_option) {
	$elearning_education_tp_theme_css .= ':root {';
	$elearning_education_tp_theme_css .= '--color-primary2: ' . esc_attr($elearning_education_tp_secoundary_color_option) . ';';
	$elearning_education_tp_theme_css .= '}';
}

//preloader
$elearning_education_tp_preloader_color1_option = get_theme_mod('elearning_education_tp_preloader_color1_option');
$elearning_education_tp_preloader_color2_option = get_theme_mod('elearning_education_tp_preloader_color2_option');
$elearning_education_tp_preloader_bg_color_option = get_theme_mod('elearning_education_tp_preloader_bg_color_option');

if($elearning_education_tp_preloader_color1_option != false){
$elearning_education_tp_theme_css .='.center1{';
	$elearning_education_tp_theme_css .='border-color: '.esc_attr($elearning_education_tp_preloader_color1_option).' !important;';
$elearning_education_tp_theme_css .='}';
}
if($elearning_education_tp_preloader_color1_option != false){
$elearning_education_tp_theme_css .='.center1 .ring::before{';
	$elearning_education_tp_theme_css .='background: '.esc_attr($elearning_education_tp_preloader_color1_option).' !important;';
$elearning_education_tp_theme_css .='}';
}
if($elearning_education_tp_preloader_color2_option != false){
$elearning_education_tp_theme_css .='.center2{';
	$elearning_education_tp_theme_css .='border-color: '.esc_attr($elearning_education_tp_preloader_color2_option).' !important;';
$elearning_education_tp_theme_css .='}';
}
if($elearning_education_tp_preloader_color2_option != false){
$elearning_education_tp_theme_css .='.center2 .ring::before{';
	$elearning_education_tp_theme_css .='background: '.esc_attr($elearning_education_tp_preloader_color2_option).' !important;';
$elearning_education_tp_theme_css .='}';
}
if($elearning_education_tp_preloader_bg_color_option != false){
$elearning_education_tp_theme_css .='.loader{';
	$elearning_education_tp_theme_css .='background: '.esc_attr($elearning_education_tp_preloader_bg_color_option).';';
$elearning_education_tp_theme_css .='}';
}

$elearning_education_tp_footer_bg_color_option = get_theme_mod('elearning_education_tp_footer_bg_color_option');
if($elearning_education_tp_footer_bg_color_option != false){
$elearning_education_tp_theme_css .='#footer{';
	$elearning_education_tp_theme_css .='background-color: '.esc_attr($elearning_education_tp_footer_bg_color_option).';';
$elearning_education_tp_theme_css .='}';
}

// logo tagline color
$elearning_education_site_tagline_color = get_theme_mod('elearning_education_site_tagline_color');

if($elearning_education_site_tagline_color != false){
$elearning_education_tp_theme_css .='.logo h1 a, .logo p, .logo p.site-title a{';
$elearning_education_tp_theme_css .='color: '.esc_attr($elearning_education_site_tagline_color).';';
$elearning_education_tp_theme_css .='}';
}

$elearning_education_logo_tagline_color = get_theme_mod('elearning_education_logo_tagline_color');
if($elearning_education_logo_tagline_color != false){
$elearning_education_tp_theme_css .='p.site-description{';
$elearning_education_tp_theme_css .='color: '.esc_attr($elearning_education_logo_tagline_color).';';
$elearning_education_tp_theme_css .='}';
}

// footer widget title color
$elearning_education_footer_widget_title_color = get_theme_mod('elearning_education_footer_widget_title_color');
if($elearning_education_footer_widget_title_color != false){
$elearning_education_tp_theme_css .='#footer h3{';
$elearning_education_tp_theme_css .='color: '.esc_attr($elearning_education_footer_widget_title_color).';';
$elearning_education_tp_theme_css .='}';
}

// copyright text color
$elearning_education_footer_copyright_text_color = get_theme_mod('elearning_education_footer_copyright_text_color');
if($elearning_education_footer_copyright_text_color != false){
$elearning_education_tp_theme_css .='#footer .site-info p, #footer .site-info a {';
$elearning_education_tp_theme_css .='color: '.esc_attr($elearning_education_footer_copyright_text_color).';';
$elearning_education_tp_theme_css .='}';
}

// header image title color
$elearning_education_header_image_title_text_color = get_theme_mod('elearning_education_header_image_title_text_color');
if($elearning_education_header_image_title_text_color != false){
$elearning_education_tp_theme_css .='.box-text h2{';
$elearning_education_tp_theme_css .='color: '.esc_attr($elearning_education_header_image_title_text_color).';';
$elearning_education_tp_theme_css .='}';
}

// menu color
$elearning_education_menu_color = get_theme_mod('elearning_education_menu_color');
if($elearning_education_menu_color != false){
$elearning_education_tp_theme_css .='.main-navigation a{';
$elearning_education_tp_theme_css .='color: '.esc_attr($elearning_education_menu_color).';';
$elearning_education_tp_theme_css .='}';
}

//Footer Font Weight
$elearning_education_footer_copyright_title_font_weight = get_theme_mod( 'elearning_education_footer_copyright_title_font_weight','');
if($elearning_education_footer_copyright_title_font_weight == '100'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 100;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '200'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 200;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '300'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 300;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '400'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 400;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '500'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 500;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '600'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 600;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '700'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 700;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '800'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 800;';
$elearning_education_tp_theme_css .='}';
}else if($elearning_education_footer_copyright_title_font_weight == '900'){
$elearning_education_tp_theme_css .='#footer .site-info p {';
    $elearning_education_tp_theme_css .='font-weight: 900;';
$elearning_education_tp_theme_css .='}';
}