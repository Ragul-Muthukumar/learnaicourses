<?php
/**
 * Course archive template.
 *
 * What this file does:
 * - Lists all published AI courses in a single catalog section.
 * Process: header → course grid → footer.
 */

get_header();

 // Count total published courses for archive proof.
$lac_archive_count = (int) wp_count_posts( 'lac_course' )->publish;
?>
<main id="main">
	<section class="catalog-hero" data-reveal>
		<div class="section__inner catalog-hero__inner">
			<div class="catalog-hero__copy">
				<p class="section__eyebrow">Explore the library</p>
				<h1 class="catalog-hero__title">AI courses designed like a product catalog</h1>
				<p class="catalog-hero__text">From $1 entry points to advanced product strategy, this library is organized for discovery, not just storage.</p>
			</div>
			<div class="catalog-hero__stats">
				<div class="catalog-hero__stat">
					<span class="catalog-hero__stat-value"><?php echo esc_html( $lac_archive_count ); ?></span>
					<span class="catalog-hero__stat-label">Published courses</span>
				</div>
				<div class="catalog-hero__stat">
					<span class="catalog-hero__stat-value">Visual</span>
					<span class="catalog-hero__stat-label">Image-first browsing</span>
				</div>
				<div class="catalog-hero__stat">
					<span class="catalog-hero__stat-value">Flexible</span>
					<span class="catalog-hero__stat-label">Beginner to advanced</span>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--courses">
		<div class="section__inner">
			<header class="section__header" data-reveal>
				<p class="section__eyebrow">Catalog</p>
				<h2 class="section__title">Find the next course worth buying</h2>
				<p class="section__support">Scan by price, skill level, and topic, then open the course page for the full breakdown.</p>
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
	</section>
	<section class="section section--catalog-note" data-reveal>
		<div class="section__inner">
			<div class="catalog-note">
				<p class="catalog-note__eyebrow">Designed for selling</p>
				<h2 class="catalog-note__title">This theme now behaves more like a premium storefront than a default blog archive.</h2>
				<p class="catalog-note__text">The grid, image treatment, pricing badges, and hero framing are all tuned to make course discovery feel intentional.</p>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
