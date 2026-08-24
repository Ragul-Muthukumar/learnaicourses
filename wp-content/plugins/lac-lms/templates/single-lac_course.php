<?php
/**
 * Single course product detail template (theme-independent).
 *
 * What this file does:
 * - Renders a full product page: hero, about, curriculum, and purchase sidebar.
 * Process:
 * 1) Load course meta, image, and lessons.
 * 2) Print the hero with price + purchase CTA.
 * 3) Print about content + curriculum list.
 * 4) Print sticky purchase card in the sidebar.
 */

 // Abort if WordPress did not bootstrap this request.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

 // Ensure the main query is on the current course post.
while ( have_posts() ) :
	the_post();
	 // Capture the course id for meta and CTAs.
	$course_id = get_the_ID();
	 // Read LMS product meta for the detail strip.
	$course_level  = get_post_meta( $course_id, '_lac_course_level', true );
	$course_hours  = get_post_meta( $course_id, '_lac_course_hours', true );
	$course_price  = function_exists( 'lac_get_course_price' ) ? lac_get_course_price( $course_id ) : (float) get_post_meta( $course_id, '_lac_course_price', true );
	$course_image  = get_the_post_thumbnail_url( $course_id, 'large' );
	$lessons       = function_exists( 'lac_get_lessons_for_course' ) ? lac_get_lessons_for_course( $course_id ) : array();
	$lessons_count = is_array( $lessons ) ? count( $lessons ) : 0;
	 // Format a human-readable price label.
	$price_label = ( $course_price > 0 ) ? '$' . number_format( $course_price, 2 ) : 'Free';
	 // Detect enrollment for optional badge copy.
	$is_enrolled = is_user_logged_in() && function_exists( 'lac_db_is_user_enrolled' ) && lac_db_is_user_enrolled( get_current_user_id(), $course_id );
	?>
<main id="main" class="lac-product">
	<article <?php post_class( 'lac-product__article' ); ?>>
		<header class="lac-product__hero">
			<div class="lac-product__hero-inner">
				<div class="lac-product__hero-copy">
					<p class="lac-product__eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
					<h1 class="lac-product__title"><?php the_title(); ?></h1>
					<p class="lac-product__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<ul class="lac-product__meta">
						<li><?php echo esc_html( ucfirst( $course_level ? $course_level : 'beginner' ) ); ?></li>
						<li><?php echo esc_html( $course_hours ? $course_hours . ' hours' : 'Self-paced' ); ?></li>
						<li><?php echo esc_html( $lessons_count . ' lessons' ); ?></li>
						<li class="lac-product__meta-price"><?php echo esc_html( $price_label ); ?></li>
					</ul>
					<?php if ( $is_enrolled ) : ?>
						<p class="lac-product__badge">You own this course</p>
					<?php endif; ?>
					<div class="lac-product__hero-actions">
						<?php
						 // Render free enroll or PayPal / mock purchase controls.
						if ( function_exists( 'lac_render_enrollment_button' ) ) {
							echo lac_render_enrollment_button( $course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
				</div>
				<div class="lac-product__hero-media">
					<?php if ( $course_image ) : ?>
						<img src="<?php echo esc_url( $course_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
					<?php else : ?>
						<div class="lac-product__hero-placeholder"><span>AI Course</span></div>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<section class="lac-product__body">
			<div class="lac-product__layout">
				<div class="lac-product__main">
					<h2 class="lac-product__section-title">About this course</h2>
					<div class="lac-product__content prose">
						<?php the_content(); ?>
					</div>

					<h2 class="lac-product__section-title">What you get</h2>
					<ul class="lac-product__perks">
						<li>Lifetime access to course materials</li>
						<li>Practical lessons you can apply immediately</li>
						<li>Self-paced learning on any device</li>
						<li>Progress tracking after purchase</li>
					</ul>

					<h2 class="lac-product__section-title">Curriculum</h2>
					<?php if ( ! empty( $lessons ) ) : ?>
						<ol class="lac-lesson-list">
							<?php foreach ( $lessons as $lesson_post ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $lesson_post ) ); ?>">
										<span><?php echo esc_html( get_the_title( $lesson_post ) ); ?></span>
										<span aria-hidden="true">→</span>
									</a>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php else : ?>
						<p class="lac-product__empty">Lessons unlock after you purchase and enroll.</p>
					<?php endif; ?>
				</div>

				<aside class="lac-product__sidebar">
					<div class="lac-product__buy-card">
						<p class="lac-product__buy-eyebrow">Purchase</p>
						<p class="lac-product__buy-price"><?php echo esc_html( $price_label ); ?></p>
						<ul class="lac-product__buy-list">
							<li><strong>Level:</strong> <?php echo esc_html( ucfirst( $course_level ? $course_level : 'beginner' ) ); ?></li>
							<li><strong>Duration:</strong> <?php echo esc_html( $course_hours ? $course_hours . ' hours' : 'Self-paced' ); ?></li>
							<li><strong>Lessons:</strong> <?php echo esc_html( (string) $lessons_count ); ?></li>
							<li><strong>Access:</strong> Lifetime</li>
						</ul>
						<div class="lac-product__buy-actions">
							<?php
							 // Duplicate purchase CTA for sticky sidebar convenience.
							if ( function_exists( 'lac_render_enrollment_button' ) ) {
								echo lac_render_enrollment_button( $course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<p class="lac-product__buy-note">
							<?php
							echo esc_html(
								$course_price > 0
									? 'Secure checkout. You are enrolled immediately after payment.'
									: 'Free course — enroll instantly when signed in.'
							);
							?>
						</p>
					</div>
				</aside>
			</div>
		</section>
	</article>
</main>
	<?php
endwhile;

get_footer();
