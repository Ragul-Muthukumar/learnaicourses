<?php
/**
 * Front page template — brand-first hero plus course catalog.
 *
 * What this file does:
 * - Renders one hero composition, then a single courses section.
 * Process:
 * 1) Output full-bleed hero with brand, headline, support line, CTA.
 * 2) Query published courses and list them in one purpose-built section.
 */

get_header();

 // Query published LMS courses for the catalog section.
$lac_course_query = new WP_Query(
	array(
		'post_type'      => 'lac_course',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<main id="main">
	<section class="hero" data-reveal>
		<div class="hero__atmosphere" aria-hidden="true"></div>
		<div class="hero__content">
			<p class="hero__brand"><?php bloginfo( 'name' ); ?></p>
			<h1 class="hero__headline">Learn AI by building real skills</h1>
			<p class="hero__support">Short, practical courses that turn AI concepts into shipped features.</p>
			<div class="hero__actions">
				<a class="button button--primary" href="<?php echo esc_url( get_post_type_archive_link( 'lac_course' ) ); ?>">Browse courses</a>
				<?php if ( ! is_user_logged_in() ) : ?>
					<a class="button button--ghost" href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>">Sign in</a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="section section--courses" data-reveal>
		<div class="section__inner">
			<header class="section__header">
				<h2 class="section__title">Courses</h2>
				<p class="section__support">Start with foundations, then move into APIs and agents.</p>
			</header>
			<?php if ( $lac_course_query->have_posts() ) : ?>
				<div class="course-grid">
					<?php
					while ( $lac_course_query->have_posts() ) :
						$lac_course_query->the_post();
						get_template_part( 'template-parts/content', 'course-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php else : ?>
				<p class="empty-state">Courses will appear here once published.</p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();
