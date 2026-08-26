<?php
/**
 * PayPal Orders API helpers for Learn AI Courses purchases.
 *
 * What this file does:
 * - Reads PayPal credentials from PHP constants, process env, or the site .env file.
 * - Creates and captures checkout orders against PayPal REST API v2.
 * Process:
 * 1) Resolve sandbox/live/mock mode from PAYPAL_MODE.
 * 2) Fetch an OAuth access token with client id + secret.
 * 3) Create a PayPal order for a priced course.
 * 4) Capture an approved order and return the capture payload.
 */

 // Block direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a PayPal setting from a PHP constant, the process env, or the site .env file.
 *
 * WordPress does not load .env by itself, so PAYPAL_MODE=mock in that file
 * was ignored until this helper parsed the site-root file.
 *
 * @param string $name Setting name such as PAYPAL_MODE.
 * @return string Trimmed value or empty string when unset.
 */
function lac_paypal_env( $name ) {
	// Prefer wp-config.php constants when they are defined and non-empty.
	if ( defined( $name ) ) {
		$constant_value = constant( $name );
		if ( is_string( $constant_value ) && '' !== trim( $constant_value ) ) {
			return trim( $constant_value );
		}
	}

	// Next, honor process / Apache environment variables.
	if ( function_exists( 'getenv' ) ) {
		$process_value = getenv( $name );
		if ( is_string( $process_value ) && '' !== trim( $process_value ) ) {
			return trim( $process_value );
		}
	}

	if ( isset( $_ENV[ $name ] ) && is_string( $_ENV[ $name ] ) && '' !== trim( $_ENV[ $name ] ) ) {
		return trim( $_ENV[ $name ] );
	}

	// Last, parse the site-root .env file (WordPress does not load it itself).
	$file_values = lac_paypal_env_file_values();
	if ( isset( $file_values[ $name ] ) ) {
		return $file_values[ $name ];
	}

	return '';
}

/**
 * Parse KEY=value lines from the site-root .env file once per request.
 *
 * @return array<string, string>
 */
function lac_paypal_env_file_values() {
	static $values = null;
	if ( is_array( $values ) ) {
		return $values;
	}

	$values = array();
	$paths  = array( ABSPATH . '.env' );
	// Also search WordPress root from the plugin path when ABSPATH differs.
	if ( defined( 'LAC_LMS_PATH' ) ) {
		$paths[] = dirname( LAC_LMS_PATH, 3 ) . '/.env';
	}

	$path = '';
	foreach ( array_unique( $paths ) as $candidate ) {
		if ( is_readable( $candidate ) ) {
			$path = $candidate;
			break;
		}
	}
	if ( '' === $path ) {
		return $values;
	}

	$lines = file( $path, FILE_IGNORE_NEW_LINES );
	if ( ! is_array( $lines ) ) {
		return $values;
	}

	foreach ( $lines as $line ) {
		$line = trim( (string) $line );
		if ( str_starts_with( $line, 'export ' ) ) {
			$line = trim( substr( $line, 7 ) );
		}
		if ( '' === $line || str_starts_with( $line, '#' ) || ! str_contains( $line, '=' ) ) {
			continue;
		}
		list( $key, $raw_value ) = explode( '=', $line, 2 );
		$key                     = trim( $key );
		if ( '' === $key ) {
			continue;
		}
		$values[ $key ] = trim( $raw_value, " \t\"'" );
	}

	return $values;
}

/**
 * Return whether a credential is a real value, not an .env.example placeholder.
 *
 * @param string $value Client id or secret.
 * @return bool True when the value can be sent to PayPal.
 */
function lac_paypal_is_usable_secret( $value ) {
	$value = strtolower( trim( $value ) );
	if ( '' === $value ) {
		return false;
	}

	// Reject .env.example placeholders such as your_sandbox_client_id.
	return ! str_starts_with( $value, 'your_' ) && 'changeme' !== $value && 'placeholder' !== $value;
}

/**
 * Return whether PayPal credentials are configured for checkout.
 *
 * @return bool True when a real client id and secret are present.
 */
function lac_paypal_is_configured() {
	return lac_paypal_is_usable_secret( lac_paypal_client_id() )
		&& lac_paypal_is_usable_secret( lac_paypal_client_secret() );
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
 * Read the PayPal client id from constants, env, or .env.
 *
 * @return string Client id or empty string when unset.
 */
function lac_paypal_client_id() {
	return lac_paypal_env( 'PAYPAL_CLIENT_ID' );
}

/**
 * Read the PayPal client secret from constants, env, or .env.
 *
 * @return string Client secret or empty string when unset.
 */
function lac_paypal_client_secret() {
	return lac_paypal_env( 'PAYPAL_CLIENT_SECRET' );
}

/**
 * Read the PayPal operating mode (sandbox, live, or mock).
 *
 * @return string sandbox, live, or mock.
 */
function lac_paypal_mode() {
	$mode = strtolower( lac_paypal_env( 'PAYPAL_MODE' ) );
	// Mock skips the PayPal SDK and uses the local purchase REST route.
	if ( 'mock' === $mode ) {
		return 'mock';
	}

	// Unknown values fall back to sandbox so live charges cannot happen by accident.
	return ( 'live' === $mode ) ? 'live' : 'sandbox';
}

/**
 * Read the checkout currency code (defaults to USD).
 *
 * @return string Uppercase ISO currency code.
 */
function lac_paypal_currency() {
	$currency = strtoupper( lac_paypal_env( 'PAYPAL_CURRENCY' ) );

	return '' !== $currency ? $currency : 'USD';
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
