<?php
/**
 * Front page for the eLearning Education child.
 *
 * Process: header from parent, institutional hero, path links, live course grid.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lac_course_count = (int) wp_count_posts( 'lac_course' )->publish;

$lac_course_query = new WP_Query(
	array(
		'post_type'      => 'lac_course',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<main id="tp_content" role="main">
	<section class="lac-hero">
		<div class="lac-wrap">
			<p class="lac-kicker">Continuing education</p>
			<h1>Courses in applied artificial intelligence</h1>
			<p class="lac-lede">Short, self-paced programs for people who already work with documents, products, or software and need a clearer way to use AI tools on the job.</p>
			<div class="lac-actions">
				<a class="lac-btn" href="<?php echo esc_url( get_post_type_archive_link( 'lac_course' ) ); ?>">View course catalog</a>
				<a class="lac-btn lac-btn--ghost" href="<?php echo esc_url( home_url( '/prompt-engineering/' ) ); ?>">Prompt engineering path</a>
			</div>
			<div class="lac-facts">
				<div>
					<strong><?php echo esc_html( $lac_course_count ); ?></strong>
					<span>Published courses</span>
				</div>
				<div>
					<strong>Self-paced</strong>
					<span>Study on your own schedule</span>
				</div>
				<div>
					<strong>From $1</strong>
					<span>Introductory through advanced</span>
				</div>
			</div>
		</div>
	</section>

	<section class="lac-section">
		<div class="lac-wrap">
			<h2>Learning paths</h2>
			<p class="lac-support">Start with one of these sequences, then open the full catalog for individual courses.</p>
			<div class="lac-path-grid">
				<a class="lac-path" href="<?php echo esc_url( home_url( '/prompt-engineering/' ) ); ?>">
					<strong>Prompt engineering</strong>
					<span>Prompt structure, refinement, and evaluating model output.</span>
				</a>
				<a class="lac-path" href="<?php echo esc_url( home_url( '/ai-agents/' ) ); ?>">
					<strong>AI agents</strong>
					<span>Tool use, task execution, and keeping automated runs reliable.</span>
				</a>
				<a class="lac-path" href="<?php echo esc_url( home_url( '/ai-automation/' ) ); ?>">
					<strong>AI automation</strong>
					<span>APIs and process flows that reduce repetitive work.</span>
				</a>
			</div>
		</div>
	</section>

	<section class="lac-section" style="padding-top:0;">
		<div class="lac-wrap">
			<h2>Recently published courses</h2>
			<p class="lac-support">A sample of the catalog. Open any course for duration, price, and lessons.</p>
			<?php if ( $lac_course_query->have_posts() ) : ?>
				<div class="lac-course-grid">
					<?php
					while ( $lac_course_query->have_posts() ) :
						$lac_course_query->the_post();
						get_template_part( 'template-parts/content', 'course-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
			<p style="margin:1.25rem 0 0;">
				<a class="lac-btn lac-btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'lac_course' ) ); ?>">All courses</a>
			</p>
		</div>
	</section>

	<section class="lac-section" style="padding-top:0;">
		<div class="lac-wrap">
			<h2>How enrollment works</h2>
			<div class="lac-steps">
				<div class="lac-step">
					<strong>1. Create an account</strong>
					<span>Use Create account or Sign in in the header. Enrollment requires a logged-in learner.</span>
				</div>
				<div class="lac-step">
					<strong>2. Enroll in a course</strong>
					<span>Open a course and use Enroll. Progress is stored against your account.</span>
				</div>
				<div class="lac-step">
					<strong>3. Follow the lessons</strong>
					<span>Lessons are listed on the course page in order. Return any time to continue.</span>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
