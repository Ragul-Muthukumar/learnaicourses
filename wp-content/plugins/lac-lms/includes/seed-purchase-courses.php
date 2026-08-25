<?php
/**
 * Seeds 50 purchasable AI course posts priced $1 through $50.
 *
 * What this file does:
 * - Inserts lac_course posts with price, level, and hours meta.
 * Process:
 * 1) Skip if the lac_purchase_courses_seeded option is already set.
 * 2) Loop prices 1–50 and create one course per price tier.
 * 3) Mark the option so the batch is not duplicated on reruns.
 */

 // Block direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return AI course title ideas indexed by price tier.
 *
 * @return array<int, string> Map of price => course title.
 */
function lac_get_purchase_course_titles() {
	 // One unique AI course title per dollar tier from $1 to $50.
	return array(
		1  => 'AI Basics: Your First Prompt',
		2  => 'ChatGPT for Everyday Tasks',
		3  => 'Write Better Emails with AI',
		4  => 'AI Image Prompts for Beginners',
		5  => 'Summarize Documents with AI',
		6  => 'AI for Social Media Captions',
		7  => 'Build a Simple AI Chatbot',
		8  => 'AI for Student Research',
		9  => 'Automate Spreadsheets with AI',
		10 => 'Midjourney Starter Guide',
		11 => 'AI Copywriting Fundamentals',
		12 => 'Prompt Engineering Essentials',
		13 => 'AI for Customer Support',
		14 => 'Create AI-Powered FAQs',
		15 => 'AI Video Script Writing',
		16 => 'Fine-Tune Your AI Workflow',
		17 => 'AI for E-commerce Product Descriptions',
		18 => 'Build AI Forms That Convert',
		19 => 'AI Translation & Localization',
		20 => 'Retrieval Basics for AI Apps',
		21 => 'AI for HR & Recruiting',
		22 => 'Voice AI & Text-to-Speech',
		23 => 'AI for Marketing Analytics',
		24 => 'Custom GPTs for Your Business',
		25 => 'AI Content Calendar Automation',
		26 => 'Embeddings Explained Simply',
		27 => 'AI for Code Review',
		28 => 'Build an AI Writing Assistant',
		29 => 'AI for Legal Document Drafting',
		30 => 'Multimodal AI Fundamentals',
		31 => 'AI Agents: Plan & Execute',
		32 => 'RAG Pipelines from Scratch',
		33 => 'AI for Sales Outreach',
		34 => 'Evaluate LLM Output Quality',
		35 => 'AI for Healthcare Admin',
		36 => 'Fine-Tuning vs Prompting',
		37 => 'AI Security & Guardrails',
		38 => 'Build AI Dashboards',
		39 => 'AI for Real Estate Listings',
		40 => 'Production LLM Deployment',
		41 => 'AI for Financial Forecasting',
		42 => 'Multi-Agent Orchestration',
		43 => 'AI for Education & Tutoring',
		44 => 'Computer Vision for Builders',
		45 => 'AI API Integration Masterclass',
		46 => 'Cost Optimization for AI Products',
		47 => 'AI Compliance & Ethics',
		48 => 'Enterprise AI Strategy',
		49 => 'Build a Full AI SaaS MVP',
		50 => 'AI Mastery: End-to-End Product',
	);
}

/**
 * Map price tier to course difficulty level.
 *
 * @param int $price_tier Dollar price from 1 to 50.
 * @return string beginner|intermediate|advanced
 */
function lac_get_level_for_price( $price_tier ) {
	 // Beginner courses for the lowest price band.
	if ( $price_tier <= 15 ) {
		return 'beginner';
	}
	 // Intermediate courses for the mid price band.
	if ( $price_tier <= 35 ) {
		return 'intermediate';
	}
	 // Advanced courses for the top price band.
	return 'advanced';
}

/**
 * Estimate course hours from the price tier.
 *
 * @param int $price_tier Dollar price from 1 to 50.
 * @return float Estimated learning hours.
 */
function lac_get_hours_for_price( $price_tier ) {
	 // Scale hours roughly with price: ~0.5h at $1 up to ~25h at $50.
	return round( max( 0.5, $price_tier * 0.5 ), 1 );
}

/**
 * Seed 50 purchasable AI courses if not already done.
 *
 * @return array{created:int, skipped:bool} Summary of the seed run.
 */
function lac_seed_purchase_courses_if_needed() {
	 // Return early when this batch was already inserted.
	if ( get_option( 'lac_purchase_courses_seeded' ) ) {
		lac_log_info( 'Purchase course seed skipped; already complete.' );
		return array(
			'created' => 0,
			'skipped' => true,
		);
	}

	 // Load the title map for all 50 tiers.
	$course_titles = lac_get_purchase_course_titles();
	 // Track how many posts were successfully created.
	$created_count = 0;

	 // Insert one course per price from $1 to $50.
	for ( $price_tier = 1; $price_tier <= 50; $price_tier++ ) {
		// Resolve title with a fallback when the map is missing an entry.
		$course_title = isset( $course_titles[ $price_tier ] )
			? $course_titles[ $price_tier ]
			: 'AI Course Tier ' . $price_tier;

		// Build excerpt and body copy for the purchasable listing.
		$course_excerpt = sprintf(
			'Purchasable AI course — $%d. Learn practical skills you can apply immediately.',
			$price_tier
		);
		$course_content = sprintf(
			'<p><strong>%s</strong> is a hands-on AI course priced at <strong>$%d</strong>.</p><p>Includes video lessons, exercises, and a certificate of completion. Enroll now to start learning.</p>',
			esc_html( $course_title ),
			$price_tier
		);

		// Insert the published course post.
		$course_id = wp_insert_post(
			array(
				'post_type'    => 'lac_course',
				'post_status'  => 'publish',
				'post_title'   => $course_title,
				'post_excerpt' => $course_excerpt,
				'post_content' => $course_content,
			)
		);

		// Skip meta when the insert failed.
		if ( is_wp_error( $course_id ) || ! $course_id ) {
			lac_log_error( 'Failed to seed purchase course at $' . $price_tier );
			continue;
		}

		// Store purchasable price and supporting meta.
		update_post_meta( $course_id, '_lac_course_price', (float) $price_tier );
		update_post_meta( $course_id, '_lac_course_level', lac_get_level_for_price( $price_tier ) );
		update_post_meta( $course_id, '_lac_course_hours', lac_get_hours_for_price( $price_tier ) );

		// Attach a default curriculum so Continue Learning has a real destination.
		if ( function_exists( 'lac_ensure_default_lessons_for_course' ) ) {
			lac_ensure_default_lessons_for_course( $course_id );
		}

		// Increment the success counter.
		$created_count++;
	}

	// Mark batch complete so reruns do not duplicate posts.
	update_option( 'lac_purchase_courses_seeded', 1 );
	lac_log_info( 'Purchase course seed complete: ' . $created_count . ' courses created.' );

	// Return a summary for CLI callers.
	return array(
		'created' => $created_count,
		'skipped' => false,
	);
}

/**
 * Backfill a default curriculum for published courses that have no lessons.
 *
 * The original purchase-course seed created priced posts without lessons, so
 * Continue Learning fell back to the course permalink and appeared to reload.
 *
 * @return void
 */
function lac_backfill_missing_course_lessons() {
	if ( get_option( 'lac_course_lessons_backfilled' ) ) {
		return;
	}

	// Avoid overlapping inserts if two requests hit init at the same time.
	if ( get_transient( 'lac_backfilling_lessons' ) ) {
		return;
	}
	set_transient( 'lac_backfilling_lessons', 1, 120 );

	if ( ! function_exists( 'lac_ensure_default_lessons_for_course' ) ) {
		delete_transient( 'lac_backfilling_lessons' );
		return;
	}

	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $course_ids as $course_id ) {
		lac_ensure_default_lessons_for_course( (int) $course_id );
	}

	update_option( 'lac_course_lessons_backfilled', 1 );
	delete_transient( 'lac_backfilling_lessons' );
	lac_log_info( 'Backfilled default lessons for courses missing a curriculum.' );
}
add_action( 'init', 'lac_backfill_missing_course_lessons', 20 );
