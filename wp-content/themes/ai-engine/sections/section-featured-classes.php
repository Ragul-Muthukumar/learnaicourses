<?php
/**
 * Choose Section
 * 
 * @package ai_engine
 */
$ai_engine_classes = get_theme_mod('ai_engine_about_setting', false);
$ai_engine_about_btn_url  = get_theme_mod( 'ai_engine_about_btn_url' );
$ai_engine_about_btn_text = get_theme_mod( 'ai_engine_about_btn_text' );
?>
<?php if ( $ai_engine_classes ) { ?>
    <div class="about-section">
        <div class="container">
            <div class="row">
            <div class="col-lg-6 col-md-12 align-self-center about-info wow fadeInDown" data-wow-duration="1.5s">
                <?php 
                    $ai_engine_about_text_extra = get_theme_mod('ai_engine_about_text_extra');
                    if ( $ai_engine_about_text_extra ) : ?>
                    <h4>
                        <?php echo esc_html($ai_engine_about_text_extra); ?>
                    </h4>
                <?php endif; ?>
                <?php 
                    $ai_engine_about_title = get_theme_mod('ai_engine_about_title');
                    if ( $ai_engine_about_title ) : ?>
                    <h2>
                        <?php echo esc_html($ai_engine_about_title); ?>
                    </h2>
                <?php endif; ?>
                <?php if ( get_theme_mod('ai_engine_about_content') ) : ?>
                    <p class="slide-extra-content"><?php echo esc_html(get_theme_mod('ai_engine_about_content')); ?></p>
                <?php endif; ?>
                <div class="icon-section">
                    <div class="icon-sec">
                        <span class="about-icon">                                     
                            <i class="<?php echo esc_attr(get_theme_mod('ai_engine_about_icon_1','fas fa-microphone-lines')); ?>"></i>                                    
                        </span>
                        <?php if ( get_theme_mod('ai_engine_icon_text_1') ) : ?>
                            <p class="icon-content"><?php echo esc_html(get_theme_mod('ai_engine_icon_text_1')); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="icon-sec">
                        <span class="about-icon">                                     
                            <i class="<?php echo esc_attr(get_theme_mod('ai_engine_about_icon_2','fas fa-star')); ?>"></i>                                    
                        </span>
                        <?php if ( get_theme_mod('ai_engine_icon_text_2') ) : ?>
                            <p class="icon-content"><?php echo esc_html(get_theme_mod('ai_engine_icon_text_2')); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="icon-sec">
                        <span class="about-icon">                                     
                            <i class="<?php echo esc_attr(get_theme_mod('ai_engine_about_icon_3','fas fa-chart-area')); ?>"></i>                                    
                        </span>
                        <?php if ( get_theme_mod('ai_engine_icon_text_3') ) : ?>
                            <p class="icon-content"><?php echo esc_html(get_theme_mod('ai_engine_icon_text_3')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="about-buttons">
                    <div class="slide-btn-green">
                        <?php if ($ai_engine_about_btn_text ) : ?>
                        <a href="<?php echo esc_url( $ai_engine_about_btn_url ); ?>">                   
                            <?php echo esc_html( $ai_engine_about_btn_text ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-4 col-md-12 align-self-end">
                <div class="imagebox d-flex justify-content-end align-items-center gap-3">
                    <div class="about-img wow zoomIn" data-wow-duration="1.5s">
                        <?php if ( get_theme_mod('ai_engine_about_image') ) : ?>
                            <img src="<?php echo esc_url(get_theme_mod('ai_engine_about_image'));?>">
                        <?php else: ?>
                            <img src="<?php echo get_stylesheet_directory_uri() . '/images/default.png'; ?>">
                        <?php endif; ?>
                    </div>
                    <div class="about-images">
                        <div class="about-img-2 pb-2 wow zoomIn" data-wow-duration="2s">
                            <?php if ( get_theme_mod('ai_engine_about_image_2') ) : ?>
                                <img src="<?php echo esc_url(get_theme_mod('ai_engine_about_image_2'));?>">
                            <?php else: ?>
                                <img src="<?php echo get_stylesheet_directory_uri() . '/images/default.png'; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="about-img-3 pt-2 wow zoomIn" data-wow-duration="2.5s">
                            <?php if ( get_theme_mod('ai_engine_about_image_3') ) : ?>
                                <img src="<?php echo esc_url(get_theme_mod('ai_engine_about_image_3'));?>">
                            <?php else: ?>
                                <img src="<?php echo get_stylesheet_directory_uri() . '/images/default.png'; ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
<?php } ?>

