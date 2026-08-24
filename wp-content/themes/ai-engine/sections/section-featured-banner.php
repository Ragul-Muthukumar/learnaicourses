<?php
/**
 * Banner Section
 * 
 * @package ai_engine
 */
$ai_engine_banner = get_theme_mod( 'ai_engine_banner_setting',false );
$ai_engine_banner_btn_url  = get_theme_mod( 'ai_engine_banner_btn_url' );
$ai_engine_banner_btn_text = get_theme_mod( 'ai_engine_banner_btn_text' );
?>
<?php if ( $ai_engine_banner ){?>
    <div class="banner wow fadeInDown" data-wow-duration="1.5s">
      <div class="banner_box">
        <div class="container">
          <div class="row">
            <div class="col-lg-5 col-md-12 align-self-center banner-info">
              <?php 
                  $ai_engine_banner_text_extra = get_theme_mod('ai_engine_banner_text_extra');
                  if ( $ai_engine_banner_text_extra ) : ?>
                  <h4>
                      <?php echo esc_html($ai_engine_banner_text_extra); ?>
                  </h4>
              <?php endif; ?>
              <?php 
                  $ai_engine_banner_title = get_theme_mod('ai_engine_banner_title');
                  if ( $ai_engine_banner_title ) : ?>
                  <h1>
                      <?php echo esc_html($ai_engine_banner_title); ?>
                  </h1>
              <?php endif; ?>
              <?php if ( get_theme_mod('ai_engine_banner_content') ) : ?>
                  <p class="slide-extra-content"><?php echo esc_html(get_theme_mod('ai_engine_banner_content')); ?></p>
              <?php endif; ?>
              <div class="banner-buttons">
                <div class="slide-btn-green">
                    <?php if ($ai_engine_banner_btn_text ) : ?>
                    <a href="<?php echo esc_url( $ai_engine_banner_btn_url ); ?>">                   
                        <?php echo esc_html( $ai_engine_banner_btn_text ); ?>
                    </a>
                    <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-md-12 align-self-end empty-div">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>