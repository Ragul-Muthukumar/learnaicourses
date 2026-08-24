<?php
/**
 * Single course template.
 *
 * What this file does:
 * - Shows course overview, enroll CTA, and ordered lesson curriculum.
 * Process:
 * 1) Render course title and meta.
 * 2) Print enrollment button from the LMS plugin.
 * 3) List linked lessons in menu order.
 */

get_header();

 // Read course meta for the detail strip.
$lac_course_id    = get_the_ID();
$lac_course_level = get_post_meta( $lac_course_id, '_lac_course_level', true );
$lac_course_hours = get_post_meta( $lac_course_id, '_lac_course_hours', true );
$lac_course_price = get_post_meta( $lac_course_id, '_lac_course_price', true );
$lac_lessons      = function_exists( 'lac_get_lessons_for_course' ) ? lac_get_lessons_for_course( $lac_course_id ) : array();
$lac_course_image = get_the_post_thumbnail_url( $lac_course_id, 'large' );
$lac_lessons_count = is_array( $lac_lessons ) ? count( $lac_lessons ) : 0;
?>
<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'course-detail' ); ?>>
			<header class="course-detail__hero" data-reveal>
				<div class="course-detail__content course-detail__content--split">
					<div class="course-detail__copy">
						<p class="course-detail__brand"><?php bloginfo( 'name' ); ?></p>
						<h1 class="course-detail__title"><?php the_title(); ?></h1>
						<p class="course-detail__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<ul class="course-detail__meta">
							<li><?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></li>
							<li><?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></li>
							<li><?php echo esc_html( ( floatval( $lac_course_price ) > 0 ) ? '$' . number_format( (float) $lac_course_price, 0 ) : 'Free' ); ?></li>
						</ul>
						<div class="course-detail__actions">
							<?php
							if ( function_exists( 'lac_render_enrollment_button' ) ) {
								echo lac_render_enrollment_button( $lac_course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
					</div>
					<div class="course-detail__media">
						<?php if ( $lac_course_image ) : ?>
							<img src="<?php echo esc_url( $lac_course_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
						<?php else : ?>
							<div class="course-detail__placeholder">
								<span>AI Course</span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</header>
			<section class="section" data-reveal>
				<div class="section__inner">
					<div class="course-layout">
						<div class="course-layout__main">
							<h2 class="section__title">About this course</h2>
							<div class="prose"><?php the_content(); ?></div>
							<h2 class="section__title">Curriculum</h2>
							<?php if ( ! empty( $lac_lessons ) ) : ?>
								<ol class="lac-lesson-list">
									<?php foreach ( $lac_lessons as $lac_lesson ) : ?>
										<li>
											<a href="<?php echo esc_url( get_permalink( $lac_lesson ) ); ?>">
												<span><?php echo esc_html( get_the_title( $lac_lesson ) ); ?></span>
												<span aria-hidden="true">→</span>
											</a>
										</li>
									<?php endforeach; ?>
								</ol>
							<?php else : ?>
								<p class="empty-state">Lessons will appear here soon.</p>
							<?php endif; ?>
						</div>
						<aside class="course-layout__sidebar">
							<div class="course-sidebar-card">
								<p class="course-sidebar-card__eyebrow">Course summary</p>
								<p class="course-sidebar-card__price"><?php echo esc_html( ( floatval( $lac_course_price ) > 0 ) ? '$' . number_format( (float) $lac_course_price, 0 ) : 'Free' ); ?></p>
								<ul class="course-sidebar-card__list">
									<li><strong>Level:</strong> <?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></li>
									<li><strong>Duration:</strong> <?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></li>
									<li><strong>Lessons:</strong> <?php echo esc_html( $lac_lessons_count ); ?></li>
									<li><strong>Access:</strong> Lifetime learning access</li>
								</ul>
								<div class="course-sidebar-card__actions">
									<?php
									if ( function_exists( 'lac_render_enrollment_button' ) ) {
										echo lac_render_enrollment_button( $lac_course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
								</div>
							</div>
						</aside>
					</div>
				</div>
			</section>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
