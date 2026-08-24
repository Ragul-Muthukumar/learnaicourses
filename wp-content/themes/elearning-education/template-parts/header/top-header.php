<?php 
/*
* Display Logo and contact details
*/
?>

<div class="top-header py-2">
  <div class="container">
    <div class="row">
      <div class="col-lg-3">
      </div>
      <div class="col-lg-9 col-md-12 text-center text-md-end box-header">
        <?php if( get_theme_mod( 'elearning_education_phone_number' ) != '') { ?>
          <span class="me-lg-3 me-2">
            <a href="tel:<?php echo esc_attr(get_theme_mod('elearning_education_phone_number','')); ?>" class="text-decoration-none">
              <i class="<?php echo esc_attr(get_theme_mod('elearning_education_phone_icon','fas fa-phone')); ?> me-2"></i>
              <?php echo esc_html( get_theme_mod('elearning_education_phone_number','')); ?>
            </a>
          </span>
        <?php } ?>

        <?php if( get_theme_mod( 'elearning_education_email_address' ) != '') { ?>
          <span class="me-lg-3 me-2 my-2 my-md-0">
            <a href="mailto:<?php echo esc_attr(get_theme_mod('elearning_education_email_address','')); ?>" class="text-decoration-none">
              <i class="<?php echo esc_attr(get_theme_mod('elearning_education_mail_icon','far fa-envelope')); ?> me-2"></i>
              <?php echo esc_html( get_theme_mod('elearning_education_email_address','')); ?>
            </a>
          </span>
        <?php } ?>

        <span class="media-links mb-3 mb-md-0 me-lg-3 me-2">
          <?php                 
            $elearning_education_header_fb_new_tab = esc_attr(get_theme_mod('elearning_education_header_fb_new_tab','true'));
            $elearning_education_header_twt_new_tab = esc_attr(get_theme_mod('elearning_education_header_twt_new_tab','true'));
            $elearning_education_header_ins_new_tab = esc_attr(get_theme_mod('elearning_education_header_ins_new_tab','true'));
            $elearning_education_header_ut_new_tab = esc_attr(get_theme_mod('elearning_education_header_ut_new_tab','true'));
            $elearning_education_header_pint_new_tab = esc_attr(get_theme_mod('elearning_education_header_pint_new_tab','true'));
            ?>
          <?php if( get_theme_mod( 'elearning_education_facebook_url' ) != '') { ?>
            <a <?php if($elearning_education_header_fb_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'elearning_education_facebook_url','' ) ); ?>"><i class="<?php echo esc_attr(get_theme_mod('elearning_education_facebook_icon','fab fa-facebook-f')); ?> me-2"></i></i></a>
          <?php } ?>
          <?php if( get_theme_mod( 'elearning_education_twitter_url' ) != '') { ?>
            <a <?php if($elearning_education_header_twt_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'elearning_education_twitter_url','' ) ); ?>"><i class="<?php echo esc_attr(get_theme_mod('elearning_education_twitter_icon','fab fa-twitter')); ?> me-2"></i></a>
          <?php } ?>
          <?php if( get_theme_mod( 'elearning_education_instagram_url' ) != '') { ?>
            <a <?php if($elearning_education_header_ins_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'elearning_education_instagram_url','' ) ); ?>"><i class="<?php echo esc_attr(get_theme_mod('elearning_education_instagram_icon','fab fa-instagram')); ?> me-2"></i></i></a>
          <?php } ?>
          <?php if( get_theme_mod( 'elearning_education_youtube_url' ) != '') { ?>
            <a <?php if($elearning_education_header_ut_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'elearning_education_youtube_url','' ) ); ?>"><i class="<?php echo esc_attr(get_theme_mod('elearning_education_youtube_icon','fab fa-youtube')); ?> me-2"></i></a>
          <?php } ?>
          <?php if( get_theme_mod( 'elearning_education_pint_url' ) != '') { ?>
            <a <?php if($elearning_education_header_pint_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'elearning_education_pint_url','' ) ); ?>"><i class="<?php echo esc_attr(get_theme_mod('elearning_education_pinterest_icon','fab fa-pinterest')); ?> me-2"></i></a>
          <?php } ?>
        </span>
        <?php if( get_theme_mod( 'elearning_education_register_button_link' ) != '' || get_theme_mod( 'elearning_education_register_button' ) != '') { ?>
          <a href="<?php echo esc_url( get_theme_mod( 'elearning_education_register_button_link','' ) ); ?>" class="register-btn"><?php echo esc_html( get_theme_mod( 'elearning_education_register_button','' ) ); ?></a>
        <?php } ?>
        <?php if( get_theme_mod( 'elearning_education_login_button_link' ) != '' || get_theme_mod( 'elearning_education_login_button' ) != '') { ?>
          <a href="<?php echo esc_url( get_theme_mod( 'elearning_education_login_button_link','' ) ); ?>" class="login-btn"><i class="fas fa-lock me-2"></i><?php echo esc_html( get_theme_mod( 'elearning_education_login_button','' ) ); ?></a>
        <?php } ?>
      </div>
    </div>
  </div> 
</div>