<?php
/**
 * AI Engine Theme Customizer.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ai_engine
 */

if( ! function_exists( 'ai_engine_customize_register' ) ):  
/**
 * Add postMessage support for site title and description for the Theme Customizer.F
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function ai_engine_customize_register( $wp_customize ) {
    require get_parent_theme_file_path('/inc/controls/changeable-icon.php');

    require get_parent_theme_file_path('/inc/controls/sortable-control.php');
    
    //Register the sortable control type.
    $wp_customize->register_control_type( 'AI_Engine_Control_Sortable' ); 
    

    if ( version_compare( get_bloginfo('version'),'4.9', '>=') ) {
        $wp_customize->get_section( 'static_front_page' )->title = __( 'Static Front Page', 'ai-engine' );
    }
	
    /* Option list of all post */	
    $ai_engine_options_posts = array();
    $ai_engine_options_posts_obj = get_posts('posts_per_page=-1');
    $ai_engine_options_posts[''] = esc_html__( 'Choose Post', 'ai-engine' );
    foreach ( $ai_engine_options_posts_obj as $ai_engine_posts ) {
    	$ai_engine_options_posts[$ai_engine_posts->ID] = $ai_engine_posts->post_title;
    }
    
    /* Option list of all categories */
    $ai_engine_args = array(
	   'type'                     => 'post',
	   'orderby'                  => 'name',
	   'order'                    => 'ASC',
	   'hide_empty'               => 1,
	   'hierarchical'             => 1,
	   'taxonomy'                 => 'category'
    ); 
    $ai_engine_option_categories = array();
    $ai_engine_category_lists = get_categories( $ai_engine_args );
    $ai_engine_option_categories[''] = esc_html__( 'Choose Category', 'ai-engine' );
    foreach( $ai_engine_category_lists as $ai_engine_category ){
        $ai_engine_option_categories[$ai_engine_category->term_id] = $ai_engine_category->name;
    }
    
    /** Default Settings */    
    $wp_customize->add_panel( 
        'wp_default_panel',
         array(
            'priority' => 10,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => esc_html__( 'Default Settings', 'ai-engine' ),
            'description' => esc_html__( 'Default section provided by wordpress customizer.', 'ai-engine' ),
        ) 
    );
    
    $wp_customize->get_section( 'title_tagline' )->panel                  = 'wp_default_panel';
    $wp_customize->get_section( 'colors' )->panel                         = 'wp_default_panel';
    $wp_customize->get_section( 'header_image' )->panel                   = 'wp_default_panel';
    $wp_customize->get_section( 'background_image' )->panel               = 'wp_default_panel';
    $wp_customize->get_section( 'static_front_page' )->panel              = 'wp_default_panel';
    
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
    
    /** Default Settings Ends */
    
    /** Site Title control */
    $wp_customize->add_setting( 
        'header_site_title', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'header_site_title',
        array(
            'label'       => __( 'Show / Hide Site Title', 'ai-engine' ),
            'section'     => 'title_tagline',
            'type'        => 'checkbox',
        )
    );

    /** Tagline control */
    $wp_customize->add_setting( 
        'header_tagline', 
        array(
            'default'           => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'header_tagline',
        array(
            'label'       => __( 'Show / Hide Tagline', 'ai-engine' ),
            'section'     => 'title_tagline',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting('logo_width', array(
        'sanitize_callback' => 'absint', 
    ));

    // Add a control for logo width
    $wp_customize->add_control('logo_width', array(
        'label' => __('Logo Width', 'ai-engine'),
        'section' => 'title_tagline',
        'type' => 'number',
        'input_attrs' => array(
            'min' => '50', 
            'max' => '500', 
            'step' => '5', 
    ),
        'default' => '100', 
    ));

    $wp_customize->add_setting( 'ai_engine_site_title_size', array(
        'default'           => 24, // Default font size in pixels
        'sanitize_callback' => 'absint', // Sanitize the input as a positive integer
    ) );

    // Add control for site title size
    $wp_customize->add_control( 'ai_engine_site_title_size', array(
        'type'        => 'number',
        'section'     => 'title_tagline', // You can change this section to your preferred section
        'label'       => __( 'Site Title Font Size', 'ai-engine' ),
        'input_attrs' => array(
            'min'  => 10,
            'max'  => 100,
            'step' => 1,
        ),
    ) );

    /** Post & Pages Settings */
    $wp_customize->add_panel( 
        'ai_engine_post_settings',
         array(
            'priority' => 11,
            'capability' => 'edit_theme_options',
            'title' => esc_html__( 'Post & Pages Settings', 'ai-engine' ),
            'description' => esc_html__( 'Customize Post & Pages Settings', 'ai-engine' ),
        ) 
    );

        /** Post Layouts */
    
    $wp_customize->add_section(
        'ai_engine_post_layout_section',
        array(
            'title' => esc_html__( 'Post Layout Settings', 'ai-engine' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_post_settings',
        )
    );

    $wp_customize->add_setting('ai_engine_post_layout_setting', array(
        'default'           => 'right-sidebar',
        'sanitize_callback' => 'ai_engine_sanitize_post_layout',
    ));

    $wp_customize->add_control('ai_engine_post_layout_setting', array(
        'label'    => __('Post Column Settings', 'ai-engine'),
        'section'  => 'ai_engine_post_layout_section',
        'settings' => 'ai_engine_post_layout_setting',
        'type'     => 'select',
        'choices'  => array(
            'one-column'   => __('One Column', 'ai-engine'),
            'right-sidebar'   => __('Right Sidebar', 'ai-engine'),
            'left-sidebar'   => __('Left Sidebar', 'ai-engine'),
            'three-column'   => __('Three Columns', 'ai-engine'),
            'four-column'   => __('Four Columns', 'ai-engine'),
        ),
    ));

     /** Post Layouts Ends */
     
    /** Post Settings */
    $wp_customize->add_section(
        'ai_engine_post_settings',
        array(
            'title' => esc_html__( 'Post Settings', 'ai-engine' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_post_settings',
        )
    );

    /** Post Heading control */
    $wp_customize->add_setting( 
        'ai_engine_post_heading_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_post_heading_setting',
        array(
            'label'       => __( 'Show / Hide Post Heading', 'ai-engine' ),
            'section'     => 'ai_engine_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Post Meta control */
    $wp_customize->add_setting( 
        'ai_engine_post_meta_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_post_meta_setting',
        array(
            'label'       => __( 'Show / Hide Post Meta', 'ai-engine' ),
            'section'     => 'ai_engine_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Post Image control */
    $wp_customize->add_setting( 
        'ai_engine_post_image_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_post_image_setting',
        array(
            'label'       => __( 'Show / Hide Post Image', 'ai-engine' ),
            'section'     => 'ai_engine_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Post Content control */
    $wp_customize->add_setting( 
        'ai_engine_post_content_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_post_content_setting',
        array(
            'label'       => __( 'Show / Hide Post Content', 'ai-engine' ),
            'section'     => 'ai_engine_post_settings',
            'type'        => 'checkbox',
        )
    );
    /** Post ReadMore control */
     $wp_customize->add_setting( 'ai_engine_read_more_setting`', array(
        'default'           => true,
        'sanitize_callback' => 'ai_engine_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'ai_engine_read_more_setting`', array(
        'type'        => 'checkbox',
        'section'     => 'ai_engine_post_settings', 
        'label'       => __( 'Display Read More Button', 'ai-engine' ),
    ) );

    $wp_customize->add_setting('ai_engine_blog_meta_order', array(
        'default' => array('heading', 'author', 'featured-image', 'content','button'),
        'sanitize_callback' => 'ai_engine_sanitize_sortable',
    ));
    $wp_customize->add_control(new AI_Engine_Control_Sortable($wp_customize, 'ai_engine_blog_meta_order', array(
        'label' => esc_html__('Post Meta Ordering', 'ai-engine'),
        'description' => __('Drag & drop post items to rearrange the ordering ( this control will not function by post format )', 'ai-engine') ,
        'section' => 'ai_engine_post_settings',
        'choices' => array(
            'heading' => __('heading', 'ai-engine') ,
            'author' => __('author', 'ai-engine') ,
            'featured-image' => __('featured-image', 'ai-engine') ,
            'content' => __('content', 'ai-engine') ,
            'button' => __('button', 'ai-engine') ,
        ) ,
    )));

    /** Post Settings Ends */

     /** Single Post Settings */
    $wp_customize->add_section(
        'ai_engine_single_post_settings',
        array(
            'title' => esc_html__( 'Single Post Settings', 'ai-engine' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_post_settings',
        )
    );

    /** Single Post Meta control */
    $wp_customize->add_setting( 
        'ai_engine_single_post_meta_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_single_post_meta_setting',
        array(
            'label'       => __( 'Show / Hide Single Post Meta', 'ai-engine' ),
            'section'     => 'ai_engine_single_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Single Post Content control */
    $wp_customize->add_setting( 
        'ai_engine_single_post_content_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_single_post_content_setting',
        array(
            'label'       => __( 'Show / Hide Single Post Content', 'ai-engine' ),
            'section'     => 'ai_engine_single_post_settings',
            'type'        => 'checkbox',
        )
    );

    //Global Color
    $wp_customize->add_section(
        'ai_engine_global_color',
        array(
            'title' => esc_html__( 'Global Color Settings', 'ai-engine' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_general_settings',
        )
    );

    $wp_customize->add_setting('ai_engine_primary_color', array(
        'default'           => '#1AB7F1',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ai_engine_primary_color', array(
        'label'    => __('Theme Primary Color', 'ai-engine'),
        'section'  => 'ai_engine_global_color',
        'settings' => 'ai_engine_primary_color',
    ))); 
    
    $wp_customize->add_setting('ai_engine_secondary_color', array(
        'default'           => '#1F1666',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ai_engine_secondary_color', array(
        'label'    => __('Theme Secondary Color', 'ai-engine'),
        'section'  => 'ai_engine_global_color',
        'settings' => 'ai_engine_secondary_color',
    ))); 

    /** Single Post Settings Ends */

         // Typography Settings Section
    $wp_customize->add_section('ai_engine_typography_settings', array(
        'title'      => esc_html__('Typography Settings', 'ai-engine'),
        'priority'   => 30,
        'capability' => 'edit_theme_options',
        'panel' => 'ai_engine_general_settings',
    ));

    // Array of fonts to choose from
    $font_choices = array(
        ''               => __('Select', 'ai-engine'),
        'Arial'          => 'Arial, sans-serif',
        'Verdana'        => 'Verdana, sans-serif',
        'Helvetica'      => 'Helvetica, sans-serif',
        'Times New Roman'=> '"Times New Roman", serif',
        'Georgia'        => 'Georgia, serif',
        'Courier New'    => '"Courier New", monospace',
        'Trebuchet MS'   => '"Trebuchet MS", sans-serif',
        'Tahoma'         => 'Tahoma, sans-serif',
        'Palatino'       => '"Palatino Linotype", serif',
        'Garamond'       => 'Garamond, serif',
        'Impact'         => 'Impact, sans-serif',
        'Comic Sans MS'  => '"Comic Sans MS", cursive, sans-serif',
        'Lucida Sans'    => '"Lucida Sans Unicode", sans-serif',
        'Arial Black'    => '"Arial Black", sans-serif',
        'Gill Sans'      => '"Gill Sans", sans-serif',
        'Segoe UI'       => '"Segoe UI", sans-serif',
        'Open Sans'      => '"Open Sans", sans-serif',
        'Josefin Sans'   => 'Josefin Sans, sans-serif',
        'Lato'           => 'Lato, sans-serif',
        'Montserrat'     => 'Montserrat, sans-serif',
        'Libre Baskerville' => 'Libre Baskerville',
    );

    // Heading Font Setting
    $wp_customize->add_setting('ai_engine_heading_font_family', array(
        'default'           => '',
        'sanitize_callback' => 'ai_engine_sanitize_choicess',
    ));
    $wp_customize->add_control('ai_engine_heading_font_family', array(
        'type'    => 'select',
        'choices' => $font_choices,
        'label'   => __('Select Font for Heading', 'ai-engine'),
        'section' => 'ai_engine_typography_settings',
    ));

    // Body Font Setting
    $wp_customize->add_setting('ai_engine_body_font_family', array(
        'default'           => '',
        'sanitize_callback' => 'ai_engine_sanitize_choicess',
    ));
    $wp_customize->add_control('ai_engine_body_font_family', array(
        'type'    => 'select',
        'choices' => $font_choices,
        'label'   => __('Select Font for Body', 'ai-engine'),
        'section' => 'ai_engine_typography_settings',
    ));

    /** Typography Settings Section End */

    /** General Settings */
    $wp_customize->add_panel( 
        'ai_engine_general_settings',
         array(
            'priority' => 11,
            'capability' => 'edit_theme_options',
            'title' => esc_html__( 'General Settings', 'ai-engine' ),
            'description' => esc_html__( 'Customize General Settings', 'ai-engine' ),
        ) 
    );

    /** General Settings */
    $wp_customize->add_section(
        'ai_engine_general_settings',
        array(
            'title' => esc_html__( 'Loader Settings', 'ai-engine' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_general_settings',
        )
    );

    /** Preloader control */
    $wp_customize->add_setting( 
        'ai_engine_header_preloader', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_header_preloader',
        array(
            'label'       => __( 'Show Preloader', 'ai-engine' ),
            'section'     => 'ai_engine_general_settings',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting('ai_engine_loader_layout_setting', array(
        'default' => 'load',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Add control for loader layout
    $wp_customize->add_control('ai_engine_loader_layout_control', array(
        'label' => __('Preloader Layout', 'ai-engine'),
        'section' => 'ai_engine_general_settings',
        'settings' => 'ai_engine_loader_layout_setting',
        'type' => 'select',
        'choices' => array(
            'load' => __('Preloader 1', 'ai-engine'),
            'load-one' => __('Preloader 2', 'ai-engine'),
            'ctn-preloader' => __('Preloader 3', 'ai-engine'),
        ),
    ));

    /** Topbar Section Settings */
    $wp_customize->add_section(
        'ai_engine_topbar_section_settings',
        array(
            'title' => esc_html__( 'Topbar Section Settings', 'ai-engine' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_home_page_settings',
        )
    );

    /** Header Section control */
    $wp_customize->add_setting( 
        'ai_engine_header_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_header_setting',
        array(
            'label'       => __( 'Show Topbar', 'ai-engine' ),
            'section'     => 'ai_engine_topbar_section_settings',
            'type'        => 'checkbox',
        )
    );

    /** Phone */

    // Phone Icon
    $wp_customize->add_setting('ai_engine_phone_icon', array(
        'default' => 'fas fa-phone',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new AI_Engine_Changeable_Icon(
        $wp_customize,'ai_engine_phone_icon',array(
        'label' => __('Phone Icon','ai-engine'),
        'section' => 'ai_engine_topbar_section_settings',
        'type' => 'icon'
    )));

    // Phone Value
    $wp_customize->add_setting('ai_engine_header_phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('ai_engine_header_phone', array(
        'label' => __('Add Phone','ai-engine'),
        'section' => 'ai_engine_topbar_section_settings',
        'type' => 'text',
    ));

    /** Email */

    // Email Icon
    $wp_customize->add_setting('ai_engine_mail_icon', array(
        'default' => 'fa-solid fa-microphone-lines',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new AI_Engine_Changeable_Icon(
        $wp_customize,'ai_engine_mail_icon',array(
        'label' => __('Mail Icon','ai-engine'),
        'section' => 'ai_engine_topbar_section_settings',
        'type' => 'icon'
    )));

    // Email Value
    $wp_customize->add_setting('ai_engine_header_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('ai_engine_header_email', array(
        'label' => __('Add Email','ai-engine'),
        'section' => 'ai_engine_topbar_section_settings',
        'type' => 'text',
    ));

    /** Social Section control */
    $wp_customize->add_setting( 
        'ai_engine_social_icon_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_social_icon_setting',
        array(
            'label'       => __( 'Show Social Icon', 'ai-engine' ),
            'section'     => 'ai_engine_topbar_section_settings',
            'type'        => 'checkbox',
        )
    );

    /**  Social Link 1 */
    $wp_customize->add_setting(
        'ai_engine_social_link_1',
        array( 
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        )
    );
    
    $wp_customize->add_control(
        'ai_engine_social_link_1',
        array(
            'label' => esc_html__( 'Add Facebook Link', 'ai-engine' ),
            'section' => 'ai_engine_topbar_section_settings',
            'type' => 'url',
        )
    );

    /**  Social Link 3 */
    $wp_customize->add_setting(
        'ai_engine_social_link_3',
        array( 
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        )
    );
    
    $wp_customize->add_control(
        'ai_engine_social_link_3',
        array(
            'label' => esc_html__( 'Add Twitter Link', 'ai-engine' ),
            'section' => 'ai_engine_topbar_section_settings',
            'type' => 'url',
        )
    );

    /**  Social Link 4 */
    $wp_customize->add_setting(
        'ai_engine_social_link_4',
        array( 
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        )
    );
    
    $wp_customize->add_control(
        'ai_engine_social_link_4',
        array(
            'label' => esc_html__( 'Add Linkedin Link', 'ai-engine' ),
            'section' => 'ai_engine_topbar_section_settings',
            'type' => 'url',
        )
    );

    /**  Social Link 2 */
    $wp_customize->add_setting(
        'ai_engine_social_link_2',
        array( 
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        )
    );
    
    $wp_customize->add_control(
        'ai_engine_social_link_2',
        array(
            'label' => esc_html__( 'Add Instagram Link', 'ai-engine' ),
            'section' => 'ai_engine_topbar_section_settings',
            'type' => 'url',
        )
    );

    /**  Social Link 5 */
    $wp_customize->add_setting(
        'ai_engine_social_link_5',
        array( 
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh'
        )
    );
    
    $wp_customize->add_control(
        'ai_engine_social_link_5',
        array(
            'label' => esc_html__( 'Add Behance Link', 'ai-engine' ),
            'section' => 'ai_engine_topbar_section_settings',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting( 
        'ai_engine_topbar_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'ai_engine_topbar_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(AI_ENGINE_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'ai_engine_topbar_section_settings'
        )
    );

    /** Social Section Settings End */

    /** Header Section Settings */
    $wp_customize->add_section(
        'ai_engine_header_section_settings',
        array(
            'title' => esc_html__( 'Header Section Settings', 'ai-engine' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_home_page_settings',
        )
    );

    /** Sticky Header control */
    $wp_customize->add_setting( 
        'ai_engine_sticky_header', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_sticky_header',
        array(
            'label'       => __( 'Show Sticky Header', 'ai-engine' ),
            'section'     => 'ai_engine_header_section_settings',
            'type'        => 'checkbox',
        )
    );

    // header button Text
    $wp_customize->add_setting('ai_engine_header_login_btn_text', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_header_login_btn_text', 
        array(
        'label'       => __('Button Text 1', 'ai-engine'),
        'section'     => 'ai_engine_header_section_settings',   
        'settings'    => 'ai_engine_header_login_btn_text',
        'type'        => 'text'
        )
    );

    // header button Url
    $wp_customize->add_setting('ai_engine_header_login_btn_url', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'esc_url_raw'
        )
    );

    $wp_customize->add_control('ai_engine_header_login_btn_url', 
        array(
        'label'       => __('Button URL 1', 'ai-engine'),
        'section'     => 'ai_engine_header_section_settings',   
        'settings'    => 'ai_engine_header_login_btn_url',
        'type'        => 'url'
        )
    );

    // header button Text
    $wp_customize->add_setting('ai_engine_header_btn_text', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_header_btn_text', 
        array(
        'label'       => __('Button Text 2', 'ai-engine'),
        'section'     => 'ai_engine_header_section_settings',   
        'settings'    => 'ai_engine_header_btn_text',
        'type'        => 'text'
        )
    );

    // header button Url
    $wp_customize->add_setting('ai_engine_header_btn_url', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'esc_url_raw'
        )
    );

    $wp_customize->add_control('ai_engine_header_btn_url', 
        array(
        'label'       => __('Button URL 2', 'ai-engine'),
        'section'     => 'ai_engine_header_section_settings',   
        'settings'    => 'ai_engine_header_btn_url',
        'type'        => 'url'
        )
    );

    // Add Setting for Menu Font Weight
    $wp_customize->add_setting( 'ai_engine_menu_font_weight', array(
        'default'           => '500',
        'sanitize_callback' => 'ai_engine_sanitize_font_weight',
    ) );

    // Add Control for Menu Font Weight
    $wp_customize->add_control( 'ai_engine_menu_font_weight', array(
        'label'    => __( 'Menu Font Weight', 'ai-engine' ),
        'section'  => 'ai_engine_header_section_settings',
        'type'     => 'select',
        'choices'  => array(
            '100' => __( '100 - Thin', 'ai-engine' ),
            '200' => __( '200 - Extra Light', 'ai-engine' ),
            '300' => __( '300 - Light', 'ai-engine' ),
            '400' => __( '400 - Normal', 'ai-engine' ),
            '500' => __( '500 - Medium', 'ai-engine' ),
            '600' => __( '600 - Semi Bold', 'ai-engine' ),
            '700' => __( '700 - Bold', 'ai-engine' ),
            '800' => __( '800 - Extra Bold', 'ai-engine' ),
            '900' => __( '900 - Black', 'ai-engine' ),
        ),
    ) );

    // Add Setting for Menu Text Transform
    $wp_customize->add_setting( 'ai_engine_menu_text_transform', array(
        'default'           => 'capitalize',
        'sanitize_callback' => 'ai_engine_sanitize_text_transform',
    ) );

    // Add Control for Menu Text Transform
    $wp_customize->add_control( 'ai_engine_menu_text_transform', array(
        'label'    => __( 'Menu Text Transform', 'ai-engine' ),
        'section'  => 'ai_engine_header_section_settings',
        'type'     => 'select',
        'choices'  => array(
            'none'       => __( 'None', 'ai-engine' ),
            'capitalize' => __( 'Capitalize', 'ai-engine' ),
            'uppercase'  => __( 'Uppercase', 'ai-engine' ),
            'lowercase'  => __( 'Lowercase', 'ai-engine' ),
        ),
    ) );

    // Menu Hover Style	
    $wp_customize->add_setting('ai_engine_menus_style',array(
        'default' => '',
        'sanitize_callback' => 'ai_engine_sanitize_choices'
	));
	$wp_customize->add_control('ai_engine_menus_style',array(
        'type' => 'select',
		'label' => __('Menu Hover Style','ai-engine'),
		'section' => 'ai_engine_header_section_settings',
		'choices' => array(
         'None' => __('None','ai-engine'),
         'Zoom In' => __('Zoom In','ai-engine'),
      ),
	));

    $wp_customize->add_setting( 
        'ai_engine_header_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'ai_engine_header_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(AI_ENGINE_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'ai_engine_header_section_settings'
        )
    );

    /** Home Page Settings */
    $wp_customize->add_panel( 
        'ai_engine_home_page_settings',
         array(
            'priority' => 9,
            'capability' => 'edit_theme_options',
            'title' => esc_html__( 'Home Page Settings', 'ai-engine' ),
            'description' => esc_html__( 'Customize Home Page Settings', 'ai-engine' ),
        ) 
    );

    $wp_customize->add_section(
        'ai_engine_banner_section_settings',
        array(
            'title' => esc_html__( 'Banner Settings', 'ai-engine' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_home_page_settings',
        )
    );
    /** Banner Section Settings */
    $wp_customize->add_section(
        'ai_engine_banner_section_settings',
        array(
            'title' => esc_html__( 'Banner Section Settings', 'ai-engine' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_home_page_settings',
        )
    );

    /** Banner Section control */
    $wp_customize->add_setting( 
        'ai_engine_banner_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_banner_setting',
        array(
            'label'       => __( 'Show Banner', 'ai-engine' ),
            'section'     => 'ai_engine_banner_section_settings',
            'type'        => 'checkbox',
        )
    );

    // Section Text
    $wp_customize->add_setting('ai_engine_banner_text_extra', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_banner_text_extra', 
        array(
        'label'       => __('Banner Sub Title', 'ai-engine'),
        'section'     => 'ai_engine_banner_section_settings',   
        'settings'    => 'ai_engine_banner_text_extra',
        'type'        => 'text'
        )
    );

    // Section Text
    $wp_customize->add_setting('ai_engine_banner_title', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_banner_title', 
        array(
        'label'       => __('Banner Title', 'ai-engine'),
        'section'     => 'ai_engine_banner_section_settings',   
        'settings'    => 'ai_engine_banner_title',
        'type'        => 'text'
        )
    );

    $wp_customize->add_setting('ai_engine_banner_content', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_banner_content', 
        array(
        'label'       => __('Banner Content', 'ai-engine'),
        'section'     => 'ai_engine_banner_section_settings',   
        'settings'    => 'ai_engine_banner_content',
        'type'        => 'text'
        )
    );

    // banner button Text
    $wp_customize->add_setting('ai_engine_banner_btn_text', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_banner_btn_text', 
        array(
        'label'       => __('Banner Button Text', 'ai-engine'),
        'section'     => 'ai_engine_banner_section_settings',   
        'settings'    => 'ai_engine_banner_btn_text',
        'type'        => 'text'
        )
    );

    // banner button Url
    $wp_customize->add_setting('ai_engine_banner_btn_url', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'esc_url_raw'
        )
    );

    $wp_customize->add_control('ai_engine_banner_btn_url', 
        array(
        'label'       => __('Banner Button URL', 'ai-engine'),
        'section'     => 'ai_engine_banner_section_settings',   
        'settings'    => 'ai_engine_banner_btn_url',
        'type'        => 'url'
        )
    );

    $wp_customize->add_setting('ai_engine_banner_background_image', array(
        'default'           => get_template_directory_uri() . '/images/default.png',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ai_engine_banner_background_image', array(
        'label' => __('Banner Background Image', 'ai-engine'),
        'section' => 'ai_engine_banner_section_settings',
        'settings' => 'ai_engine_banner_background_image',
    )));

    $wp_customize->add_setting(
        'ai_engine_banner_overlay',
        array(
            'default' => 'rgba(0,0,0,0.6)',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'ai_engine_banner_overlay',
            array(
                'label' => __('Banner Overlay Color', 'ai-engine'),
                'section' => 'ai_engine_banner_section_settings',
                'type' => 'color',
            )
        )
    );

    $wp_customize->add_setting( 
        'ai_engine_banner_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'ai_engine_banner_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(AI_ENGINE_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'ai_engine_banner_section_settings'
        )
    );

   /** About Section Start */

   $wp_customize->add_section(
        'ai_engine_classes_section_settings',
        array(
            'title' => esc_html__( 'About Section Settings', 'ai-engine' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_home_page_settings',
        )
    );

    $wp_customize->add_setting( 
        'ai_engine_about_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_about_setting',
        array(
            'label'       => __( 'Show About Section', 'ai-engine' ),
            'section'     => 'ai_engine_classes_section_settings',
            'type'        => 'checkbox',
        )
    );

    // Section Text
    $wp_customize->add_setting('ai_engine_about_text_extra', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_about_text_extra', 
        array(
        'label'       => __('Section Sub Title', 'ai-engine'),
        'section'     => 'ai_engine_classes_section_settings',   
        'settings'    => 'ai_engine_about_text_extra',
        'type'        => 'text'
        )
    );

    // Section Text
    $wp_customize->add_setting('ai_engine_about_title', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_about_title', 
        array(
        'label'       => __('Section Title', 'ai-engine'),
        'section'     => 'ai_engine_classes_section_settings',   
        'settings'    => 'ai_engine_about_title',
        'type'        => 'text'
        )
    );

    $wp_customize->add_setting('ai_engine_about_content', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_about_content', 
        array(
        'label'       => __('Section Content', 'ai-engine'),
        'section'     => 'ai_engine_classes_section_settings',   
        'settings'    => 'ai_engine_about_content',
        'type'        => 'text'
        )
    );

    $wp_customize->add_setting('ai_engine_about_icon_1', array(
        'default' => 'fas fa-microphone-lines',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new AI_Engine_Changeable_Icon(
        $wp_customize,'ai_engine_about_icon_1',array(
        'label' => __('Icon 1','ai-engine'),
        'section' => 'ai_engine_classes_section_settings',
        'type' => 'icon'
    )));

    $wp_customize->add_setting('ai_engine_icon_text_1', 
        array(
            'default'           => '',
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',    
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control('ai_engine_icon_text_1', 
        array(
            'label'       => __('Icon Text 1', 'ai-engine'),
            'section'     => 'ai_engine_classes_section_settings',   
            'settings'    => 'ai_engine_icon_text_1',
            'type'        => 'text'
        )
    );

    $wp_customize->add_setting('ai_engine_about_icon_2', array(
        'default' => 'fas fa-star',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new AI_Engine_Changeable_Icon(
        $wp_customize,'ai_engine_about_icon_2',array(
        'label' => __('Icon 2','ai-engine'),
        'section' => 'ai_engine_classes_section_settings',
        'type' => 'icon'
    )));

    $wp_customize->add_setting('ai_engine_icon_text_2', 
        array(
            'default'           => '',
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',    
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control('ai_engine_icon_text_2', 
        array(
            'label'       => __('Icon Text 2', 'ai-engine'),
            'section'     => 'ai_engine_classes_section_settings',   
            'settings'    => 'ai_engine_icon_text_2',
            'type'        => 'text'
        )
    );

    $wp_customize->add_setting('ai_engine_about_icon_3', array(
        'default' => 'fas fa-chart-area',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new AI_Engine_Changeable_Icon(
        $wp_customize,'ai_engine_about_icon_3',array(
        'label' => __('Icon 3','ai-engine'),
        'section' => 'ai_engine_classes_section_settings',
        'type' => 'icon'
    )));

    $wp_customize->add_setting('ai_engine_icon_text_3', 
        array(
            'default'           => '',
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',    
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control('ai_engine_icon_text_3', 
        array(
            'label'       => __('Icon Text 3', 'ai-engine'),
            'section'     => 'ai_engine_classes_section_settings',   
            'settings'    => 'ai_engine_icon_text_3',
            'type'        => 'text'
        )
    );

    // about button Text
    $wp_customize->add_setting('ai_engine_about_btn_text', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('ai_engine_about_btn_text', 
        array(
        'label'       => __('Button Text', 'ai-engine'),
        'section'     => 'ai_engine_classes_section_settings',   
        'settings'    => 'ai_engine_about_btn_text',
        'type'        => 'text'
        )
    );

    // about button Url
    $wp_customize->add_setting('ai_engine_about_btn_url', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'esc_url_raw'
        )
    );

    $wp_customize->add_control('ai_engine_about_btn_url', 
        array(
        'label'       => __('Button URL', 'ai-engine'),
        'section'     => 'ai_engine_classes_section_settings',   
        'settings'    => 'ai_engine_about_btn_url',
        'type'        => 'url'
        )
    );

    $wp_customize->add_setting('ai_engine_about_image', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw', 
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ai_engine_about_image', array(
        'label' => __('Add Image 1', 'ai-engine'),
        'section' => 'ai_engine_classes_section_settings',
        'settings' => 'ai_engine_about_image',
    )));

    $wp_customize->add_setting('ai_engine_about_image_2', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw', 
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ai_engine_about_image_2', array(
        'label' => __('Add Image 2', 'ai-engine'),
        'section' => 'ai_engine_classes_section_settings',
        'settings' => 'ai_engine_about_image_2',
    )));

    $wp_customize->add_setting('ai_engine_about_image_3', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw', 
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ai_engine_about_image_3', array(
        'label' => __('Add Image 3', 'ai-engine'),
        'section' => 'ai_engine_classes_section_settings',
        'settings' => 'ai_engine_about_image_3',
    )));

    $wp_customize->add_setting( 
        'ai_engine_classes_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'ai_engine_classes_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(AI_ENGINE_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'ai_engine_classes_section_settings'
        )
    );

    /** Choose Section End */

    /** Services Section Start */

   $wp_customize->add_section(
        'ai_engine_services_section_settings',
        array(
            'title' => esc_html__( 'Services Section Settings', 'ai-engine' ),
            'priority' => 40,
            'capability' => 'edit_theme_options',
            'panel' => 'ai_engine_home_page_settings',
        )
    );

    $wp_customize->add_setting( 
        'ai_engine_services_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'ai_engine_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'ai_engine_services_setting',
        array(
            'label'       => __( 'Show Services Section', 'ai-engine' ),
            'section'     => 'ai_engine_services_section_settings',
            'type'        => 'checkbox',
        )
    );

    // Section Title
    $wp_customize->add_setting(
        'ai_engine_service_title', 
        array(
            'default'           => '',
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',    
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control(
        'ai_engine_service_title', 
        array(
            'label'       => __('Section Title', 'ai-engine'),
            'section'     => 'ai_engine_services_section_settings',
            'settings'    => 'ai_engine_service_title',
            'type'        => 'text'
        )
    );

     // Section Text
    $wp_customize->add_setting(
        'ai_engine_service_text', 
        array(
            'default'           => '',
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',    
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control(
        'ai_engine_service_text', 
        array(
            'label'       => __('Section Text', 'ai-engine'),
            'section'     => 'ai_engine_services_section_settings',
            'settings'    => 'ai_engine_service_text',
            'type'        => 'text'
        )
    );

	$wp_customize->add_setting('ai_engine_claases_number',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	$wp_customize->add_control('ai_engine_claases_number',array(
		'label'	=> __('Number of post to show','ai-engine'),
		'description' => __('Add number and refresh tab','ai-engine'),
		'section'	=> 'ai_engine_services_section_settings',
		'type'		=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 6,
		)
	));
	$ai_engine_featured_post = get_theme_mod('ai_engine_claases_number');

	$args = array('numberposts' => -1);
	$post_list = get_posts($args);
	$ai_engine_i = 0;
	$pst[]='Select';
	foreach($post_list as $post){
		$pst[$post->ID] = $post->post_title;
	}

	for ( $ai_engine_i = 1; $ai_engine_i <= $ai_engine_featured_post; $ai_engine_i++ ) {
		$wp_customize->add_setting('ai_engine_services_category'.$ai_engine_i,array(
			'sanitize_callback' => 'ai_engine_sanitize_choices',
		));
		$wp_customize->add_control('ai_engine_services_category'.$ai_engine_i,array(
			'type'    => 'select',
			'choices' => $pst,
			'label' => __('Select Post','ai-engine'),
			'section' => 'ai_engine_services_section_settings',
		));

        $wp_customize->add_setting('ai_engine_service_icon'.$ai_engine_i,array(
            'default' => 'fas fa-tag',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        $wp_customize->add_control(new AI_Engine_Changeable_Icon(
            $wp_customize,'ai_engine_service_icon'.$ai_engine_i,array(
            'label' => __('Add Icon','ai-engine'),
            'section' => 'ai_engine_services_section_settings',
            'type' => 'icon'
        )));

        $wp_customize->add_setting(
            'ai_engine_service_icon_text' . $ai_engine_i,
            array(
                'default'           => '',
                'type'              => 'theme_mod',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control(
            'ai_engine_service_icon_text' . $ai_engine_i,
            array(
                'label'    => __( 'Icon Text', 'ai-engine' ),
                'section'  => 'ai_engine_services_section_settings',
                'settings' => 'ai_engine_service_icon_text' . $ai_engine_i,
                'type'     => 'text'
            )
        );

		$wp_customize->add_setting('ai_engine_author_image'.$ai_engine_i,array(
			'default'	=> '',
			'sanitize_callback'	=> 'esc_url_raw',
	    ));
	    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'ai_engine_author_image'.$ai_engine_i,array(
	      'label' => __('Author Image','ai-engine'),
	      'section' => 'ai_engine_services_section_settings',
	      'description' => __('Image size (35px x 35px)','ai-engine'),
	    )));

        $wp_customize->add_setting('ai_engine_regular_price'.$ai_engine_i,array(
			'default'	=> '',
			'sanitize_callback'	=> 'sanitize_text_field'
		));	
		$wp_customize->add_control('ai_engine_regular_price'.$ai_engine_i,array(
			'label'	=> esc_html__( 'Add Price', 'ai-engine' ),
			'section'	=> 'ai_engine_services_section_settings',
			'type'		=> 'text',
			'input_attrs' => array(
		      'placeholder' => __( '$100.00', 'ai-engine' ),
		    ),
		));

		$wp_customize->add_setting('ai_engine_service_button_label'.$ai_engine_i,array(
			'default' => 'Enroll Now',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control('ai_engine_service_button_label'.$ai_engine_i,array(
			'label' => __('Add Service Button Text','ai-engine'),
			'section' => 'ai_engine_services_section_settings',
			'setting' => 'ai_engine_service_button_label',
			'type' => 'text'
		));

        /* Star Rating Count */
        $wp_customize->add_setting(
            'ai_engine_star_rating'.$ai_engine_i,
            array(
                'default'           => '',
                'sanitize_callback' => 'absint',
            )
        );
        $wp_customize->add_control(
            'ai_engine_star_rating'.$ai_engine_i,
            array(
                'label'   => __( 'Star Rating Count', 'ai-engine' ),
                'section' => 'ai_engine_services_section_settings',
                'type'    => 'number',
                'input_attrs' => array(
                    'min'  => 1,
                    'max'  => 5,
                    'step' => 1,
                ),
            )
        );
	}

    $wp_customize->add_setting( 
        'ai_engine_services_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'ai_engine_services_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(AI_ENGINE_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'ai_engine_services_section_settings'
        )
    );

    /** Services Section End */
    
    /** Home Page Settings Ends */
    
    /** Footer Section */
    $wp_customize->add_section(
        'ai_engine_footer_section',
        array(
            'title' => __( 'Footer Settings', 'ai-engine' ),
            'priority' => 70,
            'panel' => 'ai_engine_home_page_settings',
        )
    );
    
    /** Copyright Text */
    $wp_customize->add_setting(
        'ai_engine_footer_copyright_text',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'ai_engine_footer_copyright_text',
        array(
            'label' => __( 'Copyright Info', 'ai-engine' ),
            'section' => 'ai_engine_footer_section',
            'type' => 'text',
        )
    );  
    $wp_customize->add_setting('ai_engine_footer_background_image',
        array(
        'default' => '',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'absint',
        )
    );


    $wp_customize->add_control(
         new WP_Customize_Cropped_Image_Control($wp_customize, 'ai_engine_footer_background_image',
            array(
                'label' => esc_html__('Footer Background Image', 'ai-engine'),
                /* translators: 1: image width in pixels, 2: image height in pixels */
                'description' => sprintf(esc_html__('Recommended Size %1$s px X %2$s px', 'ai-engine'), 1024, 800),
                'section' => 'ai_engine_footer_section',
                'width' => 1024,
                'height' => 800,
                'flex_width' => true,
                'flex_height' => true,
            )
        )
    );

    /** Footer Background Image Attachment */
    $wp_customize->add_setting('ai_engine_background_attachment', array(
        'default'           => 'scroll',
        'sanitize_callback' => 'ai_engine_sanitize_choices',
    ));

    $wp_customize->add_control('ai_engine_background_attachment', array(
        'label'    => __('Footer Background Attachment', 'ai-engine'),
        'section'  => 'ai_engine_footer_section',
        'settings' => 'ai_engine_background_attachment',
        'type'     => 'select',
        'choices'  => array(
            'fixed' => __('fixed','ai-engine'),
            'scroll' => __('scroll','ai-engine'),
        ),
    ));

    /* Footer Background Color*/
    $wp_customize->add_setting(
        'ai_engine_footer_background_color',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'ai_engine_footer_background_color',
            array(
                'label' => __('Footer Widget Area Background Color', 'ai-engine'),
                'section' => 'ai_engine_footer_section',
                'type' => 'color',
            )
        )
    );

     $wp_customize->add_setting('ai_engine_scroll_icon',array(
        'default'   => 'fas fa-arrow-up',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new AI_Engine_Changeable_Icon(
        $wp_customize,'ai_engine_scroll_icon',array(
        'label' => __('Scroll Top Icon','ai-engine'),
        'transport' => 'refresh',
        'section'   => 'ai_engine_footer_section',
        'type'      => 'icon'
    )));

    /** Scroll to top button shape */
    $wp_customize->add_setting('ai_engine_scroll_to_top_radius', array(
        'default'           => 'curved-box',
        'sanitize_callback' => 'ai_engine_sanitize_choices',
    ));

    $wp_customize->add_control('ai_engine_scroll_to_top_radius', array(
        'label'    => __('Scroll Top Button Shape', 'ai-engine'),
        'section'  => 'ai_engine_footer_section',
        'settings' => 'ai_engine_scroll_to_top_radius',
        'type'     => 'select',
        'choices'  => array(
            'box'        => __( 'Box', 'ai-engine' ),
            'curved-box' => __( 'Curved Box', 'ai-engine' ),
            'circle'     => __( 'Circle', 'ai-engine' ),
        ),
    ));

    $wp_customize->add_setting( 
        'ai_engine_footer_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'ai_engine_footer_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(AI_ENGINE_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'ai_engine_footer_section'
        )
    );

    // 404 PAGE SETTINGS
    $wp_customize->add_section(
        'ai_engine_404_section',
        array(
            'title' => __( '404 Page Settings', 'ai-engine' ),
            'priority' => 70,
            'panel' => 'ai_engine_general_settings',
        )
    );
   
    $wp_customize->add_setting('404_page_image', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw', // Sanitize as URL
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, '404_page_image', array(
        'label' => __('404 Page Image', 'ai-engine'),
        'section' => 'ai_engine_404_section',
        'settings' => '404_page_image',
    )));

    $wp_customize->add_setting('404_pagefirst_header', array(
        'default' => __('404', 'ai-engine'),
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field', // Sanitize as text field
    ));

    $wp_customize->add_control('404_pagefirst_header', array(
        'type' => 'text',
        'label' => __('Heading', 'ai-engine'),
        'section' => 'ai_engine_404_section',
    ));

    // Setting for 404 page header
    $wp_customize->add_setting('404_page_header', array(
        'default' => __('Sorry, that page can\'t be found!', 'ai-engine'),
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field', // Sanitize as text field
    ));

    $wp_customize->add_control('404_page_header', array(
        'type' => 'text',
        'label' => __('Heading', 'ai-engine'),
        'section' => 'ai_engine_404_section',
    ));

}
add_action( 'customize_register', 'ai_engine_customize_register' );
endif;

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function ai_engine_customize_preview_js() {
    // Use minified libraries if SCRIPT_DEBUG is false
    $ai_engine_build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $ai_engine_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'ai_engine_customizer', get_template_directory_uri() . '/js' . $ai_engine_build . '/customizer' . $ai_engine_suffix . '.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'ai_engine_customize_preview_js' );