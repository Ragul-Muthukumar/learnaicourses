<?php
/**
 * Course custom post type registration and admin meta.
 *
 * What this file does:
 * - Registers lac_course and its public rewrite slug.
 * - Saves level, hours, and price meta from the editor.
 * Process:
 * 1) Register CPT on init.
 * 2) Add meta box for course details.
 * 3) Persist validated meta on save_post.
 */

 // Exit when this file is requested directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the public Course post type.
 *
 * @return void
 */
function lac_register_course_post_type() {
	 // Define labels shown in the WordPress admin menus.
	$labels = array(
		'name'               => 'Courses',
		'singular_name'      => 'Course',
		'add_new'            => 'Add Course',
		'add_new_item'       => 'Add New Course',
		'edit_item'          => 'Edit Course',
		'new_item'           => 'New Course',
		'view_item'          => 'View Course',
		'search_items'       => 'Search Courses',
		'not_found'          => 'No courses found',
		'not_found_in_trash' => 'No courses found in Trash',
		'menu_name'          => 'Courses',
	);
	 // Configure public archive, REST, and rewrite behavior.
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => true,
		'rewrite'            => array( 'slug' => 'courses' ),
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-welcome-learn-more',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'capability_type'    => 'post',
	);
	 // Register the CPT with WordPress.
	register_post_type( 'lac_course', $args );
}

 // Hook CPT registration into the init lifecycle.
add_action( 'init', 'lac_register_course_post_type' );

/**
 * Add the course details meta box to the editor sidebar area.
 *
 * @return void
 */
function lac_add_course_meta_box() {
	 // Attach the meta box only to the course post type screen.
	add_meta_box(
		'lac_course_details',
		'Course Details',
		'lac_render_course_meta_box',
		'lac_course',
		'side',
		'default'
	);
}

 // Register the meta box during the admin meta-box stage.
add_action( 'add_meta_boxes', 'lac_add_course_meta_box' );

/**
 * Render the course details meta box fields.
 *
 * @param WP_Post $post Current post being edited.
 * @return void
 */
function lac_render_course_meta_box( $post ) {
	 // Emit a nonce field so save handlers can verify intent.
	wp_nonce_field( 'lac_save_course_meta', 'lac_course_meta_nonce' );
	 // Load existing meta values for the form defaults.
	$course_level = get_post_meta( $post->ID, '_lac_course_level', true );
	$course_hours = get_post_meta( $post->ID, '_lac_course_hours', true );
	$course_price = get_post_meta( $post->ID, '_lac_course_price', true );
	 // Fall back to beginner when no level has been stored yet.
	if ( ! $course_level ) {
		$course_level = 'beginner';
	}
	 // Print the difficulty select control.
	echo '<p><label for="lac_course_level"><strong>Level</strong></label><br />';
	echo '<select name="lac_course_level" id="lac_course_level">';
	foreach ( array( 'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced' ) as $value => $label ) {
		printf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $value ),
			selected( $course_level, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select></p>';
	 // Print the estimated hours number field.
	printf(
		'<p><label for="lac_course_hours"><strong>Hours</strong></label><br /><input type="number" step="0.5" min="0" name="lac_course_hours" id="lac_course_hours" value="%s" style="width:100%%;" /></p>',
		esc_attr( $course_hours )
	);
	 // Print the display price number field.
	printf(
		'<p><label for="lac_course_price"><strong>Price (USD)</strong></label><br /><input type="number" step="0.01" min="0" name="lac_course_price" id="lac_course_price" value="%s" style="width:100%%;" /></p>',
		esc_attr( $course_price )
	);
}

/**
 * Persist course meta after the editor saves the post.
 *
 * @param int $post_id Post id being saved.
 * @return void
 */
function lac_save_course_meta( $post_id ) {
	 // Ignore autosaves that should not overwrite meta.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	 // Verify the nonce from the meta box form.
	if ( ! isset( $_POST['lac_course_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lac_course_meta_nonce'] ) ), 'lac_save_course_meta' ) ) {
		return;
	}
	 // Ensure the current user may edit this post.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	 // Only process meta for course posts.
	if ( 'lac_course' !== get_post_type( $post_id ) ) {
		return;
	}
	 // Pull raw form values with safe defaults.
	$raw_level = isset( $_POST['lac_course_level'] ) ? sanitize_text_field( wp_unslash( $_POST['lac_course_level'] ) ) : 'beginner';
	$raw_hours = isset( $_POST['lac_course_hours'] ) ? wp_unslash( $_POST['lac_course_hours'] ) : 0;
	$raw_price = isset( $_POST['lac_course_price'] ) ? wp_unslash( $_POST['lac_course_price'] ) : 0;
	 // Validate and normalize the meta bag.
	$safe_meta = lac_validate_course_meta( $raw_level, $raw_hours, $raw_price );
	 // Persist each sanitized meta key.
	update_post_meta( $post_id, '_lac_course_level', $safe_meta['course_level'] );
	update_post_meta( $post_id, '_lac_course_hours', $safe_meta['course_hours'] );
	update_post_meta( $post_id, '_lac_course_price', $safe_meta['course_price'] );
	 // Log the successful meta save for debugging.
	lac_log_info( 'Saved course meta for post ' . absint( $post_id ) );
}

 // Hook meta persistence into the save_post action.
add_action( 'save_post', 'lac_save_course_meta' );
