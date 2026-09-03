<?php
/**
 * Shared LMS utilities: logging, id encryption, and safe API payloads.
 *
 * Process:
 * 1) Provide lac_log_info / lac_log_error wrappers around error_log.
 * 2) Encrypt numeric WordPress ids with bcrypt for API responses.
 * 3) Validate encrypted ids by comparing against known candidates.
 * 4) Strip personal fields (name, email, mobile) from any outbound array.
 */

 // Block direct file access outside of WordPress bootstrap.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Write an informational log line for operators and developers.
 *
 * @param string $message Human-readable event description.
 * @return void
 */
function lac_log_info( $message ) {
	 // Prefix info lines so they are easy to grep in debug.log.
	error_log( '[LAC INFO] ' . $message );
}

/**
 * Write an error log line when a failure needs attention.
 *
 * @param string $message Human-readable failure description.
 * @return void
 */
function lac_log_error( $message ) {
	 // Prefix error lines so they stand out beside info messages.
	error_log( '[LAC ERROR] ' . $message );
}

/**
 * Allow public learner registration on the site.
 *
 * WordPress blocks /wp-login.php?action=register until users_can_register is 1.
 *
 * @return void
 */
function lac_enable_user_registration_if_needed() {
	 // Turn on membership registration when it is currently disabled.
	if ( '1' !== (string) get_option( 'users_can_register' ) ) {
		update_option( 'users_can_register', 1 );
		lac_log_info( 'Enabled WordPress user registration (users_can_register=1).' );
	}
	 // New self-registered learners should start as subscribers, not admins.
	$default_role = get_option( 'default_role', 'subscriber' );
	if ( 'subscriber' !== $default_role ) {
		update_option( 'default_role', 'subscriber' );
		lac_log_info( 'Set default_role to subscriber for new registrations.' );
	}
}

 // Ensure registration stays available for course checkout / enroll flows.
add_action( 'init', 'lac_enable_user_registration_if_needed', 5 );

/**
 * Encrypt a numeric id with bcrypt for safe public responses.
 *
 * @param int $raw_id Internal WordPress post or user id.
 * @return string Bcrypt hash representing the id.
 */
function lac_encrypt_id( $raw_id ) {
	 // Cast to string so password_hash always receives a stable payload.
	$id_as_string = (string) absint( $raw_id );
	 // Generate a bcrypt hash; cost 10 balances security and speed.
	$encrypted_id = password_hash( $id_as_string, PASSWORD_BCRYPT, array( 'cost' => 10 ) );
	 // Fail loudly if PHP could not produce a hash.
	if ( false === $encrypted_id ) {
		lac_log_error( 'Failed to encrypt id: ' . $id_as_string );
		return '';
	}
	 // Return the encrypted identifier for API consumers.
	return $encrypted_id;
}

/**
 * Validate an encrypted id by matching it against a known raw id.
 *
 * @param string $encrypted_id Hash previously produced by lac_encrypt_id.
 * @param int    $raw_id       Candidate internal id to verify.
 * @return bool True when the hash belongs to the candidate id.
 */
function lac_validate_encrypted_id( $encrypted_id, $raw_id ) {
	 // Reject empty hashes immediately.
	if ( empty( $encrypted_id ) ) {
		return false;
	}
	 // Compare the hash to the absolute integer id string.
	return password_verify( (string) absint( $raw_id ), $encrypted_id );
}

/**
 * Resolve an encrypted course id to an internal post id.
 *
 * @param string $encrypted_id Hash from the client request.
 * @return int Internal course post id, or 0 when invalid.
 */
function lac_resolve_course_id_from_hash( $encrypted_id ) {
	 // Load published course ids to verify against the hash.
	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	 // Test each published course until a bcrypt match is found.
	foreach ( $course_ids as $course_id ) {
		if ( lac_validate_encrypted_id( $encrypted_id, $course_id ) ) {
			return (int) $course_id;
		}
	}
	 // No match means the client sent an invalid or stale token.
	lac_log_error( 'Could not resolve encrypted course id.' );
	return 0;
}

/**
 * Shape an API-safe course payload without personal details.
 *
 * @param int $course_id Internal course post id.
 * @return array Public course fields with encrypted id only.
 */
function lac_format_course_response( $course_id ) {
	 // Load the post object for title and content excerpts.
	$course_post = get_post( $course_id );
	 // Bail with an empty array when the post is missing.
	if ( ! $course_post || 'lac_course' !== $course_post->post_type ) {
		return array();
	}
	 // Read LMS meta used on the front-end cards.
	$course_level   = get_post_meta( $course_id, '_lac_course_level', true );
	$course_hours   = get_post_meta( $course_id, '_lac_course_hours', true );
	$course_price   = get_post_meta( $course_id, '_lac_course_price', true );
	 // Build a public payload that never includes author email/name/mobile.
	return array(
		'id'            => lac_encrypt_id( $course_id ),
		'title'         => get_the_title( $course_id ),
		'excerpt'       => wp_strip_all_tags( get_the_excerpt( $course_id ) ),
		'permalink'     => get_permalink( $course_id ),
		'course_level'  => $course_level ? $course_level : 'beginner',
		'course_hours'  => $course_hours ? (float) $course_hours : 0,
		'course_price'  => $course_price ? (float) $course_price : 0,
		'thumbnail_url' => get_the_post_thumbnail_url( $course_id, 'large' ),
	);
}
