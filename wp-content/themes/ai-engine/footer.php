<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ai_engine
 */
$ai_engine_scroll_top  = get_theme_mod( 'ai_engine_scroll_to_top', true );
$ai_engine_footer_background = get_theme_mod('ai_engine_footer_background_image');
$ai_engine_footer_background_url = '';
if(!empty($ai_engine_footer_background)){
    $ai_engine_footer_background = absint($ai_engine_footer_background);
    $ai_engine_footer_background_url = wp_get_attachment_url($ai_engine_footer_background);
}

$ai_engine_footer_background_color = get_theme_mod('ai_engine_footer_background_color', 'var(--primary-color)'); // New line

$ai_engine_footer_background_style = '';
if (!empty($ai_engine_footer_background_url)) {
    $ai_engine_footer_background_style = ' style="background-image: url(\'' . esc_url($ai_engine_footer_background_url) . '\'); background-repeat: no-repeat; background-size: cover;"';
} else {
    $ai_engine_footer_background_style = ' style="background-color: ' . esc_attr($ai_engine_footer_background_color) . ';"'; // Updated line
}
?>

</div>
</div>
</div>
</div>

<footer class="site-footer"<?php echo $ai_engine_footer_background_style  ?>>
    <?php if( is_active_sidebar( 'footer-one' ) || is_active_sidebar( 'footer-two' ) || is_active_sidebar( 'footer-three' ) || is_active_sidebar( 'footer-four' ) ){ ?>
        <div class="footer-t">
            <div class="container">
                <div class="row">
                    <?php 
                    if( is_active_sidebar( 'footer-one') ) {
                        echo '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">';
                        dynamic_sidebar( 'footer-one' ); 
                        echo '</div>';
                    }
                    
                    if( is_active_sidebar( 'footer-two') ) {
                        echo '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">';
                        dynamic_sidebar( 'footer-two' );
                        echo '</div>';
                    }
                    
                    if( is_active_sidebar( 'footer-three') ) {
                        echo '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">';
                        dynamic_sidebar( 'footer-three' );
                        echo '</div>';
                    }
                    
                    if( is_active_sidebar( 'footer-four' ) ) {
                        echo '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">';
                        dynamic_sidebar( 'footer-four' );
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="footer-t">
            <div class="container">
                <div class="row">
                    <!-- Archive -->
                    <aside id="archive" class="widget widget_archive col-xl-3 col-lg-3 col-md-4 col-sm-6" role="complementary" aria-label="<?php esc_attr_e('secondsidebar', 'ai-engine'); ?>">
                        <h2 class="widget-title"><?php esc_html_e('Archive List', 'ai-engine'); ?></h2>
                        <ul>
                            <?php wp_get_archives('type=monthly'); ?>
                        </ul>
                    </aside>
                    <!-- Recent Posts -->
                    <aside id="recent-posts" class="widget widget_recent_posts col-xl-3 col-lg-3 col-md-4 col-sm-6" role="complementary" aria-label="<?php esc_attr_e('thirdsidebar', 'ai-engine'); ?>">
                        <h2 class="widget-title"><?php esc_html_e('Recent Posts', 'ai-engine'); ?></h2>
                        <ul>
                            <?php
                            $args = array(
                                'post_type'      => 'post',
                                'posts_per_page' => 5,
                            );
                            $ai_engine_recent_posts = new WP_Query($args);

                            while ($ai_engine_recent_posts->have_posts()) : $ai_engine_recent_posts->the_post();
                            ?>
                                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        </ul>
                    </aside>
                    <!-- Categories -->
                    <aside id="categories" class="widget widget_categories col-xl-3 col-lg-3 col-md-4 col-sm-6" role="complementary"  aria-label="<?php esc_attr_e('fourthsidebar', 'ai-engine'); ?>">
                        <h2 class="widget-title"><?php esc_html_e('Categories', 'ai-engine'); ?></h2>
                        <ul>
                            <?php
                            $args = array(
                                'title_li' => '',
                            );
                            wp_list_categories($args);
                            ?>
                        </ul>
                    </aside>
                    <!-- Tags Widget -->
                    <aside id="tags" class="widget widget_tags col-xl-3 col-lg-3 col-md-4 col-sm-6" role="complementary" aria-label="<?php esc_attr_e('fifthsidebar', 'ai-engine'); ?>">
                        <h2 class="widget-title"><?php esc_html_e('Tags', 'ai-engine'); ?></h2>
                        <div class="tag-cloud">
                            <?php wp_tag_cloud(); ?>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    <?php } 
    do_action( 'ai_engine_footer' );
    ?>
    <?php  
    if ( $ai_engine_scroll_top ){ ?>
        <a id="button"><i class="<?php echo esc_attr(get_theme_mod('ai_engine_scroll_icon','fas fa-arrow-up')); ?>"></i></a>
    <?php } ?>
</footer>
</div>
</div>

<?php wp_footer(); ?>

</body>
</html>