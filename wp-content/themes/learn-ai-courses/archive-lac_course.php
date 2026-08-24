<?php
/**
 * Course archive template.
 *
 * What this file does:
 * - Lists all published AI courses in a single catalog section.
 * Process: header → course grid → footer.
 */

get_header();
?>
<main id="main" class="section section--courses">
	<div class="section__inner">
		<header class="section__header" data-reveal>
			<h1 class="section__title">All courses</h1>
			<p class="section__support">Pick a path and enroll to track your progress.</p>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="course-grid" data-reveal>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'course-card' );
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="empty-state">No courses published yet.</p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
