<?php
/**
 * eLearning Education Theme Page
 *
 * @package eLearning Education
 */

function elearning_education_admin_scripts() {
	wp_dequeue_script('elearning-education-custom-scripts');
}
add_action( 'admin_enqueue_scripts', 'elearning_education_admin_scripts' );

if ( ! defined( 'ELEARNING_EDUCATION_FREE_THEME_URL' ) ) {
	define( 'ELEARNING_EDUCATION_FREE_THEME_URL', 'https://www.themespride.com/products/free-elearning-education-wordpress-theme' );
}
if ( ! defined( 'ELEARNING_EDUCATION_PRO_THEME_URL' ) ) {
	define( 'ELEARNING_EDUCATION_PRO_THEME_URL', 'https://www.themespride.com/products/elearning-education-wordpress-theme' );
}
if ( ! defined( 'ELEARNING_EDUCATION_DEMO_THEME_URL' ) ) {
	define( 'ELEARNING_EDUCATION_DEMO_THEME_URL', 'https://page.themespride.com/elearning-education-pro/' );
}
if ( ! defined( 'ELEARNING_EDUCATION_DOCS_THEME_URL' ) ) {
    define( 'ELEARNING_EDUCATION_DOCS_THEME_URL', 'https://page.themespride.com/demo/docs/elearning-education-lite/' );
}
if ( ! defined( 'ELEARNING_EDUCATION_RATE_THEME_URL' ) ) {
    define( 'ELEARNING_EDUCATION_RATE_THEME_URL', 'https://wordpress.org/support/theme/elearning-education/reviews/#new-post' );
}
if ( ! defined( 'ELEARNING_EDUCATION_CHANGELOG_THEME_URL' ) ) {
    define( 'ELEARNING_EDUCATION_CHANGELOG_THEME_URL', get_template_directory() . '/readme.txt' );
}
if ( ! defined( 'ELEARNING_EDUCATION_SUPPORT_THEME_URL' ) ) {
    define( 'ELEARNING_EDUCATION_SUPPORT_THEME_URL', 'https://wordpress.org/support/theme/elearning-education/' );
}
if ( ! defined( 'ELEARNING_EDUCATION_THEME_BUNDLE' ) ) {
    define( 'ELEARNING_EDUCATION_THEME_BUNDLE', 'https://www.themespride.com/products/wordpress-theme-bundle/' );
}

/**
 * Add theme page
 */
function elearning_education_menu() {
	add_theme_page( esc_html__( 'About Theme', 'elearning-education' ), esc_html__( 'Begin Installation - Import Demo', 'elearning-education' ), 'edit_theme_options', 'elearning-education-about', 'elearning_education_about_display' );
}
add_action( 'admin_menu', 'elearning_education_menu' );

/**
 * Display About page
 */
function elearning_education_about_display() {
	$elearning_education_theme = wp_get_theme();
	?>
	<div class="wrap about-wrap full-width-layout">
		<!-- top-detail -->
		<?php
		// Only show if NOT dismissed
		if ( ! get_option('dismissed-get_started-detail', false ) ) { 
		?>
		    <!-- top-detail -->
		    <div class="detail-theme" id="detail-theme-box">
		        <button type="button" class="close-btn" id="close-detail-theme">
		            <?php esc_html_e( 'Dismiss', 'elearning-education' ); ?>
		        </button>
		        <?php 
				$elearning_education_theme = wp_get_theme(); 
				?>
				<h2>
				    <?php 
				    echo sprintf(
				        esc_html__( 'Hey, thank you for installing the %s theme!', 'elearning-education' ), 
				        esc_html( $elearning_education_theme->get( 'Name' ) )
				    ); 
				    ?>
				</h2>

		        <a href="<?php echo esc_url( admin_url( 'themes.php?page=elearning-education-about' ) ); ?>">
		            <?php esc_html_e( 'Get Started', 'elearning-education' ); ?>
		        </a>
		        <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="site-editor" target="_blank">
		            <?php esc_html_e( 'Site Editor', 'elearning-education' ); ?>
		        </a>

		        <a href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme" target="_blank">
		            <?php esc_html_e( 'Upgrade to Pro', 'elearning-education' ); ?>
		        </a>

		        <a href="<?php echo esc_url( ELEARNING_EDUCATION_THEME_BUNDLE ); ?>" class="rate-theme" target="_blank">
		            <?php esc_html_e( 'Get Bundle', 'elearning-education' ); ?>
		        </a>
		    </div>
		<?php 
		} ?>
		
		<nav class="nav-tab-wrapper wp-clearfix elearning-education-tab-sec" aria-label="<?php esc_attr_e( 'Secondary menu', 'elearning-education' ); ?>">
		    <button class="nav-tab elearning-education-tablinks active"
		        onclick="elearning_education_open_tab(event, 'tp_demo_import')">
		        <?php esc_html_e( 'One Click Demo Import', 'elearning-education' ); ?>
		    </button>

		    <button class="nav-tab elearning-education-tablinks"
		        onclick="elearning_education_open_tab(event, 'tp_about_theme')">
		        <?php esc_html_e( 'About', 'elearning-education' ); ?>
		    </button>

		    <button class="nav-tab elearning-education-tablinks"
		        onclick="elearning_education_open_tab(event, 'tp_free_vs_pro')">
		        <?php esc_html_e( 'Compare Free Vs Pro', 'elearning-education' ); ?>
		    </button>

		    <button class="nav-tab elearning-education-tablinks"
		        onclick="elearning_education_open_tab(event, 'tp_changelog')">
		        <?php esc_html_e( 'Changelog', 'elearning-education' ); ?>
		    </button>

		    <button class="nav-tab elearning-education-tablinks blink wp-bundle"
		        onclick="elearning_education_open_tab(event, 'tp_get_bundle')">
		        <?php esc_html_e( 'Get WordPress Theme Bundle (120+ Themes)', 'elearning-education' ); ?>
		    </button>
		</nav>

		<?php
			elearning_education_demo_import();

			elearning_education_main_screen();

			elearning_education_changelog_screen();

			elearning_education_free_vs_pro();

			elearning_education_get_bundle();
		?>

		<p class="actions">
			<a target="_blank"href="<?php echo esc_url( ELEARNING_EDUCATION_FREE_THEME_URL ); ?>" class="theme-info-btn" target="_blank" target="_blank"><?php esc_html_e( 'Theme Info', 'elearning-education' ); ?></a>
			<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_DEMO_THEME_URL ); ?>" class="view-demo" target="_blank"><?php esc_html_e( 'View Demo', 'elearning-education' ); ?></a>
			<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_DOCS_THEME_URL ); ?>" class="instruction-theme" target="_blank"><?php esc_html_e( 'Theme Documentation', 'elearning-education' ); ?></a>
			<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme" target="_blank"><?php esc_html_e( 'Upgrade to pro', 'elearning-education' ); ?></a>
		</p>

		<h1><?php echo esc_html( $elearning_education_theme ); ?></h1>
		<div class="about-theme">
			<div class="theme-description">
				<p class="about-text content">
					<?php
					// Remove last sentence of description.
					$elearning_education_description = explode( '. ', $elearning_education_theme->get( 'Description' ) );
					array_pop( $elearning_education_description );

					$elearning_education_description = implode( '. ', $elearning_education_description );

					echo esc_html( $elearning_education_description . '.' );
				?></p>
				
			</div>
			<div class="theme-screenshot">
				<img src="<?php echo esc_url( $elearning_education_theme->get_screenshot() ); ?>" />
			</div>
		</div>
	<?php
}


/**
 * Output the Demo Import screen (JS tab based).
 */
function elearning_education_demo_import() {

	/* ---------------------------------------------------------
	 * THEME-SPECIFIC OPTION KEYS (IMPORTANT FIX)
	 * --------------------------------------------------------- */
	$theme_slug           = get_stylesheet(); // parent or child automatically
	$demo_import_option   = $theme_slug . '_demo_imported';
	$demo_popup_option    = $theme_slug . '_demo_popup_shown';

	/* ---------------------------------------------------------
	 * LOAD WHIZZIE (CHILD FIRST, THEN PARENT)
	 * --------------------------------------------------------- */
	$child_whizzie  = get_stylesheet_directory() . '/inc/whizzie.php';
	$parent_whizzie = get_template_directory() . '/inc/whizzie.php';

	if ( file_exists( $child_whizzie ) ) {
		require_once $child_whizzie;
	} elseif ( file_exists( $parent_whizzie ) ) {
		require_once $parent_whizzie;
	}

	/* ---------------------------------------------------------
	 * SAVE DEMO IMPORT STATUS
	 * --------------------------------------------------------- */
	if ( isset( $_GET['import-demo'] ) && $_GET['import-demo'] === 'true' ) {
		update_option( $demo_import_option, true );
		delete_option( $demo_popup_option ); // allow popup once
	}

	/* ---------------------------------------------------------
	 * RESET DEMO (OPTIONAL)
	 * --------------------------------------------------------- */
	if ( isset( $_GET['reset-demo'] ) && $_GET['reset-demo'] === 'true' ) {
		delete_option( $demo_import_option );
		delete_option( $demo_popup_option );
		wp_safe_redirect( remove_query_arg( 'reset-demo' ) );
		exit;
	}

	$demo_imported  = get_option( $demo_import_option, false );
	$popup_shown    = get_option( $demo_popup_option, false );
	$show_popup_now = ( $demo_imported && ! $popup_shown );
	?>

	<div id="tp_demo_import" class="elearning-education-tabcontent">

	<?php if ( $demo_imported ) : ?>

		<!-- ================= SUCCESS STATE ================= -->
		<div class="content-row">
			<div class="col card success-demo text-center">
				<p class="imp-success">
					<?php esc_html_e( 'Demo Imported Successfully!', 'elearning-education' ); ?>
				</p><br>

				<div class="demo-button-three">
					<a class="button button-primary" href="<?php echo esc_url( home_url('/') ); ?>" target="_blank">
						<?php esc_html_e( 'View Site', 'elearning-education' ); ?>
					</a>

					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" target="_blank">
						<?php esc_html_e( 'Edit Site', 'elearning-education' ); ?>
					</a>

					<?php if ( defined( 'ELEARNING_EDUCATION_DOCS_THEME_URL' ) ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( ELEARNING_EDUCATION_DOCS_THEME_URL ); ?>" target="_blank">
							<?php esc_html_e( 'Documentation', 'elearning-education' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="theme-price col card">
			<div class="price-flex">
				<div class="price-content">
					<?php 
						$elearning_education_theme = wp_get_theme();
						?>
						<h3>
						    <?php 
						    echo sprintf(
						        esc_html__( '%s WordPress Theme ', 'elearning-education' ),
						        esc_html( $elearning_education_theme->get( 'Name' ) )
						    ); 
						    ?>
						</h3>
					<p class="main-flash"><?php 
					  printf(
					    /* translators: 1: bold FLASH DEAL text, 2: discount code */
					    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'elearning-education' ),
					    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'elearning-education' ) . '</strong>',
					    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'elearning-education' ) . '</strong>'
					  ); 
					  ?></p>
					 <p>
					  <del><?php echo esc_html__( '$59', 'elearning-education' ); ?></del>
					  <strong class="bold-price"><?php echo esc_html__( '$39', 'elearning-education' ); ?></strong>
					</p>
				</div>
				<div class="price-img">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
				</div>
			</div>
			<div class="main-pro-price">
				<?php 
					$elearning_education_theme = wp_get_theme();
					?>
				<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro">
				    <?php 
				    echo sprintf(
				        esc_html__( 'Upgrade To Premium %s Theme', 'elearning-education' ),
				        esc_html( $elearning_education_theme->get( 'Name' ) )
				    ); 
				    ?>
				</a>
			</div>
		</div>
		</div>

	<?php else : ?>

		<!-- ================= INSTALL STATE ================= -->
		<div class="content-row">
			<div class="col card demo-btn text-center">
				<form id="demo-importer-form" method="post">
					<p class="demo-title"><?php esc_html_e( 'Demo Importer', 'elearning-education' ); ?></p>
					<p class="demo-des">
						<?php esc_html_e( 'Import demo content with one click. You can customize everything later.', 'elearning-education' ); ?>
					</p>

					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Begin Installation – Import Demo', 'elearning-education' ); ?>
					</button>

					<div id="page-loader" style="display:none;margin-top:15px;">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/loader.png' ); ?>" width="40">
						<p><?php esc_html_e( 'Importing demo, please wait...', 'elearning-education' ); ?></p>
					</div>
				</form>
			</div>
			<div class="theme-price col card">
			<div class="price-flex">
				<div class="price-content">
					<?php 
						$elearning_education_theme = wp_get_theme();
						?>
						<h3>
						    <?php 
						    echo sprintf(
						        esc_html__( '%s WordPress Theme ', 'elearning-education' ),
						        esc_html( $elearning_education_theme->get( 'Name' ) )
						    ); 
						    ?>
						</h3>
					<p class="main-flash"><?php 
					  printf(
					    /* translators: 1: bold FLASH DEAL text, 2: discount code */
					    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'elearning-education' ),
					    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'elearning-education' ) . '</strong>',
					    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'elearning-education' ) . '</strong>'
					  ); 
					  ?></p>
					 <p>
					  <del><?php echo esc_html__( '$59', 'elearning-education' ); ?></del>
					  <strong class="bold-price"><?php echo esc_html__( '$39', 'elearning-education' ); ?></strong>
					</p>
				</div>
				<div class="price-img">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
				</div>
			</div>
			<div class="main-pro-price">
				<?php 
					$elearning_education_theme = wp_get_theme();
					?>
				<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro">
				    <?php 
				    echo sprintf(
				        esc_html__( 'Upgrade To Premium %s Theme', 'elearning-education' ),
				        esc_html( $elearning_education_theme->get( 'Name' ) )
				    ); 
				    ?>
				</a>
			</div>
		</div>
		</div>

		<script>
		jQuery(function($){
			$('#demo-importer-form').on('submit', function(e){
				e.preventDefault();
				if(confirm('<?php esc_html_e( 'Are you sure you want to import demo content?', 'elearning-education' ); ?>')){
					$('#page-loader').show();
					let url = new URL(window.location.href);
					url.searchParams.set('import-demo','true');
					window.location.href = url;
				}
			});
		});
		</script>

	<?php endif; ?>

	</div>

	<?php if ( $show_popup_now ) : ?>
	<!-- ================= SUCCESS POPUP (ONCE PER THEME) ================= -->
	<div id="demo-success-modal" class="modal-overlay">
		<div class="modal-content">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/demo-icon.png' ); ?>" alt="">
			<h2><?php esc_html_e( 'Demo Successfully Imported!', 'elearning-education' ); ?></h2>

			<div class="modal-buttons">
				<a class="button button-primary" href="<?php echo esc_url( home_url('/') ); ?>" target="_blank">
					<?php esc_html_e( 'View Site', 'elearning-education' ); ?>
				</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=elearning-education-about' ) ); ?>">
					<?php esc_html_e( 'Go To Dashboard', 'elearning-education' ); ?>
				</a>
			</div>
		</div>
	</div>

	<script>
	document.addEventListener("DOMContentLoaded", function () {
		const modal = document.getElementById("demo-success-modal");
		if (!modal) return;

		modal.style.display = "flex";

		fetch('<?php echo esc_url( admin_url( 'admin-ajax.php?action=elearning_education_popup_done' ) ); ?>');

		modal.querySelectorAll('a.button').forEach(btn => {
			btn.addEventListener('click', () => modal.style.display = "none");
		});
	});
	</script>
	<?php endif; ?>
<?php
}

/**
 * Output the main about screen.
 */
function elearning_education_main_screen() {
	
	?>
	<div id="tp_about_theme" class="elearning-education-tabcontent">
		<div class="content-row">
			<div class="feature-section two-col">
				<div class="col card">
					<h2 class="title"><?php esc_html_e( 'Theme Customizer', 'elearning-education' ); ?></h2>
					<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'elearning-education' ) ?></p>
					<p><a target="_blank" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Customize', 'elearning-education' ); ?></a></p>
				</div>

				<div class="col card">
					<h2 class="title"><?php esc_html_e( 'Got theme support question?', 'elearning-education' ); ?></h2>
					<p><?php esc_html_e( 'Get genuine support from genuine people. Whether it\'s customization or compatibility, our seasoned developers deliver tailored solutions to your queries.', 'elearning-education' ) ?></p>
					<p><a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_SUPPORT_THEME_URL ); ?>" class="button button-primary"><?php esc_html_e( 'Support Forum', 'elearning-education' ); ?></a></p>
				</div>
			</div>
			<div class="theme-price col card">
				<div class="price-flex">
					<div class="price-content">
						<?php 
						$elearning_education_theme = wp_get_theme();
						?>
						<h3>
						    <?php 
						    echo sprintf(
						        esc_html__( '%s WordPress Theme ', 'elearning-education' ),
						        esc_html( $elearning_education_theme->get( 'Name' ) )
						    ); 
						    ?>
						</h3>
						<p class="main-flash"><?php 
						  printf(
						    /* translators: 1: bold FLASH DEAL text, 2: discount code */
						    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'elearning-education' ),
						    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'elearning-education' ) . '</strong>',
						    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'elearning-education' ) . '</strong>'
						  ); 
						  ?></p>
						 <p>
						  <del><?php echo esc_html__( '$59', 'elearning-education' ); ?></del>
						  <strong class="bold-price"><?php echo esc_html__( '$39', 'elearning-education' ); ?></strong>
						</p>
					</div>
					<div class="price-img">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
					</div>
				</div>
				<div class="main-pro-price">
					<?php 
					$elearning_education_theme = wp_get_theme();
					?>
					<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro">
					    <?php 
					    echo sprintf(
					        esc_html__( 'Upgrade To Premium %s Theme', 'elearning-education' ),
					        esc_html( $elearning_education_theme->get( 'Name' ) )
					    ); 
					    ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Output the changelog screen.
 */
function elearning_education_changelog_screen() {
		global $wp_filesystem;
	?>
	<div id="tp_changelog" class="elearning-education-tabcontent">
	<div class="content-row">
		<div class="wrap about-wrap change-log">
			<?php
				$changelog_file = apply_filters( 'elearning_education_changelog_file', ELEARNING_EDUCATION_CHANGELOG_THEME_URL );
				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = elearning_education_parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<div class="theme-price col card">
				<div class="price-flex">
					<div class="price-content">
						<?php 
						$elearning_education_theme = wp_get_theme();
						?>
						<h3>
						    <?php 
						    echo sprintf(
						        esc_html__( '%s WordPress Theme ', 'elearning-education' ),
						        esc_html( $elearning_education_theme->get( 'Name' ) )
						    ); 
						    ?>
						</h3>
						<p class="main-flash"><?php 
						  printf(
						    /* translators: 1: bold FLASH DEAL text, 2: discount code */
						    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'elearning-education' ),
						    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'elearning-education' ) . '</strong>',
						    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'elearning-education' ) . '</strong>'
						  ); 
						  ?></p>
						 <p>
						  <del><?php echo esc_html__( '$59', 'elearning-education' ); ?></del>
						  <strong class="bold-price"><?php echo esc_html__( '$39', 'elearning-education' ); ?></strong>
						</p>
					</div>
					<div class="price-img">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
					</div>
				</div>
				<div class="main-pro-price">
					<?php 
					$elearning_education_theme = wp_get_theme();
					?>
					<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro">
					    <?php 
					    echo sprintf(
					        esc_html__( 'Upgrade To Premium %s Theme', 'elearning-education' ),
					        esc_html( $elearning_education_theme->get( 'Name' ) )
					    ); 
					    ?>
					</a>
				</div>
			</div>
	</div>
</div>
	<?php
}

/**
 * Parse changelog from readme file.
 * @param  string $content
 * @return string
 */
function elearning_education_parse_changelog( $content ) {
	// Explode content with ==  to juse separate main content to array of headings.
	$content = explode ( '== ', $content );

	$changelog_isolated = '';

	// Get element with 'Changelog ==' as starting string, i.e isolate changelog.
	foreach ( $content as $key => $value ) {
		if (strpos( $value, 'Changelog ==') === 0) {
	    	$changelog_isolated = str_replace( 'Changelog ==', '', $value );
	    }
	}

	// Now Explode $changelog_isolated to manupulate it to add html elements.
	$changelog_array = explode( '= ', $changelog_isolated );

	// Unset first element as it is empty.
	unset( $changelog_array[0] );

	$changelog = '<pre class="changelog">';

	foreach ( $changelog_array as $value) {
		// Replace all enter (\n) elements with </span><span> , opening and closing span will be added in next process.
		$value = preg_replace( '/\n+/', '</span><span>', $value );

		// Add openinf and closing div and span, only first span element will have heading class.
		$value = '<div class="block"><span class="heading">= ' . $value . '</span></div>';

		// Remove empty <span></span> element which newr formed at the end.
		$changelog .= str_replace( '<span></span>', '', $value );
	}

	$changelog .= '</pre>';

	return wp_kses_post( $changelog );
}

/**
 * Import Demo data for theme using catch themes demo import plugin
 */
function elearning_education_free_vs_pro() {
	?>
	<div id="tp_free_vs_pro" class="elearning-education-tabcontent">
	<div class="content-row">
		<div class="wrap about-wrap change-log">
			<p class="about-description"><?php esc_html_e( 'View Free vs Pro Table below:', 'elearning-education' ); ?></p>
			<div class="vs-theme-table">
				<table>
					<thead>
						<tr><th scope="col"></th>
							<th class="head" scope="col"><?php esc_html_e( 'Free Theme', 'elearning-education' ); ?></th>
							<th class="head" scope="col"><?php esc_html_e( 'Pro Theme', 'elearning-education' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><span><?php esc_html_e( 'Theme Demo Set Up', 'elearning-education' ); ?></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Additional Templates, Color options and Fonts', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Included Demo Content', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Section Ordering', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Multiple Sections', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Additional Plugins', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Premium Technical Support', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Access to Support Forums', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Free updates', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Unlimited Domains', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Responsive Design', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Live Customizer', 'elearning-education' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td class="feature feature--empty"></td>
							<td class="feature feature--empty"></td>
							<td headers="comp-2" class="td-btn-2"><a class="sidebar-button single-btn" href="<?php echo esc_url(ELEARNING_EDUCATION_PRO_THEME_URL);?>" target="_blank"><?php esc_html_e( 'Go For Premium', 'elearning-education' ); ?></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="theme-price col card">
			<div class="price-flex">
				<div class="price-content">
					<?php 
						$elearning_education_theme = wp_get_theme();
						?>
						<h3>
						    <?php 
						    echo sprintf(
						        esc_html__( '%s WordPress Theme ', 'elearning-education' ),
						        esc_html( $elearning_education_theme->get( 'Name' ) )
						    ); 
						    ?>
						</h3>
					<p class="main-flash"><?php 
					  printf(
					    /* translators: 1: bold FLASH DEAL text, 2: discount code */
					    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'elearning-education' ),
					    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'elearning-education' ) . '</strong>',
					    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'elearning-education' ) . '</strong>'
					  ); 
					  ?></p>
					 <p>
					  <del><?php echo esc_html__( '$59', 'elearning-education' ); ?></del>
					  <strong class="bold-price"><?php echo esc_html__( '$39', 'elearning-education' ); ?></strong>
					</p>
				</div>
				<div class="price-img">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
				</div>
			</div>
			<div class="main-pro-price">
				<?php 
					$elearning_education_theme = wp_get_theme();
					?>
				<a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro">
				    <?php 
				    echo sprintf(
				        esc_html__( 'Upgrade To Premium %s Theme', 'elearning-education' ),
				        esc_html( $elearning_education_theme->get( 'Name' ) )
				    ); 
				    ?>
				</a>
			</div>
		</div>
	</div>
</div>
	<?php
}

function elearning_education_get_bundle() {
	?>
	<div id="tp_get_bundle" class="elearning-education-tabcontent">
		<div class="wrap about-wrap theme-main-bundle">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-bundle.png" alt="theme-bundle" width="300" height="300" />
			<p class="bundle-link"><a target="_blank" href="<?php echo esc_url( ELEARNING_EDUCATION_THEME_BUNDLE ); ?>" class="button button-primary bundle-btn"><?php esc_html_e( 'Buy WordPress Theme Bundle (120+ Themes)', 'elearning-education' ); ?></a></p>
		</div>
	</div>
	<?php
}