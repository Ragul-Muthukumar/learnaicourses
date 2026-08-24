<?php
$ai_engine_custom_css = "";
$ai_engine_primary_color = get_theme_mod('ai_engine_primary_color');
$ai_engine_secondary_color = get_theme_mod('ai_engine_secondary_color');

/*------------------ Primary Global Color -----------*/

if ($ai_engine_primary_color) {
  $ai_engine_custom_css .= ':root {';
  $ai_engine_custom_css .= '--primary-color: ' . esc_attr($ai_engine_primary_color) . ' !important;';
  $ai_engine_custom_css .= '} ';
}

/*------------------ Secondary Global Color -----------*/

if ($ai_engine_secondary_color) {
  $ai_engine_custom_css .= ':root {';
  $ai_engine_custom_css .= '--secondary-color: ' . esc_attr($ai_engine_secondary_color) . ' !important;';
  $ai_engine_custom_css .= '} ';
}

/*-------------------- Scroll Top Alignment-------------------*/

$ai_engine_scroll_top_alignment = get_theme_mod( 'ai_engine_scroll_top_alignment','right-align');

if($ai_engine_scroll_top_alignment == 'right-align'){
$ai_engine_custom_css .='#button{';
	$ai_engine_custom_css .='right: 3%;';
$ai_engine_custom_css .='}';
}else if($ai_engine_scroll_top_alignment == 'center-align'){
$ai_engine_custom_css .='#button{';
	$ai_engine_custom_css .='right:0; left:0; margin: 0 auto;';
$ai_engine_custom_css .='}';
}else if($ai_engine_scroll_top_alignment == 'left-align'){
$ai_engine_custom_css .='#button{';
	$ai_engine_custom_css .='left: 3%;';
$ai_engine_custom_css .='}';
}

/*-------------------- Archive Page Pagination Alignment-------------------*/

$ai_engine_archive_pagination_alignment = get_theme_mod( 'ai_engine_archive_pagination_alignment','left-align');

if($ai_engine_archive_pagination_alignment == 'right-align'){
$ai_engine_custom_css .='.pagination{';
	$ai_engine_custom_css .='justify-content: end;';
$ai_engine_custom_css .='}';
}else if($ai_engine_archive_pagination_alignment == 'center-align'){
$ai_engine_custom_css .='.pagination{';
	$ai_engine_custom_css .='justify-content: center;';
$ai_engine_custom_css .='}';
}else if($ai_engine_archive_pagination_alignment == 'left-align'){
$ai_engine_custom_css .='.pagination{';
	$ai_engine_custom_css .='justify-content: start;';
$ai_engine_custom_css .='}';
}

/*-------------------- Scroll Top Responsive-------------------*/

$ai_engine_resp_scroll_top = get_theme_mod( 'ai_engine_resp_scroll_top',true);
if($ai_engine_resp_scroll_top == true && get_theme_mod( 'ai_engine_scroll_to_top',true) != true){
	$ai_engine_custom_css .='#button.show{';
		$ai_engine_custom_css .='visibility:hidden !important;';
	$ai_engine_custom_css .='} ';
}
if($ai_engine_resp_scroll_top == true){
	$ai_engine_custom_css .='@media screen and (max-width:575px) {';
	$ai_engine_custom_css .='#button.show{';
		$ai_engine_custom_css .='visibility:visible !important;';
	$ai_engine_custom_css .='} }';
}else if($ai_engine_resp_scroll_top == false){
	$ai_engine_custom_css .='@media screen and (max-width:575px){';
	$ai_engine_custom_css .='#button.show{';
		$ai_engine_custom_css .='visibility:hidden !important;';
	$ai_engine_custom_css .='} }';
}

/*-------------------- Preloader Responsive-------------------*/

$ai_engine_resp_loader = get_theme_mod('ai_engine_resp_loader',false);
if($ai_engine_resp_loader == true && get_theme_mod('ai_engine_header_preloader',false) == false){
	$ai_engine_custom_css .='@media screen and (min-width:575px){
		.preloader{';
		$ai_engine_custom_css .='display:none !important;';
	$ai_engine_custom_css .='} }';
}

if($ai_engine_resp_loader == false){
	$ai_engine_custom_css .='@media screen and (max-width:575px){
		.preloader{';
		$ai_engine_custom_css .='display:none !important;';
	$ai_engine_custom_css .='} }';
}

// Scroll to top button shape 

$ai_engine_scroll_border_radius = get_theme_mod( 'ai_engine_scroll_to_top_radius','curved-box');
if($ai_engine_scroll_border_radius == 'box'){
	$ai_engine_custom_css .='#button{';
		$ai_engine_custom_css .='border-radius: 0px;';
	$ai_engine_custom_css .='}';
}else if($ai_engine_scroll_border_radius == 'curved-box'){
	$ai_engine_custom_css .='#button{';
		$ai_engine_custom_css .='border-radius: 4px;';
	$ai_engine_custom_css .='}';
}
else if($ai_engine_scroll_border_radius == 'circle'){
	$ai_engine_custom_css .='#button{';
		$ai_engine_custom_css .='border-radius: 50%;';
	$ai_engine_custom_css .='}';
}

// Footer Background Image Attachment 

$ai_engine_footer_attachment = get_theme_mod( 'ai_engine_background_attachment','scroll');
if($ai_engine_footer_attachment == 'fixed'){
	$ai_engine_custom_css .='.site-footer{';
		$ai_engine_custom_css .='background-attachment: fixed;';
	$ai_engine_custom_css .='}';
}elseif ($ai_engine_footer_attachment == 'scroll'){
	$ai_engine_custom_css .='.site-footer{';
		$ai_engine_custom_css .='background-attachment: scroll;';
	$ai_engine_custom_css .='}';
}

// Menu Hover Style	

$ai_engine_menus_item = get_theme_mod( 'ai_engine_menus_style','None');
if($ai_engine_menus_item == 'None'){
	$ai_engine_custom_css .='#site-navigation .menu ul li a:hover, .main-navigation .menu li a:hover{';
		$ai_engine_custom_css .='';
	$ai_engine_custom_css .='}';
}else if($ai_engine_menus_item == 'Zoom In'){
	$ai_engine_custom_css .='#site-navigation .menu ul li a:hover, .main-navigation .menu li a:hover{';
		$ai_engine_custom_css .='transition: all 0.3s ease-in-out !important; transform: scale(1.2) !important;';
	$ai_engine_custom_css .='}';

	$ai_engine_custom_css .= '.main-navigation ul ul li a:hover {';
	$ai_engine_custom_css .= 'margin-left: 20px;';
	$ai_engine_custom_css .= '}';
}

// Banner Background Image
$ai_engine_banner_bg = get_theme_mod('ai_engine_banner_background_image');
if ( empty( $ai_engine_banner_bg ) ) {
	$ai_engine_banner_bg = get_template_directory_uri() . '/images/default.png';
}
$ai_engine_custom_css .= "
.banner::before {
	background-image: url('{$ai_engine_banner_bg}');
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
    position: absolute;
    width: 100%;
    min-height: 600px;
}";