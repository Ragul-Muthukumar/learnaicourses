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

 // Count published courses for homepage proof points.
$lac_course_count = (int) wp_count_posts( 'lac_course' )->publish;

 // Load the cheapest course so the value proposition feels immediate.
$lac_starting_courses = get_posts(
	array(
		'post_type'      => 'lac_course',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_lac_course_price',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
	)
);

 // Read the lowest available starting price when a course exists.
$lac_starting_price = ! empty( $lac_starting_courses ) ? (float) get_post_meta( $lac_starting_courses[0]->ID, '_lac_course_price', true ) : 0;

 // Load a higher-priced course for the featured showcase card.
$lac_featured_courses = get_posts(
	array(
		'post_type'      => 'lac_course',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_lac_course_price',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	)
);

 // Extract the featured post object when available.
$lac_featured_course = ! empty( $lac_featured_courses ) ? $lac_featured_courses[0] : null;

 // Read featured meta for the premium showcase.
$lac_featured_price = $lac_featured_course ? (float) get_post_meta( $lac_featured_course->ID, '_lac_course_price', true ) : 0;
$lac_featured_hours = $lac_featured_course ? get_post_meta( $lac_featured_course->ID, '_lac_course_hours', true ) : '';
$lac_featured_level = $lac_featured_course ? get_post_meta( $lac_featured_course->ID, '_lac_course_level', true ) : '';
$lac_featured_image = $lac_featured_course ? get_the_post_thumbnail_url( $lac_featured_course->ID, 'large' ) : '';
?>
<main id="main">
	<section class="hero" data-reveal>
		<div class="hero__content hero__content--split">
			<div class="hero__copy">
				<p class="hero__eyebrow">Launch practical AI skills faster</p>
				<h1 class="hero__headline">A cleaner way to buy and learn AI courses</h1>
				<p class="hero__support">Explore curated AI training, start with low-cost lessons, and grow into agent, API, and product workflows without the clutter.</p>
				<div class="hero__actions">
					<a class="button button--primary" href="<?php echo esc_url( get_post_type_archive_link( 'lac_course' ) ); ?>">Browse courses</a>
					<a class="button button--secondary" href="<?php echo esc_url( home_url( '/wp-json/lac-lms/v1/courses' ) ); ?>">View API</a>
				</div>
				<ul class="hero__stats">
					<li>
						<span class="hero__stat-value"><?php echo esc_html( $lac_course_count ); ?>+</span>
						<span class="hero__stat-label">AI courses</span>
					</li>
					<li>
						<span class="hero__stat-value"><?php echo esc_html( '$' . number_format( $lac_starting_price, 0 ) ); ?></span>
						<span class="hero__stat-label">Starting price</span>
					</li>
					<li>
						<span class="hero__stat-value">Self-paced</span>
						<span class="hero__stat-label">Anytime access</span>
					</li>
				</ul>
			</div>
			<div class="hero__panel">
				<?php if ( $lac_featured_course ) : ?>
					<a class="hero__featured-card" href="<?php echo esc_url( get_permalink( $lac_featured_course ) ); ?>">
						<?php if ( $lac_featured_image ) : ?>
							<div class="hero__featured-media">
								<img src="<?php echo esc_url( $lac_featured_image ); ?>" alt="<?php echo esc_attr( get_the_title( $lac_featured_course ) ); ?>" />
							</div>
						<?php endif; ?>
						<div class="hero__featured-body">
							<p class="hero__panel-label">Featured premium course</p>
							<h2 class="hero__panel-title"><?php echo esc_html( get_the_title( $lac_featured_course ) ); ?></h2>
							<p class="hero__featured-text"><?php echo esc_html( get_the_excerpt( $lac_featured_course ) ); ?></p>
							<div class="hero__featured-meta">
								<span><?php echo esc_html( ucfirst( $lac_featured_level ? $lac_featured_level : 'advanced' ) ); ?></span>
								<span><?php echo esc_html( $lac_featured_hours ? $lac_featured_hours . 'h' : 'Self-paced' ); ?></span>
								<span><?php echo esc_html( '$' . number_format( $lac_featured_price, 0 ) ); ?></span>
							</div>
						</div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="section section--feature-strip" data-reveal>
		<div class="section__inner">
			<div class="feature-strip">
				<div class="feature-strip__item">
					<p class="feature-strip__title">Beginner to advanced</p>
					<p class="feature-strip__text">Move from first prompts to production-ready AI workflows.</p>
				</div>
				<div class="feature-strip__item">
					<p class="feature-strip__title">Purchase-friendly pricing</p>
					<p class="feature-strip__text">Give visitors a simple starting point with prices from $1 upward.</p>
				</div>
				<div class="feature-strip__item">
					<p class="feature-strip__title">Visual browsing</p>
					<p class="feature-strip__text">Every course card now highlights a related featured image.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--paths" data-reveal>
		<div class="section__inner">
			<header class="section__header">
				<p class="section__eyebrow">Learning paths</p>
				<h2 class="section__title">Browse by outcome, not just by title</h2>
				<p class="section__support">These themed paths make the storefront feel guided instead of generic.</p>
			</header>
			<div class="path-grid">
				<article class="path-card path-card--blue">
					<p class="path-card__kicker">Path 01</p>
					<h3 class="path-card__title">Prompting and content workflows</h3>
					<p class="path-card__text">Start with practical wins like email writing, summaries, and content systems.</p>
				</article>
				<article class="path-card path-card--violet">
					<p class="path-card__kicker">Path 02</p>
					<h3 class="path-card__title">APIs, automation, and RAG</h3>
					<p class="path-card__text">Move into applied product work with integrations, retrieval, and structured outputs.</p>
				</article>
				<article class="path-card path-card--dark">
					<p class="path-card__kicker">Path 03</p>
					<h3 class="path-card__title">Agents and AI product execution</h3>
					<p class="path-card__text">Graduate into orchestration, deployment, optimization, and AI SaaS thinking.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="section section--courses" data-reveal>
		<div class="section__inner">
			<header class="section__header">
				<p class="section__eyebrow">Course catalog</p>
				<h2 class="section__title">Choose the next AI skill to build</h2>
				<p class="section__support">Start with foundations, then move into APIs, automation, multimodal workflows, and AI products.</p>
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
