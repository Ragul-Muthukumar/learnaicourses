<?php
/**
 * Default index template for posts and fallback loops.
 *
 * What this file does:
 * - Lists standard posts when no more specific template matches.
 * Process: header → loop → footer.
 */

get_header();
?>
<main id="main" class="section">
	<div class="section__inner">
		<header class="section__header">
			<h1 class="section__title"><?php echo esc_html( get_the_archive_title() ); ?></h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-stack">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'post-stack__item' ); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div><?php the_excerpt(); ?></div>
					</article>
					<?php
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="empty-state">Nothing here yet.</p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
