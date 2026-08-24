<?php
/**
 * Generic page template.
 *
 * What this file does:
 * - Renders standard WordPress pages with the theme chrome.
 */

get_header();
?>
<main id="main" class="section">
	<div class="section__inner section__inner--narrow" data-reveal>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
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
