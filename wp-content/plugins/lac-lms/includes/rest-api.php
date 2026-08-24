<?php
/**
 * REST API routes for Learn AI Courses LMS.
 *
 * What this file does:
 * - Exposes course listing, enrollment, and PayPal checkout endpoints.
 * Process:
 * 1) Register routes under lac-lms/v1.
 * 2) Validate input via validation.php.
 * 3) Persist via db.php / paypal.php and return encrypted ids only.
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
	 // Authenticated PayPal order creation endpoint.
	register_rest_route(
		'lac-lms/v1',
		'/paypal/create-order',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'lac_rest_paypal_create_order',
			'permission_callback' => 'lac_rest_require_login',
		)
	);
	 // Authenticated PayPal capture + enrollment endpoint.
	register_rest_route(
		'lac-lms/v1',
		'/paypal/capture-order',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'lac_rest_paypal_capture_order',
			'permission_callback' => 'lac_rest_require_login',
		)
	);
	 // Authenticated local/mock purchase endpoint for paid courses.
	register_rest_route(
		'lac-lms/v1',
		'/purchase',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'lac_rest_purchase_course',
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
 * Build the shared enrollment success payload, including the learning URL.
 *
 * @param int    $course_id Course post id.
 * @param string $status    Machine status such as enrolled or already_enrolled.
 * @param string $message   Learner-facing confirmation.
 * @return array REST payload with encrypted id and continue URL.
 */
function lac_rest_enrollment_payload( $course_id, $status, $message ) {
	return array(
		'status'       => $status,
		'message'      => $message,
		'course_id'    => lac_encrypt_id( (int) $course_id ),
		'continue_url' => function_exists( 'lac_get_continue_learning_url' )
			? lac_get_continue_learning_url( (int) $course_id )
			: (string) get_permalink( (int) $course_id ),
	);
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
	 // Block free-enroll for paid courses — they must pay via PayPal.
	$course_price = lac_get_course_price( $validated['course_id'] );
	if ( $course_price > 0 ) {
		return new WP_Error(
			'payment_required',
			'This course requires PayPal payment before enrollment.',
			array( 'status' => 402 )
		);
	}
	 // Short-circuit when already enrolled.
	if ( lac_db_is_user_enrolled( $user_id, $validated['course_id'] ) ) {
		return rest_ensure_response(
			lac_rest_enrollment_payload(
				$validated['course_id'],
				'already_enrolled',
				'You are already enrolled in this course.'
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
		lac_rest_enrollment_payload(
			$validated['course_id'],
			'enrolled',
			'Enrollment successful.'
		)
	);
}

/**
 * POST /paypal/create-order — start PayPal checkout for a paid course.
 *
 * @param WP_REST_Request $request Incoming REST request.
 * @return WP_REST_Response|WP_Error Result payload or error.
 */
function lac_rest_paypal_create_order( WP_REST_Request $request ) {
	 // Refuse checkout when PayPal credentials are missing.
	if ( ! lac_paypal_is_configured() ) {
		return new WP_Error( 'paypal_not_configured', 'PayPal is not configured on this site.', array( 'status' => 503 ) );
	}
	 // Validate course id and ensure the course is paid.
	$validated = lac_validate_paypal_create_request( $request->get_json_params() );
	if ( is_wp_error( $validated ) ) {
		return $validated;
	}
	 // Capture the purchasing learner id.
	$user_id = get_current_user_id();
	 // Skip checkout when the learner is already enrolled.
	if ( lac_db_is_user_enrolled( $user_id, $validated['course_id'] ) ) {
		return rest_ensure_response(
			lac_rest_enrollment_payload(
				$validated['course_id'],
				'already_enrolled',
				'You are already enrolled in this course.'
			)
		);
	}
	 // Insert a pending local order before talking to PayPal.
	$local_order_id = lac_db_insert_order(
		$user_id,
		$validated['course_id'],
		$validated['course_price'],
		lac_paypal_currency()
	);
	if ( false === $local_order_id ) {
		return new WP_Error( 'order_create_failed', 'Could not create a local order.', array( 'status' => 500 ) );
	}
	 // Create the remote PayPal order for the course amount.
	$paypal_order = lac_paypal_create_order(
		$local_order_id,
		get_the_title( $validated['course_id'] ),
		$validated['course_price'],
		lac_paypal_currency()
	);
	if ( is_wp_error( $paypal_order ) ) {
		lac_db_fail_order( $local_order_id, 'failed' );
		return $paypal_order;
	}
	 // Persist the PayPal order id on the local pending row.
	lac_db_set_order_paypal_id( $local_order_id, $paypal_order['id'] );
	 // Return only the PayPal order id for the JS SDK (no personal fields).
	return rest_ensure_response(
		array(
			'status'          => 'created',
			'paypal_order_id' => sanitize_text_field( $paypal_order['id'] ),
			'course_id'       => lac_encrypt_id( $validated['course_id'] ),
		)
	);
}

/**
 * POST /paypal/capture-order — capture payment and enroll the learner.
 *
 * @param WP_REST_Request $request Incoming REST request.
 * @return WP_REST_Response|WP_Error Result payload or error.
 */
function lac_rest_paypal_capture_order( WP_REST_Request $request ) {
	 // Refuse capture when PayPal credentials are missing.
	if ( ! lac_paypal_is_configured() ) {
		return new WP_Error( 'paypal_not_configured', 'PayPal is not configured on this site.', array( 'status' => 503 ) );
	}
	 // Validate the PayPal order id from the client.
	$validated = lac_validate_paypal_capture_request( $request->get_json_params() );
	if ( is_wp_error( $validated ) ) {
		return $validated;
	}
	 // Load the matching local order row.
	$order_row = lac_db_get_order_by_paypal_id( $validated['paypal_order_id'] );
	if ( ! $order_row ) {
		return new WP_Error( 'order_not_found', 'No matching local order was found.', array( 'status' => 404 ) );
	}
	 // Ensure the order belongs to the authenticated learner.
	$user_id = get_current_user_id();
	if ( (int) $order_row->user_id !== (int) $user_id ) {
		lac_log_error( 'PayPal capture blocked: order ownership mismatch.' );
		return new WP_Error( 'order_forbidden', 'This order does not belong to you.', array( 'status' => 403 ) );
	}
	 // Idempotent success when the order was already completed.
	if ( 'completed' === $order_row->status ) {
		if ( ! lac_db_is_user_enrolled( $user_id, (int) $order_row->course_id ) ) {
			lac_db_insert_enrollment( $user_id, (int) $order_row->course_id );
		}
		return rest_ensure_response(
			lac_rest_enrollment_payload(
				(int) $order_row->course_id,
				'enrolled',
				'Payment already completed. You are enrolled.'
			)
		);
	}
	 // Capture funds through the PayPal Orders API.
	$capture_payload = lac_paypal_capture_order( $validated['paypal_order_id'] );
	if ( is_wp_error( $capture_payload ) ) {
		lac_db_fail_order( (int) $order_row->id, 'failed' );
		return $capture_payload;
	}
	 // Extract the capture transaction id for local storage.
	$capture_id = lac_paypal_extract_capture_id( $capture_payload );
	 // Mark the local order completed with the capture reference.
	lac_db_complete_order( (int) $order_row->id, $capture_id );
	 // Enroll the learner when not already enrolled.
	if ( ! lac_db_is_user_enrolled( $user_id, (int) $order_row->course_id ) ) {
		$insert_id = lac_db_insert_enrollment( $user_id, (int) $order_row->course_id );
		if ( false === $insert_id ) {
			return new WP_Error( 'enroll_failed', 'Payment captured but enrollment failed. Contact support.', array( 'status' => 500 ) );
		}
	}
	 // Log completion without personal details.
	lac_log_info( 'PayPal purchase completed for local order ' . absint( $order_row->id ) );
	 // Return encrypted course id only.
	return rest_ensure_response(
		lac_rest_enrollment_payload(
			(int) $order_row->course_id,
			'enrolled',
			'Payment successful. You are now enrolled.'
		)
	);
}

/**
 * POST /purchase — complete a paid course purchase in local/mock mode.
 *
 * @param WP_REST_Request $request Incoming REST request.
 * @return WP_REST_Response|WP_Error Result payload or error.
 */
function lac_rest_purchase_course( WP_REST_Request $request ) {
	 // Allow mock purchases without credentials, or when PAYPAL_MODE=mock.
	if ( lac_paypal_is_configured() && ! lac_paypal_is_mock_mode() ) {
		return new WP_Error(
			'use_paypal',
			'Use PayPal checkout for this site.',
			array( 'status' => 400 )
		);
	}
	 // Validate course id and ensure the course is paid.
	$validated = lac_validate_paypal_create_request( $request->get_json_params() );
	if ( is_wp_error( $validated ) ) {
		return $validated;
	}
	 // Capture the purchasing learner id.
	$user_id = get_current_user_id();
	 // Skip checkout when the learner is already enrolled.
	if ( lac_db_is_user_enrolled( $user_id, $validated['course_id'] ) ) {
		return rest_ensure_response(
			lac_rest_enrollment_payload(
				$validated['course_id'],
				'already_enrolled',
				'You are already enrolled in this course.'
			)
		);
	}
	 // Create a local order row for the purchase audit trail.
	$local_order_id = lac_db_insert_order(
		$user_id,
		$validated['course_id'],
		$validated['course_price'],
		lac_paypal_currency()
	);
	if ( false === $local_order_id ) {
		return new WP_Error( 'order_create_failed', 'Could not create a local order.', array( 'status' => 500 ) );
	}
	 // Attach a mock PayPal-style order id for traceability.
	$mock_paypal_id = 'MOCK-' . absint( $local_order_id ) . '-' . time();
	lac_db_set_order_paypal_id( $local_order_id, $mock_paypal_id );
	 // Mark the order completed with a mock capture id.
	lac_db_complete_order( $local_order_id, 'MOCK-CAPTURE-' . absint( $local_order_id ) );
	 // Enroll the learner after the mock payment succeeds.
	$insert_id = lac_db_insert_enrollment( $user_id, $validated['course_id'] );
	if ( false === $insert_id ) {
		return new WP_Error( 'enroll_failed', 'Purchase recorded but enrollment failed.', array( 'status' => 500 ) );
	}
	 // Log without personal learner details.
	lac_log_info( 'Mock purchase completed for local order ' . absint( $local_order_id ) );
	 // Return encrypted course id only.
	return rest_ensure_response(
		lac_rest_enrollment_payload(
			$validated['course_id'],
			'enrolled',
			'Purchase successful. You are now enrolled.'
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
