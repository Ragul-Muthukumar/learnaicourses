<?php
$blogcodeimageoneID = get_theme_mod( 'blog_code_image_one','');
$blogcodeimagetwoID = get_theme_mod( 'blog_code_image_two','');       
$blogcodeimagethreeID = get_theme_mod( 'blog_code_image_three','');
$blogcodeimagefourID = get_theme_mod( 'blog_code_image_four','');
$blogcodeoneID = get_theme_mod( 'coupon_one_content','');
$blogcodetwoID = get_theme_mod( 'coupon_two_content','');
$blogcodethreeID = get_theme_mod( 'coupon_three_content','');
$blogcodefourID = get_theme_mod( 'coupon_four_content','');

$vouchers_array = array();
$has_code_img = false;
$has_code_txt = false;
if( !empty( $blogcodeimageoneID ) ){
	$blog_voucher_one  = wp_get_attachment_image_src( $blogcodeimageoneID,'bosa-420-300');
 	if ( is_array(  $blog_voucher_one ) ){
 		$has_code_img = true;
 		$has_code_txt = true;
   	 	$blog_vouchers_one = $blog_voucher_one[0];
   	 	$vouchers_array['image_one'] = array(
			'ID' => $blog_vouchers_one,
			'txt' => $blogcodeoneID,
		);	
  	}
}
if( !empty( $blogcodeimagetwoID ) ){
	$blog_voucher_two = wp_get_attachment_image_src( $blogcodeimagetwoID,'bosa-420-300');
	if ( is_array(  $blog_voucher_two ) ){
		$has_code_img = true;
		$has_code_txt = true;	
        $blog_vouchers_two = $blog_voucher_two[0];
        $vouchers_array['image_two'] = array(
			'ID' => $blog_vouchers_two,
			'txt' => $blogcodetwoID,
		);	
  	}
}
if( !empty( $blogcodeimagethreeID ) ){	
	$blog_voucher_three = wp_get_attachment_image_src( $blogcodeimagethreeID,'bosa-420-300');
	if ( is_array(  $blog_voucher_three ) ){
		$has_code_img = true;
		$has_code_txt = true;
      	$blog_vouchers_three = $blog_voucher_three[0];
      	$vouchers_array['image_three'] = array(
			'ID' => $blog_vouchers_three,
			'txt' => $blogcodethreeID,
		);	
  	}
}
if( !empty( $blogcodeimagefourID ) ){	
	$blog_voucher_four = wp_get_attachment_image_src( $blogcodeimagefourID,'bosa-420-300');
	if ( is_array(  $blog_voucher_four ) ){
		$has_code_img = true;
		$has_code_txt = true;
      	$blog_vouchers_four = $blog_voucher_four[0];
      	$vouchers_array['image_four'] = array(
			'ID' => $blog_vouchers_four,
			'txt' => $blogcodefourID,
		);	
  	}
}

if( !get_theme_mod( 'coupons_section', true ) && $has_code_img && $has_code_txt ){ ?>
	<section class="section-coupons-area">
		<div class="row">
			<?php foreach( $vouchers_array as $each_vouchers ){ ?>
				<div class="col-md-3 col-sm-6">
					<article class="coupon-code-content-wrap">
						<figure class= "featured-image">
							<img src="<?php echo esc_url( $each_vouchers['ID'] ); ?>">
						</figure>
						<?php if( !empty( $each_vouchers['txt'] ) ) { ?>
							<div class="redeem-code-txt">
								<?php
									echo esc_html( $each_vouchers['txt'] );
								?>
							</div>
						<?php } ?>
					</article>
				</div>
			<?php } ?>
		</div>	
	</section>
<?php } ?>