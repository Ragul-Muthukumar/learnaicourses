<?php
/*
* Display Logo and contact details
*/
  $elearning_education_args = array(
   'orderby' => 'title',
   'order' => 'ASC',
   'hide_empty' => 0,
   'parent' => 0,
  );
?>

<div class="headerbox">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-12 align-self-md-center">
        <?php $elearning_education_logo_settings = get_theme_mod( 'elearning_education_logo_settings','Different Line');
        if($elearning_education_logo_settings == 'Different Line'){ ?>
          <div class="logo">
            <?php if( has_custom_logo() ) elearning_education_the_custom_logo(); ?>
            <?php if( get_theme_mod('elearning_education_site_title_text',true) == 1){ ?>
              <?php if (is_front_page() && is_home()) : ?>
                <h1 class="text-capitalize">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                </h1> 
              <?php else : ?>
                  <p class="text-capitalize site-title">
                      <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                  </p>
              <?php endif; ?>
            <?php }?>
            <?php $elearning_education_description = get_bloginfo( 'description', 'display' );
            if ( $elearning_education_description || is_customize_preview() ) : ?>
              <?php if(get_theme_mod('elearning_education_site_tagline_text',false)){ ?>
                <p class="site-description mb-0"><?php echo esc_html($elearning_education_description); ?></p>
              <?php }?>
            <?php endif; ?>
          </div>
        <?php }else if($elearning_education_logo_settings == 'Same Line'){ ?>
          <div class="logo logo-same-line">
            <div class="row">
              <div class="col-lg-5 col-md-5 align-self-md-center">
                <?php if( has_custom_logo() ) elearning_education_the_custom_logo(); ?>
              </div>
              <div class="col-lg-7 col-md-7 align-self-md-center">
                <?php if( get_theme_mod('elearning_education_site_title_text',true) == 1){ ?>
                  <?php if (is_front_page() && is_home()) : ?>
                    <h1 class="text-capitalize">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                    </h1> 
                  <?php else : ?>
                      <p class="text-capitalize site-title">
                          <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                      </p>
                  <?php endif; ?>
                <?php }?>
                <?php $elearning_education_description = get_bloginfo( 'description', 'display' );
                if ( $elearning_education_description || is_customize_preview() ) : ?>
                  <?php if(get_theme_mod('elearning_education_site_tagline_text',false)){ ?>
                    <p class="site-description mb-0"><?php echo esc_html($elearning_education_description); ?></p>
                  <?php }?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php }?>
      </div>
      <div class="col-lg-5 col-md-6 align-self-md-center">
        <div class="search_inner">
          <?php get_search_form(); ?>
          <?php if ( class_exists( 'LearnPress' ) ) {?>
            <div class="dropdown header-show">
              <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Category
              </a>
              <div class="dropdown-menu category-search" aria-labelledby="dropdownMenuLink">
                <ul>
                  <?php
                    $elearning_education_course_categories = get_terms( 'course_category', $elearning_education_args );
                    foreach ( $elearning_education_course_categories as $course_category ) {
                    $elearning_education_course_cat_id = $course_category->term_id;
                    $elearning_education_cat_link = get_category_link( $elearning_education_course_cat_id );
                    if ($course_category->category_parent == 0) {
                    }?>
                    <li><a href="<?php echo $elearning_education_cat_link ?>"><?php echo esc_html( $course_category->name ); ?></a></li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          <?php }?>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 align-self-md-center text-md-end text-center my-4">
        <?php if( get_theme_mod( 'elearning_education_header_teacher' ) != '') { ?>
          <a href="<?php echo esc_url(home_url( '/index.php/lp-become-a-teacher/' )); ?>" class="teacher-btn me-2 me-md-4"><?php echo esc_html(get_theme_mod('elearning_education_header_teacher'));?></a>
        <?php }?>
        <a href="<?php echo esc_url( home_url( '/index.php/lp-profile/' )); ?>"><i class="far fa-user me-2"></i></a>
        <?php if( get_theme_mod( 'elearning_education_header_wishlist_url' ) != '') { ?>
          <a href="<?php echo esc_url(get_theme_mod('elearning_education_header_wishlist_url')) ?>"><i class="far fa-heart me-2"></i></a>
        <?php }?>
        <a href="<?php echo esc_url(home_url( '/index.php/lp-checkout/' )); ?>"><i class="fas fa-shopping-bag"></i></a>
      </div>
    </div>
  </div>
</div>
 