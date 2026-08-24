<?php
/**
 * Attaches professional stock photography as course featured images.
 *
 * What this file does:
 * - Maps each course to a real photograph (office, classroom, workshop).
 * - Sideloads the JPEG into the Media Library and sets it as the thumbnail.
 * Process:
 * 1) Pick a unique Unsplash photo from a topic pool.
 * 2) Download and import it.
 * 3) Replace any previous featured image on that course.
 */

 // Block direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated Unsplash photographs. These are real photos, not generated art.
 *
 * @return array<string, string[]> Topic slug => list of crop URLs.
 */
function lac_course_stock_photo_library() {
	$crop = '?auto=format&fit=crop&w=1600&h=1000&q=80';

	return array(
		'classroom'  => array(
			'https://images.unsplash.com/photo-1524178232363-1fb2b075b655' . $crop,
			'https://images.unsplash.com/photo-1509062522246-3755977927d7' . $crop,
			'https://images.unsplash.com/photo-1523240795612-9a054b0db644' . $crop,
			'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45' . $crop,
			'https://images.unsplash.com/photo-1531482615713-2afd69097998' . $crop,
			'https://images.unsplash.com/photo-1543269865-cbf77c2c1dfb' . $crop,
			'https://images.unsplash.com/photo-1577896851231-70ef18881754' . $crop,
		),
		'coding'     => array(
			'https://images.unsplash.com/photo-1498050108023-c5249f4df085' . $crop,
			'https://images.unsplash.com/photo-1461749280684-dccba630e2f6' . $crop,
			'https://images.unsplash.com/photo-1517694712202-14dd9538aa97' . $crop,
			'https://images.unsplash.com/photo-1555066931-4365d14bab8c' . $crop,
			'https://images.unsplash.com/photo-1587620962725-abab7fe55159' . $crop,
			'https://images.unsplash.com/photo-1593720213428-28a5b9e94613' . $crop,
			'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b' . $crop,
			'https://images.unsplash.com/photo-1516321318423-f06f85e504b3' . $crop,
			'https://images.unsplash.com/photo-1580894732444-8ecded7900cd' . $crop,
		),
		'writing'    => array(
			'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40' . $crop,
			'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d' . $crop,
			'https://images.unsplash.com/photo-1434030216411-0b793f4b4173' . $crop,
			'https://images.unsplash.com/photo-1455390582262-044cdead277a' . $crop,
			'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e' . $crop,
			'https://images.unsplash.com/photo-1516387938699-a93567ec168e' . $crop,
			'https://images.unsplash.com/photo-1499750310107-5fefaa4fd47d' . $crop,
			'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2' . $crop,
			'https://images.unsplash.com/photo-1471107340929-a87cd0f5b5f3' . $crop,
		),
		'meeting'    => array(
			'https://images.unsplash.com/photo-1522202176988-66273c2fd55f' . $crop,
			'https://images.unsplash.com/photo-1542744173-8e7e53415bb0' . $crop,
			'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4' . $crop,
			'https://images.unsplash.com/photo-1552664730-d307ca884978' . $crop,
			'https://images.unsplash.com/photo-1551836022-d5d88e9218df' . $crop,
			'https://images.unsplash.com/photo-1519389950473-47ba0277781c' . $crop,
			'https://images.unsplash.com/photo-1600880292203-757bb62b4baf' . $crop,
			'https://images.unsplash.com/photo-1522071820081-009f0129c71c' . $crop,
			'https://images.unsplash.com/photo-1517048676732-d65bc937f952' . $crop,
			'https://images.unsplash.com/photo-1553877522-43269d4ea984' . $crop,
			'https://images.unsplash.com/photo-1573164713714-d95e436ab8d6' . $crop,
			'https://images.unsplash.com/photo-1557804506-669a67965ba0' . $crop,
		),
		'analytics'  => array(
			'https://images.unsplash.com/photo-1551288049-bebda4e38f71' . $crop,
			'https://images.unsplash.com/photo-1460925895917-afdab827c52f' . $crop,
			'https://images.unsplash.com/photo-1554224155-6726b3ff858f' . $crop,
			'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3' . $crop,
			'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e' . $crop,
		),
		'healthcare' => array(
			'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d' . $crop,
			'https://images.unsplash.com/photo-1551076805-e1869033e154' . $crop,
			'https://images.unsplash.com/photo-1631217868264-e5b90bb7e133' . $crop,
			'https://images.unsplash.com/photo-1576091160550-2173dba999ef' . $crop,
		),
		'legal'      => array(
			'https://images.unsplash.com/photo-1589829545856-d10d557cf95f' . $crop,
			'https://images.unsplash.com/photo-1528747045269-390fe33c19f2' . $crop,
			'https://images.unsplash.com/photo-1450101499163-c8848c66ca85' . $crop,
		),
		'property'   => array(
			'https://images.unsplash.com/photo-1560518883-ce09059eeffa' . $crop,
			'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' . $crop,
			'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9' . $crop,
		),
		'support'    => array(
			'https://images.unsplash.com/photo-1560264280-88b68371db39' . $crop,
			'https://images.unsplash.com/photo-1474631245212-32dc3c8310c6' . $crop,
			'https://images.unsplash.com/photo-1521791136064-7986c2920216' . $crop,
		),
		'studio'     => array(
			'https://images.unsplash.com/photo-1485846234645-a62644f84728' . $crop,
			'https://images.unsplash.com/photo-1516035069371-29a1b244cc32' . $crop,
			'https://images.unsplash.com/photo-1590602847861-f357a9332bbc' . $crop,
			'https://images.unsplash.com/photo-1478737270239-2f02b77fc618' . $crop,
			'https://images.unsplash.com/photo-1516321497487-e288fb19713f' . $crop,
		),
		'office'     => array(
			'https://images.unsplash.com/photo-1497366216548-37526070297c' . $crop,
			'https://images.unsplash.com/photo-1497366811353-6870744d04b2' . $crop,
			'https://images.unsplash.com/photo-1531973576160-7125cd663d86' . $crop,
			'https://images.unsplash.com/photo-1556761175-4b46a572b786' . $crop,
			'https://images.unsplash.com/photo-1556761175-5973dc0f32e7' . $crop,
			'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2' . $crop,
			'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e' . $crop,
			'https://images.unsplash.com/photo-1560250097-0b93528c311a' . $crop,
			'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7' . $crop,
			'https://images.unsplash.com/photo-1664575602554-2087b04935a5' . $crop,
			'https://images.unsplash.com/photo-1588196749597-9ff147d21c0b' . $crop,
			'https://images.unsplash.com/photo-1505373877841-8d25f7d46680' . $crop,
		),
	);
}

/**
 * Choose a photo topic from the course title.
 *
 * @param string $course_title Course post title.
 * @return string Library key.
 */
function lac_course_image_topic( $course_title ) {
	$title = strtolower( $course_title );

	if ( preg_match( '/health|medical|hospital/', $title ) ) {
		return 'healthcare';
	}
	if ( preg_match( '/legal|compliance|ethics/', $title ) ) {
		return 'legal';
	}
	if ( preg_match( '/real estate|listing/', $title ) ) {
		return 'property';
	}
	if ( preg_match( '/support|faq/', $title ) ) {
		return 'support';
	}
	if ( preg_match( '/video|voice|speech|midjourney|image prompt|multimodal|camera/', $title ) ) {
		return 'studio';
	}
	if ( preg_match( '/forecast|analytics|dashboard|cost|spreadsheet|financ/', $title ) ) {
		return 'analytics';
	}
	if ( preg_match( '/code|api|saas|deploy|rag|embed|llm|chatbot|computer vision|production/', $title ) ) {
		return 'coding';
	}
	if ( preg_match( '/student|tutor|school|research|education|classroom/', $title ) ) {
		return 'classroom';
	}
	if ( preg_match( '/prompt|email|copy|writing|chatgpt|caption|document|translation/', $title ) ) {
		return 'writing';
	}
	if ( preg_match( '/agent|automat|enterprise|strategy|workflow|hr|sales|marketing|custom gpt/', $title ) ) {
		return 'meeting';
	}

	return 'office';
}

/**
 * Send a browser-like User-Agent so Unsplash serves the JPEG.
 *
 * @param array  $args Request arguments.
 * @param string $url  Request URL.
 * @return array
 */
function lac_course_image_http_args( $args, $url ) {
	if ( false !== strpos( $url, 'images.unsplash.com' ) ) {
		$args['user-agent'] = 'LearnAICourses/1.0 (local course cover import)';
		$args['timeout']    = 60;
	}
	return $args;
}

/**
 * Sideload one stock photo and set it as the course thumbnail.
 *
 * @param int    $course_id Course post id.
 * @param string $image_url Remote JPEG URL.
 * @param string $alt_text  Attachment alt/title.
 * @return int|WP_Error Attachment id on success.
 */
function lac_attach_stock_course_image( $course_id, $image_url, $alt_text ) {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	add_filter( 'http_request_args', 'lac_course_image_http_args', 10, 2 );
	$tmp_file = download_url( $image_url, 60 );
	remove_filter( 'http_request_args', 'lac_course_image_http_args', 10 );

	if ( is_wp_error( $tmp_file ) ) {
		lac_log_error( 'Stock photo download failed for course ' . absint( $course_id ) . ': ' . $tmp_file->get_error_message() );
		return $tmp_file;
	}

	$file_array = array(
		'name'     => 'course-' . absint( $course_id ) . '-cover.jpg',
		'tmp_name' => $tmp_file,
	);

	$attachment_id = media_handle_sideload( $file_array, $course_id, $alt_text );
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp_file );
		lac_log_error( 'Stock photo sideload failed for course ' . absint( $course_id ) . ': ' . $attachment_id->get_error_message() );
		return $attachment_id;
	}

	update_post_meta( (int) $attachment_id, '_wp_attachment_image_alt', $alt_text );
	set_post_thumbnail( $course_id, (int) $attachment_id );

	return (int) $attachment_id;
}

/**
 * Remove the current featured image attachment for a course.
 *
 * @param int $course_id Course post id.
 * @return void
 */
function lac_delete_course_featured_image( $course_id ) {
	$old_id = (int) get_post_thumbnail_id( $course_id );
	if ( $old_id < 1 ) {
		return;
	}

	delete_post_thumbnail( $course_id );
	wp_delete_attachment( $old_id, true );
}

/**
 * Replace every published course cover with a unique stock photograph.
 *
 * @param bool $replace_existing When true, overwrite current thumbnails.
 * @return array{success:int, failed:int, skipped:int} Counts.
 */
function lac_replace_course_featured_images( $replace_existing = true ) {
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 0 );
	}

	$library = lac_course_stock_photo_library();
	$used    = array();
	$cursors = array();

	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);

	$success_count = 0;
	$failed_count  = 0;
	$skipped_count = 0;

	foreach ( $course_ids as $course_id ) {
		if ( ! $replace_existing && has_post_thumbnail( $course_id ) ) {
			$skipped_count++;
			continue;
		}

		$title = get_the_title( $course_id );
		$topic = lac_course_image_topic( $title );
		$pools = array( $topic, 'office', 'meeting', 'writing', 'coding' );
		$url   = '';

		foreach ( $pools as $pool_key ) {
			if ( empty( $library[ $pool_key ] ) ) {
				continue;
			}
			if ( ! isset( $cursors[ $pool_key ] ) ) {
				$cursors[ $pool_key ] = 0;
			}
			$photos = $library[ $pool_key ];
			$count  = count( $photos );
			for ( $i = 0; $i < $count; $i++ ) {
				$candidate = $photos[ ( $cursors[ $pool_key ] + $i ) % $count ];
				if ( in_array( $candidate, $used, true ) ) {
					continue;
				}
				$url                   = $candidate;
				$cursors[ $pool_key ] = ( $cursors[ $pool_key ] + $i + 1 ) % $count;
				break;
			}
			if ( $url ) {
				break;
			}
		}

		if ( ! $url ) {
			$failed_count++;
			continue;
		}

		$used[] = $url;

		if ( $replace_existing ) {
			lac_delete_course_featured_image( $course_id );
		}

		$result = lac_attach_stock_course_image( $course_id, $url, $title );
		if ( is_wp_error( $result ) ) {
			$failed_count++;
			continue;
		}

		$success_count++;
		usleep( 250000 );
	}

	lac_log_info(
		sprintf(
			'Course stock photos complete: %d success, %d failed, %d skipped.',
			$success_count,
			$failed_count,
			$skipped_count
		)
	);

	return array(
		'success' => $success_count,
		'failed'  => $failed_count,
		'skipped' => $skipped_count,
	);
}

/**
 * Backward-compatible alias used by older seed callers.
 *
 * @param int $batch_limit Unused; all courses are processed.
 * @return array{success:int, failed:int, skipped:int}
 */
function lac_seed_course_featured_images( $batch_limit = 0 ) {
	unset( $batch_limit );
	return lac_replace_course_featured_images( false );
}

/**
 * Repair featured images when Media Library paths no longer match files on disk.
 *
 * What this does:
 * - Looks for course-{id}-cover.jpg under uploads/2026/08/.
 * - Updates the featured attachment path and regenerates image sizes.
 * Process:
 * 1) Skip when already repaired (option flag).
 * 2) For each published course, remap the thumbnail to the cover file if present.
 *
 * @return void
 */
function lac_repair_course_cover_attachments_if_needed() {
	 // Run this repair only once unless forced by deleting the option.
	if ( get_option( 'lac_course_covers_repaired_v1' ) ) {
		return;
	}
	 // Need image helpers to rebuild attachment metadata.
	require_once ABSPATH . 'wp-admin/includes/image.php';
	 // Resolve the uploads directory used for course covers.
	$upload_dir = wp_upload_dir();
	$rel_dir    = '2026/08';
	$abs_dir    = trailingslashit( $upload_dir['basedir'] ) . $rel_dir . '/';
	 // Load every published course id.
	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	 // Track how many courses were remapped.
	$fixed_count = 0;
	foreach ( $course_ids as $course_id ) {
		 // Build the expected cover filename for this course.
		$cover_name = 'course-' . absint( $course_id ) . '-cover.jpg';
		$cover_abs  = $abs_dir . $cover_name;
		$cover_rel  = $rel_dir . '/' . $cover_name;
		 // Skip when the cover file is not on disk.
		if ( ! file_exists( $cover_abs ) ) {
			continue;
		}
		 // Read the current featured attachment id.
		$attachment_id = (int) get_post_thumbnail_id( $course_id );
		 // Skip when the current attachment already points at the cover file.
		if ( $attachment_id > 0 ) {
			$current_file = (string) get_attached_file( $attachment_id );
			if ( $current_file && file_exists( $current_file ) && basename( $current_file ) === $cover_name ) {
				continue;
			}
		}
		 // Create an attachment when the course has no featured image.
		if ( $attachment_id < 1 ) {
			$attachment_id = wp_insert_attachment(
				array(
					'post_mime_type' => 'image/jpeg',
					'post_title'     => get_the_title( $course_id ) . ' cover',
					'post_content'   => '',
					'post_status'    => 'inherit',
					'post_parent'    => $course_id,
				),
				$cover_abs,
				$course_id,
				true
			);
			if ( is_wp_error( $attachment_id ) ) {
				lac_log_error( 'Cover attachment insert failed for course ' . absint( $course_id ) );
				continue;
			}
		} else {
			 // Point the existing attachment at the real cover file.
			update_attached_file( $attachment_id, $cover_abs );
			update_post_meta( $attachment_id, '_wp_attached_file', $cover_rel );
		}
		 // Rebuild WordPress image sizes from the cover JPEG.
		$metadata = wp_generate_attachment_metadata( $attachment_id, $cover_abs );
		if ( ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
		 // Ensure the course uses this attachment as its featured image.
		set_post_thumbnail( $course_id, $attachment_id );
		$fixed_count++;
	}
	 // Persist the one-time repair flag.
	update_option( 'lac_course_covers_repaired_v1', 1, true );
	 // Log the number of remapped covers for operators.
	lac_log_info( 'Repaired course cover attachments: ' . absint( $fixed_count ) );
}

 // Auto-repair broken cover paths after plugins load.
add_action( 'init', 'lac_repair_course_cover_attachments_if_needed', 40 );
