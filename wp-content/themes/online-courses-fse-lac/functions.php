<?php
/**
 * Child of Online Courses FSE for Learn AI Courses.
 *
 * What this file does:
 * - Loads parent FSE styles (parent enqueues get_stylesheet_uri()).
 * - Wraps LMS PHP templates with the block header and footer.
 * - Points demo buttons and hash links at the real catalog.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent stylesheet and LMS component CSS.
 *
 * @return void
 */
function lac_fse_child_enqueue_assets() {
	wp_enqueue_style(
		'online-courses-fse-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'online-courses-fse' )->get( 'Version' )
	);
	wp_enqueue_style(
		'online-courses-fse-lac-lms',
		get_stylesheet_directory_uri() . '/assets/css/lms.css',
		array( 'online-courses-fse-parent', 'online-courses-fse-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'lac_fse_child_enqueue_assets', 20 );

/**
 * Keep the public site name on Learn AI Courses even if a demo title is restored.
 *
 * @param string $name Site title from options.
 * @return string
 */
function lac_fse_branded_blogname( $name ) {
	if ( 0 === strcasecmp( trim( (string) $name ), 'Online Courses' ) ) {
		return 'Learn AI Courses';
	}

	return $name;
}
add_filter( 'option_blogname', 'lac_fse_branded_blogname' );

/**
 * Swap the parent “Online Courses” wordmark for the Learn AI Courses logo.
 *
 * Runs after the parent default-logo filter so the FSE site-logo block
 * never falls back to fse-theme-logo.png.
 *
 * @param string $html Markup from get_custom_logo().
 * @return string
 */
function lac_fse_branded_logo( $html ) {
	// Honor a logo uploaded in the Site Editor / Customizer.
	if ( get_theme_mod( 'custom_logo' ) ) {
		return $html;
	}

	$logo_path = get_stylesheet_directory() . '/assets/images/lac-logo.png';
	$logo_url  = get_stylesheet_directory_uri() . '/assets/images/lac-logo.png';

	if ( ! is_readable( $logo_path ) ) {
		return sprintf(
			'<a href="%1$s" class="custom-logo-link site-title-fallback" rel="home">%2$s</a>',
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name' ) )
		);
	}

	$size = getimagesize( $logo_path );
	$w    = ( is_array( $size ) && ! empty( $size[0] ) ) ? (int) $size[0] : 1074;
	$h    = ( is_array( $size ) && ! empty( $size[1] ) ) ? (int) $size[1] : 156;

	return sprintf(
		'<a href="%1$s" class="custom-logo-link" rel="home" aria-label="%3$s"><img src="%2$s" class="custom-logo" alt="%3$s" width="%4$d" height="%5$d" /></a>',
		esc_url( home_url( '/' ) ),
		esc_url( $logo_url ),
		esc_attr( get_bloginfo( 'name' ) ),
		$w,
		$h
	);
}
add_filter( 'get_custom_logo', 'lac_fse_branded_logo', 20 );

/**
 * Mark the body so LMS layout CSS can target this child.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function lac_fse_child_body_class( $classes ) {
	$classes[] = 'lac-fse-lms';
	return $classes;
}
add_filter( 'body_class', 'lac_fse_child_body_class' );

/**
 * Twelve courses per catalog page.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function lac_fse_child_course_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'lac_course' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'lac_fse_child_course_archive_query' );

/**
 * Point demo catalog CTAs at /courses/. Leave other buttons alone.
 *
 * @param string $block_content Rendered button HTML.
 * @param array  $block         Block data.
 * @return string
 */
function lac_fse_fix_cta_buttons( $block_content, $block ) {
	unset( $block );

	$is_catalog_cta = (bool) preg_match(
		'/Explore Courses|View all Courses|Get started|Get Started|Browse courses/i',
		$block_content
	);
	if ( ! $is_catalog_cta ) {
		return $block_content;
	}

	$catalog = esc_url( home_url( '/courses/' ) );

	if ( false !== strpos( $block_content, 'href="#"' ) ) {
		return str_replace( 'href="#"', 'href="' . $catalog . '"', $block_content );
	}

	// Parent hero/CTA buttons omit href entirely.
	if ( false === strpos( $block_content, 'href=' ) ) {
		return preg_replace( '/<a /', '<a href="' . $catalog . '" ', $block_content, 1 );
	}

	return $block_content;
}
add_filter( 'render_block_core/button', 'lac_fse_fix_cta_buttons', 10, 2 );

/**
 * Homepage Query Loop of published LMS courses.
 *
 * @return void
 */
function lac_fse_register_course_pattern() {
	register_block_pattern(
		'online-courses-fse-lac/courses',
		array(
			'title'      => 'Learn AI Courses catalog',
			'categories' => array( 'featured' ),
			'content'    => '
<!-- wp:group {"metadata":{"name":"Popular Courses"},"align":"full","style":{"spacing":{"padding":{"top":"120px","bottom":"120px","left":"20px","right":"20px"},"blockGap":"40px"}},"backgroundColor":"background-2","layout":{"type":"constrained","contentSize":"1320px"}} -->
<div class="wp-block-group alignfull has-background-2-background-color has-background" style="padding-top:120px;padding-right:20px;padding-bottom:120px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"16px","padding":{"bottom":"24px"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group" style="padding-bottom:24px">
		<!-- wp:group {"style":{"spacing":{"blockGap":"16px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"9px","bottom":"9px","left":"20px","right":"20px"}},"border":{"radius":"20px"}},"backgroundColor":"background-3","layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-background-3-background-color has-background" style="border-radius:20px;padding-top:9px;padding-right:20px;padding-bottom:9px;padding-left:20px">
				<!-- wp:heading {"textAlign":"center","level":6,"style":{"typography":{"textTransform":"uppercase","fontWeight":"700","letterSpacing":"1.4px"}},"textColor":"primary"} -->
				<h6 class="wp-block-heading has-text-align-center has-primary-color has-text-color" style="font-weight:700;letter-spacing:1.4px;text-transform:uppercase">Best Learning</h6>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
			<!-- wp:heading {"textColor":"heading","fontSize":"gigantic","fontFamily":"manrope"} -->
			<h2 class="wp-block-heading has-heading-color has-text-color has-manrope-font-family has-gigantic-font-size">Popular Courses</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"accent-3","fontSize":"regular"} -->
			<p class="has-accent-3-color has-text-color has-regular-font-size">Live catalog from the LMS — prompting, APIs, agents, and applied workflows.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"backgroundColor":"primary"} -->
			<div class="wp-block-button"><a class="wp-block-button__link has-primary-background-color has-background wp-element-button" href="/courses/">View all Courses</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
	<!-- wp:query {"queryId":21,"query":{"perPage":6,"pages":0,"offset":0,"postType":"lac_course","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"30px"}},"layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:group {"className":"lac-fse-course-card","style":{"spacing":{"blockGap":"20px","padding":{"top":"24px","bottom":"24px","left":"24px","right":"24px"}},"border":{"radius":"16px","width":"1px"}},"backgroundColor":"background-1","borderColor":"background-3","layout":{"type":"constrained"}} -->
			<div class="wp-block-group lac-fse-course-card has-border-color has-background-3-border-color has-background-1-background-color has-background" style="border-width:1px;border-radius:16px;padding-top:24px;padding-right:24px;padding-bottom:24px;padding-left:24px">
				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/10","style":{"border":{"radius":"12px"}}} /-->
				<!-- wp:post-title {"isLink":true,"level":4,"fontSize":"extra-large","fontFamily":"manrope"} /-->
				<!-- wp:post-excerpt {"excerptLength":18} /-->
				<!-- wp:read-more {"content":"View Course"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
',
		)
	);
}
add_action( 'init', 'lac_fse_register_course_pattern' );

/**
 * Course page body: enrollment, curriculum, and summary.
 *
 * Used by the FSE single-course template so header/footer stay native.
 *
 * @return string
 */
function lac_fse_course_board_shortcode() {
	$lac_course_id = get_queried_object_id();
	if ( $lac_course_id < 1 || 'lac_course' !== get_post_type( $lac_course_id ) ) {
		return '';
	}

	$lac_course_level  = get_post_meta( $lac_course_id, '_lac_course_level', true );
	$lac_course_hours  = get_post_meta( $lac_course_id, '_lac_course_hours', true );
	$lac_course_price  = get_post_meta( $lac_course_id, '_lac_course_price', true );
	$lac_lessons       = function_exists( 'lac_get_lessons_for_course' ) ? lac_get_lessons_for_course( $lac_course_id ) : array();
	$lac_course_image  = get_the_post_thumbnail_url( $lac_course_id, 'large' );
	$lac_lessons_count = is_array( $lac_lessons ) ? count( $lac_lessons ) : 0;
	$lac_price_label   = ( floatval( $lac_course_price ) > 0 ) ? '$' . number_format( (float) $lac_course_price, 0 ) : 'Free';

	ob_start();
	?>
	<header class="lac-course-hero">
		<div class="lac-wrap lac-course-hero__grid">
			<div>
				<p class="lac-kicker">Course</p>
				<h1><?php echo esc_html( get_the_title( $lac_course_id ) ); ?></h1>
				<p class="lac-lede"><?php echo esc_html( get_the_excerpt( $lac_course_id ) ); ?></p>
				<ul class="lac-meta">
					<li><?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></li>
					<li><?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></li>
					<li><?php echo esc_html( $lac_price_label ); ?></li>
				</ul>
				<?php
				if ( function_exists( 'lac_render_enrollment_button' ) ) {
					echo lac_render_enrollment_button( $lac_course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<div>
				<?php if ( $lac_course_image ) : ?>
					<img src="<?php echo esc_url( $lac_course_image ); ?>" alt="<?php echo esc_attr( get_the_title( $lac_course_id ) ); ?>" />
				<?php endif; ?>
			</div>
		</div>
	</header>
	<div class="lac-wrap lac-layout">
		<div>
			<h2>About this course</h2>
			<div class="entry-content"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $lac_course_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<h2>Curriculum</h2>
			<?php if ( ! empty( $lac_lessons ) ) : ?>
				<ol class="lac-lesson-list">
					<?php foreach ( $lac_lessons as $lac_lesson ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $lac_lesson ) ); ?>">
								<span><?php echo esc_html( get_the_title( $lac_lesson ) ); ?></span>
								<span aria-hidden="true">→</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php else : ?>
				<p>Lessons will be listed here when they are published.</p>
			<?php endif; ?>
		</div>
		<aside class="lac-summary">
			<p class="lac-kicker">Summary</p>
			<p class="lac-summary__price"><?php echo esc_html( $lac_price_label ); ?></p>
			<ul>
				<li>Level: <?php echo esc_html( ucfirst( $lac_course_level ? $lac_course_level : 'beginner' ) ); ?></li>
				<li>Duration: <?php echo esc_html( $lac_course_hours ? $lac_course_hours . ' hours' : 'Self-paced' ); ?></li>
				<li>Lessons: <?php echo esc_html( $lac_lessons_count ); ?></li>
				<li>Access: lifetime</li>
			</ul>
			<?php
			if ( function_exists( 'lac_render_enrollment_button' ) ) {
				echo lac_render_enrollment_button( $lac_course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</aside>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'lac_course_board', 'lac_fse_course_board_shortcode' );

/**
 * Lesson page body with a link back to the parent course.
 *
 * @return string
 */
function lac_fse_lesson_board_shortcode() {
	$lac_lesson_id = get_queried_object_id();
	if ( $lac_lesson_id < 1 || 'lac_lesson' !== get_post_type( $lac_lesson_id ) ) {
		return '';
	}

	$lac_parent_course_id = (int) get_post_meta( $lac_lesson_id, '_lac_parent_course_id', true );

	ob_start();
	?>
	<div class="lac-wrap lac-lesson" style="padding:2.25rem 0 3rem;">
		<?php if ( $lac_parent_course_id ) : ?>
			<p class="lac-crumb">
				<a href="<?php echo esc_url( get_permalink( $lac_parent_course_id ) ); ?>">
					← <?php echo esc_html( get_the_title( $lac_parent_course_id ) ); ?>
				</a>
			</p>
		<?php endif; ?>
		<h1><?php echo esc_html( get_the_title( $lac_lesson_id ) ); ?></h1>
		<div class="entry-content"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $lac_lesson_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'lac_lesson_board', 'lac_fse_lesson_board_shortcode' );

/**
 * Keep the FSE homepage and flush LMS rewrites when this child is activated.
 *
 * @return void
 */
function lac_fse_child_on_switch() {
	// front-page.html is the storefront; do not render a Kadence page instead.
	update_option( 'show_on_front', 'posts' );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'lac_fse_child_on_switch' );
