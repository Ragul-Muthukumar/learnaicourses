<?php
/**
 * Input validation helpers for Learn AI Courses LMS.
 *
 * What this file does:
 * - Validates enrollment and REST request payloads before DB writes.
 * Process:
 * 1) Sanitize incoming fields.
 * 2) Return WP_Error on failure or a clean array on success.
 */

 // Block direct access outside the WordPress runtime.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validate an enrollment request payload.
 *
 * @param array $request_data Raw associative request body.
 * @return array|WP_Error Sanitized data or error object.
 */
function lac_validate_enrollment_request( $request_data ) {
	 // Ensure the payload is an array before reading keys.
	if ( ! is_array( $request_data ) ) {
		return new WP_Error( 'invalid_payload', 'Enrollment payload must be an object.', array( 'status' => 400 ) );
	}
	 // Require an encrypted course id from the client.
	$encrypted_course_id = isset( $request_data['course_id'] ) ? sanitize_text_field( $request_data['course_id'] ) : '';
	 // Reject empty course identifiers immediately.
	if ( '' === $encrypted_course_id ) {
		return new WP_Error( 'missing_course_id', 'Encrypted course_id is required.', array( 'status' => 400 ) );
	}
	 // Resolve the bcrypt token to an internal course post id.
	$course_id = lac_resolve_course_id_from_hash( $encrypted_course_id );
	 // Reject hashes that do not map to a published course.
	if ( $course_id < 1 ) {
		return new WP_Error( 'invalid_course_id', 'course_id could not be validated.', array( 'status' => 400 ) );
	}
	 // Return only sanitized, validated fields for downstream use.
	return array(
		'course_id' => $course_id,
	);
}

/**
 * Validate a progress update payload.
 *
 * @param array $request_data Raw associative request body.
 * @return array|WP_Error Sanitized data or error object.
 */
function lac_validate_progress_request( $request_data ) {
	 // Reuse enrollment validation for the course id portion.
	$base = lac_validate_enrollment_request( $request_data );
	 // Bubble enrollment validation errors unchanged.
	if ( is_wp_error( $base ) ) {
		return $base;
	}
	 // Read progress percent and coerce to an integer.
	$progress_percent = isset( $request_data['progress_percent'] ) ? absint( $request_data['progress_percent'] ) : 0;
	 // Reject values outside the 0–100 range.
	if ( $progress_percent > 100 ) {
		return new WP_Error( 'invalid_progress', 'progress_percent must be between 0 and 100.', array( 'status' => 400 ) );
	}
	 // Merge progress into the sanitized base payload.
	$base['progress_percent'] = $progress_percent;
	 // Hand the clean payload back to the REST controller.
	return $base;
}

/**
 * Validate course meta saved from the admin edit screen.
 *
 * @param string $course_level Difficulty label from the form.
 * @param mixed  $course_hours Estimated hours value.
 * @param mixed  $course_price Display price value.
 * @return array Sanitized meta values ready for update_post_meta.
 */
function lac_validate_course_meta( $course_level, $course_hours, $course_price ) {
	 // Allow only known difficulty labels.
	$allowed_levels = array( 'beginner', 'intermediate', 'advanced' );
	 // Normalize the level and fall back to beginner when unexpected.
	$safe_level = in_array( $course_level, $allowed_levels, true ) ? $course_level : 'beginner';
	 // Coerce hours to a non-negative float.
	$safe_hours = max( 0, (float) $course_hours );
	 // Coerce price to a non-negative float.
	$safe_price = max( 0, (float) $course_price );
	 // Return the sanitized meta bag.
	return array(
		'course_level' => $safe_level,
		'course_hours' => $safe_hours,
		'course_price' => $safe_price,
	);
}
