<?php
/**
 * Template part for displaying Online Courses section
 *
 * @package eLearning Education
 * @subpackage elearning_education
 */

if ( get_theme_mod( 'elearning_education_online_courses_enable' ) ) { ?>
<section id="online-courses" class="text-center py-5">
  <div class="container">
    <?php 
    // Heading Section
    if ( get_theme_mod( 'elearning_education_online_courses_heading' ) ) { ?>
      <h2>
        <?php echo esc_html( get_theme_mod( 'elearning_education_online_courses_heading' ) ); ?>
        <hr>
      </h2>
    <?php } ?>

    <?php 
    // Check if LearnPress Plugin is Active
    if ( class_exists( 'LearnPress' ) ) { ?>
      <div class="owl-carousel pt-5">
        <?php
          $args = array(
            'post_type'      => 'lp_course',
            'posts_per_page' => absint( get_theme_mod( 'elearning_education_online_courses_per_page' ) ),
            'tax_query'      => taxonomy_exists( 'course_category' ) ? array(
              array(
                'taxonomy' => 'course_category',
                'field'    => 'slug',
                'terms'    => get_theme_mod( 'elearning_education_online_courses_category' ),
              ),
            ) : '',
            'order'          => 'ASC',
          );

          $loop = new WP_Query( $args );

          // Loop Through Courses
          if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) {
              $loop->the_post();
              global $post;
              $course   = LP_Global::course();
              $price    = $course->get_price_html();
              $lessons  = $course->get_items( LP_LESSON_CPT );
              $lessons  = is_array( $lessons ) ? count( $lessons ) : 0;
              $students = $course->count_students();
              $duration = learn_press_get_post_translated_duration( get_the_ID() );
              $duration = ! empty( $duration ) ? $duration : __( 'N/A', 'elearning-education' );
        ?>
          <div class="courses-box text-md-start text-center">
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
            <div class="courses-box-content p-2 mb-3">
              <span><?php echo wp_kses_post( get_the_term_list( get_the_ID(), 'course_category', '', '<span>|</span>' ) ); ?></span>
              <h3 class="p-0">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h3>
              <hr>
              <div class="courses-info">
                <div class="row">
                  <div class="col-lg-5 col-md-4 align-self-center">
                    <strong class="me-2 course-price"><?php echo esc_html( $course->get_origin_price_html() ); ?></strong>
                  </div>
                  <div class="col-lg-7 col-md-8 align-self-center">
                    <span class="me-2">
                      <i class="far fa-comment"></i> <?php echo esc_html( get_comments_number() ); ?>
                    </span>
                    <span class="me-2">
                      <i class="far fa-user me-1"></i><?php echo esc_html( $students ); ?>
                    </span>
                    <span>
                      <i class="far fa-clock me-1"></i><?php echo esc_html( $duration ); ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php 
            } // End while loop
          } else {
            echo '<p>' . esc_html__( 'No courses found.', 'elearning-education' ) . '</p>';
          }
          wp_reset_postdata(); 
        ?>
      </div>
    <?php } else { 
      // If LearnPress is not active
      echo '<p>' . esc_html__( 'LearnPress plugin is not active.', 'elearning-education' ) . '</p>';
    } ?>
  </div>
</section>
<?php } ?>
