<?php
/**
 * Course card partial used on home and archive grids.
 *
 * What this file does:
 * - Prints one interactive course link with level, hours, and price.
 * Process: read meta → render anchor card for clicking into the course.
 */

$lac_course_level = get_post_meta( get_the_ID(), '_lac_course_level', true );
$lac_course_hours = get_post_meta( get_the_ID(), '_lac_course_hours', true );
$lac_course_price = get_post_meta( get_the_ID(), '_lac_course_price', true );
$lac_price_label  = ( floatval( $lac_course_price ) > 0 ) ? '$' . number_format( (float) $lac_course_price, 0 ) : 'Free';
?>
<a class="course-card" href="<?php the_permalink(); ?>">
	<div class="course-card__body">
		<p class="course-card__level"><?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></p>
		<h3 class="course-card__title"><?php the_title(); ?></h3>
		<p class="course-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<div class="course-card__meta">
			<span><?php echo esc_html( $lac_course_hours ? $lac_course_hours . 'h' : 'Self-paced' ); ?></span>
			<span><?php echo esc_html( $lac_price_label ); ?></span>
		</div>
	</div>
</a>
