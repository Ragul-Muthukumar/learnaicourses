<?php
/**
 * Single course: overview, enrollment, and lesson list.
 *
 * Process: load LMS meta, render the header and sidebar summary, then curriculum.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lac_course_id     = get_the_ID();
$lac_course_level  = get_post_meta( $lac_course_id, '_lac_course_level', true );
$lac_course_hours  = get_post_meta( $lac_course_id, '_lac_course_hours', true );
$lac_course_price  = get_post_meta( $lac_course_id, '_lac_course_price', true );
$lac_lessons       = function_exists( 'lac_get_lessons_for_course' ) ? lac_get_lessons_for_course( $lac_course_id ) : array();
$lac_course_image  = get_the_post_thumbnail_url( $lac_course_id, 'large' );
$lac_lessons_count = is_array( $lac_lessons ) ? count( $lac_lessons ) : 0;
$lac_price_label   = ( floatval( $lac_course_price ) > 0 ) ? '$' . number_format( (float) $lac_course_price, 0 ) : 'Free';
?>
<div id="primary" <?php astra_primary_class(); ?>>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<header class="lac-course-hero">
			<div class="lac-wrap lac-course-hero__grid">
				<div>
					<p class="lac-kicker">Course</p>
					<h1><?php the_title(); ?></h1>
					<p class="lac-lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<ul class="lac-meta">
						<li><?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></li>
						<li><?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></li>
						<li><?php echo esc_html( $lac_price_label ); ?></li>
					</ul>
					<?php
					if ( function_exists( 'lac_render_enrollment_button' ) ) {
						echo lac_render_enrollment_button( $lac_course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<div>
					<?php if ( $lac_course_image ) : ?>
						<img src="<?php echo esc_url( $lac_course_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
					<?php endif; ?>
				</div>
			</div>
		</header>
		<div class="lac-wrap lac-layout">
			<div>
				<h2>About this course</h2>
				<div class="entry-content"><?php the_content(); ?></div>
				<h2>Curriculum</h2>
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
					<p>Lessons will be listed here when they are published.</p>
				<?php endif; ?>
			</div>
			<aside class="lac-summary">
				<p class="lac-kicker">Summary</p>
				<p class="lac-summary__price"><?php echo esc_html( $lac_price_label ); ?></p>
				<ul>
					<li>Level: <?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></li>
					<li>Duration: <?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></li>
					<li>Lessons: <?php echo esc_html( $lac_lessons_count ); ?></li>
					<li>Access: lifetime</li>
				</ul>
				<?php
				if ( function_exists( 'lac_render_enrollment_button' ) ) {
					echo lac_render_enrollment_button( $lac_course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</aside>
		</div>
		<?php
	endwhile;
	?>
</div>
<?php
get_footer();
