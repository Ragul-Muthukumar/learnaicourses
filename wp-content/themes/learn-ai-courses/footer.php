<?php
/**
 * Theme footer markup.
 *
 * What this file does:
 * - Closes the page with a simple brand footer and wp_footer scripts.
 * Process:
 * 1) Print footer brand line.
 * 2) Call wp_footer and close body/html.
 */
?>
<footer class="site-footer">
	<div class="site-footer__inner">
		<div class="site-footer__grid">
			<div class="site-footer__column">
				<p class="site-footer__brand"><?php bloginfo( 'name' ); ?></p>
				<p class="site-footer__note">Practical AI courses for builders, creators, and teams who want cleaner learning paths.</p>
			</div>
			<div class="site-footer__column">
				<p class="site-footer__heading">Explore</p>
				<p class="site-footer__link"><a href="<?php echo esc_url( get_post_type_archive_link( 'lac_course' ) ); ?>">All courses</a></p>
				<p class="site-footer__link"><a href="<?php echo esc_url( home_url( '/wp-json/lac-lms/v1/courses' ) ); ?>">Course API</a></p>
			</div>
			<div class="site-footer__column">
				<p class="site-footer__heading">Access</p>
				<?php if ( is_user_logged_in() ) : ?>
					<p class="site-footer__link"><a href="<?php echo esc_url( admin_url() ); ?>">Dashboard</a></p>
				<?php else : ?>
					<p class="site-footer__link"><a href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>">Sign in</a></p>
				<?php endif; ?>
				<p class="site-footer__link"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></p>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
