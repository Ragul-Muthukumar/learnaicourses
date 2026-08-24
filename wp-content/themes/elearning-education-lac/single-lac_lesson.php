<?php
/**
 * Single lesson with a link back to the parent course.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lac_parent_course_id = (int) get_post_meta( get_the_ID(), '_lac_parent_course_id', true );
?>
<main id="tp_content" role="main">
	<div class="lac-wrap lac-lesson" style="padding:2.25rem 0 3rem;">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php if ( $lac_parent_course_id ) : ?>
				<p class="lac-crumb">
					<a href="<?php echo esc_url( get_permalink( $lac_parent_course_id ) ); ?>">
						← <?php echo esc_html( get_the_title( $lac_parent_course_id ) ); ?>
					</a>
				</p>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<div class="entry-content"><?php the_content(); ?></div>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
