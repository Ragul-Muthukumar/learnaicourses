<?php
/**
 * Template part for displaying posts
 *
 * @package eLearning Education
 * @subpackage elearning_education
 */

?>

<?php $elearning_education_column_layout = get_theme_mod( 'elearning_education_sidebar_post_layout');
if($elearning_education_column_layout == 'four-column' || $elearning_education_column_layout == 'three-column' ){ ?>
	<div id="category-post">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="page-box">
			    <?php 
			        if(has_post_thumbnail()) { ?>
			        <div class="box-image">
			            <?php the_post_thumbnail();  ?>	   
			        </div>
			    <?php } ?>
			    <div class="box-content mt-2 text-center">
			        <h4><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a></h4>
	                <div class="box-info">
					    <?php 
					    // Add 'time' to your default meta order array
					    $elearning_education_blog_archive_ordering = get_theme_mod('blog_meta_order', array('date', 'author', 'comment', 'category', 'time'));

					    foreach ($elearning_education_blog_archive_ordering as $elearning_education_blog_data_order) : 
					        if ('date' === $elearning_education_blog_data_order) : ?>
					            <i class="far fa-calendar-alt mb-1"></i>
					            <a href="<?php echo esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))); ?>" class="entry-date">
					                <?php echo esc_html(get_the_date('j F, Y')); ?>
					            </a>
					        <?php elseif ('author' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-user mb-1"></i>
					            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="entry-author">
					                <?php the_author(); ?>
					            </a>
					        <?php elseif ('comment' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-comments mb-1"></i>
					            <a href="<?php comments_link(); ?>" class="entry-comments">
					                <?php comments_number(__('0 Comments', 'elearning-education'), __('1 Comment', 'elearning-education'), __('% Comments', 'elearning-education')); ?>
					            </a>
					        <?php elseif ('category' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-list mb-1"></i>
					            <?php
					            $categories = get_the_category();
					            if (!empty($categories)) :
					                foreach ($categories as $category) : ?>
					                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="entry-category">
					                        <?php echo esc_html($category->name); ?>
					                    </a>
					                <?php endforeach;
					            endif; 
					            ?>
					        <?php elseif ('time' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-clock mb-1"></i>
					            <span class="entry-time">
					                <?php echo get_the_time(); ?>
					            </span>
					        <?php endif;
					    endforeach; ?>
					</div>
			        <p><?php echo esc_html(elearning_education_excerpt_function());?></p>
			        <?php if(get_theme_mod('elearning_education_remove_read_button',true) != ''){ ?>
			            <div class="readmore-btn">
			                <a href="<?php echo esc_url( get_permalink() );?>" class="blogbutton-small" title="<?php esc_attr_e( 'Read More', 'elearning-education' ); ?>"><?php echo esc_html(get_theme_mod('elearning_education_read_more_text',__('Read More','elearning-education')));?></a>
			            </div>
			        <?php }?>
			    </div>
			    <div class="clearfix"></div>
			</div>
		</article>
	</div>
<?php } else{ ?>
<div id="category-post">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="page-box row">
		    <?php if (has_post_thumbnail()) { ?>
		    	<?php $elearning_education_post_layout = get_theme_mod( 'elearning_education_post_layout','image-content');
      			if($elearning_education_post_layout == 'image-content'){ ?>
				    <div class="box-image col-lg-6 col-md-12 align-self-center">
				        <?php the_post_thumbnail(); ?>
				    </div>
				    <div class="box-content col-lg-6 col-md-12">
				        <h4><a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
				        <div class="box-info">
						    <?php 
						    // Add 'time' to your default meta order array
						    $elearning_education_blog_archive_ordering = get_theme_mod('blog_meta_order', array('date', 'author', 'comment', 'category', 'time'));

						    foreach ($elearning_education_blog_archive_ordering as $elearning_education_blog_data_order) : 
						        if ('date' === $elearning_education_blog_data_order) : ?>
						            <i class="far fa-calendar-alt mb-1"></i>
						            <a href="<?php echo esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))); ?>" class="entry-date">
						                <?php echo esc_html(get_the_date('j F, Y')); ?>
						            </a>
						        <?php elseif ('author' === $elearning_education_blog_data_order) : ?>
						            <i class="fas fa-user mb-1"></i>
						            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="entry-author">
						                <?php the_author(); ?>
						            </a>
						        <?php elseif ('comment' === $elearning_education_blog_data_order) : ?>
						            <i class="fas fa-comments mb-1"></i>
						            <a href="<?php comments_link(); ?>" class="entry-comments">
						                <?php comments_number(__('0 Comments', 'elearning-education'), __('1 Comment', 'elearning-education'), __('% Comments', 'elearning-education')); ?>
						            </a>
						        <?php elseif ('category' === $elearning_education_blog_data_order) : ?>
						            <i class="fas fa-list mb-1"></i>
						            <?php
						            $categories = get_the_category();
						            if (!empty($categories)) :
						                foreach ($categories as $category) : ?>
						                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="entry-category">
						                        <?php echo esc_html($category->name); ?>
						                    </a>
						                <?php endforeach;
						            endif; 
						            ?>
						        <?php elseif ('time' === $elearning_education_blog_data_order) : ?>
						            <i class="fas fa-clock mb-1"></i>
						            <span class="entry-time">
						                <?php echo get_the_time(); ?>
						            </span>
						        <?php endif;
						    endforeach; ?>
						</div>
				        <p><?php echo esc_html(elearning_education_excerpt_function()); ?></p>
				        <?php if (get_theme_mod('elearning_education_remove_read_button', true) != '') { ?>
				            <div class="readmore-btn">
				                <a href="<?php echo esc_url(get_permalink()); ?>" class="blogbutton-small" title="<?php esc_attr_e('Read More', 'elearning-education'); ?>"><?php echo esc_html(get_theme_mod('elearning_education_read_more_text', __('Read More', 'elearning-education'))); ?></a>
				            </div>
				        <?php } ?>
				    </div>
			    <?php }
      			else if($elearning_education_post_layout == 'content-image'){ ?>
      				<div class="box-content col-lg-6 col-md-12">
				        <h4><a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
				        <div class="box-info">
					    <?php 
					    // Add 'time' to your default meta order array
					    $elearning_education_blog_archive_ordering = get_theme_mod('blog_meta_order', array('date', 'author', 'comment', 'category', 'time'));

					    foreach ($elearning_education_blog_archive_ordering as $elearning_education_blog_data_order) : 
					        if ('date' === $elearning_education_blog_data_order) : ?>
					            <i class="far fa-calendar-alt mb-1"></i>
					            <a href="<?php echo esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))); ?>" class="entry-date">
					                <?php echo esc_html(get_the_date('j F, Y')); ?>
					            </a>
					        <?php elseif ('author' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-user mb-1"></i>
					            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="entry-author">
					                <?php the_author(); ?>
					            </a>
					        <?php elseif ('comment' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-comments mb-1"></i>
					            <a href="<?php comments_link(); ?>" class="entry-comments">
					                <?php comments_number(__('0 Comments', 'elearning-education'), __('1 Comment', 'elearning-education'), __('% Comments', 'elearning-education')); ?>
					            </a>
					        <?php elseif ('category' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-list mb-1"></i>
					            <?php
					            $categories = get_the_category();
					            if (!empty($categories)) :
					                foreach ($categories as $category) : ?>
					                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="entry-category">
					                        <?php echo esc_html($category->name); ?>
					                    </a>
					                <?php endforeach;
					            endif; 
					            ?>
					        <?php elseif ('time' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-clock mb-1"></i>
					            <span class="entry-time">
					                <?php echo get_the_time(); ?>
					            </span>
					        <?php endif;
					    endforeach; ?>
					</div>
				        <p><?php echo esc_html(elearning_education_excerpt_function()); ?></p>
				        <?php if (get_theme_mod('elearning_education_remove_read_button', true) != '') { ?>
				            <div class="readmore-btn">
				                <a href="<?php echo esc_url(get_permalink()); ?>" class="blogbutton-small" title="<?php esc_attr_e('Read More', 'elearning-education'); ?>"><?php echo esc_html(get_theme_mod('elearning_education_read_more_text', __('Read More', 'elearning-education'))); ?></a>
				            </div>
				        <?php } ?>
				    </div>
				    <div class="box-image col-lg-6 col-md-12 align-self-center  pt-lg-0 pt-3">
				        <?php the_post_thumbnail(); ?>
				    </div>
				<?php }?>
			<?php } else { ?>
			    <div class="box-content col-lg-12 col-md-12">
			        <h4><a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			        <div class="box-info">
					    <?php 
					    // Add 'time' to your default meta order array
					    $elearning_education_blog_archive_ordering = get_theme_mod('blog_meta_order', array('date', 'author', 'comment', 'category', 'time'));

					    foreach ($elearning_education_blog_archive_ordering as $elearning_education_blog_data_order) : 
					        if ('date' === $elearning_education_blog_data_order) : ?>
					            <i class="far fa-calendar-alt mb-1"></i>
					            <a href="<?php echo esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))); ?>" class="entry-date">
					                <?php echo esc_html(get_the_date('j F, Y')); ?>
					            </a>
					        <?php elseif ('author' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-user mb-1"></i>
					            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="entry-author">
					                <?php the_author(); ?>
					            </a>
					        <?php elseif ('comment' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-comments mb-1"></i>
					            <a href="<?php comments_link(); ?>" class="entry-comments">
					                <?php comments_number(__('0 Comments', 'elearning-education'), __('1 Comment', 'elearning-education'), __('% Comments', 'elearning-education')); ?>
					            </a>
					        <?php elseif ('category' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-list mb-1"></i>
					            <?php
					            $categories = get_the_category();
					            if (!empty($categories)) :
					                foreach ($categories as $category) : ?>
					                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="entry-category">
					                        <?php echo esc_html($category->name); ?>
					                    </a>
					                <?php endforeach;
					            endif; 
					            ?>
					        <?php elseif ('time' === $elearning_education_blog_data_order) : ?>
					            <i class="fas fa-clock mb-1"></i>
					            <span class="entry-time">
					                <?php echo get_the_time(); ?>
					            </span>
					        <?php endif;
					    endforeach; ?>
					</div>
			        <p><?php echo esc_html(elearning_education_excerpt_function()); ?></p>
			        <?php if (get_theme_mod('elearning_education_remove_read_button', true) != '') { ?>
			            <div class="readmore-btn">
			                <a href="<?php echo esc_url(get_permalink()); ?>" class="blogbutton-small" title="<?php esc_attr_e('Read More', 'elearning-education'); ?>"><?php echo esc_html(get_theme_mod('elearning_education_read_more_text', __('Read More', 'elearning-education'))); ?></a>
			            </div>
			        <?php } ?>
			    </div>
			<?php } ?>
		    <div class="clearfix"></div>
		</div>
	</article>
</div>
<?php } ?>