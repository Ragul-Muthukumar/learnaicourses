<?php
/**
 * PayPal Orders API helpers for Learn AI Courses purchases.
 *
 * What this file does:
 * - Reads PayPal credentials from environment constants.
 * - Creates and captures checkout orders against PayPal REST API v2.
 * Process:
 * 1) Resolve sandbox/live API host from PAYPAL_MODE.
 * 2) Fetch an OAuth access token with client id + secret.
 * 3) Create a PayPal order for a priced course.
 * 4) Capture an approved order and return the capture payload.
 */

 // Block direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return whether PayPal credentials are configured for checkout.
 *
 * @return bool True when client id and secret are present.
 */
function lac_paypal_is_configured() {
	 // Require both public client id and private client secret.
	return lac_paypal_client_id() !== '' && lac_paypal_client_secret() !== '';
}

/**
 * Return whether local mock checkout is enabled (no real PayPal call).
 *
 * @return bool True when PAYPAL_MODE is mock.
 */
function lac_paypal_is_mock_mode() {
	 // Mock mode lets local demos purchase without sandbox credentials.
	return 'mock' === lac_paypal_mode();
}

/**
 * Read the PayPal client id from a defined constant.
 *
 * @return string Client id or empty string when unset.
 */
function lac_paypal_client_id() {
	 // Prefer the environment-backed constant from .env / wp-config.
	if ( defined( 'PAYPAL_CLIENT_ID' ) && is_string( PAYPAL_CLIENT_ID ) ) {
		return trim( PAYPAL_CLIENT_ID );
	}
	 // Fall back to empty so callers can detect misconfiguration.
	return '';
}

/**
 * Read the PayPal client secret from a defined constant.
 *
 * @return string Client secret or empty string when unset.
 */
function lac_paypal_client_secret() {
	 // Prefer the environment-backed constant from .env / wp-config.
	if ( defined( 'PAYPAL_CLIENT_SECRET' ) && is_string( PAYPAL_CLIENT_SECRET ) ) {
		return trim( PAYPAL_CLIENT_SECRET );
	}
	 // Fall back to empty so callers can detect misconfiguration.
	return '';
}

/**
 * Read the PayPal operating mode (sandbox, live, or mock).
 *
 * @return string sandbox, live, or mock.
 */
function lac_paypal_mode() {
	 // Default safely to sandbox for remote PayPal testing.
	$mode = 'sandbox';
	 // Allow operators to flip mode via environment constant.
	if ( defined( 'PAYPAL_MODE' ) && is_string( PAYPAL_MODE ) ) {
		$mode = strtolower( trim( PAYPAL_MODE ) );
	}
	 // Accept mock for local demo purchases without credentials.
	if ( 'mock' === $mode ) {
		return 'mock';
	}
	 // Normalize unexpected values back to sandbox.
	return ( 'live' === $mode ) ? 'live' : 'sandbox';
}

/**
 * Read the checkout currency code (defaults to USD).
 *
 * @return string Uppercase ISO currency code.
 */
function lac_paypal_currency() {
	 // Default to USD for the seeded course catalog prices.
	$currency = 'USD';
	 // Allow operators to override via environment constant.
	if ( defined( 'PAYPAL_CURRENCY' ) && is_string( PAYPAL_CURRENCY ) ) {
		$currency = strtoupper( trim( PAYPAL_CURRENCY ) );
	}
	 // Reject empty overrides and keep USD.
	return $currency !== '' ? $currency : 'USD';
}

/**
 * Resolve the PayPal REST API host for the current mode.
 *
 * @return string API base URL without a trailing slash.
 */
function lac_paypal_api_base() {
	 // Use the live host only when explicitly configured.
	if ( 'live' === lac_paypal_mode() ) {
		return 'https://api-m.paypal.com';
	}
	 // Otherwise use the sandbox host for safe testing.
	return 'https://api-m.sandbox.paypal.com';
}

/**
 * Fetch a short-lived OAuth access token from PayPal.
 *
 * @return string|WP_Error Access token string or error object.
 */
function lac_paypal_get_access_token() {
	 // Abort early when credentials are missing.
	if ( ! lac_paypal_is_configured() ) {
		lac_log_error( 'PayPal access token requested without credentials.' );
		return new WP_Error( 'paypal_not_configured', 'PayPal is not configured.', array( 'status' => 503 ) );
	}
	 // Reuse a cached token while it remains valid.
	$cached_token = get_transient( 'lac_paypal_access_token' );
	if ( is_string( $cached_token ) && $cached_token !== '' ) {
		return $cached_token;
	}
	 // Build Basic auth from client id and secret.
	$auth_header = base64_encode( lac_paypal_client_id() . ':' . lac_paypal_client_secret() );
	 // Request a client-credentials token from PayPal.
	$response = wp_remote_post(
		lac_paypal_api_base() . '/v1/oauth2/token',
		array(
			'timeout' => 30,
			'headers' => array(
				'Authorization' => 'Basic ' . $auth_header,
				'Content-Type'  => 'application/x-www-form-urlencoded',
			),
			'body'    => array(
				'grant_type' => 'client_credentials',
			),
		)
	);
	 // Surface transport failures to the caller.
	if ( is_wp_error( $response ) ) {
		lac_log_error( 'PayPal token HTTP error: ' . $response->get_error_message() );
		return $response;
	}
	 // Decode the JSON body from PayPal.
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	 // Require a successful HTTP status and access_token field.
	$status_code = (int) wp_remote_retrieve_response_code( $response );
	if ( $status_code < 200 || $status_code >= 300 || empty( $body['access_token'] ) ) {
		lac_log_error( 'PayPal token response invalid with status ' . $status_code );
		return new WP_Error( 'paypal_token_failed', 'Could not authenticate with PayPal.', array( 'status' => 502 ) );
	}
	 // Cache slightly shorter than the reported expiry window.
	$expires_in = isset( $body['expires_in'] ) ? max( 60, absint( $body['expires_in'] ) - 60 ) : 300;
	set_transient( 'lac_paypal_access_token', $body['access_token'], $expires_in );
	 // Log success without printing the secret token value.
	lac_log_info( 'PayPal access token acquired for mode ' . lac_paypal_mode() );
	 // Return the bearer token for subsequent Orders API calls.
	return $body['access_token'];
}

/**
 * Perform an authenticated JSON request against the PayPal API.
 *
 * @param string $method HTTP method such as GET or POST.
 * @param string $path   API path beginning with a slash.
 * @param array  $payload Optional JSON body as an associative array.
 * @return array|WP_Error Decoded response body or error object.
 */
function lac_paypal_api_request( $method, $path, $payload = array() ) {
	 // Obtain a bearer token before calling the Orders API.
	$access_token = lac_paypal_get_access_token();
	if ( is_wp_error( $access_token ) ) {
		return $access_token;
	}
	 // Assemble remote request arguments shared by all methods.
	$args = array(
		'method'  => strtoupper( $method ),
		'timeout' => 45,
		'headers' => array(
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type'  => 'application/json',
		),
	);
	 // Attach a JSON body when the caller provided a payload.
	if ( ! empty( $payload ) ) {
		$args['body'] = wp_json_encode( $payload );
	}
	 // Execute the remote request against the mode-specific host.
	$response = wp_remote_request( lac_paypal_api_base() . $path, $args );
	 // Bubble WordPress HTTP client failures upward.
	if ( is_wp_error( $response ) ) {
		lac_log_error( 'PayPal API transport error on ' . $path . ': ' . $response->get_error_message() );
		return $response;
	}
	 // Decode the response body for callers.
	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = json_decode( wp_remote_retrieve_body( $response ), true );
	 // Treat non-2xx PayPal responses as errors with a safe message.
	if ( $status_code < 200 || $status_code >= 300 ) {
		$error_name = is_array( $body ) && isset( $body['name'] ) ? $body['name'] : 'unknown';
		lac_log_error( 'PayPal API ' . $path . ' failed with ' . $status_code . ' / ' . $error_name );
		return new WP_Error( 'paypal_api_failed', 'PayPal request failed.', array( 'status' => 502 ) );
	}
	 // Normalize a null body into an empty array for callers.
	return is_array( $body ) ? $body : array();
}

/**
 * Create a PayPal checkout order for a course purchase.
 *
 * @param int    $local_order_id Local lac_orders primary key.
 * @param string $course_title   Human-readable course title for the receipt.
 * @param float  $amount         Amount to charge in major units.
 * @param string $currency       ISO currency code.
 * @return array|WP_Error PayPal order payload or error object.
 */
function lac_paypal_create_order( $local_order_id, $course_title, $amount, $currency ) {
	 // Format money as a fixed two-decimal string required by PayPal.
	$amount_value = number_format( (float) $amount, 2, '.', '' );
	 // Build the Orders API create payload with capture intent.
	$payload = array(
		'intent'         => 'CAPTURE',
		'purchase_units' => array(
			array(
				'reference_id' => 'lac-order-' . absint( $local_order_id ),
				'description'  => wp_strip_all_tags( $course_title ),
				'custom_id'    => (string) absint( $local_order_id ),
				'amount'       => array(
					'currency_code' => strtoupper( $currency ),
					'value'         => $amount_value,
				),
			),
		),
		'application_context' => array(
			'brand_name'          => 'Learn AI Courses',
			'user_action'         => 'PAY_NOW',
			'shipping_preference' => 'NO_SHIPPING',
		),
	);
	 // POST the order definition to PayPal.
	$result = lac_paypal_api_request( 'POST', '/v2/checkout/orders', $payload );
	 // Bubble API failures unchanged.
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	 // Require a PayPal order id in the successful response.
	if ( empty( $result['id'] ) ) {
		lac_log_error( 'PayPal create order response missing id.' );
		return new WP_Error( 'paypal_create_failed', 'PayPal did not return an order id.', array( 'status' => 502 ) );
	}
	 // Log the remote order id without personal learner details.
	lac_log_info( 'PayPal order created: ' . sanitize_text_field( $result['id'] ) );
	 // Return the full PayPal payload for the REST layer.
	return $result;
}

/**
 * Capture funds for an approved PayPal order.
 *
 * @param string $paypal_order_id PayPal Orders API id.
 * @return array|WP_Error Capture payload or error object.
 */
function lac_paypal_capture_order( $paypal_order_id ) {
	 // POST an empty capture against the approved order id.
	$result = lac_paypal_api_request(
		'POST',
		'/v2/checkout/orders/' . rawurlencode( sanitize_text_field( $paypal_order_id ) ) . '/capture',
		array()
	);
	 // Bubble API failures unchanged.
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	 // Confirm the order reached COMPLETED status after capture.
	$status = isset( $result['status'] ) ? $result['status'] : '';
	if ( 'COMPLETED' !== $status ) {
		lac_log_error( 'PayPal capture returned unexpected status: ' . sanitize_text_field( $status ) );
		return new WP_Error( 'paypal_capture_incomplete', 'PayPal payment was not completed.', array( 'status' => 402 ) );
	}
	 // Log capture success using only the remote order id.
	lac_log_info( 'PayPal order captured: ' . sanitize_text_field( $paypal_order_id ) );
	 // Return the capture payload for enrollment finalization.
	return $result;
}

/**
 * Extract the first capture id from a PayPal capture response.
 *
 * @param array $capture_payload Decoded capture response body.
 * @return string Capture id or empty string when missing.
 */
function lac_paypal_extract_capture_id( $capture_payload ) {
	 // Guard against non-array payloads.
	if ( ! is_array( $capture_payload ) ) {
		return '';
	}
	 // Walk purchase units until a capture id is found.
	$units = isset( $capture_payload['purchase_units'] ) ? $capture_payload['purchase_units'] : array();
	foreach ( $units as $unit ) {
		 // Skip units that lack payments.captures.
		if ( empty( $unit['payments']['captures'][0]['id'] ) ) {
			continue;
		}
		 // Return the first capture transaction id.
		return sanitize_text_field( $unit['payments']['captures'][0]['id'] );
	}
	 // No capture id means the payload shape was unexpected.
	return '';
}

/**
 * Read the numeric course price meta for a course post.
 *
 * @param int $course_id Course post id.
 * @return float Non-negative price in major currency units.
 */
function lac_get_course_price( $course_id ) {
	 // Load the stored price meta and coerce to float.
	$raw_price = get_post_meta( absint( $course_id ), '_lac_course_price', true );
	 // Treat missing meta as free (zero).
	return max( 0, (float) $raw_price );
}
