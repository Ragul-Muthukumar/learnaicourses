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
