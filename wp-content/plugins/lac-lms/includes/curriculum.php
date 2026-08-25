<?php
/**
 * Detailed course curricula and lesson HTML.
 *
 * What this file does:
 * - Turns a course-specific outline into full lesson pages.
 * - Looks up a catalog by slug, then falls back to a title-based outline.
 * - Refreshes existing stub lessons so the public catalog is no longer four short rows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once LAC_LMS_PATH . 'includes/curriculum-catalog.php';

/**
 * Compact lesson spec used by the catalog file.
 *
 * @param string   $title    Curriculum title shown on the course page.
 * @param string   $excerpt  One-line summary under the title.
 * @param string   $detail   Walkthrough copy for the lesson page.
 * @param string[] $topics   Bullet list of what the learner will cover.
 * @param string   $practice Practice prompt at the end of the lesson.
 * @return array{title:string,excerpt:string,detail:string,topics:array<int,string>,practice:string}
 */
function lac_cl( $title, $excerpt, $detail, $topics, $practice ) {
	return array(
		'title'    => $title,
		'excerpt'  => $excerpt,
		'detail'   => $detail,
		'topics'   => $topics,
		'practice' => $practice,
	);
}

/**
 * Build the HTML body for one lesson page.
 *
 * @param array{title?:string,excerpt?:string,detail?:string,topics?:array<int,string>,practice?:string} $spec Lesson spec.
 * @param string                                                                                          $course_title Parent course title.
 * @param int                                                                                             $position     1-based lesson number.
 * @param int                                                                                             $total        Lesson count in the outline.
 * @return string Safe HTML for post_content.
 */
function lac_render_lesson_html( $spec, $course_title, $position, $total ) {
	$title        = isset( $spec['title'] ) ? (string) $spec['title'] : 'Lesson';
	$excerpt      = isset( $spec['excerpt'] ) ? (string) $spec['excerpt'] : '';
	$detail       = isset( $spec['detail'] ) ? (string) $spec['detail'] : '';
	$topics       = isset( $spec['topics'] ) && is_array( $spec['topics'] ) ? $spec['topics'] : array();
	$practice     = isset( $spec['practice'] ) ? (string) $spec['practice'] : '';
	$course_title = is_string( $course_title ) && '' !== $course_title ? $course_title : 'this course';

	$html  = '<p class="lac-lesson-kicker">Lesson ' . absint( $position ) . ' of ' . absint( $total ) . ' · ' . esc_html( $course_title ) . '</p>';
	if ( '' !== $excerpt ) {
		$html .= '<p class="lac-lesson-goal"><strong>In this lesson.</strong> ' . esc_html( $excerpt ) . '</p>';
	}

	$html .= '<h2>What you will learn</h2>';
	$html .= '<ul>';
	foreach ( $topics as $topic ) {
		$topic = is_string( $topic ) ? trim( $topic ) : '';
		if ( '' === $topic ) {
			continue;
		}
		$html .= '<li>' . esc_html( $topic ) . '</li>';
	}
	$html .= '</ul>';

	$html .= '<h2>Walkthrough</h2>';
	$html .= '<p>' . esc_html( $detail ) . '</p>';
	$html .= '<p>Work through the ideas in order. After each point, pause and connect it to a task you already do — a document, a workflow, or a feature you own. The goal of ' . esc_html( $course_title ) . ' is usable skill, not a pile of notes.</p>';
	$html .= '<p>If something is unclear, rewrite it in your own words before you continue. Teaching the step back to yourself is the fastest way to see gaps.</p>';

	$html .= '<h2>Practice</h2>';
	$html .= '<p>' . esc_html( $practice ) . '</p>';
	$html .= '<p>Keep the first attempt small. A finished example you can reuse beats a perfect plan you never run.</p>';

	$html .= '<h2>Check your understanding</h2>';
	$html .= '<ul>';
	$html .= '<li>Can you explain the goal of this lesson in one sentence to a teammate?</li>';
	if ( ! empty( $topics[0] ) ) {
		$html .= '<li>Where would you apply “' . esc_html( (string) $topics[0] ) . '” in your own work this week?</li>';
	}
	$html .= '<li>What would you change on a second pass of the practice?</li>';
	$html .= '</ul>';

	if ( $position < $total ) {
		$html .= '<p><strong>Next.</strong> Continue to the following lesson when the practice has a real artifact, even a rough one.</p>';
	} else {
		$html .= '<p><strong>Next.</strong> You have finished the curriculum for ' . esc_html( $course_title ) . '. Use the checklist from this lesson the next time you start similar work.</p>';
	}

	unset( $title );
	return $html;
}

/**
 * Extra lessons appended so longer courses are not a four-row outline.
 *
 * @param string $course_title Course title.
 * @param string $level        beginner|intermediate|advanced.
 * @return array<int, array{title:string,excerpt:string,detail:string,topics:array<int,string>,practice:string}>
 */
function lac_curriculum_depth_extras( $course_title, $level ) {
	$course_title = is_string( $course_title ) && '' !== $course_title ? $course_title : 'this course';
	$extras       = array(
		lac_cl(
			'Workshop: a complete pass on a real task',
			'Combine the earlier lessons into one working example you can keep.',
			'This workshop is the midpoint of ' . $course_title . '. Pick one real task from your job or project and run the full workflow from setup through a first result. Do not start a second example until the first one exists. The point is to feel the whole loop, including the messy middle, so later lessons on quality and shipping have something concrete to improve.',
			array(
				'Choose a task with a clear finish line',
				'Reuse templates and checklists from earlier lessons',
				'Time-box the first pass so you actually finish',
			),
			'Complete one end-to-end example for ' . $course_title . '. Save the prompt, the output, and three notes on what you would change.'
		),
		lac_cl(
			'Quality bar, common mistakes, and a 30-day plan',
			'Lock in a checklist so the skill survives after the course ends.',
			'Skills fade when they stay in a course tab. This lesson turns ' . $course_title . ' into a repeatable habit: a quality bar you can check in two minutes, the mistakes that usually sneak back in, and a 30-day plan with one practice block a week. You will leave with a short checklist, not a vague intention to “use AI more.”',
			array(
				'A minimum quality bar you can scan quickly',
				'Mistakes that undo good work under deadline pressure',
				'A 30-day practice plan with one artifact per week',
			),
			'Write a one-page checklist for ' . $course_title . ' and schedule four practice blocks on your calendar.'
		),
	);

	if ( 'advanced' === $level ) {
		$extras[] = lac_cl(
			'Measure results so you know the work is actually better',
			'Add a simple evaluation so quality and cost are not guesswork.',
			'Advanced work fails quietly when nobody measures it. For ' . $course_title . ' you will define pass/fail examples, a small review set, and a note on what “better” means — faster, cheaper, clearer, or safer. Keep the harness small enough to run every time you change a prompt, a model, or a workflow step.',
			array(
				'Gold examples and obvious failures',
				'A review set you can rerun after each change',
				'What to log so regressions are visible',
			),
			'Build a 10-example review set for your workshop artifact and score it before and after one improvement.'
		);
		$extras[] = lac_cl(
			'Ship it: handoff, monitoring, and the next project',
			'Put the workflow where other people can use it without you in the room.',
			'A personal demo is not a shipped skill. This last lesson covers handing ' . $course_title . ' to a teammate or into a product: the short write-up, the failure cases to watch, and the next project that stretches the same muscle. You should be able to point to an artifact, a checklist, and an owner.',
			array(
				'A one-page handoff others can follow',
				'What to watch after the first week in use',
				'Choosing the next project that builds on this one',
			),
			'Write the handoff page for your workshop example and name the next project you will start within two weeks.'
		);
	}

	return $extras;
}

/**
 * Fallback outline when a course is not in the catalog.
 *
 * @param string $course_title Course title.
 * @param string $course_lede  Excerpt or short description.
 * @return array<int, array{title:string,excerpt:string,detail:string,topics:array<int,string>,practice:string}>
 */
function lac_curriculum_fallback_outline( $course_title, $course_lede ) {
	$course_title = is_string( $course_title ) && '' !== $course_title ? $course_title : 'this course';
	$course_lede  = is_string( $course_lede ) ? trim( $course_lede ) : '';
	$why          = '' !== $course_lede ? $course_lede : 'You will learn a practical workflow you can reuse at work.';

	return array(
		lac_cl(
			'What this course is for (and what it is not)',
			$why,
			$course_title . ' starts by naming the job to be done. You will see who the skill is for, which problems it actually solves, and which problems belong in a different course. That framing keeps later lessons focused. Read the outcomes once, then write one sentence for why you enrolled so you can check it at the end.',
			array(
				'The job this skill is meant to do',
				'Who gets value from it first',
				'What you will not cover so the path stays clear',
			),
			'Write one sentence: “I will use ' . $course_title . ' to ___ for ___ by the end of this week.”'
		),
		lac_cl(
			'Core ideas you need before you practice',
			'Learn the vocabulary and mental model used in every later lesson.',
			'Most confusion in ' . $course_title . ' is fuzzy language. This lesson defines the few terms you will reuse, the difference between a good result and a lucky one, and the constraints (time, cost, accuracy, tone) you must set up front. You should finish able to explain the model in plain language to a teammate.',
			array(
				'Key terms in everyday language',
				'What “good” looks like for this skill',
				'Constraints to set before you start generating',
			),
			'Define three terms from this lesson in your own words and give one example for each.'
		),
		lac_cl(
			'Tools, setup, and a working starter template',
			'Leave with a template you can copy instead of a blank page.',
			'A blank chat box wastes the first ten minutes of every session. In ' . $course_title . ' you will set up the workspace, save a starter template with role, task, and output format, and learn which inputs are worth preparing. The template is the artifact — you will keep using it in the workshop.',
			array(
				'What to prepare before you open the tool',
				'A starter template with role, task, and format',
				'How to save and version a prompt you like',
			),
			'Save a starter template for ' . $course_title . ' and run it once on a tiny real example.'
		),
		lac_cl(
			'A step-by-step workflow you can repeat',
			'Turn the template into a sequence with checks at each step.',
			'This is the main method of ' . $course_title . '. You will walk a complete sequence: gather inputs, run the first pass, critique the output, and revise with a tighter prompt. Each step has a stop rule so you do not loop forever. By the end you should be able to run the workflow without looking at the lesson.',
			array(
				'Inputs worth collecting',
				'First pass versus revision pass',
				'When to stop and ship the draft',
			),
			'Run the full workflow on one real task and label each step in your notes.'
		),
		lac_cl(
			'Guided practice with feedback you can see',
			'Produce a real artifact and judge it against a short rubric.',
			'Practice without a rubric feels busy and teaches little. Here you will complete a defined exercise for ' . $course_title . ', then score it on clarity, usefulness, and one course-specific criterion. If the score is low, you revise once — not five times. That is how the skill sticks.',
			array(
				'A practice task with a finish line',
				'A three-point rubric you can reuse',
				'One revision pass, then stop',
			),
			'Finish the practice artifact and score it 1–5 on clarity, usefulness, and accuracy.'
		),
		lac_cl(
			'Bring it into real work',
			'Fit the workflow into a calendar, a team, or a product.',
			'The last core lesson of ' . $course_title . ' is about transfer. You will pick where the workflow lives (a doc, a ticket, a feature), who else needs to see the output, and how you will notice if quality drops. Then you will choose the next small project that uses the same skill under slightly harder conditions.',
			array(
				'Where the workflow belongs in your week',
				'How to share outputs with other people',
				'A next project that stretches the same skill',
			),
			'Schedule the workflow once this week and write the next project in one line.'
		),
	);
}

/**
 * Resolve the catalog key for a course post.
 *
 * @param int $course_id Course post id.
 * @return string Catalog slug or empty string.
 */
function lac_curriculum_catalog_key( $course_id ) {
	$library = lac_curriculum_catalog();
	$slug    = sanitize_title( (string) get_post_field( 'post_name', $course_id ) );
	if ( isset( $library[ $slug ] ) ) {
		return $slug;
	}

	// Price-tier courses keep their original seed slug even if the public title changed.
	if ( function_exists( 'lac_get_purchase_course_titles' ) ) {
		$price  = (int) get_post_meta( $course_id, '_lac_course_price', true );
		$titles = lac_get_purchase_course_titles();
		if ( $price >= 1 && isset( $titles[ $price ] ) ) {
			$seed_slug = sanitize_title( $titles[ $price ] );
			if ( isset( $library[ $seed_slug ] ) ) {
				return $seed_slug;
			}
		}
	}

	$title_slug = sanitize_title( (string) get_the_title( $course_id ) );
	if ( isset( $library[ $title_slug ] ) ) {
		return $title_slug;
	}

	return '';
}

/**
 * Full lesson blueprint for a published course.
 *
 * @param int $course_id Course post id.
 * @return array<int, array{title:string,content:string,excerpt:string}>
 */
function lac_get_curriculum_blueprint_for_course( $course_id ) {
	$course_id = absint( $course_id );
	if ( $course_id < 1 ) {
		return array();
	}

	$course_title = get_the_title( $course_id );
	$course_lede  = get_the_excerpt( $course_id );
	$level        = strtolower( (string) get_post_meta( $course_id, '_lac_course_level', true ) );
	if ( ! in_array( $level, array( 'beginner', 'intermediate', 'advanced' ), true ) ) {
		$level = 'beginner';
	}

	$library = lac_curriculum_catalog();
	$key     = lac_curriculum_catalog_key( $course_id );
	$outline = ( '' !== $key && isset( $library[ $key ] ) )
		? $library[ $key ]
		: lac_curriculum_fallback_outline( $course_title, $course_lede );

	$outline = array_merge( $outline, lac_curriculum_depth_extras( $course_title, $level ) );
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

/**
 * Insert missing lessons from the detailed blueprint.
 *
 * @param int $course_id Course post id.
 * @return WP_Post[] Lessons after the ensure step.
 */
function lac_insert_curriculum_lessons_for_course( $course_id ) {
	$blueprint = lac_get_curriculum_blueprint_for_course( $course_id );
	foreach ( $blueprint as $lesson_index => $lesson ) {
		$lesson_id = wp_insert_post(
			array(
				'post_type'    => 'lac_lesson',
				'post_status'  => 'publish',
				'post_title'   => $lesson['title'],
				'post_excerpt' => $lesson['excerpt'],
				'post_content' => $lesson['content'],
				'menu_order'   => $lesson_index + 1,
			)
		);
		if ( ! is_wp_error( $lesson_id ) && $lesson_id ) {
			update_post_meta( $lesson_id, '_lac_parent_course_id', $course_id );
		}
	}

	lac_log_info( 'Created detailed curriculum for course ' . absint( $course_id ) );

	return lac_get_lessons_for_course( $course_id );
}

/**
 * Replace short stub curricula with the detailed outline.
 *
 * Existing lesson IDs stay stable when possible so continue-learning links
 * keep working. Extra lessons are inserted; leftover stub rows are trashed.
 *
 * @param int $course_id Course post id.
 * @return int Number of lessons after the refresh.
 */
function lac_refresh_course_curriculum( $course_id ) {
	$course_id = absint( $course_id );
	$blueprint = lac_get_curriculum_blueprint_for_course( $course_id );
	if ( empty( $blueprint ) ) {
		return 0;
	}

	$existing = lac_get_lessons_for_course( $course_id );

	foreach ( $blueprint as $index => $lesson ) {
		if ( isset( $existing[ $index ] ) && $existing[ $index ] instanceof WP_Post ) {
			wp_update_post(
				array(
					'ID'           => (int) $existing[ $index ]->ID,
					'post_title'   => $lesson['title'],
					'post_excerpt' => $lesson['excerpt'],
					'post_content' => $lesson['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index + 1,
				)
			);
			continue;
		}

		$lesson_id = wp_insert_post(
			array(
				'post_type'    => 'lac_lesson',
				'post_status'  => 'publish',
				'post_title'   => $lesson['title'],
				'post_excerpt' => $lesson['excerpt'],
				'post_content' => $lesson['content'],
				'menu_order'   => $index + 1,
			)
		);
		if ( ! is_wp_error( $lesson_id ) && $lesson_id ) {
			update_post_meta( $lesson_id, '_lac_parent_course_id', $course_id );
		}
	}

	$keep = count( $blueprint );
	if ( count( $existing ) > $keep ) {
		for ( $index = $keep, $total = count( $existing ); $index < $total; $index++ ) {
			if ( $existing[ $index ] instanceof WP_Post ) {
				wp_trash_post( (int) $existing[ $index ]->ID );
			}
		}
	}

	return count( lac_get_lessons_for_course( $course_id ) );
}

/**
 * One-time expansion of every published course curriculum.
 *
 * @return void
 */
function lac_expand_published_course_curricula() {
	if ( get_option( 'lac_curriculum_expanded_v1' ) ) {
		return;
	}
	if ( get_transient( 'lac_expanding_curriculum' ) ) {
		return;
	}
	set_transient( 'lac_expanding_curriculum', 1, 180 );

	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $course_ids as $course_id ) {
		lac_refresh_course_curriculum( (int) $course_id );
	}

	update_option( 'lac_curriculum_expanded_v1', 1 );
	delete_transient( 'lac_expanding_curriculum' );
	lac_log_info( 'Expanded published course curricula to detailed outlines.' );
}
add_action( 'init', 'lac_expand_published_course_curricula', 25 );
