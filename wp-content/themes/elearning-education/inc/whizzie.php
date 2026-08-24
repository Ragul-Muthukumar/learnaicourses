<?php 
if (isset($_GET['import-demo']) && $_GET['import-demo'] == true) {

     // Function to install and activate plugins
    function elearning_education_import_demo_content() {

        // Display the preloader only for plugin installation
        echo '<div id="plugin-loader" style="display: flex; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999;">
                <img src="' . esc_url(get_template_directory_uri()) . '/assets/images/loader.png" alt="Loading..." width="60" height="60" />
              </div>';

        // Define the plugins you want to install and activate
        $plugins = array(
            array(
                'slug' => 'woocommerce',
                'file' => 'woocommerce/woocommerce.php',
                'url'  => 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip'
            ),
            array(
                'slug' => 'yith-woocommerce-wishlist',
                'file' => 'yith-woocommerce-wishlist/init.php',
                'url'  => 'https://downloads.wordpress.org/plugin/yith-woocommerce-wishlist.latest-stable.zip'
            ),
            array(
                'slug' => 'advanced-appointment-booking-scheduling',
                'file' => 'advanced-appointment-booking-scheduling/advanced-appointment-booking.php',
                'url'  => 'https://downloads.wordpress.org/plugin/advanced-appointment-booking-scheduling.zip'
            ),
        );

        // Include required files for plugin installation
        if (!function_exists('plugins_api')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
        }
        if (!function_exists('activate_plugin')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        include_once(ABSPATH . 'wp-admin/includes/file.php');
        include_once(ABSPATH . 'wp-admin/includes/misc.php');
        include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

        // Loop through each plugin
        foreach ($plugins as $plugin) {
            $plugin_file = WP_PLUGIN_DIR . '/' . $plugin['file'];

            // Check if the plugin is installed
            if (!file_exists($plugin_file)) {
                // If the plugin is not installed, download and install it
                $upgrader = new Plugin_Upgrader();
                $result = $upgrader->install($plugin['url']);

                // Check for installation errors
                if (is_wp_error($result)) {
                    error_log('Plugin installation failed: ' . $plugin['slug'] . ' - ' . $result->get_error_message());
                    continue;
                }
            }

            // If the plugin folder exists but the plugin is not active, activate it
            if (file_exists($plugin_file) && !is_plugin_active($plugin['file'])) {
                $result = activate_plugin($plugin['file']);

                // Check for activation errors
                if (is_wp_error($result)) {
                    error_log('Plugin activation failed: ' . $plugin['slug'] . ' - ' . $result->get_error_message());
                }
            }
        }

        // Hide the preloader after the process is complete
        echo '<script type="text/javascript">
                document.getElementById("plugin-loader").style.display = "none";
              </script>';

        // Add filter to skip WooCommerce setup wizard after activation
        add_filter('woocommerce_prevent_automatic_wizard_redirect', '__return_true');
    }

    // Call the import function
    elearning_education_import_demo_content();


    // ------- Create Nav Menu --------
$elearning_education_menuname = 'Main Menus';
$elearning_education_bpmenulocation = 'primary-menu';
$elearning_education_menu_exists = wp_get_nav_menu_object($elearning_education_menuname);

if (!$elearning_education_menu_exists) {
    $elearning_education_menu_id = wp_create_nav_menu($elearning_education_menuname);

    // Create Home Page
    $elearning_education_home_title = 'Home';
    $elearning_education_home = array(
        'post_type' => 'page',
        'post_title' => $elearning_education_home_title,
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'home'
    );
    $elearning_education_home_id = wp_insert_post($elearning_education_home);

    // Assign Home Page Template
    add_post_meta($elearning_education_home_id, '_wp_page_template', 'page-template/front-page.php');

    // Update options to set Home Page as the front page
    update_option('page_on_front', $elearning_education_home_id);
    update_option('show_on_front', 'page');

    // Add Home Page to Menu
    wp_update_nav_menu_item($elearning_education_menu_id, 0, array(
        'menu-item-title' => __('Home', 'elearning-education'),
        'menu-item-classes' => 'home',
        'menu-item-url' => home_url('/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $elearning_education_home_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create About Us Page with Dummy Content
    $elearning_education_about_title = 'About Us';
    $elearning_education_about_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>

             Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br> 

                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br> 

                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
    $elearning_education_about = array(
        'post_type' => 'page',
        'post_title' => $elearning_education_about_title,
        'post_content' => $elearning_education_about_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'about-us'
    );
    $elearning_education_about_id = wp_insert_post($elearning_education_about);

    // Add About Us Page to Menu
    wp_update_nav_menu_item($elearning_education_menu_id, 0, array(
        'menu-item-title' => __('About Us', 'elearning-education'),
        'menu-item-classes' => 'about-us',
        'menu-item-url' => home_url('/about-us/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $elearning_education_about_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create Services Page with Dummy Content
    $elearning_education_services_title = 'Services';
    $elearning_education_services_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>

             Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br> 

                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br> 

                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
    $elearning_education_services = array(
        'post_type' => 'page',
        'post_title' => $elearning_education_services_title,
        'post_content' => $elearning_education_services_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'services'
    );
    $elearning_education_services_id = wp_insert_post($elearning_education_services);

    // Add Services Page to Menu
    wp_update_nav_menu_item($elearning_education_menu_id, 0, array(
        'menu-item-title' => __('Services', 'elearning-education'),
        'menu-item-classes' => 'services',
        'menu-item-url' => home_url('/services/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $elearning_education_services_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create Pages Page with Dummy Content
    $elearning_education_pages_title = 'Pages';
    $elearning_education_pages_content = '<h2>Our Pages</h2>
    <p>Explore all the pages we have on our website. Find information about our services, company, and more.</p>';
    $elearning_education_pages = array(
        'post_type' => 'page',
        'post_title' => $elearning_education_pages_title,
        'post_content' => $elearning_education_pages_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'pages'
    );
    $elearning_education_pages_id = wp_insert_post($elearning_education_pages);

    // Add Pages Page to Menu
    wp_update_nav_menu_item($elearning_education_menu_id, 0, array(
        'menu-item-title' => __('Pages', 'elearning-education'),
        'menu-item-classes' => 'pages',
        'menu-item-url' => home_url('/pages/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $elearning_education_pages_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create Contact Page with Dummy Content
    $elearning_education_contact_title = 'Contact';
    $elearning_education_contact_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>

             Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br> 

                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br> 

                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
    $elearning_education_contact = array(
        'post_type' => 'page',
        'post_title' => $elearning_education_contact_title,
        'post_content' => $elearning_education_contact_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'contact'
    );
    $elearning_education_contact_id = wp_insert_post($elearning_education_contact);

    // Add Contact Page to Menu
    wp_update_nav_menu_item($elearning_education_menu_id, 0, array(
        'menu-item-title' => __('Contact', 'elearning-education'),
        'menu-item-classes' => 'contact',
        'menu-item-url' => home_url('/contact/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $elearning_education_contact_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Set the menu location if it's not already set
    if (!has_nav_menu($elearning_education_bpmenulocation)) {
        $locations = get_theme_mod('nav_menu_locations'); // Use 'nav_menu_locations' to get locations array
        if (empty($locations)) {
            $locations = array();
        }
        $locations[$elearning_education_bpmenulocation] = $elearning_education_menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}

        //---Header--//
        set_theme_mod('elearning_education_phone_number', '1(858) 255 6155');
        set_theme_mod('elearning_education_email_address', 'admin@education.com');
        set_theme_mod('elearning_education_register_button', 'REGISTER');
        set_theme_mod('elearning_education_register_button_link', '#');
        set_theme_mod('elearning_education_login_button', 'LOGIN');
        set_theme_mod('elearning_education_login_button_link', '#');

        set_theme_mod('elearning_education_header_teacher', 'Become an Instructor');
        set_theme_mod('elearning_education_header_wishlist_url', '#');

        set_theme_mod('elearning_education_header_fb_new_tab', true);
        set_theme_mod('elearning_education_facebook_url', '#');
        set_theme_mod('elearning_education_facebook_icon', 'fab fa-facebook-f');

        set_theme_mod('elearning_education_header_twt_new_tab', true);
        set_theme_mod('elearning_education_twitter_url', '#');
        set_theme_mod('elearning_education_twitter_icon', 'fab fa-twitter');

        set_theme_mod('elearning_education_header_ins_new_tab', true);
        set_theme_mod('elearning_education_instagram_url', '#');
        set_theme_mod('elearning_education_instagram_icon', 'fab fa-instagram');

        set_theme_mod('elearning_education_header_ut_new_tab', true);
        set_theme_mod('elearning_education_youtube_url', '#');
        set_theme_mod('elearning_education_youtube_icon', 'fab fa-youtube');

        set_theme_mod('elearning_education_header_pint_new_tab', true);
        set_theme_mod('elearning_education_pint_url', '#');
        set_theme_mod('elearning_education_pint_icon', 'fab fa-pinterest');

        // Slider Section
        set_theme_mod('elearning_education_slider_arrows', true);
        set_theme_mod('elearning_education_slider_top', 'OUR FREE CAREER TOOLS');

        for ($i = 1; $i <= 4; $i++) {
            $elearning_education_title = 'FIND YOUR IDEAL CAREER';
            $elearning_education_content = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard.';

            // Create post object
            $my_post = array(
                'post_title'    => wp_strip_all_tags($elearning_education_title),
                'post_content'  => $elearning_education_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
            );

            // Insert the post into the database
            $post_id = wp_insert_post($my_post);

            // Check for errors when inserting the post
            if (is_wp_error($post_id)) {
                error_log('Error creating slider page: ' . $post_id->get_error_message());
                continue; // Skip to the next iteration if error occurs
            }

            // Set the theme mod for the slider page
            set_theme_mod('elearning_education_slider_page' . $i, $post_id);

            // Set the slider image
            $image_url = get_template_directory_uri() . '/assets/images/slider-img.png';
            $image_id = media_sideload_image($image_url, $post_id, null, 'id');

            // Handle image download errors
            if (!is_wp_error($image_id)) {
                // Set the downloaded image as the post's featured image
                set_post_thumbnail($post_id, $image_id);
            } else {
                error_log('Error downloading slider image: ' . $image_id->get_error_message());
            }
        }


    // Set theme mods for the section
    set_theme_mod('elearning_education_online_courses_enable', true);
    set_theme_mod('elearning_education_online_courses_heading', 'POPULAR ONLINE COURSE');
    set_theme_mod('elearning_education_online_courses_category', 'postcategory1');
    set_theme_mod('elearning_education_online_courses_per_page', 4);

    // Get category and title dynamically from Customizer settings
    $category_name = get_theme_mod('elearning_education_online_courses_category', 'postcategory1'); // Default: Photography
    $course_titles = array(
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit", 
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit", 
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit", 
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit"
    );

    // Create or retrieve the category term ID in 'course_category'
    $elearning_education_term = term_exists($category_name, 'course_category');
    if ($elearning_education_term === 0 || $elearning_education_term === null) {
        // If the term doesn't exist, create it
        $elearning_education_term = wp_insert_term($category_name, 'course_category');
    }

    if (is_wp_error($elearning_education_term)) {
        error_log('Error creating category: ' . $elearning_education_term->get_error_message());
    } else {
        // Get term ID
        $elearning_education_term_id = is_array($elearning_education_term) ? $elearning_education_term['term_id'] : $elearning_education_term;

        // Loop to create 4 products for each category
        for ($i = 0; $i < 4; $i++) {
            // Get the title for the course
            $course_title = $course_titles[$i];

            // Create post object for the course
            $course_post = array(
                'post_title'    => wp_strip_all_tags($course_title),
                'post_status'   => 'publish',
                'post_type'     => 'lp_course', // Use LearnPress post type for courses
                'tax_input'     => array(
                    'course_category' => array($elearning_education_term_id) // Assign category to the course
                ),
            );

            // Insert the course post
            $course_post_id = wp_insert_post($course_post);

            if (is_wp_error($course_post_id)) {
                error_log('Error creating course: ' . $course_post_id->get_error_message());
            } else {
                // Add meta information (price, duration)
                update_post_meta($course_post_id, '_lp_price', 60);
                update_post_meta($course_post_id, '_lp_duration', '10 hours');

                // Handle the featured image using media_sideload_image
                $image_url = get_template_directory_uri() . '/assets/images/post-img' . ($i + 1) . '.png';
                $image_id = media_sideload_image($image_url, $course_post_id, null, 'id');

                if (!is_wp_error($image_id)) {
                    // Assign the featured image to the course
                    set_post_thumbnail($course_post_id, $image_id);
                } else {
                    error_log('Error downloading image: ' . $image_id->get_error_message());
                }
            }
        }
    }

        
    }

?>