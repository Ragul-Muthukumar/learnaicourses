<?php
/**
 * Front-end enrollment and purchase UI helpers.
 *
 * What this file does:
 * - Renders enroll / buy CTAs on course pages that link to checkout.
 * - Keeps continue-learning links for learners already enrolled.
 * Process:
 * 1) Detect whether the current user is enrolled.
 * 2) For free courses, output an enroll link to checkout.
 * 3) For paid courses, output an enroll / buy link to checkout.
 */

 // Guard against direct file hits.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the enroll / continue / checkout CTA for a course page.
 *
 * @param int $course_id Course post id.
 * @return string HTML markup for the CTA.
 */
function lac_render_enrollment_button( $course_id ) {
	 // Read the course price to choose free enroll vs paid checkout labels.
	$course_price = lac_get_course_price( $course_id );
	 // Build the shared checkout destination for this course.
	$checkout_url = lac_get_checkout_url_for_course( $course_id );
	 // When logged in and already enrolled, show a continue-learning link.
	if ( is_user_logged_in() && lac_db_is_user_enrolled( get_current_user_id(), $course_id ) ) {
		ob_start();
		?>
		<a
			class="lac-enroll-button lac-enroll-link is-enrolled"
			href="<?php echo esc_url( lac_get_continue_learning_url( $course_id ) ); ?>"
		>
			<?php echo esc_html( 'Continue learning' ); ?>
		</a>
		<?php
		return ob_get_clean();
	}
	 // Paid courses send learners to checkout before payment.
	if ( $course_price > 0 ) {
		$price_text = number_format( $course_price, 2 );
		ob_start();
		?>
		<a
			class="lac-enroll-button lac-enroll-link is-purchase"
			href="<?php echo esc_url( $checkout_url ); ?>"
		>
			<?php echo esc_html( sprintf( 'Enroll now — $%s', $price_text ) ); ?>
		</a>
		<?php
		return ob_get_clean();
	}
	 // Free courses also route through checkout for a consistent flow.
	ob_start();
	?>
	<a
		class="lac-enroll-button lac-enroll-link is-available"
		href="<?php echo esc_url( $checkout_url ); ?>"
	>
		<?php echo esc_html( 'Enroll free' ); ?>
	</a>
	<?php
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
