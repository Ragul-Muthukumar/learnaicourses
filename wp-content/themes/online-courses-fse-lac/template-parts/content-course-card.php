<?php
/**
 * Course card used on the catalog.
 *
 * Process: read course meta, then render a linked card with image, level, and price.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lac_course_level  = get_post_meta( get_the_ID(), '_lac_course_level', true );
$lac_course_hours  = get_post_meta( get_the_ID(), '_lac_course_hours', true );
$lac_course_price  = get_post_meta( get_the_ID(), '_lac_course_price', true );
$lac_price_label   = ( floatval( $lac_course_price ) > 0 ) ? '$' . number_format( (float) $lac_course_price, 0 ) : 'Free';
$lac_thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
?>
<a class="course-card" href="<?php the_permalink(); ?>">
	<div class="course-card__media">
		<?php if ( $lac_thumbnail_url ) : ?>
			<img src="<?php echo esc_url( $lac_thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
		<?php endif; ?>
	</div>
	<div class="course-card__body">
		<div class="course-card__meta-row">
			<span><?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></span>
			<span><?php echo esc_html( $lac_price_label ); ?></span>
		</div>
		<h3 class="course-card__title"><?php the_title(); ?></h3>
		<p class="course-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<div class="course-card__meta-row">
			<span><?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></span>
			<span>View Course</span>
		</div>
	</div>
</a>
