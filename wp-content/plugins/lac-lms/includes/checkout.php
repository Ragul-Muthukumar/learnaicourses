<?php
/**
 * Checkout page helpers and shortcode for Learn AI Courses LMS.
 *
 * What this file does:
 * - Ensures a WordPress checkout page exists with the [lac_checkout] shortcode.
 * - Builds checkout URLs from course slugs for enroll / buy CTAs.
 * - Renders the checkout summary and payment / enrollment actions.
 * Process:
 * 1) Create or resolve the checkout page on plugin load.
 * 2) Read ?course=slug on the checkout page and resolve the course id.
 * 3) Output course summary plus lac_render_checkout_actions() for completion.
 */

 // Block direct access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the stored checkout page id option value.
 *
 * @return int WordPress page id or 0 when unset.
 */
function lac_get_checkout_page_id() {
	 // Read the persisted checkout page id from options.
	return absint( get_option( 'lac_checkout_page_id', 0 ) );
}

/**
 * Read the Bingeme deposit cookie set by bgcheckout.
 *
 * @return array<string,mixed>
 */
function lac_get_bingeme_session() {
	if ( function_exists( 'bg_get_cart_session' ) ) {
		return bg_get_cart_session();
	}
	if ( empty( $_COOKIE['bg_cart_data'] ) ) {
		return array();
	}
	$data = maybe_unserialize( wp_unslash( $_COOKIE['bg_cart_data'] ) );
	return is_array( $data ) ? $data : array();
}

/**
 * Whether the current checkout is a Bingeme deposit payment via LMS UI.
 * Requires the bg_cart_data cookie set by /bgcart/?txn_id=...
 *
 * @return bool
 */
function lac_is_bingeme_checkout() {
	if ( empty( $_COOKIE['bg_cart_data'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return false;
	}
	if ( function_exists( 'bg_is_lms_checkout_session' ) && bg_is_lms_checkout_session() ) {
		return true;
	}
	$session = lac_get_bingeme_session();
	return ! empty( $session['lms'] )
		&& ! empty( $session['id'] )
		&& isset( $session['amount'] )
		&& (float) $session['amount'] > 0
		&& ! empty( $session['course_id'] );
}

/**
 * Amount to charge: Bingeme txn amount when present, otherwise course catalog price.
 *
 * @param int $course_id Course post id.
 * @return float
 */
function lac_get_effective_checkout_price( $course_id ) {
	if ( lac_is_bingeme_checkout() ) {
		$session = lac_get_bingeme_session();
		return max( 0, (float) $session['amount'] );
	}
	return function_exists( 'lac_get_course_price' ) ? lac_get_course_price( $course_id ) : 0;
}

/**
 * Post-payment return URL for Bingeme deposits.
 *
 * @return string
 */
function lac_get_bingeme_return_url() {
	$session = lac_get_bingeme_session();
	if ( ! empty( $session['url'] ) && is_string( $session['url'] ) ) {
		return $session['url'];
	}
	return home_url( '/' );
}

/**
 * Mark the Bingeme deposit active after successful LMS PayPal payment,
 * and create a WooCommerce order (with PayPal transaction id) for reconciliation.
 *
 * @param string $paypal_txn_id Optional PayPal capture / transaction id.
 * @param int    $course_id     Course used on checkout.
 * @param float  $amount        Charged amount.
 * @return array{ok:bool,wc_order_id:int}
 */
function lac_complete_bingeme_deposit( $paypal_txn_id = '', $course_id = 0, $amount = 0 ) {
	if ( ! lac_is_bingeme_checkout() ) {
		return array(
			'ok'          => false,
			'wc_order_id' => 0,
		);
	}

	$session   = lac_get_bingeme_session();
	$user_id   = get_current_user_id();
	$amount    = $amount > 0 ? (float) $amount : (float) ( $session['amount'] ?? 0 );
	$course_id = $course_id > 0 ? absint( $course_id ) : absint( $session['course_id'] ?? 0 );

	$wc_order_id = 0;
	if ( function_exists( 'bg_create_woocommerce_order_from_lms' ) ) {
		$wc_order_id = (int) bg_create_woocommerce_order_from_lms(
			array(
				'amount'        => $amount,
				'course_id'     => $course_id,
				'user_id'       => $user_id,
				'paypal_txn_id' => $paypal_txn_id,
				'deposit_id'    => $session['id'] ?? '',
				'txn_id'        => $session['txn_id'] ?? '',
			)
		);
	}

	if ( function_exists( 'update_bg_deposit' ) ) {
		$data = array(
			'id' => $session['id'],
		);
		if ( '' !== $paypal_txn_id ) {
			$data['paypal_txn_id'] = sanitize_text_field( $paypal_txn_id );
		}
		update_bg_deposit( 'active', $data );
	}

	return array(
		'ok'          => true,
		'wc_order_id' => $wc_order_id,
	);
}

/**
 * Create the checkout page once when it is missing or trashed.
 *
 * @return int Checkout page id or 0 on failure.
 */
function lac_ensure_checkout_page() {
	 // Reuse an existing valid checkout page when the option still points to it.
	$page_id = lac_get_checkout_page_id();
	if ( $page_id > 0 && 'publish' === get_post_status( $page_id ) ) {
		return $page_id;
	}
	 // Look up a previously created page by slug before inserting another copy.
	$existing_pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'name'           => 'checkout',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! empty( $existing_pages ) ) {
		$page_id = (int) $existing_pages[0];
		update_option( 'lac_checkout_page_id', $page_id );
		lac_log_info( 'Reused existing checkout page id ' . $page_id );
		return $page_id;
	}
	 // Insert a new checkout page containing the LMS checkout shortcode.
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Checkout',
			'post_name'    => 'checkout',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[lac_checkout]',
		),
		true
	);
	 // Surface insert failures to the operator log.
	if ( is_wp_error( $page_id ) ) {
		lac_log_error( 'Could not create checkout page: ' . $page_id->get_error_message() );
		return 0;
	}
	 // Persist the new page id for later URL building.
	update_option( 'lac_checkout_page_id', (int) $page_id );
	lac_log_info( 'Created checkout page id ' . (int) $page_id );
	return (int) $page_id;
}

/**
 * Build the checkout URL for a course using its post slug.
 *
 * @param int $course_id Internal course post id.
 * @return string Checkout URL or empty string when the page is unavailable.
 */
function lac_get_checkout_url_for_course( $course_id ) {
	 // Resolve the checkout page id, creating the page when needed.
	$page_id = lac_get_checkout_page_id();
	if ( $page_id < 1 ) {
		$page_id = lac_ensure_checkout_page();
	}
	 // Abort when no checkout page could be created.
	if ( $page_id < 1 ) {
		return '';
	}
	 // Read the course slug for a stable query argument.
	$course_slug = get_post_field( 'post_name', absint( $course_id ) );
	if ( ! is_string( $course_slug ) || '' === $course_slug ) {
		return '';
	}
	 // Append the course slug to the checkout permalink.
	return add_query_arg( 'course', rawurlencode( $course_slug ), get_permalink( $page_id ) );
}

/**
 * Resolve a published course id from a URL slug parameter.
 *
 * @param string $course_slug Sanitized post_name value.
 * @return int Course post id or 0 when not found.
 */
function lac_resolve_course_id_from_slug( $course_slug ) {
	 // Normalize the slug the same way WordPress stores post_name.
	$course_slug = sanitize_title( $course_slug );
	if ( '' === $course_slug ) {
		return 0;
	}
	 // Query by slug because bcrypt ids are unsuitable for URLs.
	$course_ids = get_posts(
		array(
			'post_type'      => 'lac_course',
			'post_status'    => 'publish',
			'name'           => $course_slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	 // Return the first match or zero when nothing published uses that slug.
	return ! empty( $course_ids ) ? (int) $course_ids[0] : 0;
}

/**
 * Return the best continue-learning URL for an enrolled learner.
 *
 * @param int $course_id Course post id.
 * @return string Permalink to the first lesson or the course page.
 */
function lac_get_continue_learning_url( $course_id ) {
	$course_id = absint( $course_id );
	if ( $course_id < 1 ) {
		return '';
	}

	// Create a default curriculum when the course was published without lessons.
	$lesson_posts = function_exists( 'lac_ensure_default_lessons_for_course' )
		? lac_ensure_default_lessons_for_course( $course_id )
		: ( function_exists( 'lac_get_lessons_for_course' ) ? lac_get_lessons_for_course( $course_id ) : array() );

	if ( ! empty( $lesson_posts ) ) {
		$lesson_url = get_permalink( $lesson_posts[0] );
		if ( is_string( $lesson_url ) && '' !== $lesson_url ) {
			return $lesson_url;
		}
	}

	// Last resort only: the course permalink (should be unused once lessons exist).
	return (string) get_permalink( $course_id );
}

/**
 * Render checkout actions: login prompt, enroll, mock purchase, or PayPal.
 *
 * @param int $course_id Course post id.
 * @return string HTML for the checkout action panel.
 */
function lac_render_checkout_actions( $course_id ) {
	 // Encrypt the course id for REST requests from the checkout page.
	$encrypted_course_id = lac_encrypt_id( $course_id );
	 // Bingeme deposits reuse this UI but charge the txn amount, not catalog price.
	$is_bg_checkout = lac_is_bingeme_checkout();
	$course_price   = lac_get_effective_checkout_price( $course_id );
	 // Start output buffering for the action panel markup.
	ob_start();
	?>
	<div class="lac-checkout__actions" data-lac-checkout-actions="1" data-continue_url="<?php echo esc_attr( $is_bg_checkout ? lac_get_bingeme_return_url() : lac_get_continue_learning_url( $course_id ) ); ?>">
		<?php if ( ! is_user_logged_in() ) : ?>
			<a class="lac-enroll-button lac-enroll-link is-login" href="<?php echo esc_url( wp_login_url( lac_get_checkout_url_for_course( $course_id ) ) ); ?>"><span class="lac-enroll-button__label"><?php echo esc_html( 'Sign in to continue' ); ?></span></a>
			<p class="lac-enroll-hint">
				<?php echo esc_html( 'You need an account before enrolling or purchasing a course.' ); ?>
			</p>
		<?php elseif ( ! $is_bg_checkout && lac_db_is_user_enrolled( get_current_user_id(), $course_id ) ) : ?>
			<a class="lac-enroll-button lac-enroll-link is-enrolled" href="<?php echo esc_url( lac_get_continue_learning_url( $course_id ) ); ?>"><span class="lac-enroll-button__label"><?php echo esc_html( 'Continue learning' ); ?></span></a>
			<p class="lac-enroll-message">
				<?php echo esc_html( 'You are already enrolled in this course.' ); ?>
			</p>
		<?php elseif ( $course_price > 0 ) : ?>
			<?php
			$price_text = number_format( $course_price, 2 );
			?>
			<p class="lac-checkout__digital-badge"><?php echo esc_html( 'Digital purchase' ); ?></p>
			<p class="lac-checkout__digital-note">
				<?php echo esc_html( 'This is an online digital course. Access is delivered immediately after payment. All digital sales are final — no refunds.' ); ?>
				<?php if ( ! $is_bg_checkout ) : ?>
					<a href="<?php echo esc_url( home_url( '/refund-policy/' ) ); ?>"><?php echo esc_html( 'Refund Policy' ); ?></a>
				<?php endif; ?>
			</p>
			<?php
			if ( lac_paypal_is_configured() && ! lac_paypal_is_mock_mode() ) :
				?>
				<div
					class="lac-paypal-wrap"
					data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>"
					data-course_price="<?php echo esc_attr( number_format( $course_price, 2, '.', '' ) ); ?>"
				>
					<p class="lac-paypal-price"><?php echo esc_html( sprintf( 'Digital course total: $%s', $price_text ) ); ?></p>
					<div class="lac-paypal-button-container"></div>
					<p class="lac-enroll-message" hidden></p>
				</div>
			<?php elseif ( lac_paypal_allows_instant_purchase() ) : ?>
				<button type="button" class="lac-enroll-button is-purchase" data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>" data-action="purchase" data-course_price="<?php echo esc_attr( number_format( $course_price, 2, '.', '' ) ); ?>"><span class="lac-enroll-button__label"><?php echo esc_html( sprintf( 'Buy digital course (demo) — $%s', $price_text ) ); ?></span></button>
				<p class="lac-enroll-message" hidden></p>
				<p class="lac-enroll-hint">
					<?php echo esc_html( 'Demo / mock mode only. No real payment is charged.' ); ?>
				</p>
			<?php else : ?>
				<p class="lac-checkout__payment-blocked">
					<?php echo esc_html( sprintf( 'Checkout for this $%s course is temporarily unavailable.', $price_text ) ); ?>
				</p>
				<p class="lac-enroll-hint">
					<?php echo esc_html( 'Please try again later, or contact support if you need help completing your purchase.' ); ?>
				</p>
				<p class="lac-enroll-hint">
					<a href="mailto:fenllinskiii16@gmail.com"><?php echo esc_html( 'Contact support' ); ?></a>
				</p>
			<?php
			 // Log for site owners — never show PayPal setup details on the storefront.
			if ( function_exists( 'lac_log_error' ) ) {
				lac_log_error( 'Checkout blocked: PayPal is not configured (set PAYPAL_CLIENT_ID, PAYPAL_CLIENT_SECRET, PAYPAL_MODE).' );
			}
			endif; ?>
		<?php else : ?>
			<button type="button" class="lac-enroll-button is-available" data-course_id="<?php echo esc_attr( $encrypted_course_id ); ?>" data-action="enroll" data-course_price="0"><span class="lac-enroll-button__label"><?php echo esc_html( 'Complete enrollment' ); ?></span></button>
			<p class="lac-enroll-message" hidden></p>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Shortcode [lac_checkout] — full checkout page body for one course.
 *
 * @return string Checkout HTML or an error message.
 */
function lac_checkout_shortcode() {
	 // Read the course slug from the query string.
	$course_slug = isset( $_GET['course'] ) ? sanitize_text_field( wp_unslash( $_GET['course'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	 // Resolve the slug to an internal course id.
	$course_id = lac_resolve_course_id_from_slug( $course_slug );
	 // Show guidance when the URL is missing or invalid.
	if ( $course_id < 1 ) {
		return '<div class="lac-checkout lac-checkout--empty"><p>' . esc_html( 'Choose a course to enroll or purchase, then return to checkout.' ) . ' <a href="' . esc_url( get_post_type_archive_link( 'lac_course' ) ) . '">' . esc_html( 'Browse courses' ) . '</a></p></div>';
	}
	 // Load course meta used in the checkout summary card.
	$course_level  = get_post_meta( $course_id, '_lac_course_level', true );
	$course_hours  = get_post_meta( $course_id, '_lac_course_hours', true );
	$course_price  = lac_get_effective_checkout_price( $course_id );
	$is_bg_checkout = lac_is_bingeme_checkout();
	$thumbnail_url = get_the_post_thumbnail_url( $course_id, 'large' );
	$price_label   = $course_price > 0 ? '$' . number_format( $course_price, 2 ) : 'Free';
	 // Build the checkout layout with summary and actions.
	ob_start();
	?>
	<div class="lac-checkout<?php echo $is_bg_checkout ? ' lac-checkout--bingeme' : ''; ?>">
		<div class="lac-checkout__grid">
			<section class="lac-checkout__summary">
				<p class="lac-checkout__eyebrow"><?php echo esc_html( 'Checkout' ); ?></p>
				<h1 class="lac-checkout__title"><?php echo esc_html( get_the_title( $course_id ) ); ?></h1>
				<p class="lac-checkout__excerpt"><?php echo esc_html( get_the_excerpt( $course_id ) ); ?></p>
				<ul class="lac-checkout__meta">
					<li><?php echo esc_html( ucfirst( $course_level ? $course_level : 'beginner' ) ); ?></li>
					<li><?php echo esc_html( $course_hours ? $course_hours . ' hours' : 'Self-paced' ); ?></li>
					<li><?php echo esc_html( $price_label ); ?></li>
				</ul>
				<?php if ( $is_bg_checkout ) : ?>
					<a class="lac-checkout__back lac-checkout__back--bingeme" href="<?php echo esc_url( lac_get_bingeme_return_url() ); ?>">
						<?php echo esc_html( '← Return to account' ); ?>
					</a>
				<?php else : ?>
					<a class="lac-checkout__back" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>">
						<?php echo esc_html( '← Back to course details' ); ?>
					</a>
				<?php endif; ?>
			</section>
			<aside class="lac-checkout__panel">
				<?php if ( $thumbnail_url ) : ?>
					<img class="lac-checkout__image" src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title( $course_id ) ); ?>" />
				<?php endif; ?>
				<p class="lac-checkout__total-label"><?php echo esc_html( 'Digital course total' ); ?></p>
				<p class="lac-checkout__total"><?php echo esc_html( $price_label ); ?></p>
				<?php if ( $course_price > 0 ) : ?>
					<p class="lac-checkout__fulfillment"><?php echo esc_html( 'Fulfillment: instant digital access (no shipping).' ); ?></p>
				<?php endif; ?>
				<?php echo lac_render_checkout_actions( $course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</aside>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

 // Register the checkout shortcode with WordPress.
add_shortcode( 'lac_checkout', 'lac_checkout_shortcode' );

 /**
  * Ensure the checkout page exists after the plugin loads.
  *
  * @return void
  */
function lac_checkout_bootstrap_page() {
	 lac_ensure_checkout_page();
}

 // Create or reuse the checkout page on every request until stored.
add_action( 'init', 'lac_checkout_bootstrap_page' );

/**
 * Return the published Refund Policy page HTML (digital goods, no refunds).
 *
 * @return string Block markup for the refund-policy page.
 */
function lac_get_digital_no_refund_policy_content() {
	 // Keep contact email in one place for policy copy.
	$support_email = 'fenllinskiii16@gmail.com';
	 // Build Gutenberg-friendly HTML for the policy page body.
	return '<!-- wp:heading -->
<h2 class="wp-block-heading">Refund Policy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">All products on Learn AI Courses are <strong>digital purchases</strong> only (online courses delivered instantly as digital content — not physical goods). <strong>All sales are final. There are no refunds.</strong></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">1. Digital purchase — no refunds</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">When you pay, you receive immediate online access to digital course materials. Because this is a digital product and access starts right away, we do not offer refunds, returns, exchanges, or cancellations after payment—including mistaken purchases or change of mind.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">2. Before you buy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">Please review the course title, description, curriculum, and price carefully before checkout. If you have questions, email <a href="mailto:' . esc_attr( $support_email ) . '">' . esc_html( $support_email ) . '</a> <strong>before</strong> purchasing.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">3. Access problems</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">If you paid but cannot open your digital course because of a technical issue on our side, email <a href="mailto:' . esc_attr( $support_email ) . '">' . esc_html( $support_email ) . '</a>. We will help restore access. Access support is not a refund.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">4. Free courses</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">Free enrollments have no purchase amount, so no refund applies.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">5. Contact</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="wp-block-paragraph">For billing or access help, email <a href="mailto:' . esc_attr( $support_email ) . '">' . esc_html( $support_email ) . '</a>.</p>
<!-- /wp:paragraph -->';
}

/**
 * Overwrite the live Refund Policy page once with digital no-refund copy.
 *
 * Hostinger often still shows the old 7-day text until this runs after deploy.
 *
 * @return void
 */
function lac_sync_refund_policy_page_if_needed() {
	 // Skip when this digital policy version was already written.
	if ( get_option( 'lac_refund_policy_digital_v2' ) ) {
		return;
	}
	 // Find the published refund-policy page by slug.
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'name'           => 'refund-policy',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	 // Abort quietly when the page does not exist yet.
	if ( empty( $pages ) ) {
		lac_log_error( 'Refund policy page (slug refund-policy) was not found for sync.' );
		return;
	}
	 // Replace page content with the digital no-refunds policy.
	$update_result = wp_update_post(
		array(
			'ID'           => (int) $pages[0],
			'post_content' => lac_get_digital_no_refund_policy_content(),
		),
		true
	);
	 // Log failures so operators can fix the page manually.
	if ( is_wp_error( $update_result ) ) {
		lac_log_error( 'Could not sync refund policy page: ' . $update_result->get_error_message() );
		return;
	}
	 // Mark complete so the page is not rewritten on every request.
	update_option( 'lac_refund_policy_digital_v2', 1, true );
	lac_log_info( 'Synced Refund Policy page to digital purchase / no-refunds copy.' );
}

 // Run the one-time refund policy sync after WordPress is ready.
add_action( 'init', 'lac_sync_refund_policy_page_if_needed', 30 );

/**
 * Replace leftover *@learnaicourses.local emails with the real support inbox.
 *
 * @return void
 */
function lac_replace_local_emails_in_content_if_needed() {
	 // Skip when this email cleanup already ran.
	if ( get_option( 'lac_local_emails_replaced_v1' ) ) {
		return;
	}
	 // Real inbox used on the live site for all public contact addresses.
	$real_email = 'fenllinskiii16@gmail.com';
	 // Map every known .local alias to the same real inbox.
	$local_aliases = array(
		'privacy@learnaicourses.local',
		'billing@learnaicourses.local',
		'legal@learnaicourses.local',
		'support@learnaicourses.local',
		'admin@learnaicourses.local',
	);
	 // Load published pages that still mention a .local email.
	$page_ids = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			's'              => '@learnaicourses.local',
		)
	);
	 // Rewrite each matching page body and excerpt.
	foreach ( $page_ids as $page_id ) {
		$page_post = get_post( $page_id );
		if ( ! $page_post ) {
			continue;
		}
		$new_content = $page_post->post_content;
		$new_excerpt = $page_post->post_excerpt;
		foreach ( $local_aliases as $local_alias ) {
			$new_content = str_replace( $local_alias, $real_email, $new_content );
			$new_excerpt = str_replace( $local_alias, $real_email, $new_excerpt );
		}
		 // Persist only when something actually changed.
		if ( $new_content !== $page_post->post_content || $new_excerpt !== $page_post->post_excerpt ) {
			wp_update_post(
				array(
					'ID'           => (int) $page_id,
					'post_content' => $new_content,
					'post_excerpt' => $new_excerpt,
				)
			);
		}
	}
	 // Also fix the WordPress admin email option when it still uses .local.
	$admin_email = get_option( 'admin_email' );
	if ( is_string( $admin_email ) && false !== strpos( $admin_email, '@learnaicourses.local' ) ) {
		update_option( 'admin_email', $real_email );
	}
	 // Mark complete so we do not rewrite pages on every request.
	update_option( 'lac_local_emails_replaced_v1', 1, true );
	lac_log_info( 'Replaced @learnaicourses.local emails with ' . $real_email );
}

 // Run the one-time .local email cleanup after pages are queryable.
add_action( 'init', 'lac_replace_local_emails_in_content_if_needed', 31 );

/**
 * Remove duplicate Legal & privacy email lines on the Contact page.
 *
 * Replacing privacy@ and legal@ .local aliases with one Gmail left two identical mailto links.
 *
 * @return void
 */
function lac_fix_duplicate_contact_emails_if_needed() {
	 // Skip when this cleanup already completed.
	if ( get_option( 'lac_contact_email_deduped_v1' ) ) {
		return;
	}
	 // Find published contact pages by common slugs.
	$contact_ids = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'name'           => 'contact',
		)
	);
	$contact_us_ids = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'name'           => 'contact-us',
		)
	);
	$page_ids = array_unique( array_merge( $contact_ids, $contact_us_ids ) );
	 // Collapse duplicated consecutive mailto links to a single address.
	$duplicate_html = '<a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a><br><a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>';
	$single_html    = '<a href="mailto:fenllinskiii16@gmail.com">fenllinskiii16@gmail.com</a>';
	foreach ( $page_ids as $page_id ) {
		$page_post = get_post( $page_id );
		if ( ! $page_post ) {
			continue;
		}
		$new_content = str_replace( $duplicate_html, $single_html, $page_post->post_content );
		 // Also repair mangled privacy@gmail composite addresses if present.
		$new_content = str_replace( 'privacy@fenllinskiii16@gmail.com', 'fenllinskiii16@gmail.com', $new_content );
		if ( $new_content !== $page_post->post_content ) {
			wp_update_post(
				array(
					'ID'           => (int) $page_id,
					'post_content' => $new_content,
				)
			);
		}
	}
	 // Mark complete so the rewrite runs only once.
	update_option( 'lac_contact_email_deduped_v1', 1, true );
	lac_log_info( 'Deduped Contact page Legal & privacy email links.' );
}

 // Run after the .local email replacement so duplicates from that pass are cleaned.
add_action( 'init', 'lac_fix_duplicate_contact_emails_if_needed', 32 );
