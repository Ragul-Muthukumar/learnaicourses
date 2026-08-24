<?php

function bosa_ai_robotics_default_styles(){

	// Begin Style
	$css = '<style>';

	# Header Image / Slider
	#Header Image Height
	$header_image_height = get_theme_mod( 'header_image_height', 120 );
	$css .= '
		@media only screen and (min-width: 992px) {
			.site-header:not(.sticky-header) .header-image-wrap {
				height: '. esc_attr( $header_image_height ) .'px;
				width: 100%;
				position: relative;
			}
		}
	';

	$feature_posts_height = get_theme_mod( 'feature_posts_height', 350 );
	$css .= '
		.feature-posts-layout-one .feature-posts-image {
			height: '. esc_attr( $feature_posts_height ) .'px;
			overflow: hidden;
		}
	';

	#Bottom Footer image width
	if( get_theme_mod( 'footer_layout', 'footer_eight' ) == 'footer_one' || get_theme_mod( 'footer_layout', 'footer_eight' ) == 'footer_two' || get_theme_mod( 'footer_layout', 'footer_eight' ) == 'footer_eight' ){
		$bottom_footer_image_width = get_theme_mod( 'bottom_footer_image_width', 270 );
		$css .= '
			.bottom-footer-image-wrap > a {
				max-width: '. esc_attr( $bottom_footer_image_width ) .'px;
				overflow: hidden;
				display: inline-block;
			}
		';
	}

	# Footer Border
	if( get_theme_mod( 'disable_footer_border', false ) ){
		$css .= '
			.site-footer-eight .social-profile {
				border-bottom: none;
			}
		';
	}

	$site_primary_color = get_theme_mod( 'site_primary_color', '#EB5A3E' );
	$css .= '
		/* Primary Background */
		.header-cart a.cart-contents span.count {
			background-color: '. esc_attr( $site_primary_color ) .';
		}
	';

	$site_hover_color = get_theme_mod( 'site_hover_color', '#086abd' );
	$css .= '
		/* Primary Hover */
		.summary .yith-wcwl-add-to-wishlist a:hover i,
		.summary .yith-wcwl-add-to-wishlist a:focus i {
			color: '. esc_attr( $site_hover_color ) .';
			border-color: '. esc_attr( $site_hover_color ) .';
		}
	';

	# Theme Skins
	# Dark Skin
	if( get_theme_mod( 'skin_select', 'default' ) == 'dark' ){
		$css .= '
			.header-one .bottom-header .overlay, 
			.header-two .bottom-header .overlay, 
			.header-three .bottom-header, 
			.header-three .mobile-menu-container {
				background-color: transparent;
			}
		    .transparent-header .header-two.site-header .top-header {
		    	background-color: transparent;
		    }
		    .transparent-header .header-two.site-header .bottom-header .overlay {
		    	background-color: transparent;
		    }
		    .mid-header .overlay {
		    	background-color: transparent;
		    }
		';
	}

	// Featured Pages
	$featured_pages_title_color = get_theme_mod( 'featured_pages_title_color', '#1a1a1a' );
	$featured_pages_hover_color = get_theme_mod( 'featured_pages_hover_color', '#086abd' );
	$css .= '
		.feature-pages-content .feature-pages-title {
			color: '. esc_attr( $featured_pages_title_color ) .';
		}
		.feature-pages-content .feature-pages-title a:hover,
		.feature-pages-content .feature-pages-title a:focus {
			color: '. esc_attr( $featured_pages_hover_color ) .';
		}
	';

	$featured_pages_overlay_opacity = get_theme_mod( 'featured_pages_overlay_opacity', 2 );
	$css .= '
		.feature-pages-layout-one .feature-pages-content-wrap .feature-pages-image:before {
		 	background-color: rgba(0, 0, 0, 0.'. esc_attr( $featured_pages_overlay_opacity ) .');
		}
	';

	$featured_pages_height = get_theme_mod( 'featured_pages_height', 250 );
	$css .= '
		.feature-pages-layout-one .feature-pages-image {
			height: '. esc_attr( $featured_pages_height ) .'px;
			overflow: hidden;
		}
	';

	# Featured Page Image Sizes
	#Cover Size
	if( get_theme_mod( 'featured_pages_image_size', 'cover' ) == 'cover' ){
		$css .= '
			.feature-pages-content-wrap .feature-pages-image {
				background-position: center center;
				background-repeat: no-repeat;
				background-size: cover;
			}
		';
	}
	#Repeat Size
	elseif( get_theme_mod( 'featured_pages_image_size', 'cover' ) == 'pattern' ){
		$css .= '
			.feature-pages-content-wrap .feature-pages-image {
				background-position: center center;
				background-repeat: repeat;
				background-size: inherit;
			}
		';
	}
	#Fit Size
	elseif( get_theme_mod( 'featured_pages_image_size', 'cover' ) == 'norepeat' ){
		$css .= '
			.feature-pages-content-wrap .feature-pages-image {
				background-position: center center;
				background-repeat: no-repeat;
				background-size: inherit;
			}
		';
	}

	$featured_pages_radius = get_theme_mod( 'featured_pages_radius', 0 );
	$css .= '
		.feature-pages-content-wrap .feature-pages-image {
			border-radius: '. esc_attr( $featured_pages_radius ) .'px;
			overflow: hidden;
		}
	';

	#Featured Pages Title Alignment
	if( get_theme_mod( 'featured_pages_title_alignment', 'align-center' ) == 'align-bottom' ){
		$css .= '
	    	.feature-pages-layout-one .feature-pages-image {
				-webkit-align-items: flex-end;
	    		-moz-align-items: flex-end;
	    		-ms-align-items: flex-end;
	    		-ms-flex-align: flex-end;
	    		align-items: flex-end;
	    	}
	    	.feature-pages-layout-one .feature-pages-content {
	    		margin-bottom: 20px;
	    	}
		';
	}elseif( get_theme_mod( 'featured_pages_title_alignment', 'align-center' ) == 'align-top' ) {
		$css .= '
			.feature-pages-layout-one .feature-pages-image {
				-webkit-align-items: flex-start;
	    		-moz-align-items: flex-start;
	    		-ms-align-items: flex-start;
	    		-ms-flex-align: flex-start;
	    		align-items: flex-start;
	    	}
	    	.feature-pages-layout-one .feature-pages-content {
	    		margin-top: 20px;
	    	}
		';
	}elseif( get_theme_mod( 'featured_pages_title_alignment', 'align-center' ) == 'align-center' ) {
		$css .= '
			.feature-pages-layout-one .feature-pages-image {
				-webkit-align-items: center;
	    		-moz-align-items: center;
	    		-ms-align-items: center;
	    		-ms-flex-align: center;
	    		align-items: center;
	    	}
		';
	}

	# Responsive Featured Posts / Pages
	if( get_theme_mod( 'disable_mobile_feature_posts', false ) ){
		$css .= '
			@media screen and (max-width: 767px){
				.section-feature-posts-area,
				.section-feature-pages-area {
	    			display: none;
				}
			}
		';
	}

	# Grid Thumbnail post border radius
	$posts_border_radius_thumbnail = get_theme_mod( 'posts_border_radius_thumbnail', 0 );
	if( $posts_border_radius_thumbnail != 0 ){
		$css .= '
			#primary .grid-thumbnail article:not(.sticky).list-post .featured-image a {
	    		border-radius: '. esc_attr( $posts_border_radius_thumbnail ) .'px;
	    	}
	    	.grid-thumbnail article.sticky.list-post {
	    		border-radius: '. esc_attr( $posts_border_radius_thumbnail ) .'px;
	    	}
		';
	}

	#Featured Posts Two color
	$feature_posts_two_title_color 		= get_theme_mod( 'feature_posts_two_title_color', '#FFFFFF' );
	$feature_posts_two_category_bgcolor = get_theme_mod( 'feature_posts_two_category_bgcolor', '#EB5A3E' );
	$feature_posts_two_category_color 	= get_theme_mod( 'feature_posts_two_category_color', '#FFFFFF' );
	$feature_posts_two_meta_color 		= get_theme_mod( 'feature_posts_two_meta_color', '#FFFFFF' );
	$feature_posts_two_meta_icon_color 	= get_theme_mod( 'feature_posts_two_meta_icon_color', '#FFFFFF' );
	$feature_posts_two_hover_color 		= get_theme_mod( 'feature_posts_two_hover_color', '#a8d8ff' );
	$css .= '
		.section-feature-posts-two-area .feature-posts-content .feature-posts-title {
			color: '. esc_attr( $feature_posts_two_title_color ) .';
		}
		.section-feature-posts-two-area .feature-posts-content .feature-posts-title a:hover,
		.section-feature-posts-two-area .feature-posts-content .feature-posts-title a:focus {
			color: '. esc_attr( $feature_posts_two_hover_color ) .';
		}
		.section-feature-posts-two-area .feature-posts-content .cat-links a {
			background-color: '. esc_attr( $feature_posts_two_category_bgcolor ) .';
			color: '. esc_attr( $feature_posts_two_category_color) .';
		}
		.section-feature-posts-two-area .feature-posts-content .cat-links a:hover,
		.section-feature-posts-two-area .feature-posts-content .cat-links a:focus {
			background-color: '. esc_attr( $feature_posts_two_hover_color ) .';
			color: #FFFFFF;
		}
		.section-feature-posts-two-area .feature-posts-content .entry-meta a {
			color: '. esc_attr( $feature_posts_two_meta_color ) .';
		}
		.section-feature-posts-two-area .feature-posts-content .entry-meta a:before {
			color: '. esc_attr( $feature_posts_two_meta_icon_color ) .';
		}
		.section-feature-posts-two-area .feature-posts-content .entry-meta a:hover,
		.section-feature-posts-two-area .feature-posts-content .entry-meta a:focus,
		.section-feature-posts-two-area .feature-posts-content .entry-meta a:hover:before,
		.section-feature-posts-two-area .feature-posts-content .entry-meta a:focus:before {
			color: '. esc_attr( $feature_posts_two_hover_color ) .';
		}
	';

	# Featured Posts Overlay Opacity
	$feature_posts_two_overlay_opacity = get_theme_mod( 'feature_posts_two_overlay_opacity', 4 );
	$css .= '
		.section-feature-posts-two-area .feature-posts-image:before {
		 	background-color: rgba(0, 0, 0, 0.'. esc_attr( $feature_posts_two_overlay_opacity ) .');
		}
	';

	# Featured Posts Two Image Sizes
	#Cover Size
	if( get_theme_mod( 'feature_posts_two_image_size', 'cover' ) == 'cover' ){
		$css .= '
			.section-feature-posts-two-area .feature-posts-image {
				background-position: center center;
				background-repeat: no-repeat;
				background-size: cover;
			}
		';
	}
	#Repeat Size
	elseif( get_theme_mod( 'feature_posts_two_image_size', 'cover' ) == 'pattern' ){
		$css .= '
			.section-feature-posts-two-area .feature-posts-image {
				background-position: center center;
				background-repeat: repeat;
				background-size: inherit;
			}
		';
	}
	#Fit Size
	elseif( get_theme_mod( 'feature_posts_two_image_size', 'cover' ) == 'norepeat' ){
		$css .= '
			.section-feature-posts-two-area .feature-posts-image {
				background-position: center center;
				background-repeat: no-repeat;
				background-size: inherit;
			}
		';
	}

	# Featured Posts Two Border Radius
	$feature_posts_two_radius = get_theme_mod( 'feature_posts_two_radius', 0 );
	$css .= '
		.section-feature-posts-two-area .feature-posts-image {
    		border-radius: '. esc_attr( $feature_posts_two_radius ) .'px;
    		overflow: hidden;
    	}
	';

	#Featured Posts Two Content Alignment
	if( get_theme_mod( 'feature_posts_two_vertical_title_alignment', 'align-bottom' ) == 'align-bottom' ){
		$css .= '
	    	.section-feature-posts-two-area .feature-posts-image {
				-webkit-align-items: flex-end;
	    		-moz-align-items: flex-end;
	    		-ms-align-items: flex-end;
	    		-ms-flex-align: flex-end;
	    		align-items: flex-end;
	    	}
	    	.section-feature-posts-two-area .feature-posts-content {
	    		margin-bottom: 20px;
	    	}
		';
	}elseif( get_theme_mod( 'feature_posts_two_vertical_title_alignment', 'align-bottom' ) == 'align-top' ) {
		$css .= '
			.section-feature-posts-two-area .feature-posts-image {
				-webkit-align-items: flex-start;
	    		-moz-align-items: flex-start;
	    		-ms-align-items: flex-start;
	    		-ms-flex-align: flex-start;
	    		align-items: flex-start;
	    	}
	    	.section-feature-posts-two-area .feature-posts-content {
	    		margin-top: 20px;
	    	}
		';
	}elseif( get_theme_mod( 'feature_posts_two_vertical_title_alignment', 'align-bottom' ) == 'align-center' ) {
		$css .= '
			.section-feature-posts-two-area .feature-posts-image {
				-webkit-align-items: center;
	    		-moz-align-items: center;
	    		-ms-align-items: center;
	    		-ms-flex-align: center;
	    		align-items: center;
	    	}
		';
	}

	# Responsive Featured Posts Two
	if( get_theme_mod( 'disable_mobile_feature_posts_two', false ) ){
		$css .= '
			@media screen and (max-width: 767px){
				.section-feature-posts-two-area {
	    			display: none;
				}
			}
		';
	}

	# Responsive Blog Advert
	if( get_theme_mod( 'disable_mobile_blog_advertisement_banner', false ) ){
		$css .= '
			@media screen and (max-width: 767px){
				.section-advert {
	    			display: none;
				}
			}
		';
	}

	# Responsive Services
	if( get_theme_mod( 'disable_mobile_services', false ) ){
		$css .= '
			@media screen and (max-width: 767px){
				.section-services-area {
	    			display: none;
				}
			}
		';
	}

	# Responsive Featured Categories
	if( get_theme_mod('disable_mobile_featured_categories', false ) ){
		$css .='
			@media screen and (max-width: 767px){
				.section-feature-category-area {
	    			display: none;
				}
			}
		';
	}

	# Responsive Comments
	if( get_theme_mod( 'disable_mobile_comments', false ) ){
		$css .= '
			@media screen and (max-width: 767px){
				.section-comments-area {
	    			display: none;
				}
			}
		';
	}
	
	# Responsive Coupons
	if( get_theme_mod( 'disable_mobile_coupons', false ) ){
		$css .= '
			@media screen and (max-width: 767px){
				.section-coupons-area {
	    			display: none;
				}
			}
		';
	}

	# Transparent Header Button
	$transparent_header_btn_hover_color 	= '';
	if( !get_theme_mod( 'disable_header_button', false ) ){
		if( get_theme_mod( 'header_layout', 'header_two' ) == 'header_two' ){
			$transparent_header_btn_defaults = array(
				array(
					'transparent_header_btn_type' 				=> 'button-outline',
					'transparent_header_home_btn_bg_color'		=> '#EB5A3E',
					'transparent_header_home_btn_border_color'	=> '#ffffff',
					'transparent_header_home_btn_text_color'	=> '#ffffff',
					'transparent_header_btn_bg_color'			=> '#EB5A3E',
					'transparent_header_btn_border_color'		=> '#1a1a1a',
					'transparent_header_btn_text_color'			=> '#1a1a1a',
					'transparent_header_btn_hover_color'		=> '#086abd',
					'transparent_header_btn_text' 				=> '',
					'transparent_header_btn_link' 				=> '',
					'transparent_header_btn_target'				=> true,
					'transparent_header_btn_radius'				=> 0,
				),		
			);
			$transparent_header_buttons = get_theme_mod( 'transparent_header_button_repeater', $transparent_header_btn_defaults );
			if( !empty( $transparent_header_buttons ) && is_array( $transparent_header_buttons ) ){
				$i = 1;
		    	foreach( $transparent_header_buttons as $value ){
		    		$transparent_header_btn_bg_color 		= $value['transparent_header_btn_bg_color'];
		    		$transparent_header_btn_border_color 	= $value['transparent_header_btn_border_color'];
		    		$transparent_header_btn_text_color 		= $value['transparent_header_btn_text_color'];
		    		$transparent_header_btn_hover_color 	= $value['transparent_header_btn_hover_color'];
		    		$transparent_header_btn_radius 			= $value['transparent_header_btn_radius'];
		    		if( $value['transparent_header_btn_type'] == 'button-primary' ){
				    		$css .= '
								.header-two.sticky-header .header-btn-'. $i .'.button-primary {
									background-color: '. esc_attr( $transparent_header_btn_bg_color ) .';
									color: '. esc_attr( $transparent_header_btn_text_color ) .';
								}
							';
					}elseif( $value['transparent_header_btn_type'] == 'button-outline' ){
						$css .= '
							.header-two.sticky-header .header-btn-'. $i .'.button-outline {
								border-color: '. esc_attr( $transparent_header_btn_border_color ) .';
								color: '. esc_attr( $transparent_header_btn_text_color ) .';
							}
						';
					}elseif( $value['transparent_header_btn_type'] == 'button-text' ){
						$css .= '
							.header-two.sticky-header .header-btn-'. $i .'.button-text {
								color: '. esc_attr( $transparent_header_btn_text_color ) .';
								padding: 0;
							}
						';
					}
					if( ( !get_theme_mod( 'disable_transparent_header_page', true ) && is_page() ) || ( !get_theme_mod( 'disable_transparent_header_post', true ) && is_single() ) || is_front_page() ){
						$transparent_header_btn_bg_color 		= $value['transparent_header_home_btn_bg_color'];
		    			$transparent_header_btn_border_color 	= $value['transparent_header_home_btn_border_color'];
		    			$transparent_header_btn_text_color 		= $value['transparent_header_home_btn_text_color'];
		    		}
		    		if( $value['transparent_header_btn_type'] == 'button-primary' ){
			    		$css .= '
							.site-header .header-btn-'. $i .'.button-primary {
								background-color: '. esc_attr( $transparent_header_btn_bg_color ) .';
								color: '. esc_attr( $transparent_header_btn_text_color ) .';
							}

							.site-header .header-btn-'. $i .'.button-primary:hover,
							.site-header .header-btn-'. $i .'.button-primary:focus,
							.site-header .header-btn-'. $i .'.button-primary:active,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-primary:hover,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-primary:focus,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-primary:active {
								background-color: '. esc_attr( $transparent_header_btn_hover_color ) .';
								color: #ffffff;
							}

							.site-header .header-btn-'. $i .'.button-primary {
								border-radius: '. esc_attr( $transparent_header_btn_radius ) .'px;
							}
						';
					}elseif( $value['transparent_header_btn_type'] == 'button-outline' ){
						$css .= '

							.site-header .header-btn-'. $i .'.button-outline {
								border-color: '. esc_attr( $transparent_header_btn_border_color ) .';
								color: '. esc_attr( $transparent_header_btn_text_color ) .';
							}

							.site-header .header-btn-'. $i .'.button-outline:hover,
							.site-header .header-btn-'. $i .'.button-outline:focus,
							.site-header .header-btn-'. $i .'.button-outline:active,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-outline:hover,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-outline:focus,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-outline:active {
								background-color: '. esc_attr( $transparent_header_btn_hover_color ) .';
								border-color: '. esc_attr( $transparent_header_btn_hover_color ) .';
								color: #ffffff;
							}

							.site-header .header-btn-'. $i .'.button-outline {
								border-radius: '. esc_attr( $transparent_header_btn_radius ) .'px;
							}
						';
					}elseif( $value['transparent_header_btn_type'] == 'button-text' ){
						$css .= '
							.site-header .header-btn-'. $i .'.button-text {
								color: '. esc_attr( $transparent_header_btn_text_color ) .';
								padding: 0;
							}
							.site-header .header-btn-'. $i .'.button-text:hover,
							.site-header .header-btn-'. $i .'.button-text:focus,
							.site-header .header-btn-'. $i .'.button-text:active,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-text:hover,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-text:focus,
							.transparent-header .header-two.sticky-header .header-btn-'. $i .'.button-text:active {
								color: '. esc_attr( $transparent_header_btn_hover_color ) .';
							}
						';
					}
					$i++;
		    	}
		    }
		}
	}

	if( get_theme_mod( 'header_layout', 'header_two' ) == 'header_two' && ( is_front_page() || ( !get_theme_mod( 'disable_transparent_header_post', true ) && is_single() ) || ( !get_theme_mod( 'disable_transparent_header_page', true ) && is_page() ) ) && get_theme_mod( 'header_separate_logo', '' ) ){
		$css .= '
			.site-header .site-branding img {
				display: block;
			}
		';
	}

	if( get_theme_mod( 'header_layout', 'header_two' ) == 'header_two' ){
		$transparent_header_top_background_color = get_theme_mod( 'transparent_header_top_background_color', '' );
		$transparent_header_bottom_background_color = get_theme_mod( 'transparent_header_bottom_background_color', '' );
		$transparent_header_top_header_color = get_theme_mod( 'transparent_header_top_header_color', '#ffffff' );
		$top_hover_color_transparent_header = get_theme_mod( 'top_hover_color_transparent_header', '#086abd' );
		$site_title_color_transparent_header = get_theme_mod( 'site_title_color_transparent_header', '#ffffff' );
		$site_tagline_color_transparent_header = get_theme_mod( 'site_tagline_color_transparent_header', '#e6e6e6' );
		$content_color_transparent_header = get_theme_mod( 'content_color_transparent_header', '#ffffff' );
		$content_hover_color_transparent_header = get_theme_mod( 'content_hover_color_transparent_header', '#086abd' );

		if( is_front_page() && !is_home() && !defined('ELEMENTOR_VERSION') ){
			#Header two separate colors
			$site_title_color_transparent_header = get_theme_mod( 'site_title_color', '#030303' );
			$site_tagline_color_transparent_header = get_theme_mod( 'site_tagline_color', '#767676' );

			$transparent_header_top_background_color = get_theme_mod( 'top_header_background_color', '' );
			$transparent_header_top_header_color = get_theme_mod( 'top_header_text_color', '#333333' );
			$top_hover_color_transparent_header = get_theme_mod( 'top_header_text_link_hover_color', '#086abd' );

			$transparent_header_bottom_background_color = get_theme_mod( 'bottom_header_background_color', '' );
			$content_color_transparent_header = get_theme_mod( 'bottom_header_text_color', '#333333' );
			$content_hover_color_transparent_header = get_theme_mod( 'bottom_header_text_link_hover_color', '#086abd' );
			
			$css .= '
				.home.page .header-two .overlay-header {
		            position: static;
		          }
			';
		}

		$css .= '
			/* Transparent Top Header */
			.transparent-header .header-two.site-header .top-header {
				background-color: '. esc_attr( $transparent_header_top_background_color ) .';
			}
			
			/* Site Title */
			.transparent-header .site-header.header-two:not(.sticky-header) .site-branding .site-title {
				color: '. esc_attr( $site_title_color_transparent_header ) .';
			}
			/* Tagline */
			.transparent-header .site-header.header-two:not(.sticky-header) .site-branding .site-description {
				color: '. esc_attr( $site_tagline_color_transparent_header ) .';
			}
			/* Top Header Color */
			.transparent-header .header-two.site-header .header-contact ul a,
			.transparent-header .header-two.site-header .header-contact ul li,
			.transparent-header .header-two.site-header .social-profile ul li a, 
			.transparent-header .header-two.site-header .header-search-wrap .search-icon {
				color: '. esc_attr( $transparent_header_top_header_color ) .';
			}
			.transparent-header .header-two.site-header .header-contact ul a:hover,
			.transparent-header .header-two.site-header .header-contact ul a:focus,
			.transparent-header .header-two.site-header .header-contact ul a:active,
			.transparent-header .header-two.site-header .social-profile ul li a:hover, 
			.transparent-header .header-two.site-header .social-profile ul li a:focus, 
			.transparent-header .header-two.site-header .social-profile ul li a:active, 
			.transparent-header .header-two.site-header .header-search-wrap .search-icon:hover,
			.transparent-header .header-two.site-header .header-search-wrap .search-icon:focus,
			.transparent-header .header-two.site-header .header-search-wrap .search-icon:active {
				color: '. esc_attr( $top_hover_color_transparent_header ) .';
			}
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon .icon-bar,
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon .icon-bar:before, 
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon .icon-bar:after {
				background-color: '. esc_attr( $transparent_header_top_header_color ) .';
			}
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:hover .icon-bar,
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:focus .icon-bar,
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:focus .icon-bar,
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:hover .icon-bar:before, 
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:focus .icon-bar:before, 
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:active .icon-bar:before, 
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:hover .icon-bar:after,
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:focus .icon-bar:after,
			.transparent-header .header-two:not(.sticky-header) .alt-menu-icon a:active .icon-bar:after {
				background-color: '. esc_attr( $top_hover_color_transparent_header ) .';
			}

			/* Transparent bottom Header */
			.transparent-header .header-two.site-header .bottom-header .overlay {
				background-color: '. esc_attr( $transparent_header_bottom_background_color ) .';
			}
			/* Header Menu */
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu > li > a {
				color: '. esc_attr( $content_color_transparent_header ) .';
			}

			@media only screen and (max-width: 991px) {
				.transparent-header .header-two .alt-menu-icon .iconbar-label {
				    color: '. esc_attr( $transparent_header_top_header_color ) .';
				}
				.transparent-header .header-two .alt-menu-icon a:hover .iconbar-label,
				.transparent-header .header-two .alt-menu-icon a:focus .iconbar-label,
				.transparent-header .header-two .alt-menu-icon a:active .iconbar-label {
					color: '. esc_attr( $top_hover_color_transparent_header ) .';
				}
				.transparent-header .header-two:not(.sticky-header) .mobile-menu-container .slicknav_menu .slicknav_menutxt {
					color: '. esc_attr( $content_color_transparent_header) .';
				}
				.transparent-header .header-two:not(.sticky-header) .mobile-menu-container .slicknav_menu .slicknav_btn .slicknav_icon span, 
				.transparent-header .header-two:not(.sticky-header) .mobile-menu-container .slicknav_menu .slicknav_btn .slicknav_icon span:first-child:before, 
				.transparent-header .header-two:not(.sticky-header) .mobile-menu-container .slicknav_menu .slicknav_btn .slicknav_icon span:first-child:after {
					background-color: '. esc_attr( $content_color_transparent_header ) .';
				}
			}

			/* Transparent Header bottom Hover Color*/
			.transparent-header .site-header.header-two:not(.sticky-header) .site-branding .site-title a:hover,
			.transparent-header .site-header.header-two:not(.sticky-header) .site-branding .site-title a:focus,
			.transparent-header .site-header.header-two:not(.sticky-header) .site-branding .site-title a:active,  
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li > a:hover,
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li > a:focus,
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li > a:active,
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li:hover > a, 
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li:focus > a, 
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li:active > a,
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:hover .slicknav_menutxt,
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:focus .slicknav_menutxt,
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:active .slicknav_menutxt,
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li.current-menu-item > a,
			.transparent-header .header-two:not(.sticky-header) .main-navigation ul.menu li.current_page_item > a {
				color: '. esc_attr( $content_hover_color_transparent_header ) .';
			}
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:hover .slicknav_icon span, 
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:focus .slicknav_icon span, 
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:active .slicknav_icon span, 
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:hover .slicknav_icon span:first-child:before, 
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:focus .slicknav_icon span:first-child:before, 
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:active .slicknav_icon span:first-child:before, 
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:hover .slicknav_icon span:first-child:after,
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:focus .slicknav_icon span:first-child:after,
			.transparent-header .header-two .mobile-menu-container .slicknav_menu .slicknav_btn:active .slicknav_icon span:first-child:after {
				background-color: '. esc_attr( $content_hover_color_transparent_header ) .';
			}
		';
	}
	
	// End Style
	$css .= '</style>';

	// return generated & compressed CSS
	echo str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css); 
}
add_action( 'wp_head', 'bosa_ai_robotics_default_styles', 99 );