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
		<p class="site-footer__brand"><?php bloginfo( 'name' ); ?></p>
		<p class="site-footer__note">Practical AI courses for builders who ship.</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
