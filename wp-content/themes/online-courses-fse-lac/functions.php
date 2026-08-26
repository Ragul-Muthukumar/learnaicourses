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
	$checkout_page_id = function_exists( 'lac_get_checkout_page_id' ) ? lac_get_checkout_page_id() : 0;
	if ( is_page( 'checkout' ) || ( $checkout_page_id > 0 && is_page( $checkout_page_id ) ) ) {
		$classes[] = 'lac-is-checkout';
	}
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
 * Drop the leftover About Us CTA under the about section.
 *
 * The pattern already has an About Us label above the content, and this
 * extra button has no destination.
 *
 * @param string $block_content Rendered buttons HTML.
 * @param array  $block         Parsed block.
 * @return string
 */
function lac_fse_remove_duplicate_about_us_button( $block_content, $block ) {
	$inner_blocks = isset( $block['innerBlocks'] ) ? $block['innerBlocks'] : array();
	if ( 1 !== count( $inner_blocks ) ) {
		return $block_content;
	}

	$label = trim( wp_strip_all_tags( (string) ( $inner_blocks[0]['innerHTML'] ?? '' ) ) );
	if ( 0 !== strcasecmp( $label, 'About Us' ) ) {
		return $block_content;
	}

	return '';
}
add_filter( 'render_block_core/buttons', 'lac_fse_remove_duplicate_about_us_button', 10, 2 );

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
	$lac_lessons       = function_exists( 'lac_ensure_default_lessons_for_course' )
		? lac_ensure_default_lessons_for_course( $lac_course_id )
		: ( function_exists( 'lac_get_lessons_for_course' ) ? lac_get_lessons_for_course( $lac_course_id ) : array() );
	$lac_course_image  = get_the_post_thumbnail_url( $lac_course_id, 'large' );
	$lac_lessons_count = is_array( $lac_lessons ) ? count( $lac_lessons ) : 0;
	$lac_price_label   = ( floatval( $lac_course_price ) > 0 )
		? '$' . number_format( (float) $lac_course_price, 2 )
		: 'Free';

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
			<p class="lac-curriculum-lede">Lessons are listed in order. Each one has a goal, a walkthrough, practice, and a short check before you continue.</p>
			<?php if ( ! empty( $lac_lessons ) ) : ?>
				<ol class="lac-lesson-list">
					<?php foreach ( $lac_lessons as $lac_lesson_index => $lac_lesson ) : ?>
						<?php
						$lac_lesson_excerpt = trim( (string) $lac_lesson->post_excerpt );
						if ( '' === $lac_lesson_excerpt ) {
							$lac_lesson_excerpt = wp_trim_words( wp_strip_all_tags( (string) $lac_lesson->post_content ), 18 );
						}
						$lac_excerpt_html = ( '' !== $lac_lesson_excerpt )
							? '<span class="lac-lesson-list__excerpt">' . esc_html( $lac_lesson_excerpt ) . '</span>'
							: '';
						// Echo the row with no newlines inside <a>. The shortcode block runs wpautop,
						// which turns those newlines into <br> and inflates each curriculum card.
						echo '<li><a href="' . esc_url( get_permalink( $lac_lesson ) ) . '">';
						echo '<span class="lac-lesson-list__index">' . esc_html( str_pad( (string) ( $lac_lesson_index + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span>';
						echo '<span class="lac-lesson-list__body"><span class="lac-lesson-list__title">' . esc_html( get_the_title( $lac_lesson ) ) . '</span>' . $lac_excerpt_html . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<span class="lac-lesson-list__go" aria-hidden="true">→</span>';
						echo '</a></li>';
						?>
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
	$lac_siblings         = ( $lac_parent_course_id && function_exists( 'lac_get_lessons_for_course' ) )
		? lac_get_lessons_for_course( $lac_parent_course_id )
		: array();
	$lac_lesson_number    = 0;
	$lac_lesson_total     = is_array( $lac_siblings ) ? count( $lac_siblings ) : 0;
	$lac_prev_lesson      = null;
	$lac_next_lesson      = null;
	if ( $lac_lesson_total > 0 ) {
		foreach ( $lac_siblings as $lac_sibling_index => $lac_sibling ) {
			if ( (int) $lac_sibling->ID === $lac_lesson_id ) {
				$lac_lesson_number = $lac_sibling_index + 1;
				if ( $lac_sibling_index > 0 ) {
					$lac_prev_lesson = $lac_siblings[ $lac_sibling_index - 1 ];
				}
				if ( $lac_sibling_index + 1 < $lac_lesson_total ) {
					$lac_next_lesson = $lac_siblings[ $lac_sibling_index + 1 ];
				}
				break;
			}
		}
	}

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
		<?php if ( $lac_lesson_number > 0 ) : ?>
			<p class="lac-lesson-progress">Lesson <?php echo esc_html( (string) $lac_lesson_number ); ?> of <?php echo esc_html( (string) $lac_lesson_total ); ?></p>
		<?php endif; ?>
		<h1><?php echo esc_html( get_the_title( $lac_lesson_id ) ); ?></h1>
		<div class="entry-content"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $lac_lesson_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php if ( $lac_prev_lesson || $lac_next_lesson ) : ?>
			<nav class="lac-lesson-nav" aria-label="Lesson">
				<?php if ( $lac_prev_lesson ) : ?>
					<a class="lac-lesson-nav__link" href="<?php echo esc_url( get_permalink( $lac_prev_lesson ) ); ?>">
						<span class="lac-lesson-nav__dir">Previous</span>
						<span class="lac-lesson-nav__title"><?php echo esc_html( get_the_title( $lac_prev_lesson ) ); ?></span>
					</a>
				<?php else : ?>
					<span class="lac-lesson-nav__link is-empty"></span>
				<?php endif; ?>
				<?php if ( $lac_next_lesson ) : ?>
					<a class="lac-lesson-nav__link is-next" href="<?php echo esc_url( get_permalink( $lac_next_lesson ) ); ?>">
						<span class="lac-lesson-nav__dir">Next</span>
						<span class="lac-lesson-nav__title"><?php echo esc_html( get_the_title( $lac_next_lesson ) ); ?></span>
					</a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
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

/**
 * Absolute URI for a child-theme favicon file, cache-busted with the theme version.
 *
 * @param string $filename File inside assets/images/favicon/.
 * @return string
 */
function lac_fse_favicon_asset_url( $filename ) {
	$version = (string) wp_get_theme()->get( 'Version' );

	return add_query_arg(
		'ver',
		$version,
		get_stylesheet_directory_uri() . '/assets/images/favicon/' . ltrim( $filename, '/' )
	);
}

/**
 * Favicon / touch-icon markup for desktop tabs, bookmarks, and mobile home screens.
 *
 * Uses the graduation-cap cropped from lac-logo.png. Query-string versions
 * avoid stale browser cache after the icon set changes.
 *
 * @return string[]
 */
function lac_fse_favicon_meta_tags() {
	$ico    = lac_fse_favicon_asset_url( 'favicon.ico' );
	$png16  = lac_fse_favicon_asset_url( 'favicon-16x16.png' );
	$png32  = lac_fse_favicon_asset_url( 'favicon-32x32.png' );
	$png192 = lac_fse_favicon_asset_url( 'android-chrome-192x192.png' );
	$apple  = lac_fse_favicon_asset_url( 'apple-touch-icon.png' );
	$manifest = lac_fse_favicon_asset_url( 'site.webmanifest' );

	return array(
		sprintf( '<link rel="icon" href="%s" sizes="any" />', esc_url( $ico ) ),
		sprintf( '<link rel="icon" type="image/png" href="%s" sizes="16x16" />', esc_url( $png16 ) ),
		sprintf( '<link rel="icon" type="image/png" href="%s" sizes="32x32" />', esc_url( $png32 ) ),
		sprintf( '<link rel="icon" type="image/png" href="%s" sizes="192x192" />', esc_url( $png192 ) ),
		sprintf( '<link rel="apple-touch-icon" href="%s" sizes="180x180" />', esc_url( $apple ) ),
		sprintf( '<meta name="msapplication-TileImage" content="%s" />', esc_url( $png192 ) ),
		sprintf( '<link rel="manifest" href="%s" />', esc_url( $manifest ) ),
	);
}

/**
 * Print favicon tags when WordPress is not already outputting a Site Icon.
 *
 * @return void
 */
function lac_fse_output_favicon_fallback() {
	// wp_site_icon() already prints these when the Site Icon option is set.
	if ( has_site_icon() ) {
		return;
	}

	echo implode( "\n", lac_fse_favicon_meta_tags() ) . "\n";
}
add_action( 'wp_head', 'lac_fse_output_favicon_fallback', 1 );
add_action( 'login_head', 'lac_fse_output_favicon_fallback', 1 );

/**
 * Always print favicon tags in wp-admin (core does not hook wp_site_icon there).
 *
 * @return void
 */
function lac_fse_output_admin_favicon() {
	echo implode( "\n", lac_fse_favicon_meta_tags() ) . "\n";
}
add_action( 'admin_head', 'lac_fse_output_admin_favicon', 1 );

/**
 * Prefer the theme favicon files over auto-cropped media-library sizes.
 *
 * WordPress Site Icon apple-touch images keep transparency, which iOS fills
 * with black. The theme 180px file uses a white background for home screens.
 *
 * @param string[] $meta_tags Core Site Icon tags.
 * @return string[]
 */
function lac_fse_filter_site_icon_meta_tags( $meta_tags ) {
	unset( $meta_tags );

	return lac_fse_favicon_meta_tags();
}
add_filter( 'site_icon_meta_tags', 'lac_fse_filter_site_icon_meta_tags' );

/**
 * Map a requested Site Icon pixel size to a theme favicon file.
 *
 * @param int $size Requested edge length.
 * @return string Filename inside assets/images/favicon/.
 */
function lac_fse_favicon_file_for_size( $size ) {
	$size = (int) $size;
	if ( $size >= 192 ) {
		return 'site-icon-512.png';
	}
	if ( $size >= 180 ) {
		return 'apple-touch-icon.png';
	}
	if ( $size >= 64 ) {
		return 'android-chrome-192x192.png';
	}

	return 'favicon-32x32.png';
}

/**
 * Use theme favicon files when the media-library Site Icon URL is missing.
 *
 * Production admin bar requested lac-site-icon-512-1-150x150.png (404).
 * Theme files in git always exist, so the toolbar icon stays visible.
 *
 * @param string $url     Attachment URL from core.
 * @param int    $size    Requested size.
 * @param int    $blog_id Blog ID (unused; single site).
 * @return string
 */
function lac_fse_filter_get_site_icon_url( $url, $size, $blog_id ) {
	unset( $blog_id );

	if ( $url ) {
		$uploads = wp_get_upload_dir();
		$path    = str_replace( $uploads['baseurl'], $uploads['basedir'], $url );
		$path    = preg_replace( '/\?.*$/', '', (string) $path );
		if ( is_string( $path ) && is_readable( $path ) ) {
			return $url;
		}
	}

	return lac_fse_favicon_asset_url( lac_fse_favicon_file_for_size( $size ) );
}
add_filter( 'get_site_icon_url', 'lac_fse_filter_get_site_icon_url', 10, 3 );

/**
 * Write the Site Icon PNG and the -1 thumbnail name production requests.
 *
 * @param string $source Absolute path to the 512px theme icon.
 * @return void
 */
function lac_fse_write_site_icon_upload_aliases( $source ) {
	$uploads = wp_get_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . '2026/08';
	if ( ! wp_mkdir_p( $dir ) || ! is_readable( $source ) ) {
		return;
	}

	// Skip work when the production admin-bar filename is already on disk.
	if ( is_readable( $dir . '/lac-site-icon-512-1-150x150.png' ) && is_readable( $dir . '/lac-site-icon-512-1.png' ) ) {
		return;
	}

	foreach ( array( 'lac-site-icon-512.png', 'lac-site-icon-512-1.png' ) as $name ) {
		copy( $source, $dir . '/' . $name );
	}

	$image = wp_get_image_editor( $source );
	if ( is_wp_error( $image ) ) {
		return;
	}
	$image->resize( 150, 150, true );
	$image->save( $dir . '/lac-site-icon-512-150x150.png' );
	$image->save( $dir . '/lac-site-icon-512-1-150x150.png' );
}

/**
 * Register the 512px brand icon as the WordPress Site Icon when none is set.
 *
 * Also restores a missing attached file so the admin-bar icon does not 404.
 *
 * @return void
 */
function lac_fse_ensure_site_icon() {
	$source = get_stylesheet_directory() . '/assets/images/favicon/site-icon-512.png';
	if ( ! is_readable( $source ) ) {
		return;
	}

	// Keep both upload filenames on disk so local and production URLs resolve.
	lac_fse_write_site_icon_upload_aliases( $source );

	$current_id = (int) get_option( 'site_icon' );
	if ( $current_id > 0 && wp_attachment_is_image( $current_id ) ) {
		$attached = (string) get_attached_file( $current_id );
		if ( $attached && is_readable( $attached ) ) {
			return;
		}
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( 'lac-site-icon-512-1.png', null, (string) file_get_contents( $source ) );
	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
		return;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'Learn AI Courses site icon',
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $attachment_id ) || $attachment_id < 1 ) {
		return;
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Learn AI Courses' );
	update_option( 'site_icon', (int) $attachment_id );
}
add_action( 'init', 'lac_fse_ensure_site_icon', 20 );

/**
 * Publish policy pages once so footer links resolve instead of 404.
 *
 * Reuses the core Privacy Policy page when it already exists, including
 * when WordPress left it in draft after setup.
 *
 * @return void
 */
function lac_fse_ensure_policy_pages() {
	foreach ( lac_fse_policy_page_definitions() as $page ) {
		$existing_id = lac_fse_find_policy_page_id( $page['slug'] );
		if ( $existing_id > 0 ) {
			if ( 'publish' !== get_post_status( $existing_id ) ) {
				wp_update_post(
					array(
						'ID'          => $existing_id,
						'post_status' => 'publish',
					)
				);
			}
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $page['content'],
			)
		);
		if ( $page_id > 0 && 'privacy-policy' === $page['slug'] && ! get_option( 'wp_page_for_privacy_policy' ) ) {
			update_option( 'wp_page_for_privacy_policy', (int) $page_id );
		}
	}
}
add_action( 'init', 'lac_fse_ensure_policy_pages', 21 );

/**
 * Find a policy page by slug across published and draft statuses.
 *
 * @param string $slug Page slug.
 * @return int Page id or 0 when missing.
 */
function lac_fse_find_policy_page_id( $slug ) {
	if ( 'privacy-policy' === $slug ) {
		$privacy_id = (int) get_option( 'wp_page_for_privacy_policy', 0 );
		if ( $privacy_id > 0 && get_post( $privacy_id ) ) {
			return $privacy_id;
		}
	}

	$existing = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
			'name'                   => $slug,
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'suppress_filters'       => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	return ! empty( $existing ) ? (int) $existing[0] : 0;
}

/**
 * Titles, slugs, and Gutenberg content for storefront policy pages.
 *
 * @return array<int, array{title:string,slug:string,content:string}>
 */
function lac_fse_policy_page_definitions() {
	return array(
		array(
			'title'   => 'Terms & Conditions',
			'slug'    => 'terms-and-conditions',
			'content' => lac_fse_terms_page_content(),
		),
		array(
			'title'   => 'Privacy Policy',
			'slug'    => 'privacy-policy',
			'content' => lac_fse_privacy_page_content(),
		),
		array(
			'title'   => 'Refund Policy',
			'slug'    => 'refund-policy',
			'content' => lac_fse_refund_page_content(),
		),
		array(
			'title'   => 'Contact Us',
			'slug'    => 'contact-us',
			'content' => lac_fse_contact_page_content(),
		),
	);
}

/**
 * Gutenberg markup for the Terms & Conditions page.
 *
 * Written for an individually owned website, not a company or organization.
 *
 * @return string
 */
function lac_fse_terms_page_content() {
	return '<!-- wp:paragraph -->
<p>Learn AI Courses is the name of this website. I own and operate it as an individual person. These terms are an agreement between you and me. They are not an agreement with a company, corporation, or other business entity.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>By browsing the site, creating an account, or buying a course, you agree to this page. If you do not agree, please do not use the site.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Accounts</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>You are responsible for the information you provide at sign-up and for keeping your login details private. One account is for one learner. Please do not share your account or resell course materials.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Courses and access</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>After you enroll, paid and free courses stay in your account unless I have to remove a course for legal or practical reasons. Lesson content, prompts, and downloads are for your personal learning. They are not a license to copy, publish, or sell the curriculum.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Payments</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The price on a course page is due at checkout. Paid orders are processed through PayPal. A completed payment enrolls you in that course. Refunds, when they apply, follow the <a href="/refund-policy/">Refund Policy</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Acceptable use</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Please use the site for learning. Do not try to disrupt it, copy course content at scale, or upload material you do not have the right to share. I may suspend an account that breaks these rules.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Privacy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>How I handle account and site data is described in the <a href="/privacy-policy/">Privacy Policy</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Changes</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>I may update these terms if the catalog or checkout flow changes. The current version is always the one published on this page.</p>
<!-- /wp:paragraph -->';
}

/**
 * Gutenberg markup for the Privacy Policy page.
 *
 * Written for an individually owned website, not a company or organization.
 *
 * @return string
 */
function lac_fse_privacy_page_content() {
	return '<!-- wp:paragraph -->
<p>Learn AI Courses is a personal website that I own and operate as an individual. I am not a company or other business entity. This page explains what personal information I collect, why I collect it, and how you can ask about it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">What I collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>When you create an account, I store the name, email address, and login details you provide. Course enrollments and lesson progress stay attached to that account so you can return to your courses. I collect only what I need to run accounts, course access, and checkout.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Payments</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Paid checkouts are processed by PayPal. I keep an order record so I can confirm enrollment and handle refunds under the <a href="/refund-policy/">Refund Policy</a>. Card details are handled by PayPal and are not stored on this website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Login and session cookies keep you signed in and remember display choices. These cookies are needed for the account and checkout flows to work.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">How I use and share information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>I use your information to operate this website, provide course access, and respond to account or refund requests. I do not sell your personal information. I may share what is needed with PayPal to complete a payment or refund, or if I am required to do so by law.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Your choices</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>You can review and update the profile details on your account. To ask for a copy or deletion of personal data I hold, use the <a href="/contact-us/">Contact Us</a> page from the email address on that account. I may keep records I am required to keep for legal, security, or payment reasons. Related rules are in the <a href="/terms-and-conditions/">Terms &amp; Conditions</a>.</p>
<!-- /wp:paragraph -->';
}

/**
 * Gutenberg markup for the Refund Policy page.
 *
 * Written for an individually owned website, not a company or organization.
 *
 * @return string
 */
function lac_fse_refund_page_content() {
	return '<!-- wp:paragraph -->
<p>I own and operate Learn AI Courses as an individual. This page explains when I can refund a paid course and how to ask for one. Refunds are handled by me personally, not by a company.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">14-day refund window</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you purchased a paid course, you may request a full refund within 14 days of the payment date, provided you have not completed more than two lessons in that course. Free courses have no charge, so they are not eligible for a refund.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">How to request a refund</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Send a refund request through the <a href="/contact-us/">Contact Us</a> page. Include the course name and the PayPal order or receipt email. I process eligible refunds back to the original payment method. PayPal timing can take several business days after I approve the request.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">When a refund is not available</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>I cannot refund a purchase after the 14-day window, after more than two lessons have been completed, or when access was shared or the content was copied. If you were charged twice for the same course, I will refund the extra payment once I confirm it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"manrope"} -->
<h2 class="wp-block-heading has-manrope-font-family">Related pages</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Course use is also covered by the <a href="/terms-and-conditions/">Terms &amp; Conditions</a>. Account data is covered by the <a href="/privacy-policy/">Privacy Policy</a>. Questions go to <a href="/contact-us/">Contact Us</a>.</p>
<!-- /wp:paragraph -->';
}

/**
 * Gutenberg markup for the Contact Us page.
 *
 * PayPal requires a public way for buyers to reach the site owner.
 *
 * @return string
 */
function lac_fse_contact_page_content() {
	return '<!-- wp:shortcode -->
[lac_contact_form]
<!-- /wp:shortcode -->';
}

/**
 * Public owner name shown on Contact Us.
 *
 * @return string
 */
function lac_fse_contact_name() {
	return 'Fenllin Skill P';
}

/**
 * Email address used for public customer contact and form delivery.
 *
 * @return string
 */
function lac_fse_contact_email() {
	return 'Fenllinskiii16@gmail.com';
}

/**
 * Public address shown on Contact Us.
 *
 * @return string
 */
function lac_fse_contact_address() {
	return '4nd home, platinum villa, blackberry street, sindhu salai, mugalivakam, chennai 600125';
}

/**
 * Public mobile number shown on Contact Us.
 *
 * @return string
 */
function lac_fse_contact_phone() {
	return '+91 73581 13783';
}

/**
 * Allowed contact form topics.
 *
 * @return string[]
 */
function lac_fse_contact_topics() {
	return array(
		'Course question',
		'Payment or refund',
		'Account or privacy',
		'Something else',
	);
}

/**
 * Render the public contact email and message form.
 *
 * @return string
 */
function lac_fse_contact_form_shortcode() {
	$name       = lac_fse_contact_name();
	$email      = lac_fse_contact_email();
	$phone      = lac_fse_contact_phone();
	$phone_href = 'tel:' . preg_replace( '/\s+/', '', $phone );
	$status     = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
	$user       = wp_get_current_user();
	$name_value = ( $user && $user->exists() ) ? $user->display_name : '';
	$mail_value = ( $user && $user->exists() ) ? $user->user_email : '';

	ob_start();

	if ( 'sent' === $status ) {
		echo '<p class="lac-contact-form__notice is-success">Thanks. Your message was received and a reply will be sent to the email you entered.</p>';
	} elseif ( 'invalid' === $status ) {
		echo '<p class="lac-contact-form__notice is-error">Please enter your name, a valid email, a topic, and a message.</p>';
	} elseif ( 'limited' === $status ) {
		echo '<p class="lac-contact-form__notice is-error">Please wait a little before sending another message.</p>';
	} elseif ( 'failed' === $status ) {
		echo '<p class="lac-contact-form__notice is-error">The message could not be sent just now. Please email directly and try again later.</p>';
	}
	?>
	<div class="lac-contact-details">
		<p class="lac-contact-details__intro">Feel free to contact and reach us.</p>
		<p>Questions or feedback about a course, payment, refund, or your account are welcome. Use the details below or send a message.</p>
		<div class="lac-contact-details__item">
			<h2>Name</h2>
			<p><?php echo esc_html( $name ); ?></p>
		</div>
		<div class="lac-contact-details__item">
			<h2>Address</h2>
			<p><?php echo nl2br( esc_html( lac_fse_contact_address() ) ); ?></p>
		</div>
		<div class="lac-contact-details__item">
			<h2>Mobile</h2>
			<p><a href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a></p>
		</div>
		<div class="lac-contact-details__item">
			<h2>Email</h2>
			<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
		</div>
	</div>
	<?php if ( 'sent' !== $status ) : ?>
	<form class="lac-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="lac_contact" />
		<?php wp_nonce_field( 'lac_contact', 'lac_contact_nonce' ); ?>
		<p class="lac-contact-form__hp" aria-hidden="true">
			<label for="lac_website">Website</label>
			<input type="text" id="lac_website" name="lac_website" value="" tabindex="-1" autocomplete="off" />
		</p>
		<label>
			<span>Name</span>
			<input type="text" name="lac_name" value="<?php echo esc_attr( $name_value ); ?>" required maxlength="120" autocomplete="name" />
		</label>
		<label>
			<span>Email</span>
			<input type="email" name="lac_email" value="<?php echo esc_attr( $mail_value ); ?>" required maxlength="120" autocomplete="email" />
		</label>
		<label>
			<span>Topic</span>
			<select name="lac_topic" required>
				<option value="">Choose a topic</option>
				<?php foreach ( lac_fse_contact_topics() as $topic ) : ?>
					<option value="<?php echo esc_attr( $topic ); ?>"><?php echo esc_html( $topic ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label>
			<span>Message</span>
			<textarea name="lac_message" rows="6" required maxlength="4000"></textarea>
		</label>
		<button class="lac-contact-form__submit" type="submit">Send message</button>
	</form>
	<?php endif; ?>
	<?php

	return (string) ob_get_clean();
}
add_shortcode( 'lac_contact_form', 'lac_fse_contact_form_shortcode' );

/**
 * Redirect back to Contact Us with a status query argument.
 *
 * @param string $status sent, invalid, limited, or failed.
 * @return void
 */
function lac_fse_contact_redirect( $status ) {
	$page_id = lac_fse_find_policy_page_id( 'contact-us' );
	$url     = $page_id > 0 ? get_permalink( $page_id ) : home_url( '/contact-us/' );
	wp_safe_redirect( add_query_arg( 'contact', sanitize_key( $status ), $url ) );
	exit;
}

/**
 * Accept a public contact form post, then email the site owner.
 *
 * @return void
 */
function lac_fse_handle_contact_form() {
	$nonce = isset( $_POST['lac_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['lac_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'lac_contact' ) ) {
		lac_fse_contact_redirect( 'invalid' );
	}

	// Silent success when the hidden honeypot field is filled.
	if ( ! empty( $_POST['lac_website'] ) ) {
		lac_fse_contact_redirect( 'sent' );
	}

	$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	$ip_key     = 'lac_contact_' . md5( $ip_address );
	if ( false !== get_transient( $ip_key ) ) {
		lac_fse_contact_redirect( 'limited' );
	}

	$name    = isset( $_POST['lac_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lac_name'] ) ) : '';
	$email   = isset( $_POST['lac_email'] ) ? sanitize_email( wp_unslash( $_POST['lac_email'] ) ) : '';
	$topic   = isset( $_POST['lac_topic'] ) ? sanitize_text_field( wp_unslash( $_POST['lac_topic'] ) ) : '';
	$message = isset( $_POST['lac_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lac_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || ! in_array( $topic, lac_fse_contact_topics(), true ) || '' === $message ) {
		lac_fse_contact_redirect( 'invalid' );
	}

	$to = lac_fse_contact_email();
	if ( '' === $to ) {
		lac_fse_contact_redirect( 'failed' );
	}

	$subject = sprintf( '[Learn AI Courses] %s from %s', $topic, $name );
	$body    = "Name: {$name}\nEmail: {$email}\nTopic: {$topic}\n\n{$message}\n";
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $email,
	);

	$sent = wp_mail( $to, $subject, $body, $headers );
	if ( ! $sent ) {
		lac_fse_contact_redirect( 'failed' );
	}

	set_transient( $ip_key, 1, HOUR_IN_SECONDS );
	lac_fse_contact_redirect( 'sent' );
}
add_action( 'admin_post_nopriv_lac_contact', 'lac_fse_handle_contact_form' );
add_action( 'admin_post_lac_contact', 'lac_fse_handle_contact_form' );


