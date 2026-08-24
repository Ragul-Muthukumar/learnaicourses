<?php
/**
 * Database access layer for Learn AI Courses LMS.
 *
 * What this file does:
 * - Owns every SQL statement used by the plugin.
 * Process:
 * 1) Create the enrollments table on activation.
 * 2) Insert, fetch, and check enrollment rows via named functions.
 */

 // Prevent direct script execution outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the full table name for learner enrollments.
 *
 * @return string Prefixed enrollments table name.
 */
function lac_db_enrollments_table() {
	 // Use the global wpdb instance for the configured table prefix.
	global $wpdb;
	 // Append a stable suffix so the table is unique to this plugin.
	return $wpdb->prefix . 'lac_enrollments';
}

/**
 * Create or upgrade the enrollments table schema.
 *
 * @return void
 */
function lac_db_create_enrollment_table() {
	 // Access wpdb for charset collation helpers.
	global $wpdb;
	 // Resolve the physical table name with prefix.
	$table_name = lac_db_enrollments_table();
	 // Capture the database charset/collation for dbDelta compatibility.
	$charset_collate = $wpdb->get_charset_collate();
	 // Define columns: learner, course, timestamps, and progress percent.
	$sql = "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		course_id bigint(20) unsigned NOT NULL,
		progress_percent tinyint(3) unsigned NOT NULL DEFAULT 0,
		enrolled_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY user_course (user_id, course_id),
		KEY course_id (course_id)
	) {$charset_collate};";
	 // Load WordPress schema upgrade helpers for dbDelta.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	 // Apply the schema in an idempotent way.
	dbDelta( $sql );
	 // Confirm schema creation for operators.
	lac_log_info( 'Enrollment table ensured: ' . $table_name );
}

/**
 * Insert a new enrollment row for a learner and course.
 *
 * @param int $user_id   WordPress user id.
 * @param int $course_id Course post id.
 * @return int|false Insert id on success, false on failure.
 */
function lac_db_insert_enrollment( $user_id, $course_id ) {
	 // Use the global database handle for inserts.
	global $wpdb;
	 // Perform a parameterized insert to avoid SQL injection.
	$inserted = $wpdb->insert(
		lac_db_enrollments_table(),
		array(
			'user_id'           => absint( $user_id ),
			'course_id'         => absint( $course_id ),
			'progress_percent'  => 0,
		),
		array( '%d', '%d', '%d' )
	);
	 // Log and return false when the insert fails.
	if ( false === $inserted ) {
		lac_log_error( 'Enrollment insert failed for user ' . absint( $user_id ) );
		return false;
	}
	 // Return the new primary key for callers.
	return (int) $wpdb->insert_id;
}

/**
 * Check whether a learner is already enrolled in a course.
 *
 * @param int $user_id   WordPress user id.
 * @param int $course_id Course post id.
 * @return bool True when an enrollment row exists.
 */
function lac_db_is_user_enrolled( $user_id, $course_id ) {
	 // Use the global database handle for the lookup.
	global $wpdb;
	 // Count matching rows with prepared placeholders.
	$row_count = (int) $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM ' . lac_db_enrollments_table() . ' WHERE user_id = %d AND course_id = %d',
			absint( $user_id ),
			absint( $course_id )
		)
	);
	 // Any positive count means the learner is enrolled.
	return $row_count > 0;
}

/**
 * Fetch enrollment rows for a single learner.
 *
 * @param int $user_id WordPress user id.
 * @return array List of enrollment objects.
 */
function lac_db_get_enrollments_for_user( $user_id ) {
	 // Use the global database handle for the select.
	global $wpdb;
	 // Return all enrollments for the learner ordered by newest first.
	$results = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT * FROM ' . lac_db_enrollments_table() . ' WHERE user_id = %d ORDER BY enrolled_at DESC',
			absint( $user_id )
		)
	);
	 // Normalize null results to an empty array for callers.
	return is_array( $results ) ? $results : array();
}

/**
 * Update progress percent for an enrollment row.
 *
 * @param int $user_id           WordPress user id.
 * @param int $course_id         Course post id.
 * @param int $progress_percent  Progress value from 0 to 100.
 * @return bool True when a row was updated.
 */
function lac_db_update_progress( $user_id, $course_id, $progress_percent ) {
	 // Use the global database handle for the update.
	global $wpdb;
	 // Clamp progress into the valid percentage range.
	$safe_progress = max( 0, min( 100, absint( $progress_percent ) ) );
	 // Update the matching enrollment with the new progress value.
	$updated = $wpdb->update(
		lac_db_enrollments_table(),
		array( 'progress_percent' => $safe_progress ),
		array(
			'user_id'   => absint( $user_id ),
			'course_id' => absint( $course_id ),
		),
		array( '%d' ),
		array( '%d', '%d' )
	);
	 // Treat a non-false return as success (including zero-change updates).
	return false !== $updated;
}

/**
 * Return the full table name for PayPal purchase orders.
 *
 * @return string Prefixed orders table name.
 */
function lac_db_orders_table() {
	 // Use the global wpdb instance for the configured table prefix.
	global $wpdb;
	 // Append a stable suffix unique to this plugin's order records.
	return $wpdb->prefix . 'lac_orders';
}

/**
 * Create or upgrade the PayPal orders table schema.
 *
 * @return void
 */
function lac_db_create_orders_table() {
	 // Access wpdb for charset collation helpers.
	global $wpdb;
	 // Resolve the physical table name with prefix.
	$table_name = lac_db_orders_table();
	 // Capture the database charset/collation for dbDelta compatibility.
	$charset_collate = $wpdb->get_charset_collate();
	 // Define columns for learner, course, amount, PayPal ids, and status.
	$sql = "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		course_id bigint(20) unsigned NOT NULL,
		amount decimal(10,2) NOT NULL DEFAULT 0.00,
		currency varchar(8) NOT NULL DEFAULT 'USD',
		status varchar(32) NOT NULL DEFAULT 'pending',
		paypal_order_id varchar(64) NOT NULL DEFAULT '',
		paypal_capture_id varchar(64) NOT NULL DEFAULT '',
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		KEY user_id (user_id),
		KEY course_id (course_id),
		KEY paypal_order_id (paypal_order_id),
		KEY status (status)
	) {$charset_collate};";
	 // Load WordPress schema upgrade helpers for dbDelta.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	 // Apply the schema in an idempotent way.
	dbDelta( $sql );
	 // Confirm schema creation for operators.
	lac_log_info( 'Orders table ensured: ' . $table_name );
}

/**
 * Insert a pending purchase order before PayPal approval.
 *
 * @param int    $user_id   WordPress user id.
 * @param int    $course_id Course post id.
 * @param float  $amount    Charged amount in major currency units.
 * @param string $currency  ISO currency code such as USD.
 * @return int|false Insert id on success, false on failure.
 */
function lac_db_insert_order( $user_id, $course_id, $amount, $currency ) {
	 // Use the global database handle for inserts.
	global $wpdb;
	 // Perform a parameterized insert for the pending order row.
	$inserted = $wpdb->insert(
		lac_db_orders_table(),
		array(
			'user_id'   => absint( $user_id ),
			'course_id' => absint( $course_id ),
			'amount'    => round( (float) $amount, 2 ),
			'currency'  => strtoupper( sanitize_text_field( $currency ) ),
			'status'    => 'pending',
		),
		array( '%d', '%d', '%f', '%s', '%s' )
	);
	 // Log and return false when the insert fails.
	if ( false === $inserted ) {
		lac_log_error( 'Order insert failed for course ' . absint( $course_id ) );
		return false;
	}
	 // Return the new primary key for PayPal custom_id linkage.
	return (int) $wpdb->insert_id;
}

/**
 * Attach the PayPal order id to a local pending order row.
 *
 * @param int    $order_id        Local order primary key.
 * @param string $paypal_order_id PayPal Orders API id.
 * @return bool True when a row was updated.
 */
function lac_db_set_order_paypal_id( $order_id, $paypal_order_id ) {
	 // Use the global database handle for the update.
	global $wpdb;
	 // Store the remote PayPal order id on the matching local row.
	$updated = $wpdb->update(
		lac_db_orders_table(),
		array( 'paypal_order_id' => sanitize_text_field( $paypal_order_id ) ),
		array( 'id' => absint( $order_id ) ),
		array( '%s' ),
		array( '%d' )
	);
	 // Treat a non-false return as success.
	return false !== $updated;
}

/**
 * Fetch one order by its PayPal order id.
 *
 * @param string $paypal_order_id PayPal Orders API id.
 * @return object|null Order row object or null when missing.
 */
function lac_db_get_order_by_paypal_id( $paypal_order_id ) {
	 // Use the global database handle for the lookup.
	global $wpdb;
	 // Load the newest matching order for the given PayPal id.
	$row = $wpdb->get_row(
		$wpdb->prepare(
			'SELECT * FROM ' . lac_db_orders_table() . ' WHERE paypal_order_id = %s ORDER BY id DESC LIMIT 1',
			sanitize_text_field( $paypal_order_id )
		)
	);
	 // Return the row object or null for callers.
	return $row ? $row : null;
}

/**
 * Fetch one order by local primary key.
 *
 * @param int $order_id Local order primary key.
 * @return object|null Order row object or null when missing.
 */
function lac_db_get_order_by_id( $order_id ) {
	 // Use the global database handle for the lookup.
	global $wpdb;
	 // Load the single order row by primary key.
	$row = $wpdb->get_row(
		$wpdb->prepare(
			'SELECT * FROM ' . lac_db_orders_table() . ' WHERE id = %d LIMIT 1',
			absint( $order_id )
		)
	);
	 // Return the row object or null for callers.
	return $row ? $row : null;
}

/**
 * Mark an order completed and store the PayPal capture id.
 *
 * @param int    $order_id          Local order primary key.
 * @param string $paypal_capture_id PayPal capture transaction id.
 * @return bool True when a row was updated.
 */
function lac_db_complete_order( $order_id, $paypal_capture_id ) {
	 // Use the global database handle for the update.
	global $wpdb;
	 // Flip status to completed and persist the capture reference.
	$updated = $wpdb->update(
		lac_db_orders_table(),
		array(
			'status'            => 'completed',
			'paypal_capture_id' => sanitize_text_field( $paypal_capture_id ),
		),
		array( 'id' => absint( $order_id ) ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	 // Treat a non-false return as success.
	return false !== $updated;
}

/**
 * Mark an order failed with an optional status label.
 *
 * @param int    $order_id Local order primary key.
 * @param string $status   Failure status such as failed or cancelled.
 * @return bool True when a row was updated.
 */
function lac_db_fail_order( $order_id, $status = 'failed' ) {
	 // Use the global database handle for the update.
	global $wpdb;
	 // Allow only known non-success statuses for safety.
	$safe_status = in_array( $status, array( 'failed', 'cancelled' ), true ) ? $status : 'failed';
	 // Persist the failure status on the matching order row.
	$updated = $wpdb->update(
		lac_db_orders_table(),
		array( 'status' => $safe_status ),
		array( 'id' => absint( $order_id ) ),
		array( '%s' ),
		array( '%d' )
	);
	 // Treat a non-false return as success.
	return false !== $updated;
}

/**
 * Check whether the learner already has a completed paid order for a course.
 *
 * @param int $user_id   WordPress user id.
 * @param int $course_id Course post id.
 * @return bool True when a completed order exists.
 */
function lac_db_user_has_paid_order( $user_id, $course_id ) {
	 // Use the global database handle for the lookup.
	global $wpdb;
	 // Count completed purchases linking this user and course.
	$row_count = (int) $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM ' . lac_db_orders_table() . ' WHERE user_id = %d AND course_id = %d AND status = %s',
			absint( $user_id ),
			absint( $course_id ),
			'completed'
		)
	);
	 // Any positive count means the course was already purchased.
	return $row_count > 0;
}
