<?php
/**
 * Seeds demo AI courses and lessons on first activation.
 *
 * What this file does:
 * - Creates three sample courses with sequenced lessons when the option flag is empty.
 * Process:
 * 1) Check lac_demo_seeded option.
 * 2) Insert courses and nested lessons.
 * 3) Mark the option so seeding does not repeat.
 */

 // Prevent direct execution.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seed demo content once for local evaluation.
 *
 * @return void
 */
function lac_seed_demo_content_if_needed() {
	 // Skip when demo content was already created.
	if ( get_option( 'lac_demo_seeded' ) ) {
		lac_log_info( 'Demo seed skipped; already marked complete.' );
		return;
	}
	 // Define three sample AI courses with metadata and lesson titles.
	$demo_courses = array(
		array(
			'title'   => 'Generative AI Foundations',
			'excerpt' => 'Prompting, model types, and responsible AI workflows for beginners.',
			'content' => '<p>Learn how modern generative models work, how to write strong prompts, and how to evaluate outputs safely.</p>',
			'level'   => 'beginner',
			'hours'   => 6,
			'price'   => 0,
			'lessons' => array(
				'What generative AI is (and is not)',
				'Prompt patterns that actually work',
				'Evaluating model outputs',
				'Safety and attribution basics',
			),
		),
		array(
			'title'   => 'Build AI Apps with APIs',
			'excerpt' => 'Ship practical AI features: chat, summarization, and retrieval-style flows.',
			'content' => '<p>Wire AI APIs into a product: authentication, rate limits, structured outputs, and UX patterns for latency.</p>',
			'level'   => 'intermediate',
			'hours'   => 10,
			'price'   => 49,
			'lessons' => array(
				'Choosing the right model endpoint',
				'Structured outputs and tool use',
				'Caching and cost control',
				'Shipping a chat feature end-to-end',
			),
		),
		array(
			'title'   => 'AI Agents & Automation',
			'excerpt' => 'Design multi-step agents that plan, call tools, and recover from failures.',
			'content' => '<p>Go beyond single prompts: planning loops, tool registries, memory, and evaluation harnesses for agents.</p>',
			'level'   => 'advanced',
			'hours'   => 12,
			'price'   => 79,
			'lessons' => array(
				'Agent architectures that scale',
				'Tool calling and guardrails',
				'Memory and long-running tasks',
				'Evaluating agent reliability',
			),
		),
	);
	 // Insert each demo course and its lessons.
	foreach ( $demo_courses as $demo_course ) {
		// Create the parent course post.
		$course_id = wp_insert_post(
			array(
				'post_type'    => 'lac_course',
				'post_status'  => 'publish',
				'post_title'   => $demo_course['title'],
				'post_excerpt' => $demo_course['excerpt'],
				'post_content' => $demo_course['content'],
			)
		);
		 // Skip lesson creation if the course insert failed.
		if ( is_wp_error( $course_id ) || ! $course_id ) {
			lac_log_error( 'Failed to seed course: ' . $demo_course['title'] );
			continue;
		}
		 // Store course meta used by the theme cards.
		update_post_meta( $course_id, '_lac_course_level', $demo_course['level'] );
		update_post_meta( $course_id, '_lac_course_hours', $demo_course['hours'] );
		update_post_meta( $course_id, '_lac_course_price', $demo_course['price'] );
		 // Create ordered lessons under this course.
		foreach ( $demo_course['lessons'] as $lesson_index => $lesson_title ) {
			$lesson_id = wp_insert_post(
				array(
					'post_type'    => 'lac_lesson',
					'post_status'  => 'publish',
					'post_title'   => $lesson_title,
					'post_content' => '<p>' . esc_html( $lesson_title ) . ' — walk through the concepts with examples, then try the practice prompt at the end of the lesson.</p>',
					'menu_order'   => $lesson_index + 1,
				)
			);
			 // Attach the lesson to its parent course when insert succeeds.
			if ( ! is_wp_error( $lesson_id ) && $lesson_id ) {
				update_post_meta( $lesson_id, '_lac_parent_course_id', $course_id );
			}
		}
		 // Note each seeded course id for operators.
		lac_log_info( 'Seeded demo course id ' . absint( $course_id ) );
	}
	 // Flip the option so activation does not reseed.
	update_option( 'lac_demo_seeded', 1 );
	 // Confirm completion in the log.
	lac_log_info( 'Demo course seed completed.' );
}
