<?php
/**
 * Standard pages without the empty image banner.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="tp_content" role="main">
	<div class="lac-wrap" style="padding:2.25rem 0 3rem;">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1><?php the_title(); ?></h1>
			<div class="entry-content"><?php the_content(); ?></div>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
