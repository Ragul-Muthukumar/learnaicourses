<?php 
/**
 * Template part for displaying Featured Services Section
 *
 * @package Machine Learning
 */

$ai_engine_services      = get_theme_mod( 'ai_engine_services_setting', false );
$ai_engine_service_title = get_theme_mod( 'ai_engine_service_title' );
$ai_engine_service_text  = get_theme_mod( 'ai_engine_service_text' );
?>
<?php if ( $ai_engine_services ) : ?>
    <div class="our-services">
        <div class="container">
            <div class="side-border">
                <?php if ( $ai_engine_service_title ) : ?>
                    <h3><?php echo esc_html( $ai_engine_service_title ); ?></h3>
                <?php endif; ?>

                <?php if ( $ai_engine_service_text ) : ?>
                    <p class="title-text"><?php echo esc_html( $ai_engine_service_text ); ?></p>
                <?php endif; ?>
            </div>
            <div class="row py-2">
            <?php
                $ai_engine_featured_post = get_theme_mod('ai_engine_claases_number');
                for ($ai_engine_i=1; $ai_engine_i <= $ai_engine_featured_post; $ai_engine_i++) {
                $ai_engine_postData=  get_theme_mod('ai_engine_services_category'.$ai_engine_i);
                if($ai_engine_postData){ ?>
                <?php
                    $args = array(
                    'p' => esc_html($ai_engine_postData ,'ai-engine'),
                    'posts_per_page' => 6,
                    'post_type' => 'post'
                    );
                    $query = new WP_Query( $args );
                    if ( $query->have_posts() ) :
                    while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="col-xl-4 col-md-6 col-12 services-main">
                        <div class="services-box">
                            <div class="classes-inner-box">
                                <?php if(has_post_thumbnail()){ ?>
                                <?php the_post_thumbnail(); ?>
                                <?php } else {?>
                                <img src="<?php echo esc_url(get_template_directory_uri()) ?>/images/default.png" alt="<?php echo esc_attr('Post Image', 'ai-engine'); ?>">
                                <?php }?>
                            </div>
                            <div class="service-like">
                                <?php echo do_shortcode('[posts_like_dislike]');?>
                            </div>
                        <div class="popular-content">
                            <div class="row">
                                <?php 
                                $ai_engine_service_icon = get_theme_mod('ai_engine_service_icon'.$ai_engine_i, 'fas fa-tag');
                                $ai_engine_icon_text = get_theme_mod('ai_engine_service_icon_text'.$ai_engine_i);   
                                if( !empty($ai_engine_service_icon) || !empty($ai_engine_icon_text) ){ 
                                ?>
                                    <div class="col-xl-4 col-lg-4 align-self-center">
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
                                    </div>
                                <?php } ?>
                                <?php if ( get_theme_mod('ai_engine_author_image'.$ai_engine_i) != "" ) { ?>
                                    <div class="col-xl-4 col-lg-4 align-self-center author-desc">
                                        <div class="service-author-info">
                                            <img class="author-img" src="<?php echo esc_url(get_theme_mod('ai_engine_author_image'.$ai_engine_i)); ?>" alt="" title="#slidecaption">

                                            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' )) ); ?>">
                                                <?php the_author(); ?>
                                                <span class="screen-reader-text"><?php the_author(); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if( get_theme_mod('ai_engine_regular_price'.$ai_engine_i) != ''){ ?>
                                    <div class="col-xl-4 col-lg-4 align-self-center price-info">
                                        <div class="price-section">
                                            <span class="regular-price">
                                                <?php echo esc_html(get_theme_mod('ai_engine_regular_price'.$ai_engine_i));?>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php 
                        $ai_engine_star_rating = get_theme_mod( 'ai_engine_star_rating' . $ai_engine_i);

                        if( $ai_engine_star_rating != '' ) :
                        ?>
                            <div class="post-reviews">
                                <?php for ( $ai_engine_star = 1; $ai_engine_star <= 5; $ai_engine_star++ ) : ?>
                                    <?php if ( $ai_engine_star <= $ai_engine_star_rating ) : ?>
                                        <i class="fas fa-star"></i>
                                    <?php else : ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <div class="service-desc">
                            <div>
                                <h3 class="text-capitalize service-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="screen-reader-text"><?php the_title(); ?></span></a></h3>
                            </div>
                            <div>
                            <a class="service-btn" href="<?php the_permalink(); ?>">
                                <?php echo esc_html(get_theme_mod('ai_engine_service_button_label'.$ai_engine_i, __('Enroll Now', 'ai-engine'))); ?>
                                <span class="screen-reader-text">
                            </a>
                            </div>
                        </div>
                        </div>
                    </div>
                    <?php endwhile;
                    wp_reset_postdata();
                    endif; ?>
                <?php }
            } ?>
            </div>
        </div>
    </div>
<?php endif; ?>