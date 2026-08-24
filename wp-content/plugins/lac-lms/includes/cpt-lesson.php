<?php
/**
 * Lesson custom post type linked to parent courses.
 *
 * What this file does:
 * - Registers lac_lesson with a public rewrite slug.
 * - Stores the parent course relationship as post meta.
 * Process:
 * 1) Register CPT on init.
 * 2) Render a parent-course selector meta box.
 * 3) Save the selected parent course on post save.
 */

 // Prevent direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Lesson post type.
 *
 * @return void
 */
function lac_register_lesson_post_type() {
	 // Admin menu and editor labels for lessons.
	$labels = array(
		'name'               => 'Lessons',
		'singular_name'      => 'Lesson',
		'add_new'            => 'Add Lesson',
		'add_new_item'       => 'Add New Lesson',
		'edit_item'          => 'Edit Lesson',
		'new_item'           => 'New Lesson',
		'view_item'          => 'View Lesson',
		'search_items'       => 'Search Lessons',
		'not_found'          => 'No lessons found',
		'menu_name'          => 'Lessons',
	);
	 // Public single pages with REST support for the block editor.
	$args = array(
		'labels'          => $labels,
		'public'          => true,
		'has_archive'     => false,
		'rewrite'         => array( 'slug' => 'lessons' ),
		'show_in_rest'    => true,
		'menu_icon'       => 'dashicons-media-text',
		'supports'        => array( 'title', 'editor', 'page-attributes' ),
		'capability_type' => 'post',
	);
	 // Register the lesson CPT.
	register_post_type( 'lac_lesson', $args );
}

 // Bind lesson CPT registration to init.
add_action( 'init', 'lac_register_lesson_post_type' );

/**
 * Add the parent course meta box on lesson screens.
 *
 * @return void
 */
function lac_add_lesson_meta_box() {
	 // Attach only to the lesson editor.
	add_meta_box(
		'lac_lesson_parent',
		'Parent Course',
		'lac_render_lesson_meta_box',
		'lac_lesson',
		'side',
		'default'
	);
}

 // Register the lesson meta box.
add_action( 'add_meta_boxes', 'lac_add_lesson_meta_box' );

/**
 * Render the parent course dropdown for a lesson.
 *
 * @param WP_Post $post Current lesson post.
 * @return void
 */
function lac_render_lesson_meta_box( $post ) {
	 // Security nonce for the save handler.
	wp_nonce_field( 'lac_save_lesson_meta', 'lac_lesson_meta_nonce' );
	 // Currently selected parent course id.
	$parent_course_id = (int) get_post_meta( $post->ID, '_lac_parent_course_id', true );
	 // Load published courses for the selector.
	$courses = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	 // Start the select element.
	echo '<p><label for="lac_parent_course_id"><strong>Course</strong></label><br />';
	echo '<select name="lac_parent_course_id" id="lac_parent_course_id" style="width:100%;">';
	echo '<option value="0">— Select course —</option>';
	 // Output one option per published course.
	foreach ( $courses as $course ) {
		printf(
			'<option value="%1$d" %2$s>%3$s</option>',
			(int) $course->ID,
			selected( $parent_course_id, (int) $course->ID, false ),
			esc_html( get_the_title( $course ) )
		);
	}
	echo '</select></p>';
	 // Explain menu_order usage for lesson sequencing.
	echo '<p class="description">Use Order (Attributes) to sequence lessons.</p>';
}

/**
 * Save the parent course relationship for a lesson.
 *
 * @param int $post_id Lesson post id.
 * @return void
 */
function lac_save_lesson_meta( $post_id ) {
	 // Skip autosave revisions.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	 // Verify the form nonce.
	if ( ! isset( $_POST['lac_lesson_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lac_lesson_meta_nonce'] ) ), 'lac_save_lesson_meta' ) ) {
		return;
	}
	 // Capability gate for editors.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	 // Only lessons store this meta key.
	if ( 'lac_lesson' !== get_post_type( $post_id ) ) {
		return;
	}
	 // Sanitize the selected parent course id.
	$parent_course_id = isset( $_POST['lac_parent_course_id'] ) ? absint( $_POST['lac_parent_course_id'] ) : 0;
	 // Persist the relationship meta.
	update_post_meta( $post_id, '_lac_parent_course_id', $parent_course_id );
	 // Log the save for operators.
	lac_log_info( 'Saved lesson parent course for post ' . absint( $post_id ) );
}

 // Hook lesson meta saving.
add_action( 'save_post', 'lac_save_lesson_meta' );

/**
 * Fetch ordered lessons belonging to a course.
 *
 * @param int $course_id Parent course post id.
 * @return WP_Post[] Lesson posts in menu order.
 */
function lac_get_lessons_for_course( $course_id ) {
	 // Query lessons by numeric parent-course meta so string/int storage still matches.
	return get_posts(
		array(
			'post_type'      => 'lac_lesson',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_lac_parent_course_id',
					'value'   => absint( $course_id ),
					'compare' => '=',
					'type'    => 'NUMERIC',
				),
			),
		)
	);
}

/**
 * Detailed lesson outline for a course (catalog + extras) or a title-only fallback.
 *
 * @param string $course_title Published course title.
 * @param int    $course_id    Optional course post id for catalog lookup.
 * @return array<int, array{title:string, content:string, excerpt?:string}> Lesson blueprints.
 */
function lac_default_lesson_blueprint( $course_title, $course_id = 0 ) {
	if ( $course_id > 0 && function_exists( 'lac_get_curriculum_blueprint_for_course' ) ) {
		$blueprint = lac_get_curriculum_blueprint_for_course( $course_id );
		if ( ! empty( $blueprint ) ) {
			return $blueprint;
		}
	}

	$course_title = is_string( $course_title ) && '' !== $course_title ? $course_title : 'this course';
	if ( function_exists( 'lac_curriculum_fallback_outline' ) && function_exists( 'lac_render_lesson_html' ) ) {
		$outline = lac_curriculum_fallback_outline( $course_title, '' );
		if ( function_exists( 'lac_curriculum_depth_extras' ) ) {
			$outline = array_merge( $outline, lac_curriculum_depth_extras( $course_title, 'beginner' ) );
		}
		$total   = count( $outline );
		$lessons = array();
		foreach ( $outline as $index => $spec ) {
			$lessons[] = array(
				'title'   => (string) $spec['title'],
				'excerpt' => (string) $spec['excerpt'],
				'content' => lac_render_lesson_html( $spec, $course_title, $index + 1, $total ),
			);
		}
		return $lessons;
	}

	return array();
}

/**
 * Create the default curriculum when a published course has no lessons.
 *
 * Used by Continue Learning, checkout, and the course board so enrolled
 * learners always have a real learning page to open.
 *
 * @param int $course_id Course post id.
 * @return WP_Post[] Lessons after the ensure step (possibly newly created).
 */
function lac_ensure_default_lessons_for_course( $course_id ) {
	$course_id = absint( $course_id );
	if ( $course_id < 1 || 'lac_course' !== get_post_type( $course_id ) ) {
		return array();
	}

	$existing_lessons = lac_get_lessons_for_course( $course_id );
	if ( ! empty( $existing_lessons ) ) {
		return $existing_lessons;
	}

	// Only published courses get a public curriculum.
	if ( 'publish' !== get_post_status( $course_id ) ) {
		return array();
	}

	if ( function_exists( 'lac_insert_curriculum_lessons_for_course' ) ) {
		return lac_insert_curriculum_lessons_for_course( $course_id );
	}

	$course_title = get_the_title( $course_id );
	foreach ( lac_default_lesson_blueprint( $course_title, $course_id ) as $lesson_index => $lesson ) {
		$lesson_id = wp_insert_post(
			array(
				'post_type'    => 'lac_lesson',
				'post_status'  => 'publish',
				'post_title'   => $lesson['title'],
				'post_excerpt' => isset( $lesson['excerpt'] ) ? $lesson['excerpt'] : '',
				'post_content' => $lesson['content'],
				'menu_order'   => $lesson_index + 1,
			)
		);
		if ( ! is_wp_error( $lesson_id ) && $lesson_id ) {
			update_post_meta( $lesson_id, '_lac_parent_course_id', $course_id );
		}
	}

	lac_log_info( 'Created default lessons for course ' . $course_id );

	return lac_get_lessons_for_course( $course_id );
}
