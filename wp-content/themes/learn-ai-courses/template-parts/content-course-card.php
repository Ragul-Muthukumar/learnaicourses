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
$lac_thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
?>
<a class="course-card" href="<?php the_permalink(); ?>">
	<?php if ( $lac_thumbnail_url ) : ?>
		<div class="course-card__media">
			<img src="<?php echo esc_url( $lac_thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
		</div>
	<?php endif; ?>
	<div class="course-card__body">
		<div class="course-card__badges">
			<p class="course-card__level"><?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></p>
			<p class="course-card__price"><?php echo esc_html( $lac_price_label ); ?></p>
		</div>
		<h3 class="course-card__title"><?php the_title(); ?></h3>
		<p class="course-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<div class="course-card__meta">
			<span><?php echo esc_html( $lac_course_hours ? $lac_course_hours . 'h' : 'Self-paced' ); ?></span>
			<span>View course</span>
		</div>
	</div>
</a>
