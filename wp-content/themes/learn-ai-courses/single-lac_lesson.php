<?php
/**
 * Single lesson template.
 *
 * What this file does:
 * - Displays lesson content with a link back to the parent course.
 * Process: load parent course meta → render lesson body → footer.
 */

get_header();

$lac_parent_course_id = (int) get_post_meta( get_the_ID(), '_lac_parent_course_id', true );
?>
<main id="main" class="section">
	<div class="section__inner section__inner--narrow" data-reveal>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php if ( $lac_parent_course_id ) : ?>
				<p class="crumb">
					<a href="<?php echo esc_url( get_permalink( $lac_parent_course_id ) ); ?>">
						← <?php echo esc_html( get_the_title( $lac_parent_course_id ) ); ?>
					</a>
				</p>
			<?php endif; ?>
			<article <?php post_class(); ?>>
				<h1 class="section__title"><?php the_title(); ?></h1>
				<div class="prose"><?php the_content(); ?></div>
			</article>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
