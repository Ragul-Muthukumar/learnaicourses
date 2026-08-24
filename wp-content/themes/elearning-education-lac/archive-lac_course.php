<?php
/**
 * Course catalog archive.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lac_archive_count = (int) wp_count_posts( 'lac_course' )->publish;
?>
<main id="tp_content" role="main">
	<header class="lac-page-head">
		<div class="lac-wrap">
			<p class="lac-kicker">Catalog</p>
			<h1>Course catalog</h1>
			<p class="lac-lede"><?php echo esc_html( $lac_archive_count ); ?> courses covering prompting, APIs, agents, and applied workflows. Open a course for level, hours, and price.</p>
		</div>
	</header>
	<div class="lac-section" style="padding-top:0;">
		<div class="lac-wrap">
			<?php if ( have_posts() ) : ?>
				<div class="lac-course-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'course-card' );
					endwhile;
					?>
				</div>
				<div class="lac-pagination">
					<?php the_posts_pagination(); ?>
				</div>
			<?php else : ?>
				<p>No courses are published yet.</p>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php
get_footer();
