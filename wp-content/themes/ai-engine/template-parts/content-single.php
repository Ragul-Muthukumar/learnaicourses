<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ai_engine
 */
$ai_engine_single_meta_setting  = get_theme_mod( 'ai_engine_single_post_meta_setting' , true );
$ai_engine_single_content_setting  = get_theme_mod( 'ai_engine_single_post_content_setting' , true );
$ai_engine_service_icon = get_theme_mod('ai_engine_service_icon1', 'fas fa-tag');
$ai_engine_icon_text = get_theme_mod('ai_engine_service_icon_text1');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php

		if ( 'post' === get_post_type() ) : ?>
		<?php
		if ( $ai_engine_single_meta_setting ){ ?>
			<div class="entry-meta">
				<?php ai_engine_posted_on(); ?>
			</div><!-- .entry-meta -->
		<?php } ?>
		<?php
		endif; ?>
	</header><!-- .entry-header -->
	
    <?php
	if ( $ai_engine_single_content_setting ){ ?>
		<div class="entry-content" itemprop="text">
			<?php
			if( is_single()){
				the_content( sprintf(
					/* translators: %s: Name of current post. */
					wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'ai-engine' ), array( 'span' => array( 'class' => array() ) ) ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				) );
				}else{
				the_excerpt();
				}
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ai-engine' ),
					'after'  => '</div>',
				) );
			?>
		</div>
		<!-- .entry-content -->

		<div class="popular-content">
			<div class="single-page-content">
				<?php if ( get_theme_mod('ai_engine_author_image1') != "" ) { ?>
					<div class="service-author-info">

						<img class="author-img" 
						src="<?php echo esc_url(get_theme_mod('ai_engine_author_image1')); ?>" 
						alt="">

						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' )) ); ?>">
							<?php the_author(); ?>
						</a>

					</div>
				<?php } ?>	
				<?php if( !empty($ai_engine_service_icon) || !empty($ai_engine_icon_text) ){ ?>
					<div class="service-icons">

						<?php if( !empty($ai_engine_service_icon) ){ ?>
							<span class="service-icon">
								<i class="<?php echo esc_attr($ai_engine_service_icon); ?>"></i>
							</span>
						<?php } ?>

						<?php if( !empty($ai_engine_icon_text) ){ ?>
							<span class="service-text">
								<span><?php echo esc_html($ai_engine_icon_text); ?></span>
							</span>
						<?php } ?>

					</div>
				<?php } ?>
				<?php if( get_theme_mod('ai_engine_regular_price1') != ''){ ?>
					<div class="price-section">

						<span class="regular-price">
							<?php echo esc_html(get_theme_mod('ai_engine_regular_price1'));?>
						</span>

					</div>				
				<?php } ?>
			</div>
		</div>

    <?php } ?>
</article><!-- #post-## -->