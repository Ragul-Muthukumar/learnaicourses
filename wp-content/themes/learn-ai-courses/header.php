<?php
/**
 * Theme header markup.
 *
 * What this file does:
 * - Opens HTML, prints head hooks, and renders the brand-first site header.
 * Process:
 * 1) Emit doctype and language attributes.
 * 2) Call wp_head.
 * 3) Render brand mark + primary navigation.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
	<div class="site-header__inner">
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="site-brand__mark" aria-hidden="true"></span>
			<span class="site-brand__name"><?php bloginfo( 'name' ); ?></span>
		</a>
		<button class="site-nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
			<span class="site-nav-toggle__label">Menu</span>
		</button>
		<nav id="site-nav" class="site-nav" data-site-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => 'lac_theme_fallback_menu',
				)
			);
			?>
		</nav>
	</div>
</header>
