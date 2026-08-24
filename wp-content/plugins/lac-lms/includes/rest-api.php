<?php
/**
 * REST API routes for Learn AI Courses LMS.
 *
 * What this file does:
 * - Exposes course listing and enrollment endpoints.
 * Process:
 * 1) Register routes under lac-lms/v1.
 * 2) Validate input via validation.php.
 * 3) Persist via db.php and return encrypted ids only.
 */

 // Block direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all LMS REST routes.
 *
 * @return void
 */
function lac_register_rest_routes() {
	 // Public course catalog endpoint.
	register_rest_route(
		'lac-lms/v1',
		'/courses',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'lac_rest_list_courses',
			'permission_callback' => '__return_true',
		)
	);
	 // Authenticated enrollment endpoint.
	register_rest_route(
		'lac-lms/v1',
		'/enroll',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'lac_rest_enroll',
			'permission_callback' => 'lac_rest_require_login',
		)
	);
	 // Authenticated progress update endpoint.
	register_rest_route(
		'lac-lms/v1',
		'/progress',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'lac_rest_update_progress',
			'permission_callback' => 'lac_rest_require_login',
		)
	);
}

 // Bind route registration to rest_api_init.
add_action( 'rest_api_init', 'lac_register_rest_routes' );

/**
 * Permission callback requiring an authenticated learner.
 *
 * @return bool|WP_Error True when logged in, error otherwise.
 */
function lac_rest_require_login() {
	 // Allow only signed-in users to mutate enrollment state.
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'rest_forbidden', 'Authentication required.', array( 'status' => 401 ) );
	}
	return true;
}

/**
 * GET /courses — list published courses with encrypted ids.
 *
 * @return WP_REST_Response Course collection response.
 */
function lac_rest_list_courses() {
	 // Query all published courses newest first.
	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	 // Map each id into a public-safe payload.
	$courses = array_map( 'lac_format_course_response', $course_ids );
	 // Log the catalog hit without personal data.
	lac_log_info( 'REST listed ' . count( $courses ) . ' courses.' );
	 // Return the collection wrapped in a REST response.
	return rest_ensure_response(
		array(
			'courses' => array_values( $courses ),
		)
	);
}

/**
 * POST /enroll — enroll the current user in a course.
 *
 * @param WP_REST_Request $request Incoming REST request.
 * @return WP_REST_Response|WP_Error Result payload or error.
 */
function lac_rest_enroll( WP_REST_Request $request ) {
	 // Validate and sanitize the JSON body.
	$validated = lac_validate_enrollment_request( $request->get_json_params() );
	 // Bubble validation failures to the client.
	if ( is_wp_error( $validated ) ) {
		return $validated;
	}
	 // Capture the current learner id for the insert.
	$user_id = get_current_user_id();
	 // Short-circuit when already enrolled.
	if ( lac_db_is_user_enrolled( $user_id, $validated['course_id'] ) ) {
		return rest_ensure_response(
			array(
				'status'  => 'already_enrolled',
				'message' => 'You are already enrolled in this course.',
			)
		);
	}
	 // Insert the enrollment row.
	$insert_id = lac_db_insert_enrollment( $user_id, $validated['course_id'] );
	 // Fail with a 500 when the database write fails.
	if ( false === $insert_id ) {
		return new WP_Error( 'enroll_failed', 'Could not create enrollment.', array( 'status' => 500 ) );
	}
	 // Log success without exposing personal learner details.
	lac_log_info( 'Enrollment created with row id ' . absint( $insert_id ) );
	 // Return a success payload with encrypted course id only.
	return rest_ensure_response(
		array(
			'status'    => 'enrolled',
			'message'   => 'Enrollment successful.',
			'course_id' => lac_encrypt_id( $validated['course_id'] ),
		)
	);
}

/**
 * POST /progress — update progress for an enrollment.
 *
 * @param WP_REST_Request $request Incoming REST request.
 * @return WP_REST_Response|WP_Error Result payload or error.
 */
function lac_rest_update_progress( WP_REST_Request $request ) {
	 // Validate course id and progress percent.
	$validated = lac_validate_progress_request( $request->get_json_params() );
	 // Return validation errors unchanged.
	if ( is_wp_error( $validated ) ) {
		return $validated;
	}
	 // Require an existing enrollment before updating progress.
	$user_id = get_current_user_id();
	if ( ! lac_db_is_user_enrolled( $user_id, $validated['course_id'] ) ) {
		return new WP_Error( 'not_enrolled', 'Enroll before updating progress.', array( 'status' => 400 ) );
	}
	 // Persist the new progress value.
	$updated = lac_db_update_progress( $user_id, $validated['course_id'], $validated['progress_percent'] );
	 // Fail when the update could not run.
	if ( ! $updated ) {
		return new WP_Error( 'progress_failed', 'Could not update progress.', array( 'status' => 500 ) );
	}
	 // Confirm the write in logs.
	lac_log_info( 'Progress updated for course ' . absint( $validated['course_id'] ) );
	 // Respond with encrypted course id and the stored percent.
	return rest_ensure_response(
		array(
			'status'           => 'updated',
			'course_id'        => lac_encrypt_id( $validated['course_id'] ),
			'progress_percent' => $validated['progress_percent'],
		)
	);
}
