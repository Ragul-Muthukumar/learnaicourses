<?php
/**
 * Front-end enrollment UI helpers and shortcodes.
 *
 * What this file does:
 * - Renders enroll buttons and enrollment status.
 * Process:
 * 1) Detect whether the current user is enrolled.
 * 2) Output CTA markup consumed by assets/js/common.js.
 */

 // Guard against direct file hits.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the enroll / continue CTA for a course.
 *
 * @param int $course_id Course post id.
 * @return string HTML markup for the CTA.
 */
function lac_render_enrollment_button( $course_id ) {
	 // Encrypt the course id for the data attribute and REST body.
	$encrypted_course_id = lac_encrypt_id( $course_id );
	 // Default CTA label for anonymous or unenrolled visitors.
	$button_label = 'Enroll free';
	 // CSS modifier reflecting enrollment state.
	$button_state = 'is-available';
	 // When logged in, check the enrollments table.
	if ( is_user_logged_in() && lac_db_is_user_enrolled( get_current_user_id(), $course_id ) ) {
		$button_label = 'Continue learning';
		$button_state = 'is-enrolled';
	}
	 // Build the interactive button markup.
	ob_start();
	?>
	<button
		type="button"
		class="lac-enroll-button <?php echo esc_attr( $button_state ); ?>"
		data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>"
		data-action="enroll"
	>
		<?php echo esc_html( $button_label ); ?>
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
