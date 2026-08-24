<?php
/**
 * Front-end enrollment and purchase UI helpers.
 *
 * What this file does:
 * - Renders enroll / buy CTAs and enrollment status on product pages.
 * Process:
 * 1) Detect whether the current user is enrolled.
 * 2) For free courses, output an enroll button.
 * 3) For paid courses, output PayPal Buttons or a mock Buy now control.
 */

 // Guard against direct file hits.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the enroll / continue / purchase CTA for a course.
 *
 * @param int $course_id Course post id.
 * @return string HTML markup for the CTA.
 */
function lac_render_enrollment_button( $course_id ) {
	 // Encrypt the course id for the data attribute and REST body.
	$encrypted_course_id = lac_encrypt_id( $course_id );
	 // Read the course price to choose free enroll vs paid purchase.
	$course_price = lac_get_course_price( $course_id );
	 // When logged in and already enrolled, show continue state.
	if ( is_user_logged_in() && lac_db_is_user_enrolled( get_current_user_id(), $course_id ) ) {
		ob_start();
		?>
		<button
			type="button"
			class="lac-enroll-button is-enrolled"
			data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>"
			data-action="enroll"
			data-course_price="0"
		>
			<?php echo esc_html( 'Continue learning' ); ?>
		</button>
		<p class="lac-enroll-message" hidden></p>
		<?php
		return ob_get_clean();
	}
	 // Paid courses: real PayPal when configured, otherwise mock Buy now.
	if ( $course_price > 0 ) {
		 // Format the display price once for labels.
		$price_text = number_format( $course_price, 2 );
		 // Live / sandbox PayPal Smart Buttons path.
		if ( lac_paypal_is_configured() && ! lac_paypal_is_mock_mode() ) {
			ob_start();
			?>
			<div
				class="lac-paypal-wrap"
				data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>"
				data-course_price="<?php echo esc_attr( number_format( $course_price, 2, '.', '' ) ); ?>"
			>
				<p class="lac-paypal-price"><?php echo esc_html( sprintf( 'Buy for $%s', $price_text ) ); ?></p>
				<div class="lac-paypal-button-container"></div>
				<p class="lac-enroll-message" hidden></p>
			</div>
			<?php
			return ob_get_clean();
		}
		 // Mock / local purchase button when PayPal credentials are absent.
		ob_start();
		?>
		<button
			type="button"
			class="lac-enroll-button is-purchase"
			data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>"
			data-action="purchase"
			data-course_price="<?php echo esc_attr( number_format( $course_price, 2, '.', '' ) ); ?>"
		>
			<?php echo esc_html( sprintf( 'Buy now — $%s', $price_text ) ); ?>
		</button>
		<p class="lac-enroll-message" hidden></p>
		<?php if ( ! lac_paypal_is_mock_mode() && ! lac_paypal_is_configured() ) : ?>
			<p class="lac-enroll-hint">
				<?php echo esc_html( 'Local checkout mode. Set PAYPAL_MODE=mock or add PayPal credentials in .env.' ); ?>
			</p>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}
	 // Free courses keep the classic enroll button.
	ob_start();
	?>
	<button
		type="button"
		class="lac-enroll-button is-available"
		data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>"
		data-action="enroll"
		data-course_price="0"
	>
		<?php echo esc_html( 'Enroll free' ); ?>
	</button>
	<p class="lac-enroll-message" hidden></p>
	<?php
	 // Return the buffered HTML string.
	return ob_get_clean();
}

/**
 * Shortcode [lac_enroll] that prints the CTA for the current course.
 *
 * @return string CTA HTML or empty string outside course context.
 */
function lac_enroll_shortcode() {
	 // Only render on single course templates.
	if ( ! is_singular( 'lac_course' ) ) {
		return '';
	}
	 // Delegate to the shared renderer using the queried post.
	return lac_render_enrollment_button( get_the_ID() );
}

 // Register the enroll shortcode with WordPress.
add_shortcode( 'lac_enroll', 'lac_enroll_shortcode' );
