<?php
/**
 * Attaches topic-related featured images to all course posts.
 *
 * What this file does:
 * - Downloads an AI-generated cover image per course title and sets it as the featured image.
 * Process:
 * 1) Query published lac_course posts missing _thumbnail_id.
 * 2) Build a visual prompt from the course title.
 * 3) Sideload the image into the Media Library.
 * 4) Assign the attachment as the post thumbnail.
 */

 // Block direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a Pollinations.ai image URL from a course title.
 *
 * @param string $course_title Published course post title.
 * @return string Remote JPEG URL for sideloading.
 */
function lac_build_course_image_url( $course_title ) {
	 // Compose a descriptive visual prompt tied to the course topic.
	$image_prompt = sprintf(
		'Professional online course cover banner about %s, artificial intelligence education, modern tech, clean design, no text',
		$course_title
	);
	 // Encode spaces and punctuation for the Pollinations path segment.
	$encoded_prompt = rawurlencode( $image_prompt );
	 // Return a fixed-size JPEG suitable for WordPress thumbnails.
	return 'https://image.pollinations.ai/prompt/' . $encoded_prompt . '?width=800&height=600&nologo=true';
}

/**
 * Sideload a remote image and attach it as the course featured image.
 *
 * @param int    $course_id    Course post id.
 * @param string $course_title Course title used for alt text and prompt.
 * @return int|WP_Error Attachment id on success.
 */
function lac_attach_course_featured_image( $course_id, $course_title ) {
	 // Load WordPress media helpers required for sideloading.
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	 // Resolve the remote image URL from the course title.
	$image_url = lac_build_course_image_url( $course_title );

	 // Download the remote JPEG to a temporary file first.
	$tmp_file = download_url( $image_url, 120 );

	 // Return early when the download fails.
	if ( is_wp_error( $tmp_file ) ) {
		lac_log_error(
			'Image download failed for course ' . absint( $course_id ) . ': ' . $tmp_file->get_error_message()
		);
		return $tmp_file;
	}

	 // Build the sideload array with a safe filename derived from the title.
	$file_array = array(
		'name'     => sanitize_file_name( $course_title ) . '.jpg',
		'tmp_name' => $tmp_file,
	);

	 // Import the temp file into the Media Library.
	$attachment_id = media_handle_sideload( $file_array, $course_id, $course_title );

	 // Remove the temp file when sideload fails.
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp_file );
		lac_log_error(
			'Image sideload failed for course ' . absint( $course_id ) . ': ' . $attachment_id->get_error_message()
		);
		return $attachment_id;
	}

	 // Assign the new attachment as the featured image.
	set_post_thumbnail( $course_id, (int) $attachment_id );
	lac_log_info( 'Featured image set for course ' . absint( $course_id ) );

	return (int) $attachment_id;
}

/**
 * Attach featured images to every course post that lacks one.
 *
 * @param int $batch_limit Optional max posts to process in one run (0 = all).
 * @return array{success:int, failed:int, skipped:int} Run summary counts.
 */
function lac_seed_course_featured_images( $batch_limit = 0 ) {
	 // Allow long runs when many images are downloaded sequentially.
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 0 );
	}

	 // Fetch published courses ordered by oldest first.
	$query_args = array(
		'post_type'      => 'lac_course',
		'post_status'    => 'publish',
		'posts_per_page' => $batch_limit > 0 ? $batch_limit : -1,
		'orderby'        => 'ID',
		'order'          => 'ASC',
		'fields'         => 'ids',
	);

	 // Query only courses without a featured image when supported.
	$query_args['meta_query'] = array(
		array(
			'key'     => '_thumbnail_id',
			'compare' => 'NOT EXISTS',
		),
	);

	$course_ids = get_posts( $query_args );

	 // Track run statistics for CLI output.
	$success_count = 0;
	$failed_count  = 0;
	$skipped_count = 0;

	 // Process each course post individually to avoid memory spikes.
	foreach ( $course_ids as $course_id ) {
		// Skip posts that gained a thumbnail since the query ran.
		if ( has_post_thumbnail( $course_id ) ) {
			$skipped_count++;
			continue;
		}

		// Read the title that drives the image prompt.
		$course_title = get_the_title( $course_id );

		// Attempt sideload + thumbnail assignment.
		$result = lac_attach_course_featured_image( $course_id, $course_title );

		if ( is_wp_error( $result ) ) {
			$failed_count++;
			continue;
		}

		$success_count++;

		// Brief pause to reduce rate-limit errors from the image provider.
		usleep( 500000 );
	}

	// Log aggregate results for operators.
	lac_log_info(
		sprintf(
			'Course image seed complete: %d success, %d failed, %d skipped.',
			$success_count,
			$failed_count,
			$skipped_count
		)
	);

	// Return counts for WP-CLI callers.
	return array(
		'success' => $success_count,
		'failed'  => $failed_count,
		'skipped' => $skipped_count,
	);
}
