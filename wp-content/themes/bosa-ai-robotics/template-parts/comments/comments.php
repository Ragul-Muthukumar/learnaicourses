<?php
$page_one 	= get_theme_mod( 'comments_page_one', '' );
$page_two 	= get_theme_mod( 'comments_page_two', '' );
$page_three = get_theme_mod( 'comments_page_three', '' );
$page_four 	= get_theme_mod( 'comments_page_four', '' );
$page_five	= get_theme_mod( 'comments_page_five', '' );
$page_six 	= get_theme_mod( 'comments_page_six', '' );

$page_array = array();
$has_page = false;
if( !empty( $page_one ) ){
	$has_page = true;
	$page_array['page_one'] = array(
		'ID' => $page_one,
	);
}
if( !empty( $page_two ) ){
	$has_page = true;
	$page_array['page_two'] = array(
		'ID' => $page_two,
	);
}
if( !empty( $page_three ) ){
	$has_page = true;
	$page_array['page_three'] = array(
		'ID' => $page_three,
	);
}
if( !empty( $page_four ) ){
	$has_page = true;
	$page_array['page_four'] = array(
		'ID' => $page_four,
	);
}
if( !empty( $page_five ) ){
	$has_page = true;
	$page_array['page_five'] = array(
		'ID' => $page_five,
	);
}
if( !empty( $page_six ) ){
	$has_page = true;
	$page_array['page_six'] = array(
		'ID' => $page_six,
	);
}

if( !get_theme_mod( 'disable_comments_section', true ) && $has_page ){ ?>
	<section class="section-comments-area">
			<div class="row justify-content-center">
				<?php foreach( $page_array as $each_page ){ ?>
					<div class="col-sm-6 col-lg-4">
						<article class="comments-item">
							<h5 class="comments-content">	
								<?php 
								$excerpt = get_the_excerpt( $each_page[ 'ID' ] );
								$result  = wp_trim_words( $excerpt, 15, '' );
								echo esc_html( $result );
								?>
							</h5>
							<div class="author-content">
								<figure class= "featured-image">
									<?php echo get_the_post_thumbnail( $each_page[ 'ID' ], 'bosa-420-300' ); ?>
								</figure>
								<h6 class="entry-title">
									<a href="<?php echo esc_url( get_permalink( $each_page[ 'ID' ] ) ); ?>">
										<?php echo esc_html( get_the_title( $each_page[ 'ID' ] ) ); ?>
									</a>
								</h6>	
							</div>
						</article>
					</div>
				<?php } ?>
			</div>
	</section>
<?php } ?>