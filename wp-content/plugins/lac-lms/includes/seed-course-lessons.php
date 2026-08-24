<?php
/**
 * Seed default curriculum lessons for courses that have none.
 *
 * What this file does:
 * - Ensures every purchasable course has a basic lesson outline on its detail page.
 * Process:
 * 1) Find published courses with zero linked lessons.
 * 2) Insert four starter lessons linked via parent course meta.
 * 3) Mark the seed complete with an option flag.
 */

 // Guard against direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create default lessons for courses missing a curriculum.
 *
 * @return void
 */
function lac_seed_default_lessons_if_needed() {
	 // Skip when this seed has already run successfully.
	if ( get_option( 'lac_seeded_default_lessons_v1' ) ) {
		return;
	}
	 // Load every published course id.
	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	 // Lesson title templates applied to each empty course.
	$lesson_titles = array(
		'1. Course kickoff and goals',
		'2. Core skills practice',
		'3. Guided project walkthrough',
		'4. Finish strong and next steps',
	);
	 // Track how many courses received a curriculum.
	$seeded_courses = 0;
	foreach ( $course_ids as $course_id ) {
		 // Skip courses that already have lessons.
		$existing_lessons = lac_get_lessons_for_course( $course_id );
		if ( ! empty( $existing_lessons ) ) {
			continue;
		}
		 // Build a short course-specific intro for each lesson body.
		$course_title = get_the_title( $course_id );
		foreach ( $lesson_titles as $index => $lesson_title ) {
			 // Insert a published lesson post.
			$lesson_id = wp_insert_post(
				array(
					'post_type'    => 'lac_lesson',
					'post_status'  => 'publish',
					'post_title'   => $lesson_title,
					'post_content' => 'In this lesson for "' . $course_title . '", you will practice the skill, apply it to a real example, and lock in a repeatable workflow.',
					'menu_order'   => $index + 1,
				),
				true
			);
			 // Skip meta write when the insert failed.
			if ( is_wp_error( $lesson_id ) || ! $lesson_id ) {
				lac_log_error( 'Failed to seed lesson for course ' . absint( $course_id ) );
				continue;
			}
			 // Link the lesson to its parent course.
			update_post_meta( $lesson_id, '_lac_parent_course_id', absint( $course_id ) );
		}
		 // Count this course as seeded.
		$seeded_courses++;
	}
	 // Persist the seed flag so we do not duplicate lessons.
	update_option( 'lac_seeded_default_lessons_v1', 1 );
	 // Confirm the seed for operators.
	lac_log_info( 'Seeded default lessons for ' . absint( $seeded_courses ) . ' courses.' );
}

 // Run the curriculum seed after CPTs are registered.
add_action( 'init', 'lac_seed_default_lessons_if_needed', 30 );
