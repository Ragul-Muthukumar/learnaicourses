<?php
/**
 * eLearning Education: Customizer
 *
 * @package eLearning Education
 * @subpackage elearning_education
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function elearning_education_customize_register( $wp_customize ) {

	// Pro Version
    class elearning_education_Customize_Pro_Version extends WP_Customize_Control {
        public $type = 'pro_options';

        public function render_content() {
            echo '<span>Unlock Premium <strong>'. esc_html( $this->label ) .'</strong>? </span>';
            echo '<a href="'. esc_url($this->description) .'" target="_blank">';
                echo '<span class="dashicons dashicons-info"></span>';
                echo '<strong> '. esc_html( ELEARNING_EDUCATION_BUY_TEXT,'elearning-education' ) .'<strong></a>';
            echo '</a>';
        }
    }

    // Custom Controls
    function elearning_education_sanitize_custom_control( $input ) {
        return $input;
    }

	require get_parent_theme_file_path('/inc/controls/icon-changer.php');

	require get_parent_theme_file_path('/inc/controls/range-slider-control.php');

	// Register the custom control type.
	$wp_customize->register_control_type( 'Elearning_Education_Toggle_Control' );

	//Register the sortable control type.
	$wp_customize->register_control_type( 'Elearning_Education_Control_Sortable' );

	//add home page setting pannel
	$wp_customize->add_panel( 'elearning_education_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Custom Home page', 'elearning-education' ),
	    'description' => __( 'Description of what this panel does.', 'elearning-education' ),
	) );

	//TP Color Option
	$wp_customize->add_section('elearning_education_color_option',array(
     'title'         => __('TP Color Option', 'elearning-education'),
     'priority' => 6,
     'panel' => 'elearning_education_panel_id'
    ) );

	$wp_customize->add_setting( 'elearning_education_tp_color_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_tp_color_option', array(
			'label'     => __('Theme First Color', 'elearning-education'),
	    'description' => __('It will change the complete theme color in one click.', 'elearning-education'),
	    'section' => 'elearning_education_color_option',
	    'settings' => 'elearning_education_tp_color_option',
  	)));

 
  	$wp_customize->add_setting( 'elearning_education_tp_secoundary_color_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_tp_secoundary_color_option', array(
			'label'     => __('Theme Second Color', 'elearning-education'),
	    'description' => __('It will change the complete theme color in one click.', 'elearning-education'),
	    'section' => 'elearning_education_color_option',
	    'settings' => 'elearning_education_tp_secoundary_color_option',
  	)));

	//Sidebar Position
	$wp_customize->add_section('elearning_education_tp_general_settings',array(
		'title' => __('TP General Option', 'elearning-education'),
		'priority' => 1,
		'panel' => 'elearning_education_panel_id'
	) );

   $wp_customize->add_setting('elearning_education_tp_body_layout_settings',array(
		'default' => 'Full',
		'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
   $wp_customize->add_control('elearning_education_tp_body_layout_settings',array(
        'type' => 'radio',
        'label'     => __('Body Layout Setting', 'elearning-education'),
        'description'   => __('This option work for complete body, if you want to set the complete website in container.', 'elearning-education'),
        'section' => 'elearning_education_tp_general_settings',
        'choices' => array(
            'Full' => __('Full','elearning-education'),
            'Container' => __('Container','elearning-education'),
            'Container Fluid' => __('Container Fluid','elearning-education')
        ),
	) );

   // Add Settings and Controls for Post Layout
	$wp_customize->add_setting('elearning_education_sidebar_post_layout',array(
      'default' => 'right',
      'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_sidebar_post_layout',array(
        'type' => 'radio',
        'label'     => __('Post Sidebar Position', 'elearning-education'),
        'description'   => __('This option work for blog page, blog single page, archive page and search page.', 'elearning-education'),
        'section' => 'elearning_education_tp_general_settings',
        'choices' => array(
            'full' => __('Full','elearning-education'),
            'left' => __('Left','elearning-education'),
            'right' => __('Right','elearning-education'),
            'three-column' => __('Three Columns','elearning-education'),
            'four-column' => __('Four Columns','elearning-education'),
            'grid' => __('Grid Layout','elearning-education')
        ),
	) );
	// Add Settings and Controls for Post sidebar Layout
	$wp_customize->add_setting('elearning_education_sidebar_single_post_layout',array(
        'default' => 'right',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_sidebar_single_post_layout',array(
        'type' => 'radio',
        'label'     => __('Single Post Sidebar Position', 'elearning-education'),
        'description'   => __('This option work for single blog page', 'elearning-education'),
        'section' => 'elearning_education_tp_general_settings',
        'choices' => array(
            'full' => __('Full','elearning-education'),
            'left' => __('Left','elearning-education'),
            'right' => __('Right','elearning-education'),
        ),
	) );
	// Add Settings and Controls for Page Layout
	$wp_customize->add_setting('elearning_education_sidebar_page_layout',array(
        'default' => 'right',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_sidebar_page_layout',array(
        'type' => 'radio',
        'label'     => __('Page Sidebar Position', 'elearning-education'),
        'description'   => __('This option work for pages.', 'elearning-education'),
        'section' => 'elearning_education_tp_general_settings',
        'choices' => array(
            'full' => __('Full','elearning-education'),
            'left' => __('Left','elearning-education'),
            'right' => __('Right','elearning-education')
        ),
	) );

	$wp_customize->add_setting( 'elearning_education_sticky', array(
		'default'           => false,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_sticky', array(
		'label'       => esc_html__( 'Show / Hide Sticky Header', 'elearning-education' ),
		'section'     => 'elearning_education_tp_general_settings',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_sticky',
	) ) );

	//tp typography option
	$elearning_education_font_array = array(
		''                       => 'No Fonts',
		'Abril Fatface'          => 'Abril Fatface',
		'Acme'                   => 'Acme',
		'Anton'                  => 'Anton',
		'Architects Daughter'    => 'Architects Daughter',
		'Arimo'                  => 'Arimo',
		'Arsenal'                => 'Arsenal',
		'Arvo'                   => 'Arvo',
		'Alegreya'               => 'Alegreya',
		'Alfa Slab One'          => 'Alfa Slab One',
		'Averia Serif Libre'     => 'Averia Serif Libre',
		'Bangers'                => 'Bangers',
		'Boogaloo'               => 'Boogaloo',
		'Bad Script'             => 'Bad Script',
		'Bitter'                 => 'Bitter',
		'Bree Serif'             => 'Bree Serif',
		'BenchNine'              => 'BenchNine',
		'Cabin'                  => 'Cabin',
		'Cardo'                  => 'Cardo',
		'Courgette'              => 'Courgette',
		'Cherry Swash'           => 'Cherry Swash',
		'Cormorant Garamond'     => 'Cormorant Garamond',
		'Crimson Text'           => 'Crimson Text',
		'Cuprum'                 => 'Cuprum',
		'Cookie'                 => 'Cookie',
		'Chewy'                  => 'Chewy',
		'Days One'               => 'Days One',
		'Dosis'                  => 'Dosis',
		'Droid Sans'             => 'Droid Sans',
		'Economica'              => 'Economica',
		'Fredoka One'            => 'Fredoka One',
		'Fjalla One'             => 'Fjalla One',
		'Francois One'           => 'Francois One',
		'Frank Ruhl Libre'       => 'Frank Ruhl Libre',
		'Gloria Hallelujah'      => 'Gloria Hallelujah',
		'Great Vibes'            => 'Great Vibes',
		'Handlee'                => 'Handlee',
		'Hammersmith One'        => 'Hammersmith One',
		'Inconsolata'            => 'Inconsolata',
		'Indie Flower'           => 'Indie Flower',
		'IM Fell English SC'     => 'IM Fell English SC',
		'Julius Sans One'        => 'Julius Sans One',
		'Josefin Slab'           => 'Josefin Slab',
		'Josefin Sans'           => 'Josefin Sans',
		'Kanit'                  => 'Kanit',
		'Lobster'                => 'Lobster',
		'Lato'                   => 'Lato',
		'Lora'                   => 'Lora',
		'Libre Baskerville'      => 'Libre Baskerville',
		'Lobster Two'            => 'Lobster Two',
		'Merriweather'           => 'Merriweather',
		'Monda'                  => 'Monda',
		'Montserrat'             => 'Montserrat',
		'Muli'                   => 'Muli',
		'Marck Script'           => 'Marck Script',
		'Noto Serif'             => 'Noto Serif',
		'Open Sans'              => 'Open Sans',
		'Overpass'               => 'Overpass',
		'Overpass Mono'          => 'Overpass Mono',
		'Oxygen'                 => 'Oxygen',
		'Orbitron'               => 'Orbitron',
		'Patua One'              => 'Patua One',
		'Pacifico'               => 'Pacifico',
		'Padauk'                 => 'Padauk',
		'Playball'               => 'Playball',
		'Playfair Display'       => 'Playfair Display',
		'PT Sans'                => 'PT Sans',
		'Philosopher'            => 'Philosopher',
		'Permanent Marker'       => 'Permanent Marker',
		'Poiret One'             => 'Poiret One',
		'Quicksand'              => 'Quicksand',
		'Quattrocento Sans'      => 'Quattrocento Sans',
		'Raleway'                => 'Raleway',
		'Rubik'                  => 'Rubik',
		'Rokkitt'                => 'Rokkitt',
		'Russo One'              => 'Russo One',
		'Righteous'              => 'Righteous',
		'Slabo'                  => 'Slabo',
		'Source Sans Pro'        => 'Source Sans Pro',
		'Shadows Into Light Two' => 'Shadows Into Light Two',
		'Shadows Into Light'     => 'Shadows Into Light',
		'Sacramento'             => 'Sacramento',
		'Shrikhand'              => 'Shrikhand',
		'Tangerine'              => 'Tangerine',
		'Ubuntu'                 => 'Ubuntu',
		'VT323'                  => 'VT323',
		'Varela Round'           => 'Varela Round',
		'Vampiro One'            => 'Vampiro One',
		'Vollkorn'               => 'Vollkorn',
		'Volkhov'                => 'Volkhov',
		'Yanone Kaffeesatz'      => 'Yanone Kaffeesatz'
	);
	$wp_customize->add_section('elearning_education_typography_option',array(
		'title'         => __('TP Typography Option', 'elearning-education'),
		'priority' => 1,
		'panel' => 'elearning_education_panel_id'
   	));

   	$wp_customize->add_setting('elearning_education_heading_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'elearning_education_sanitize_choices',
	));
	$wp_customize->add_control(	'elearning_education_heading_font_family', array(
		'section' => 'elearning_education_typography_option',
		'label'   => __('heading Fonts', 'elearning-education'),
		'type'    => 'select',
		'choices' => $elearning_education_font_array,
	));

	$wp_customize->add_setting('elearning_education_body_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'elearning_education_sanitize_choices',
	));
	$wp_customize->add_control(	'elearning_education_body_font_family', array(
		'section' => 'elearning_education_typography_option',
		'label'   => __('Body Fonts', 'elearning-education'),
		'type'    => 'select',
		'choices' => $elearning_education_font_array,
	));

	//TP Blog Option
	$wp_customize->add_section('elearning_education_blog_option',array(
     	'title' => __('TP Blog Option', 'elearning-education'),
     	'priority' => 8,
     	'panel' => 'elearning_education_panel_id'
    ) );

    $wp_customize->add_setting('elearning_education_edit_blog_page_title',array(
		'default'=> __('Home','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_edit_blog_page_title',array(
		'label'	=> __('Change Blog Page Title','elearning-education'),
		'section'=> 'elearning_education_blog_option',
		'type'=> 'text'
	));

	$wp_customize->add_setting('elearning_education_edit_blog_page_description',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_edit_blog_page_description',array(
		'label'	=> __('Add Blog Page Description','elearning-education'),
		'section'=> 'elearning_education_blog_option',
		'type'=> 'text'
	));

	/** Meta Order */
    $wp_customize->add_setting('blog_meta_order', array(
        'default' => array('date', 'author', 'comment', 'category', 'time'),
        'sanitize_callback' => 'elearning_education_sanitize_sortable',
    ));
    $wp_customize->add_control(new Elearning_Education_Control_Sortable($wp_customize, 'blog_meta_order', array(
    	'label' => esc_html__('Meta Order', 'elearning-education'),
        'description' => __('Drag & Drop post items to re-arrange the order and also hide and show items as per the need by clicking on the eye icon.', 'elearning-education') ,
        'section' => 'elearning_education_blog_option',
        'choices' => array(
            'date' => __('date', 'elearning-education') ,
            'author' => __('author', 'elearning-education') ,
            'comment' => __('comment', 'elearning-education') ,
            'category' => __('category', 'elearning-education'),
            'time' => __('time', 'elearning-education'),
        ) ,
    )));
    $wp_customize->add_setting( 'elearning_education_excerpt_count', array(
		'default'              => 35,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'elearning_education_sanitize_number_range',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'elearning_education_excerpt_count', array(
		'label'       => esc_html__( 'Edit Excerpt Limit','elearning-education' ),
		'section'     => 'elearning_education_blog_option',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	$wp_customize->add_setting('elearning_education_show_first_caps',array(
        'default' => false,
        'sanitize_callback' => 'elearning_education_sanitize_checkbox',
    ));
	$wp_customize->add_control( 'elearning_education_show_first_caps',array(
		'label' => esc_html__('First Cap (First Capital Letter)', 'elearning-education'),
		'type' => 'checkbox',
		'section' => 'elearning_education_blog_option',
	));

	$wp_customize->add_setting('elearning_education_read_more_text',array(
		'default'=> __('Read More','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_read_more_text',array(
		'label'	=> __('Edit Button Text','elearning-education'),
		'section'=> 'elearning_education_blog_option',
		'type'=> 'text'
	));

	$wp_customize->add_setting('elearning_education_post_image_round', array(
	  'default' => '0',
      'sanitize_callback' => 'elearning_education_sanitize_number_range',
	));
	$wp_customize->add_control(new elearning_education_Range_Slider($wp_customize, 'elearning_education_post_image_round', array(
       'section' => 'elearning_education_blog_option',
      'label' => esc_html__('Edit Post Image Border Radius', 'elearning-education'),
      'input_attrs' => array(
        'min' => 0,
        'max' => 180,
        'step' => 1
    )
	)));

	$wp_customize->add_setting('elearning_education_post_image_width', array(
	  'default' => '',
      'sanitize_callback' => 'elearning_education_sanitize_number_range',
	));
	$wp_customize->add_control(new elearning_education_Range_Slider($wp_customize, 'elearning_education_post_image_width', array(
       'section' => 'elearning_education_blog_option',
      'label' => esc_html__('Edit Post Image Width', 'elearning-education'),
      'input_attrs' => array(
        'min' => 0,
        'max' => 367,
        'step' => 1
    )
	)));

	$wp_customize->add_setting('elearning_education_post_image_length', array(
	  'default' => '',
      'sanitize_callback' => 'elearning_education_sanitize_number_range',
	));
	$wp_customize->add_control(new elearning_education_Range_Slider($wp_customize, 'elearning_education_post_image_length', array(
       'section' => 'elearning_education_blog_option',
      'label' => esc_html__('Edit Post Image height', 'elearning-education'),
      'input_attrs' => array(
        'min' => 0,
        'max' => 900,
        'step' => 1
    )
	)));
	
	$wp_customize->add_setting( 'elearning_education_remove_read_button', array(
	   'default'           => true,
	   'transport'         => 'refresh',
	   'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_remove_read_button', array(
		 'label'       => esc_html__( 'Show / Hide Read More Button', 'elearning-education' ),
		 'section'     => 'elearning_education_blog_option',
		 'type'        => 'toggle',
		 'settings'    => 'elearning_education_remove_read_button',
	) ) );
   	$wp_customize->selective_refresh->add_partial( 'elearning_education_remove_read_button', array(
		'selector' => '.readmore-btn',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_remove_read_button',
	) );
	$wp_customize->add_setting( 'elearning_education_remove_tags', array(
	 'default'           => true,
	 'transport'         => 'refresh',
	 'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	 ) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_remove_tags', array(
		 'label'       => esc_html__( 'Show / Hide Tags Option', 'elearning-education' ),
		 'section'     => 'elearning_education_blog_option',
		 'type'        => 'toggle',
		 'settings'    => 'elearning_education_remove_tags',
	) ) );
    $wp_customize->selective_refresh->add_partial( 'elearning_education_remove_tags', array(
		'selector' => '.box-content a[rel="tag"]',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_remove_tags',
	) );
	$wp_customize->add_setting( 'elearning_education_remove_category', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new eLearning_Education_Toggle_Control( $wp_customize, 'elearning_education_remove_category', array(
		'label'       => esc_html__( 'Show / Hide Category Option', 'elearning-education' ),
		'section'     => 'elearning_education_blog_option',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_remove_category',
	) ) );
    $wp_customize->selective_refresh->add_partial( 'elearning_education_remove_category', array(
		'selector' => '.box-content a[rel="category"]',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_remove_category',
	));
	$wp_customize->add_setting( 'elearning_education_remove_comment', array(
	 'default'           => true,
	 'transport'         => 'refresh',
	 'sanitize_callback' => 'elearning_education_sanitize_checkbox',
 	) );

	$wp_customize->add_control( new eLearning_Education_Toggle_Control( $wp_customize, 'elearning_education_remove_comment', array(
	 'label'       => esc_html__( 'Show / Hide Comment Form', 'elearning-education' ),
	 'section'     => 'elearning_education_blog_option',
	 'type'        => 'toggle',
	 'settings'    => 'elearning_education_remove_comment',
	) ) );

	$wp_customize->add_setting( 'elearning_education_remove_related_post', array(
	 'default'           => true,
	 'transport'         => 'refresh',
	 'sanitize_callback' => 'elearning_education_sanitize_checkbox',
 	) );

	$wp_customize->add_control( new eLearning_Education_Toggle_Control( $wp_customize, 'elearning_education_remove_related_post', array(
	 'label'       => esc_html__( 'Show / Hide Related Post', 'elearning-education' ),
	 'section'     => 'elearning_education_blog_option',
	 'type'        => 'toggle',
	 'settings'    => 'elearning_education_remove_related_post',
	) ) );

	$wp_customize->add_setting('elearning_education_related_post_heading',array(
		'default'=> __('Related Posts','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_related_post_heading',array(
		'label'	=> __('Related Post Title','elearning-education'),
		'section'=> 'elearning_education_blog_option',
		'type'=> 'text'
	));
	
	$wp_customize->add_setting( 'elearning_education_related_post_per_page', array(
		'default'              => 3,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'elearning_education_sanitize_number_range',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'elearning_education_related_post_per_page', array(
		'label'       => esc_html__( 'Related Post Per Page','elearning-education' ),
		'section'     => 'elearning_education_blog_option',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 3,
			'max'              => 9,
		),
	) );

	$wp_customize->add_setting( 'elearning_education_related_post_per_columns', array(
		'default'              => 3,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'elearning_education_sanitize_number_range',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'elearning_education_related_post_per_columns', array(
		'label'       => esc_html__( 'Related Post Per Row','elearning-education' ),
		'section'     => 'elearning_education_blog_option',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 4,
		),
	) );
	
	$wp_customize->add_setting('elearning_education_post_layout',array(
        'default' => 'image-content',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_post_layout',array(
        'type' => 'radio',
        'label'     => __('Post Layout', 'elearning-education'),
        'description' => __( 'Control Works only for full,left and right sidebar position in archieve posts', 'elearning-education' ),
        'section' => 'elearning_education_blog_option',
        'choices' => array(
            'image-content' => __('Media-Content','elearning-education'),
            'content-image' => __('Content-Media','elearning-education'),
        ),
	) );

	//Mobile Responsive
	$wp_customize->add_section('elearning_education_mobile_media_option',array(
		'title'         => __('Mobile Responsive media', 'elearning-education'),
		'description' => __('Control will not function if the toggle in the main settings is off.', 'elearning-education'),
		'priority' => 22,
		'panel' => 'elearning_education_panel_id'
	) );

	$wp_customize->add_setting( 'elearning_education_mobile_blog_description', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new elearning_education_Toggle_Control( $wp_customize, 'elearning_education_mobile_blog_description', array(
		'label'       => esc_html__( 'Show / Hide Blog Page Description', 'elearning-education' ),
		'section'     => 'elearning_education_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_mobile_blog_description',
	) ) );

	$wp_customize->add_setting( 'elearning_education_return_to_header_mob', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_return_to_header_mob', array(
		'label'       => esc_html__( 'Show / Hide Return to header', 'elearning-education' ),
		'section'     => 'elearning_education_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_return_to_header_mob',
	) ) );

	$wp_customize->add_setting( 'elearning_education_slider_buttom_mob', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_slider_buttom_mob', array(
		'label'       => esc_html__( 'Show / Hide Slider Button', 'elearning-education' ),
		'section'     => 'elearning_education_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_slider_buttom_mob',
	) ) );

	$wp_customize->add_setting( 'elearning_education_related_post_mob', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_related_post_mob', array(
		'label'       => esc_html__( 'Show / Hide Related Post', 'elearning-education' ),
		'section'     => 'elearning_education_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_related_post_mob',
	) ) );

	//Slider height
    $wp_customize->add_setting('elearning_education_slider_img_height_responsive',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('elearning_education_slider_img_height_responsive',array(
        'label' => __('Slider Height','elearning-education'),
        'description'   => __('Add slider height in px(eg. 700px).','elearning-education'),
        'section'=> 'elearning_education_mobile_media_option',
        'type'=> 'text'
    ));

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_responsive_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_responsive_pro_version_logo', array(
        'section'     => 'elearning_education_mobile_media_option',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//TP Preloader Option
	$wp_customize->add_section('elearning_education_prelaoder_option',array(
		'title'         => __('TP Preloader Option', 'elearning-education'),
		'priority' => 3,
		'panel' => 'elearning_education_panel_id'
	) );

	$wp_customize->add_setting( 'elearning_education_preloader_show_hide', array(
		'default'           => false,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_preloader_show_hide', array(
		'label'       => esc_html__( 'Show / Hide Preloader Option', 'elearning-education' ),
		'section'     => 'elearning_education_prelaoder_option',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_preloader_show_hide',
	) ) );

	$wp_customize->add_setting( 'elearning_education_tp_preloader_color1_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_tp_preloader_color1_option', array(
			'label'     => __('Preloader First Ring Color', 'elearning-education'),
	    'description' => __('It will change the complete theme preloader ring 1 color in one click.', 'elearning-education'),
	    'section' => 'elearning_education_prelaoder_option',
	    'settings' => 'elearning_education_tp_preloader_color1_option',
  	)));

  	$wp_customize->add_setting( 'elearning_education_tp_preloader_color2_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_tp_preloader_color2_option', array(
			'label'     => __('Preloader Second Ring Color', 'elearning-education'),
	    'description' => __('It will change the complete theme preloader ring 2 color in one click.', 'elearning-education'),
	    'section' => 'elearning_education_prelaoder_option',
	    'settings' => 'elearning_education_tp_preloader_color2_option',
  	)));

  	$wp_customize->add_setting( 'elearning_education_tp_preloader_bg_color_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_tp_preloader_bg_color_option', array(
			'label'     => __('Preloader Background Color', 'elearning-education'),
	    'description' => __('It will change the complete theme preloader bg color in one click.', 'elearning-education'),
	    'section' => 'elearning_education_prelaoder_option',
	    'settings' => 'elearning_education_tp_preloader_bg_color_option',
  	)));

  	// Pro Version
    $wp_customize->add_setting( 'elearning_education_preloader_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_preloader_pro_version_logo', array(
        'section'     => 'elearning_education_prelaoder_option',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

    	//TP Single Blog Option
	$wp_customize->add_section('elearning_education_single_blog_option',array(
        'title' => __('Single Post Option', 'elearning-education'),
        'priority' => 1,
        'panel' => 'elearning_education_panel_id'
    ) );

    /** Meta Order */
    $wp_customize->add_setting('elearning_education_single_blog_meta_order', array(
        'default' => array('date', 'author', 'comment','category', 'time'),
        'sanitize_callback' => 'elearning_education_sanitize_sortable',
    ));
    $wp_customize->add_control(new elearning_education_Control_Sortable($wp_customize, 'elearning_education_single_blog_meta_order', array(
    	'label' => esc_html__('Meta Order', 'elearning-education'),
        'description' => __('Drag & Drop post items to re-arrange the order and also hide and show items as per the need by clicking on the eye icon.', 'elearning-education') ,
        'section' => 'elearning_education_single_blog_option',
        'choices' => array(
            'date' => __('date', 'elearning-education') ,
            'author' => __('author', 'elearning-education') ,
            'comment' => __('comment', 'elearning-education') ,
            'category' => __('category', 'elearning-education') ,
            'time' => __('time', 'elearning-education') ,
        ) ,
    )));

    $wp_customize->add_setting('elearning_education_single_post_date_icon',array(
		'default'	=> 'far fa-calendar-alt',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
       $wp_customize,'elearning_education_single_post_date_icon',array(
		'label'	=> __('Change Date Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_single_blog_option',
		'type'		=> 'elearning-education-icon'
	)));

	$wp_customize->add_setting('elearning_education_single_post_author_icon',array(
		'default'	=> 'fas fa-user',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
       $wp_customize,'elearning_education_single_post_author_icon',array(
		'label'	=> __('Change Author Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_single_blog_option',
		'type'		=> 'elearning-education-icon'
	)));

	$wp_customize->add_setting('elearning_education_single_post_comment_icon',array(
		'default'	=> 'fas fa-comments',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
       $wp_customize,'elearning_education_single_post_comment_icon',array(
		'label'	=> __('Change Comment Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_single_blog_option',
		'type'		=> 'elearning-education-icon'
	)));

	$wp_customize->add_setting('elearning_education_single_post_category_icon',array(
		'default'	=> 'fas fa-list',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
       $wp_customize,'elearning_education_single_post_category_icon',array(
		'label'	=> __('Change Category Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_single_blog_option',
		'type'		=> 'elearning-education-icon'
	)));

	$wp_customize->add_setting('elearning_education_single_post_time_icon',array(
		'default'	=> 'fas fa-clock',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
       $wp_customize,'elearning_education_single_post_time_icon',array(
		'label'	=> __('Change Time Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_single_blog_option',
		'type'		=> 'elearning-education-icon'
	)));

  	//MENU TYPOGRAPHY
	$wp_customize->add_section( 'elearning_education_menu_typography', array(
    	'title'      => __( 'Menu Typography', 'elearning-education' ),
    	'priority' => 10,
		'panel' => 'elearning_education_panel_id'
	) );

	$wp_customize->add_setting('elearning_education_menu_font_weight',array(
        'default' => '',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_menu_font_weight',array(
     'type' => 'radio',
     'label'     => __('Font Weight', 'elearning-education'),
     'section' => 'elearning_education_menu_typography',
     'type' => 'select',
     'choices' => array(
         '100' => __('100','elearning-education'),
         '200' => __('200','elearning-education'),
         '300' => __('300','elearning-education'),
         '400' => __('400','elearning-education'),
         '500' => __('500','elearning-education'),
         '600' => __('600','elearning-education'),
         '700' => __('700','elearning-education'),
         '800' => __('800','elearning-education'),
         '900' => __('900','elearning-education')
     ),
	) );

	$wp_customize->add_setting('elearning_education_menu_text_tranform',array(
		'default' => '',
		'sanitize_callback' => 'elearning_education_sanitize_choices'
 	));
 	$wp_customize->add_control('elearning_education_menu_text_tranform',array(
		'type' => 'select',
		'label' => __('Menu Text Transform','elearning-education'),
		'section' => 'elearning_education_menu_typography',
		'choices' => array(
		   'Uppercase' => __('Uppercase','elearning-education'),
		   'Lowercase' => __('Lowercase','elearning-education'),
		   'Capitalize' => __('Capitalize','elearning-education'),
		),
	) );

	$wp_customize->add_setting('elearning_education_menu_font_size', array(
	'default' => '',
    'sanitize_callback' => 'elearning_education_sanitize_number_range',
	));

	$wp_customize->add_control(new Elearning_Education_Range_Slider($wp_customize, 'elearning_education_menu_font_size', array(
    'section' => 'elearning_education_menu_typography',
    'label' => esc_html__('Font Size', 'elearning-education'),
    'input_attrs' => array(
        'min' => 0,
        'max' => 20,
        'step' => 1
    )
	)));

	$wp_customize->add_setting('elearning_education_menus_item_style',array(
		'default' => '',
		'transport' => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_menus_item_style',array(
		'type' => 'select',
		'section' => 'elearning_education_menu_typography',
		'label' => __('Menu Hover Effect','elearning-education'),
		'choices' => array(
			'None' => __('None','elearning-education'),
			'Zoom In' => __('Zoom In','elearning-education'),
		),
	) );

	$wp_customize->add_setting( 'elearning_education_menu_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_menu_color', array(
			'label'     => __('Change Menu Color', 'elearning-education'),
	    'section' => 'elearning_education_menu_typography',
	    'settings' => 'elearning_education_menu_color',
  	)));

  	// Pro Version
    $wp_customize->add_setting( 'elearning_education_menu_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_menu_pro_version_logo', array(
        'section'     => 'elearning_education_menu_typography',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	// Top Bar
	$wp_customize->add_section( 'elearning_education_topbar', array(
    	'title'      => __( 'Contact Details', 'elearning-education' ),
    	'description' => __( 'Add your contact details', 'elearning-education' ),
    	'priority' => 12,
		'panel' => 'elearning_education_panel_id'
	) );

	$wp_customize->add_setting('elearning_education_phone_number',array(
		'default'=> '',
		'sanitize_callback'	=> 'elearning_education_sanitize_phone_number'
	));
	$wp_customize->add_control('elearning_education_phone_number',array(
		'label'	=> __('Add Phone Number','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('elearning_education_phone_icon',array(
		'default'	=> 'fas fa-phone',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_phone_icon',array(
		'label'	=> __('Phone Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->selective_refresh->add_partial( 'elearning_education_phone_number', array(
		'selector' => '.top-header span',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_phone_number',
	) );

	$wp_customize->add_setting('elearning_education_email_address',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_email'
	));
	$wp_customize->add_control('elearning_education_email_address',array(
		'label'	=> __('Add Mail Address','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('elearning_education_mail_icon',array(
		'default'	=> 'far fa-envelope',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_mail_icon',array(
		'label'	=> __('Mail Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_topbar',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('elearning_education_register_button',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_register_button',array(
		'label'	=> __('Add Button Text','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('elearning_education_register_button_link',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_register_button_link',array(
		'label'	=> __('Add Button URL','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'url'
	));

	$wp_customize->add_setting('elearning_education_login_button',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_login_button',array(
		'label'	=> __('Add Button Text','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('elearning_education_login_button_link',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_login_button_link',array(
		'label'	=> __('Add Button URL','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'url'
	));

	$wp_customize->add_setting('elearning_education_header_teacher',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_header_teacher',array(
		'label'	=> __('Add Become Teacher Text','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'text'
	));

	$wp_customize->selective_refresh->add_partial( 'elearning_education_header_teacher', array(
		'selector' => 'a.teacher-btn',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_header_teacher',
	) );

	$wp_customize->add_setting('elearning_education_header_wishlist_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_header_wishlist_url',array(
		'label'	=> __('Add Wishlist Link','elearning-education'),
		'section'=> 'elearning_education_topbar',
		'type'=> 'url'
	));

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_header_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_header_pro_version_logo', array(
        'section'     => 'elearning_education_topbar',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	// Social Media
	$wp_customize->add_section( 'elearning_education_social_media', array(
    	'title'      => __( 'Social Media Links', 'elearning-education' ),
    	'description' => __( 'Add your Social Links', 'elearning-education' ),
    	'priority' => 14,
		'panel' => 'elearning_education_panel_id'
	) );

	$wp_customize->add_setting( 'elearning_education_header_fb_new_tab', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_header_fb_new_tab', array(
		'label'       => esc_html__( 'Open in new tab', 'elearning-education' ),
		'section'     => 'elearning_education_social_media',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_header_fb_new_tab',
	) ) );

	$wp_customize->add_setting('elearning_education_facebook_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_facebook_url',array(
		'label'	=> __('Facebook Link','elearning-education'),
		'section'=> 'elearning_education_social_media',
		'type'=> 'url'
	));
	 $wp_customize->add_setting('elearning_education_facebook_icon',array(
		'default'	=> 'fab fa-facebook-f',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_facebook_icon',array(
		'label'	=> __('Facebook Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_social_media',
		'type'		=> 'icon'
	)));
	$wp_customize->selective_refresh->add_partial( 'elearning_education_facebook_url', array(
		'selector' => '.media-links a',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_facebook_url',
	) );

	$wp_customize->add_setting( 'elearning_education_header_twt_new_tab', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_header_twt_new_tab', array(
		'label'       => esc_html__( 'Open in new tab', 'elearning-education' ),
		'section'     => 'elearning_education_social_media',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_header_twt_new_tab',
	) ) );

	$wp_customize->add_setting('elearning_education_twitter_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_twitter_url',array(
		'label'	=> __('Twitter Link','elearning-education'),
		'section'=> 'elearning_education_social_media',
		'type'=> 'url'
	));

	$wp_customize->add_setting('elearning_education_twitter_icon',array(
		'default'	=> 'fab fa-twitter',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_twitter_icon',array(
		'label'	=> __('Twitter Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_social_media',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting( 'elearning_education_header_ins_new_tab', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_header_ins_new_tab', array(
		'label'       => esc_html__( 'Open in new tab', 'elearning-education' ),
		'section'     => 'elearning_education_social_media',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_header_ins_new_tab',
	) ) );

	$wp_customize->add_setting('elearning_education_instagram_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_instagram_url',array(
		'label'	=> __('Instagram Link','elearning-education'),
		'section'=> 'elearning_education_social_media',
		'type'=> 'url'
	));

	$wp_customize->add_setting('elearning_education_instagram_icon',array(
		'default'	=> 'fab fa-instagram',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_instagram_icon',array(
		'label'	=> __('Instagram Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_social_media',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting( 'elearning_education_header_ut_new_tab', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_header_ut_new_tab', array(
		'label'       => esc_html__( 'Open in new tab', 'elearning-education' ),
		'section'     => 'elearning_education_social_media',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_header_ut_new_tab',
	) ) );

	$wp_customize->add_setting('elearning_education_youtube_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_youtube_url',array(
		'label'	=> __('YouTube Link','elearning-education'),
		'section'=> 'elearning_education_social_media',
		'type'=> 'url'
	));

	$wp_customize->add_setting('elearning_education_youtube_icon',array(
		'default'	=> 'fab fa-youtube',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_youtube_icon',array(
		'label'	=> __('YouTube Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_social_media',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting( 'elearning_education_header_pint_new_tab', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_header_pint_new_tab', array(
		'label'       => esc_html__( 'Open in new tab', 'elearning-education' ),
		'section'     => 'elearning_education_social_media',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_header_pint_new_tab',
	) ) );

	$wp_customize->add_setting('elearning_education_pinterest_icon',array(
		'default'	=> 'fab fa-pinterest',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_pinterest_icon',array(
		'label'	=> __('Pinterest Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_social_media',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('elearning_education_pint_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('elearning_education_pint_url',array(
		'label'	=> __('Pinterest Link','elearning-education'),
		'section'=> 'elearning_education_social_media',
		'type'=> 'url'
	));

	$wp_customize->add_setting('elearning_education_social_icon_fontsize',array(
		'default'=> '12',
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_social_icon_fontsize',array(
		'label'	=> __('Social Icons Font Size in PX','elearning-education'),
		'type'=> 'number',
		'section'=> 'elearning_education_social_media',
		'input_attrs' => array(
			'step' => 1,
			'min'  => 0,
			'max'  => 100,
				),
	));

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_social_media_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_social_media_pro_version_logo', array(
        'section'     => 'elearning_education_social_media',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//home page slider
	$wp_customize->add_section( 'elearning_education_slider_section' , array(
    	'title'      => __( 'Slider Section', 'elearning-education' ),
    	'priority' => 16,
		'panel' => 'elearning_education_panel_id'
	) );

	$wp_customize->add_setting('elearning_education_slider_arrows',array(
       'default' => true,
       'sanitize_callback'	=> 'elearning_education_sanitize_checkbox'
    ));
		$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_slider_arrows', array(
			'label'       => esc_html__( 'Show / Hide Slider', 'elearning-education' ),
			'priority' => 1,
			'section'     => 'elearning_education_slider_section',
			'type'        => 'toggle',
			'settings'    => 'elearning_education_slider_arrows',
		) ) );

	$wp_customize->add_setting( 'elearning_education_show_slider_title', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new elearning_education_Toggle_Control( $wp_customize, 'elearning_education_show_slider_title', array(
		'label'       => esc_html__( 'Show / Hide Slider Heading', 'elearning-education' ),
		'priority' => 1,
		'section'     => 'elearning_education_slider_section',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_show_slider_title',
	) ) );

	$wp_customize->add_setting( 'elearning_education_show_slider_content', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new elearning_education_Toggle_Control( $wp_customize, 'elearning_education_show_slider_content', array(
		'label'       => esc_html__( 'Show / Hide Slider Content', 'elearning-education' ),
		'section'     => 'elearning_education_slider_section',
		'priority' => 1,
		'type'        => 'toggle',
		'settings'    => 'elearning_education_show_slider_content',
	) ) );

    $wp_customize->selective_refresh->add_partial( 'elearning_education_slider_arrows', array(
		'selector' => '#slider .carousel-caption',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_slider_arrows',
	) );

	for ( $elearning_education_count = 1; $elearning_education_count <= 4; $elearning_education_count++ ) {

		$wp_customize->add_setting( 'elearning_education_slider_page' . $elearning_education_count, array(
			'default'           => '',
			'sanitize_callback' => 'elearning_education_sanitize_dropdown_pages'
		) );

		$wp_customize->add_control( 'elearning_education_slider_page' . $elearning_education_count, array(
			'label'    => __( 'Select Slide Image Page', 'elearning-education' ),
			'priority' => 1,
			'section'  => 'elearning_education_slider_section',
			'type'     => 'dropdown-pages'
		) );
	}

	$wp_customize->add_setting('elearning_education_slider_top',array(
	    'default'=> '',
	    'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_slider_top',array(
	    'label' => __('Add Slider Top Text','elearning-education'),
	    'section'=> 'elearning_education_slider_section',
	    'type'=> 'text'
	));

	$wp_customize->add_setting( 'elearning_education_slider_button', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_slider_button', array(
		'label'       => esc_html__( 'Show / Hide Slider Button', 'elearning-education' ),
		'section'     => 'elearning_education_slider_section',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_slider_button',
	) ) );

	//Slider excerpt
	$wp_customize->add_setting( 'elearning_education_slider_excerpt_length', array(
		'default'              => 18,
		'sanitize_callback'	=> 'absint',
	) );
	$wp_customize->add_control( 'elearning_education_slider_excerpt_length', array(
		'label'       => esc_html__( 'Slider Excerpt length','elearning-education' ),
		'section'     => 'elearning_education_slider_section',
		'type'        => 'number',
		'settings'    => 'elearning_education_slider_excerpt_length',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	) );

	$wp_customize->add_setting( 'elearning_education_slider_opacity_setting', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new elearning_education_Toggle_Control( $wp_customize, 'elearning_education_slider_opacity_setting', array(
		'label'       => esc_html__( 'Show / Hide Image Opacity', 'elearning-education' ),
		'section'     => 'elearning_education_slider_section',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_slider_opacity_setting',
	) ) );

    $wp_customize->add_setting( 'elearning_education_image_opacity_color', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_image_opacity_color', array(
        'label' => __('Slider Image Opacity Color', 'elearning-education'),
        'section' => 'elearning_education_slider_section',
        'settings' => 'elearning_education_image_opacity_color',
    )));

    $wp_customize->add_setting('elearning_education_slider_opacity',array(
        'default'=> '',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
    ));
    $wp_customize->add_control('elearning_education_slider_opacity',array(
        'type' => 'select',
        'label' => esc_html__('Slider Image Opacity','elearning-education'),
        'choices' => array(
            '0'   => '0',
            '0.1' => '0.1',
            '0.2' => '0.2',
            '0.3' => '0.3',
            '0.4' => '0.4',
            '0.5' => '0.5',
            '0.6' => '0.6',
            '0.7' => '0.7',
            '0.8' => '0.8',
            '0.9' => '0.9',
            '1'   => '1',
        ),
        'section'=> 'elearning_education_slider_section',
    ));

    //Slider height
    $wp_customize->add_setting('elearning_education_slider_img_height',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('elearning_education_slider_img_height',array(
        'label' => __('Slider Height','elearning-education'),
        'description'   => __('Add slider height in px(eg. 700px).','elearning-education'),
        'section'=> 'elearning_education_slider_section',
        'type'=> 'text'
    ));
	
	$wp_customize->add_setting('elearning_education_slider_content_layout',array(
        'default' => '',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_slider_content_layout',array(
        'type' => 'radio',
        'label'     => __('Slider Content Layout', 'elearning-education'),
        'section' => 'elearning_education_slider_section',
        'choices' => array(
            'CENTER-ALIGN' => __('CENTER-ALIGN','elearning-education'),
            'LEFT-ALIGN' => __('LEFT-ALIGN','elearning-education'),
            'RIGHT-ALIGN' => __('RIGHT-ALIGN','elearning-education'),
        ),
	) );

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_slider_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_slider_pro_version_logo', array(
        'section'     => 'elearning_education_slider_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//Online Courses Section
	$wp_customize->add_section('elearning_education_online_courses_section',array(
		'title'	=> __('Online Courses Section','elearning-education'),
		'priority' => 18,
		'panel' => 'elearning_education_panel_id',
	));

	$wp_customize->add_setting( 'elearning_education_online_courses_enable', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_online_courses_enable', array(
		'label'       => esc_html__( 'Show / Hide section', 'elearning-education' ),
		'section'     => 'elearning_education_online_courses_section',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_online_courses_enable',
	) ) );

	$wp_customize->add_setting('elearning_education_online_courses_heading',array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_online_courses_heading',array(
		'label'	=> __('Courses Title','elearning-education'),
		'section'	=> 'elearning_education_online_courses_section',
		'type'		=> 'text'
	));
	$wp_customize->selective_refresh->add_partial( 'elearning_education_online_courses_heading', array(
		'selector' => '#online-courses h2',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_online_courses_heading',
	) );

	$wp_customize->add_setting('elearning_education_online_courses_per_page',array(
		'default' => '',
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_online_courses_per_page',array(
		'label'	=> __('Number Of Courses','elearning-education'),
		'section'	=> 'elearning_education_online_courses_section',
		'type'		=> 'number'
	));

	$elearning_education_args = array(
		'type'                     => 'lp_course',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'term_group',
		'order'                    => 'ASC',
		'hide_empty'               => false,
		'hierarchical'             => 1,
		'number'                   => '',
		'taxonomy'                 => 'course_category',
		'pad_counts'               => false
	);
	$elearning_education_categories = get_categories($elearning_education_args);
	$cat_posts = array();
	$m = 0;
	$cat_posts[]='Select';
	foreach($elearning_education_categories as $category){
		if($m==0){
			$default = $category->slug;
			$m++;
		}
		$cat_posts[$category->slug] = $category->name;
	}

	$wp_customize->add_setting('elearning_education_online_courses_category',array(
		'default'	=> 'select',
		'priority' => 8,
		'sanitize_callback' => 'elearning_education_sanitize_select',
	));
	$wp_customize->add_control('elearning_education_online_courses_category',array(
		'type'    => 'select',
		'choices' => $cat_posts,
		'label' => __('Select category to display courses ','elearning-education'),
		'section' => 'elearning_education_online_courses_section',
	));

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_about_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_about_pro_version_logo', array(
        'section'     => 'elearning_education_online_courses_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//footer
	$wp_customize->add_section('elearning_education_footer_section',array(
		'title'	=> __('Footer Widget Settings','elearning-education'),
		'priority' => 20,
		'panel' => 'elearning_education_panel_id'
	));

	// footer columns
	$wp_customize->add_setting('elearning_education_footer_columns',array(
		'default'	=> 4,
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_footer_columns',array(
		'label'	=> __('Footer Widget Columns','elearning-education'),
		'section'	=> 'elearning_education_footer_section',
		'setting'	=> 'elearning_education_footer_columns',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 4,
		),
	));

	$wp_customize->add_setting( 'elearning_education_tp_footer_bg_color_option', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_tp_footer_bg_color_option', array(
			'label'     => __('Footer Widget Background Color', 'elearning-education'),
			'description' => __('It will change the complete footer widget backgorund color.', 'elearning-education'),
			'section' => 'elearning_education_footer_section',
			'settings' => 'elearning_education_tp_footer_bg_color_option',
	)));

	$wp_customize->selective_refresh->add_partial( 'elearning_education_footer_text', array(
		'selector' => '#footer p',
		'render_callback' => 'elearning_education_customize_partial_elearning_education_footer_text',
	) );

	$wp_customize->add_setting('elearning_education_footer_widget_image',array(
		'default'	=> '',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'elearning_education_footer_widget_image',array(
    'label' => __('Footer Widget Background Image','elearning-education'),
    'section' => 'elearning_education_footer_section'
	)));

	//footer widget title font size
	$wp_customize->add_setting('elearning_education_footer_widget_title_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_footer_widget_title_font_size',array(
		'label'	=> __('Change Footer Widget Title Font Size in PX','elearning-education'),
		'section'	=> 'elearning_education_footer_section',
	    'setting'	=> 'elearning_education_footer_widget_title_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	$wp_customize->add_setting( 'elearning_education_footer_widget_title_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_footer_widget_title_color', array(
			'label'     => __('Change Footer Widget Title Color', 'elearning-education'),
	    'section' => 'elearning_education_footer_section',
	    'settings' => 'elearning_education_footer_widget_title_color',
  	)));

  	$wp_customize->add_setting('elearning_education_footer_widget_title_font_weight',array(
        'default' => '',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_footer_widget_title_font_weight',array(
     'type' => 'radio',
     'label'     => __('Change Footer Widget Title Font Weight', 'elearning-education'),
     'section' => 'elearning_education_footer_section',
     'type' => 'select',
     'choices' => array(
         '100' => __('100','elearning-education'),
         '200' => __('200','elearning-education'),
         '300' => __('300','elearning-education'),
         '400' => __('400','elearning-education'),
         '500' => __('500','elearning-education'),
         '600' => __('600','elearning-education'),
         '700' => __('700','elearning-education'),
         '800' => __('800','elearning-education'),
         '900' => __('900','elearning-education')
     ),
	) );

	$wp_customize->add_setting('elearning_education_footer_widget_title_text_tranform',array(
		'default' => '',
		'sanitize_callback' => 'elearning_education_sanitize_choices'
 	));
 	$wp_customize->add_control('elearning_education_footer_widget_title_text_tranform',array(
		'type' => 'select',
		'label' => __('Change Footer Widget Title Letter Case','elearning-education'),
		'section' => 'elearning_education_footer_section',
		'choices' => array(
		   'Uppercase' => __('Uppercase','elearning-education'),
		   'Lowercase' => __('Lowercase','elearning-education'),
		   'Capitalize' => __('Capitalize','elearning-education'),
		),
	) );

	// Add Settings and Controls for position
	$wp_customize->add_setting('elearning_education_footer_widget_title_position',array(
        'default' => '',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_footer_widget_title_position',array(
        'type' => 'radio',
        'label'     => __('Change Footer Widget Position', 'elearning-education'),
        'description'   => __('This option work for Footer Widget', 'elearning-education'),
        'section' => 'elearning_education_footer_section',
        'choices' => array(
            'Right' => __('Right','elearning-education'),
            'Left' => __('Left','elearning-education'),
            'Center' => __('Center','elearning-education')
        ),
	) );

	$wp_customize->add_setting('elearning_education_return_to_header',array(
		 'default' => true,
		 'sanitize_callback'	=> 'elearning_education_sanitize_checkbox'
	));
	$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_return_to_header', array(
		'label'       => esc_html__( 'Show / Hide Return To Header', 'elearning-education' ),
		'section'     => 'elearning_education_footer_section',
		'type'        => 'toggle',
		'settings'    => 'elearning_education_return_to_header',
	) ) );

	 $wp_customize->add_setting('elearning_education_scroll_top_icon',array(
		'default'	=> 'fas fa-arrow-up',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Elearning_Education_Icon_Changer(
        $wp_customize,'elearning_education_scroll_top_icon',array(
		'label'	=> __('Scroll to top Icon','elearning-education'),
		'transport' => 'refresh',
		'section'	=> 'elearning_education_footer_section',
		'type'		=> 'icon'
	)));

   // Add Settings and Controls for Scroll top
	$wp_customize->add_setting('elearning_education_scroll_top_position',array(
        'default' => 'Right',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_scroll_top_position',array(
        'type' => 'radio',
        'label'     => __('Scroll to top Position', 'elearning-education'),
        'description'   => __('This option work for scroll to top', 'elearning-education'),
        'section' => 'elearning_education_footer_section',
        'choices' => array(
            'Right' => __('Right','elearning-education'),
            'Left' => __('Left','elearning-education'),
            'Center' => __('Center','elearning-education')
        ),
	) );

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_footer_widget_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_footer_widget_pro_version_logo', array(
        'section'     => 'elearning_education_footer_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//footer
	$wp_customize->add_section('elearning_education_footer_copyright_section',array(
		'title'	=> __('Footer Copyright Settings','elearning-education'),
		'priority' => 20,
		'description'	=> __('Add copyright text.','elearning-education'),
		'panel' => 'elearning_education_panel_id'
	));

	$wp_customize->add_setting('elearning_education_footer_text',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_footer_text',array(
		'label'	=> __('Copyright Text','elearning-education'),
		'section'	=> 'elearning_education_footer_copyright_section',
		'type'		=> 'text'
	));

	//footer widget title font size
	$wp_customize->add_setting('elearning_education_footer_copyright_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_footer_copyright_font_size',array(
		'label'	=> __('Change Footer Copyright Font Size in PX','elearning-education'),
		'section'	=> 'elearning_education_footer_copyright_section',
	    'setting'	=> 'elearning_education_footer_copyright_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	$wp_customize->add_setting('elearning_education_footer_copyright_title_font_weight',array(
        'default' => '',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_footer_copyright_title_font_weight',array(
     'type' => 'radio',
     'label'     => __('Change Footer Copyright Text Font Weight', 'elearning-education'),
     'section' => 'elearning_education_footer_copyright_section',
     'type' => 'select',
     'choices' => array(
         '100' => __('100','elearning-education'),
         '200' => __('200','elearning-education'),
         '300' => __('300','elearning-education'),
         '400' => __('400','elearning-education'),
         '500' => __('500','elearning-education'),
         '600' => __('600','elearning-education'),
         '700' => __('700','elearning-education'),
         '800' => __('800','elearning-education'),
         '900' => __('900','elearning-education')
     ),
	) );

	$wp_customize->add_setting( 'elearning_education_footer_copyright_text_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_footer_copyright_text_color', array(
			'label'     => __('Change Footer Copyright Text Color', 'elearning-education'),
	    'section' => 'elearning_education_footer_copyright_section',
	    'settings' => 'elearning_education_footer_copyright_text_color',
  	)));

  	$wp_customize->add_setting('elearning_education_footer_copyright_top_bottom_padding',array(
		'default'	=> '',
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_footer_copyright_top_bottom_padding',array(
		'label'	=> __('Change Footer Copyright Padding in PX','elearning-education'),
		'section'	=> 'elearning_education_footer_copyright_section',
	    'setting'	=> 'elearning_education_footer_copyright_top_bottom_padding',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	// Add Settings and Controls for copyright
	$wp_customize->add_setting('elearning_education_copyright_text_position',array(
        'default' => 'Center',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_copyright_text_position',array(
        'type' => 'radio',
        'label'     => __('Copyright Text Position', 'elearning-education'),
        'description'   => __('This option work for Copyright', 'elearning-education'),
        'section' => 'elearning_education_footer_copyright_section',
        'choices' => array(
            'Right' => __('Right','elearning-education'),
            'Left' => __('Left','elearning-education'),
            'Center' => __('Center','elearning-education')
        ),
       ) );

	// Pro Version
    $wp_customize->add_setting( 'elearning_education_copyright_pro_version_logo', array(
        'sanitize_callback' => 'elearning_education_sanitize_custom_control'
    ));
    $wp_customize->add_control( new elearning_education_Customize_Pro_Version ( $wp_customize,'elearning_education_copyright_pro_version_logo', array(
        'section'     => 'elearning_education_footer_copyright_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'elearning-education' ),
        'description' => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ),
        'priority'    => 100
    )));

	$wp_customize->get_setting( 'blogname' )->transport          = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport   = 'postMessage';

	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'elearning_education_customize_partial_blogname',
	) );
	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'elearning_education_customize_partial_blogdescription',
	) );

		$wp_customize->add_setting('elearning_education_site_title_text',array(
			 'default' => true,
			 'sanitize_callback'	=> 'elearning_education_sanitize_checkbox'
		));
		$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_site_title_text', array(
			'label'       => esc_html__( 'Show / Hide Site Title', 'elearning-education' ),
			'section'     => 'title_tagline',
			'type'        => 'toggle',
			'settings'    => 'elearning_education_site_title_text',
		) ) );

		// logo site title size
		$wp_customize->add_setting('elearning_education_site_title_font_size',array(
			'default'	=> 30,
			'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
		));
		$wp_customize->add_control('elearning_education_site_title_font_size',array(
			'label'	=> __('Site Title Font Size in PX','elearning-education'),
			'section'	=> 'title_tagline',
			'setting'	=> 'elearning_education_site_title_font_size',
			'type'	=> 'number',
			'input_attrs' => array(
				'step'             => 1,
				'min'              => 0,
				'max'              => 80,
			),
		));

		$wp_customize->add_setting( 'elearning_education_site_tagline_color', array(
		    'default' => '',
		    'sanitize_callback' => 'sanitize_hex_color'
	  	));
	  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_site_tagline_color', array(
				'label'     => __('Change Site Title Color', 'elearning-education'),
		    'section' => 'title_tagline',
		    'settings' => 'elearning_education_site_tagline_color',
	  	)));

		$wp_customize->add_setting('elearning_education_site_tagline_text',array(
			 'default' => false,
			 'sanitize_callback'	=> 'elearning_education_sanitize_checkbox'
		));
		$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_site_tagline_text', array(
			'label'       => esc_html__( 'Show / Hide Site Tagline', 'elearning-education' ),
			'section'     => 'title_tagline',
			'type'        => 'toggle',
			'settings'    => 'elearning_education_site_tagline_text',
		) ) );


		// logo site tagline size
	$wp_customize->add_setting('elearning_education_site_tagline_font_size',array(
		'default'	=> 15,
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_site_tagline_font_size',array(
		'label'	=> __('Site Tagline Font Size in PX','elearning-education'),
		'section'	=> 'title_tagline',
		'setting'	=> 'elearning_education_site_tagline_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	$wp_customize->add_setting( 'elearning_education_logo_tagline_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_logo_tagline_color', array(
			'label'     => __('Change Site Tagline Color', 'elearning-education'),
	    'section' => 'title_tagline',
	    'settings' => 'elearning_education_logo_tagline_color',
  	)));

    $wp_customize->add_setting('elearning_education_logo_width',array(
		'default' => 100,
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	 $wp_customize->add_control('elearning_education_logo_width',array(
		'label'	=> esc_html__('Here You Can Customize Your Logo Size','elearning-education'),
		'section'	=> 'title_tagline',
		'type'		=> 'number'
	));

	$wp_customize->add_setting('elearning_education_logo_settings',array(
        'default' => 'Different Line',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
    $wp_customize->add_control('elearning_education_logo_settings',array(
        'type' => 'radio',
        'label'     => __('Logo Layout Settings', 'elearning-education'),
        'description'   => __('Here you have two options 1. Logo and Site tite in differnt line. 2. Logo and Site title in same line.', 'elearning-education'),
        'section' => 'title_tagline',
        'choices' => array(
            'Different Line' => __('Different Line','elearning-education'),
            'Same Line' => __('Same Line','elearning-education')
        ),
	) );

	$wp_customize->add_setting('elearning_education_per_columns',array(
		'default'=> 3,
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_per_columns',array(
		'label'	=> __('Product Per Row','elearning-education'),
		'section'=> 'woocommerce_product_catalog',
		'type'=> 'number'
	));

	$wp_customize->add_setting('elearning_education_product_per_page',array(
		'default'=> 9,
		'sanitize_callback'	=> 'elearning_education_sanitize_number_absint'
	));
	$wp_customize->add_control('elearning_education_product_per_page',array(
		'label'	=> __('Product Per Page','elearning-education'),
		'section'=> 'woocommerce_product_catalog',
		'type'=> 'number'
	));

		$wp_customize->add_setting('elearning_education_product_sidebar',array(
			 'default' => true,
			 'sanitize_callback'	=> 'elearning_education_sanitize_checkbox'
		));
		$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_product_sidebar', array(
			'label'       => esc_html__( 'Show / Hide Shop Page Sidebar', 'elearning-education' ),
			'section'     => 'woocommerce_product_catalog',
			'type'        => 'toggle',
			'settings'    => 'elearning_education_product_sidebar',
		) ) );

		$wp_customize->add_setting('elearning_education_single_product_sidebar',array(
			 'default' => true,
			 'sanitize_callback'	=> 'elearning_education_sanitize_checkbox'
		));
		$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_single_product_sidebar', array(
			'label'       => esc_html__( 'Show / Hide Product Page Sidebar', 'elearning-education' ),
			'section'     => 'woocommerce_product_catalog',
			'type'        => 'toggle',
			'settings'    => 'elearning_education_single_product_sidebar',
		) ) );

		$wp_customize->add_setting( 'elearning_education_related_product', array(
			'default'           => true,
			'transport'         => 'refresh',
			'sanitize_callback' => 'elearning_education_sanitize_checkbox',
		) );
		$wp_customize->add_control( new Elearning_Education_Toggle_Control( $wp_customize, 'elearning_education_related_product', array(
			'label'       => esc_html__( 'Show / Hide related product', 'elearning-education' ),
			'section'     => 'woocommerce_product_catalog',
			'type'        => 'toggle',
			'settings'    => 'elearning_education_related_product',
		) ) );
		
	//Page template settings
	$wp_customize->add_panel( 'elearning_education_page_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Page Template Settings', 'elearning-education' ),
	    'description' => __( 'Description of what this panel does.', 'elearning-education' ),
	) );

	// 404 PAGE
	$wp_customize->add_section('elearning_education_404_page_section',array(
		'title'         => __('404 Page', 'elearning-education'),
		'description'   => 'Here you can customize 404 Page content.',
		'panel' => 'elearning_education_page_panel_id'
	) );

	$wp_customize->add_setting('elearning_education_edit_404_title',array(
		'default'=> __('Oops! That page cant be found.','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	$wp_customize->add_control('elearning_education_edit_404_title',array(
		'label'	=> __('Edit Title','elearning-education'),
		'section'=> 'elearning_education_404_page_section',
		'type'=> 'text',
	));

	$wp_customize->add_setting('elearning_education_edit_404_text',array(
		'default'=> __('It looks like nothing was found at this location. Maybe try a search?','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_edit_404_text',array(
		'label'	=> __('Edit Text','elearning-education'),
		'section'=> 'elearning_education_404_page_section',
		'type'=> 'text'
	));

	// Search Results
	$wp_customize->add_section('elearning_education_no_result_section',array(
		'title'         => __('Search Results', 'elearning-education'),
		'description'   => 'Here you can customize Search Result content.',
		'panel' => 'elearning_education_page_panel_id'
	) );

	$wp_customize->add_setting('elearning_education_edit_no_result_title',array(
		'default'=> __('Nothing Found','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	$wp_customize->add_control('elearning_education_edit_no_result_title',array(
		'label'	=> __('Edit Title','elearning-education'),
		'section'=> 'elearning_education_no_result_section',
		'type'=> 'text',
	));

	$wp_customize->add_setting('elearning_education_edit_no_result_text',array(
		'default'=> __('Sorry, but nothing matched your search terms. Please try again with some different keywords.','elearning-education'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('elearning_education_edit_no_result_text',array(
		'label'	=> __('Edit Text','elearning-education'),
		'section'=> 'elearning_education_no_result_section',
		'type'=> 'text'
	));

	// Header Image Height
    $wp_customize->add_setting(
        'elearning_education_header_image_height',
        array(
            'default'           => 350,
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control(
        'elearning_education_header_image_height',
        array(
            'label'       => esc_html__( 'Header Image Height', 'elearning-education' ),
            'section'     => 'header_image',
            'type'        => 'number',
            'description' => esc_html__( 'Control the height of the header image. Default is 350px.', 'elearning-education' ),
            'input_attrs' => array(
                'min'  => 220,
                'max'  => 1000,
                'step' => 1,
            ),
        )
    );

    // Header Background Position
    $wp_customize->add_setting(
        'elearning_education_header_background_position',
        array(
            'default'           => 'center',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'elearning_education_header_background_position',
        array(
            'label'       => esc_html__( 'Header Background Position', 'elearning-education' ),
            'section'     => 'header_image',
            'type'        => 'select',
            'choices'     => array(
                'top'    => esc_html__( 'Top', 'elearning-education' ),
                'center' => esc_html__( 'Center', 'elearning-education' ),
                'bottom' => esc_html__( 'Bottom', 'elearning-education' ),
            ),
            'description' => esc_html__( 'Choose how you want to position the header image.', 'elearning-education' ),
        )
    );

    // Header Image Parallax Effect
    $wp_customize->add_setting(
        'elearning_education_header_background_attachment',
        array(
            'default'           => 1,
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control(
        'elearning_education_header_background_attachment',
        array(
            'label'       => esc_html__( 'Header Image Parallax', 'elearning-education' ),
            'section'     => 'header_image',
            'type'        => 'checkbox',
            'description' => esc_html__( 'Add a parallax effect on page scroll.', 'elearning-education' ),
        )
    );

    //Opacity
	$wp_customize->add_setting('elearning_education_header_banner_opacity_color',array(
       'default'              => '0.7',
       'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
    $wp_customize->add_control( 'elearning_education_header_banner_opacity_color', array(
		'label'       => esc_html__( 'Header Image Opacity','elearning-education' ),
		'section'     => 'header_image',
		'type'        => 'select',
		'settings'    => 'elearning_education_header_banner_opacity_color',
		'choices' => array(
           '0' =>  esc_attr(__('0','elearning-education')),
           '0.1' =>  esc_attr(__('0.1','elearning-education')),
           '0.2' =>  esc_attr(__('0.2','elearning-education')),
           '0.3' =>  esc_attr(__('0.3','elearning-education')),
           '0.4' =>  esc_attr(__('0.4','elearning-education')),
           '0.5' =>  esc_attr(__('0.5','elearning-education')),
           '0.6' =>  esc_attr(__('0.6','elearning-education')),
           '0.7' =>  esc_attr(__('0.7','elearning-education')),
           '0.8' =>  esc_attr(__('0.8','elearning-education')),
           '0.9' =>  esc_attr(__('0.9','elearning-education'))
		), 
	) );

   $wp_customize->add_setting( 'elearning_education_header_banner_image_overlay', array(
	    'default'   => true,
	    'transport' => 'refresh',
	    'sanitize_callback' => 'elearning_education_sanitize_checkbox',
	));
	$wp_customize->add_control( new elearning_education_Toggle_Control( $wp_customize, 'elearning_education_header_banner_image_overlay', array(
	    'label'   => esc_html__( 'Show / Hide Header Image Overlay', 'elearning-education' ),
	    'section' => 'header_image',
	)));

    $wp_customize->add_setting('elearning_education_header_banner_image_ooverlay_color', array(
		'default'           => '#000',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'elearning_education_header_banner_image_ooverlay_color', array(
		'label'    => __('Header Image Overlay Color', 'elearning-education'),
		'section'  => 'header_image',
	)));

    $wp_customize->add_setting(
        'elearning_education_header_image_title_font_size',
        array(
            'default'           => 32,
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control(
        'elearning_education_header_image_title_font_size',
        array(
            'label'       => esc_html__( 'Change Header Image Title Font Size', 'elearning-education' ),
            'section'     => 'header_image',
            'type'        => 'number',
            'description' => esc_html__( 'Control the font Size of the header image title. Default is 32px.', 'elearning-education' ),
            'input_attrs' => array(
                'min'  => 10,
                'max'  => 200,
                'step' => 1,
            ),
        )
    );

	$wp_customize->add_setting( 'elearning_education_header_image_title_text_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'elearning_education_header_image_title_text_color', array(
			'label'     => __('Change Header Image Title Color', 'elearning-education'),
	    'section' => 'header_image',
	    'settings' => 'elearning_education_header_image_title_text_color',
  	)));

  	//Woocommerce settings
	$wp_customize->add_section('elearning_education_woocommerce_section', array(
		'title'    => __('WooCommerce Options', 'elearning-education'),
		'priority' => null,
		'panel'    => 'woocommerce',
	));

	$wp_customize->add_setting('elearning_education_sale_tag_position',array(
        'default' => 'right',
        'sanitize_callback' => 'elearning_education_sanitize_choices'
	));
	$wp_customize->add_control('elearning_education_sale_tag_position',array(
        'type' => 'radio',
        'label'     => __('Sale Badge Position', 'elearning-education'),
        'description'   => __('This option work for Archieve Products', 'elearning-education'),
        'section' => 'elearning_education_woocommerce_section',
        'choices' => array(
            'left' => __('Left','elearning-education'),
            'right' => __('Right','elearning-education'),
        ),
	) );

  	$wp_customize->add_setting('elearning_education_woocommerce_sale_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'absint'
	));
	$wp_customize->add_control('elearning_education_woocommerce_sale_font_size',array(
		'label'	=> __('Sale Font Size','elearning-education'),

		'section'=> 'elearning_education_woocommerce_section',
		'settings'    => 'elearning_education_woocommerce_sale_font_size',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	));

	$wp_customize->add_setting('elearning_education_woocommerce_sale_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'absint'
	));
	$wp_customize->add_control('elearning_education_woocommerce_sale_padding_top_bottom',array(
		'label'	=> __('Sale Padding Top Bottom','elearning-education'),
		'section'=> 'elearning_education_woocommerce_section',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	));

	$wp_customize->add_setting('elearning_education_woocommerce_sale_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'absint'
	));
	$wp_customize->add_control('elearning_education_woocommerce_sale_padding_left_right',array(
		'label'	=> __('Sale Padding Left Right','elearning-education'),
		'section'=> 'elearning_education_woocommerce_section',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	));

	$wp_customize->add_setting( 'elearning_education_woocommerce_sale_border_radius', array(
		'default'              => '100',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'absint'
	) );
	$wp_customize->add_control( 'elearning_education_woocommerce_sale_border_radius', array(
		'label'       => esc_html__( 'Sale Border Radius','elearning-education' ),
		'section'     => 'elearning_education_woocommerce_section',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	) );
}
add_action( 'customize_register', 'elearning_education_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @since eLearning Education 1.0
 * @see elearning_education_customize_register()
 *
 * @return void
 */
function elearning_education_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since eLearning Education 1.0
 * @see elearning_education_customize_register()
 *
 * @return void
 */
function elearning_education_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

if ( ! defined( 'ELEARNING_EDUCATION_PRO_THEME_NAME' ) ) {
	define( 'ELEARNING_EDUCATION_PRO_THEME_NAME', esc_html__( 'Education Pro Theme', 'elearning-education' ));
}
if ( ! defined( 'ELEARNING_EDUCATION_PRO_THEME_URL' ) ) {
	define( 'ELEARNING_EDUCATION_PRO_THEME_URL', esc_url('https://www.themespride.com/products/elearning-education-wordpress-theme'));
}
if ( ! defined( 'ELEARNING_EDUCATION_DOCS_URL' ) ) {
	define( 'ELEARNING_EDUCATION_DOCS_URL', esc_url('https://page.themespride.com/demo/docs/elearning-education-lite/'));
}

if ( ! defined( 'ELEARNING_EDUCATION_TEXT' ) ) {
    define( 'ELEARNING_EDUCATION_TEXT', __( 'eLearning Education Pro','elearning-education' ));
}
if ( ! defined( 'ELEARNING_EDUCATION_BUY_TEXT' ) ) {
    define( 'ELEARNING_EDUCATION_BUY_TEXT', __( 'Upgrade Pro','elearning-education' ));
}


add_action( 'customize_register', function( $manager ) {

// Load custom sections.
load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

    $manager->register_section_type( elearning_Education_Button::class );

    $manager->add_section(
        new elearning_Education_Button( $manager, 'elearning_education_pro', [
            'title'       => esc_html( ELEARNING_EDUCATION_TEXT,'elearning-education' ),
            'priority'    => 0,
            'button_text' => __( 'GET PREMIUM', 'elearning-education' ),
            'button_url'  => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL )
        ] )
    );

} );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class eLearning_Education_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'eLearning_Education_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new eLearning_Education_Customize_Section_Pro(
				$manager,
				'elearning_education_section_pro',
				array(
					'priority'   => 9,
					'title'    => ELEARNING_EDUCATION_PRO_THEME_NAME,
					'pro_text' => esc_html__( 'Upgrade Pro', 'elearning-education' ),
					'pro_url'  => esc_url( ELEARNING_EDUCATION_PRO_THEME_URL, 'elearning-education' ),
				)
			)
		);

		// Register sections.
		$manager->add_section(
			new eLearning_Education_Customize_Section_Pro(
				$manager,
				'elearning_education_documentation',
				array(
					'priority'   => 500,
					'title'    => esc_html__( 'Theme Documentation', 'elearning-education' ),
					'pro_text' => esc_html__( 'Click Here', 'elearning-education' ),
					'pro_url'  => esc_url( ELEARNING_EDUCATION_DOCS_URL, 'elearning-education'),
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'elearning-education-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/assets/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'elearning-education-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/assets/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
eLearning_Education_Customize::get_instance();
