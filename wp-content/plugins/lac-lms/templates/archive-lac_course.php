<?php
/**
 * Course catalog archive template (theme-independent).
 *
 * What this file does:
 * - Lists every published course as a product card with price and detail link.
 * Process:
 * 1) Print catalog heading.
 * 2) Loop published lac_course posts.
 * 3) Show thumbnail, price, excerpt, and View details CTA.
 */

 // Abort if WordPress did not bootstrap this request.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="lac-catalog">
	<header class="lac-catalog__hero">
		<div class="lac-catalog__hero-inner">
			<p class="lac-catalog__eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<h1 class="lac-catalog__title">All courses</h1>
			<p class="lac-catalog__subtitle">Open any course for full details and purchase options.</p>
		</div>
	</header>

	<section class="lac-catalog__grid-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="lac-catalog__grid">
				<?php
				while ( have_posts() ) :
					the_post();
					 // Capture ids and price for the product card.
					$course_id    = get_the_ID();
					$course_price = function_exists( 'lac_get_course_price' ) ? lac_get_course_price( $course_id ) : (float) get_post_meta( $course_id, '_lac_course_price', true );
					$course_level = get_post_meta( $course_id, '_lac_course_level', true );
					$price_label  = ( $course_price > 0 ) ? '$' . number_format( $course_price, 2 ) : 'Free';
					$thumb_url    = get_the_post_thumbnail_url( $course_id, 'medium_large' );
					?>
					<article <?php post_class( 'lac-catalog-card' ); ?>>
						<a class="lac-catalog-card__media" href="<?php the_permalink(); ?>">
							<?php if ( $thumb_url ) : ?>
								<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
							<?php else : ?>
								<span class="lac-catalog-card__placeholder">AI</span>
							<?php endif; ?>
						</a>
						<div class="lac-catalog-card__body">
							<p class="lac-catalog-card__price"><?php echo esc_html( $price_label ); ?></p>
							<h2 class="lac-catalog-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="lac-catalog-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<p class="lac-catalog-card__meta"><?php echo esc_html( ucfirst( $course_level ? $course_level : 'beginner' ) ); ?></p>
							<a class="lac-catalog-card__cta" href="<?php the_permalink(); ?>">
								<?php echo esc_html( $course_price > 0 ? 'View details & buy →' : 'View details & enroll →' ); ?>
							</a>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>
			<div class="lac-catalog__pagination">
				<?php
				 // Render archive pagination when more than one page exists.
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '← Prev',
						'next_text' => 'Next →',
					)
				);
				?>
			</div>
		<?php else : ?>
			<p class="lac-catalog__empty">No courses published yet.</p>
		<?php endif; ?>
	</section>
</main>
<?php
get_footer();
