<?php
/**
 * @package Bgcheckout
 */
/*
Plugin Name: Bg Checkout
Plugin URI: https://templarcorp.co.uk/
Description: BG Checkout
Version: 1.0.0
Requires at least: 1.0
Requires PHP: 5.2
Author: templarcorp
Author URI: https://templarcorp.co.uk/
License: GPLv2 or later
Text Domain: templarcorp
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/**
 * Read Bingeme settings from wp-config, process env, or the site-root .env.
 *
 * @param string $name Setting name such as BG_API_KEY.
 * @return string
 */
function bg_checkout_env( $name ) {
	if ( defined( $name ) ) {
		$value = constant( $name );
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			return trim( $value );
		}
	}
	if ( function_exists( 'getenv' ) ) {
		$value = getenv( $name );
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			return trim( $value );
		}
	}
	static $file_values = null;
	if ( null === $file_values ) {
		$file_values = array();
		$paths       = array( ABSPATH . '.env' );
		if ( defined( 'BGCHECKOUT_PLUGIN_DIR' ) ) {
			$paths[] = dirname( BGCHECKOUT_PLUGIN_DIR, 3 ) . '/.env';
		}
		foreach ( array_unique( $paths ) as $path ) {
			if ( ! is_readable( $path ) ) {
				continue;
			}
			foreach ( file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
				$line = trim( $line );
				if ( '' === $line || '#' === $line[0] || false === strpos( $line, '=' ) ) {
					continue;
				}
				list( $key, $val ) = explode( '=', $line, 2 );
				$file_values[ trim( $key ) ] = trim( $val, " \t\"'" );
			}
			break;
		}
	}
	return isset( $file_values[ $name ] ) ? (string) $file_values[ $name ] : '';
}

/**
 * Define BG_* constants from .env when wp-config did not set them.
 *
 * @return void
 */
function bg_checkout_define_constants() {
	if ( ! defined( 'BGCHECKOUT_PLUGIN_DIR' ) ) {
		define( 'BGCHECKOUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
	$defaults = array(
		'BG_API_URL'       => '',
		'BG_API_KEY'       => '',
		'BG_ENV'           => 'local',
		'BG_COOKIE'        => '',
		'BG_USER_PASSWORD' => 'ChangeMeBgUser',
	);
	foreach ( $defaults as $name => $fallback ) {
		if ( ! defined( $name ) ) {
			$value = bg_checkout_env( $name );
			define( $name, '' !== $value ? $value : $fallback );
		}
	}
}
bg_checkout_define_constants();

add_action( 'woocommerce_init', 'bgcart' );
add_action('woocommerce_payment_complete', 'payment_complete', 10, 1);
add_action('woocommerce_order_status_changed', 'order_status_changed', 10, 3);
add_action('woocommerce_thankyou', 'handle_thankyou_redirect', 10, 1);
add_action( 'wp_insert_post', 'order_created', 10, 3 );
// Only empty the cart for Bingeme deposit add-to-cart requests (not every shop add).
add_filter( 'woocommerce_add_cart_item_data', 'bg_custom_add_to_cart', 10, 2 );
add_filter( 'woocommerce_add_to_cart_redirect', 'misha_skip_cart_redirect_checkout' );

function misha_skip_cart_redirect_checkout( $url ) {
	return wc_get_checkout_url();
}

/**
 * Empty the cart before a Bingeme deposit product is added so only that amount is paid.
 *
 * @param array $cart_item_data Cart item data.
 * @param int   $product_id    Product being added.
 * @return array
 */
function bg_custom_add_to_cart( $cart_item_data, $product_id = 0 ) {
	$is_bg_product = $product_id && get_post_meta( (int) $product_id, '_bg_dynamic_product', true );
	$is_bg_request = ! empty( $_COOKIE['bg_cart_data'] ) || ( isset( $_REQUEST['bg_deposit'] ) && '1' === $_REQUEST['bg_deposit'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $is_bg_product || $is_bg_request ) {
		if ( function_exists( 'WC' ) && WC()->cart ) {
			WC()->cart->empty_cart();
		}
	}
	return $cart_item_data;
}

/**
 * Find an existing Bingeme dynamic product for an amount, or create one.
 *
 * @param float $amount Deposit amount.
 * @return int Product ID or 0 on failure.
 */
function bg_get_or_create_amount_product( $amount ) {
	$amount = (float) $amount;
	if ( $amount <= 0 ) {
		return 0;
	}

	// Prefer products created by this plugin for the exact amount.
	$query = new WP_Query(
		array(
			'posts_per_page' => 1,
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_bg_dynamic_product',
					'value' => '1',
				),
				array(
					'key'   => '_price',
					'value' => wc_format_decimal( $amount, wc_get_price_decimals() ),
				),
			),
		)
	);
	if ( ! empty( $query->posts ) ) {
		return (int) $query->posts[0];
	}

	return (int) bg_create_dynamic_amount_product( $amount );
}

/**
 * Ensure WooCommerce cart/session is ready after forced login, then add product and go to checkout.
 *
 * @param int $product_id Product to purchase.
 * @return void
 */
function bg_add_product_and_redirect_to_checkout( $product_id ) {
	$product_id = absint( $product_id );
	if ( $product_id < 1 ) {
		wp_die( 'Failed to prepare product for payment. Please try again.' );
	}

	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_die( 'WooCommerce cart is not available.' );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_purchasable() ) {
		error_log( 'BG Error: product not purchasable id=' . $product_id . ' exists=' . ( $product ? '1' : '0' ) );
		wp_die( 'The payment product is not purchasable. Please contact support.' );
	}

	// Persist the customer session cookie so the cart survives the checkout redirect.
	if ( WC()->session ) {
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
		// Bind cart session to the user we just logged in as.
		if ( is_user_logged_in() && WC()->customer ) {
			WC()->customer->set_id( get_current_user_id() );
			WC()->customer->set_email( wp_get_current_user()->user_email );
			WC()->customer->set_billing_email( wp_get_current_user()->user_email );
		}
	}

	// Avoid emptying from inside the add_cart_item_data filter on this direct path.
	remove_filter( 'woocommerce_add_cart_item_data', 'bg_custom_add_to_cart', 10 );
	WC()->cart->empty_cart();
	$added = WC()->cart->add_to_cart( $product_id, 1 );
	add_filter( 'woocommerce_add_cart_item_data', 'bg_custom_add_to_cart', 10, 2 );

	if ( ! $added ) {
		error_log( 'BG Error: add_to_cart failed for product_id=' . $product_id );
		wp_safe_redirect( add_query_arg( array( 'add-to-cart' => $product_id, 'bg_deposit' => '1' ), wc_get_cart_url() ) );
		exit;
	}

	WC()->cart->calculate_totals();
	if ( WC()->session ) {
		WC()->session->save_data();
	}
	wp_safe_redirect( wc_get_checkout_url() );
	exit;
}

function order_created( $post_id, $post, $update) {
	if ( ! $post_id || get_post_type( $post_id ) != 'shop_order' || $update == 1 ) {
		return;
	}

	$deposit_id = get_post_meta($post_id, '_bg_deposit_id', true);
	$bg_data = [];
	if(isset($_COOKIE['bg_cart_data'])) {
		$bg_data = unserialize(stripslashes($_COOKIE['bg_cart_data']));
	}
	if (!$deposit_id && count($bg_data)) {
		add_post_meta($post_id, '_bg_deposit_id', esc_sql($bg_data['id']));
	}
}

function getAmount() {

	$txn_id = sanitize_text_field(esc_sql($_GET['txn_id']));
	try{
		$apiKey = BG_API_KEY;
		$url = BG_API_URL;
		$cookie = [];
		if(BG_ENV != "prod"){
			$cookie = [
				'ckDevTest' => BG_COOKIE,
				'ckdev' => BG_COOKIE
                	];
		}
        $apiUrl = $url."api/get/deposits/amount";
        $request_data = [
                'txn_id'     => $txn_id
        ];
        $request_arguments = array(
                        'headers'=>['X-API-key' => $apiKey],
			'body' => json_encode($request_data),
            'cookies' => $cookie,
            'sslverify' => (defined('BG_ENV') && BG_ENV === 'prod') ? true : false,
                );

        $response = wp_remote_post($apiUrl, $request_arguments);
		$response_code = wp_remote_retrieve_response_code($response);
        if ($response_code === 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if ($data !== null) {
                return $data;
            }
        }
		return false;

    }  catch(Exception $e){
		trigger_error('Binge API Error:' . $e->getMessage());
    }

	return false;
}
function bgcart( ) {
	if ( strpos($_SERVER['REQUEST_URI'], 'bgcart') != false ) {
		$bg_redirect_url_domain = BG_API_URL;
		$bg_user_password = BG_USER_PASSWORD;
		if ( '' === trim( (string) $bg_redirect_url_domain ) || '' === trim( (string) BG_API_KEY ) ) {
			wp_die( 'BG Checkout is installed but BG_API_URL and BG_API_KEY are not set in .env.' );
		}
		$rowData = getAmount();
		if ( $rowData && is_array( $rowData ) ) {
			// Add taxes to the deposit amount when Bingeme returns them.
			$rowData['amount'] = (float) $rowData['amount'] + (float) ( $rowData['taxes'] ?? 0 );
		}
	 	error_log('BG Cart Data: ' . print_r($rowData, true));
		if ($rowData && is_array($rowData) && isset($rowData['id'])) {
			$session_data['id'] = $rowData['id'];
			$session_data['txn_id'] = $rowData['txn_id']; // Store txn_id in cookie
			$user = get_user_by('email', $rowData['email']);
			if (!$user) {
				wp_create_user($rowData['email'], $bg_user_password, $rowData['email']);
				$user = get_user_by('email', $rowData['email']);
			}

			wp_clear_auth_cookie();
			wp_set_current_user ( $user->ID );
			wp_set_auth_cookie  ( $user->ID );

			// Set redirect URL to deposit/status/{txn_id} route
			if (!$rowData['ref'] || $rowData['ref'] == "/") {
				// Default to wallet page
				$session_data['url'] = $bg_redirect_url_domain . 'my/wallet';
			}
            elseif (strpos($rowData['ref'], 'http://') === 0 || strpos($rowData['ref'], 'https://') === 0) {
                // If ref is a full URL, extract domain and set deposit/status route
                $parsedUrl = parse_url($rowData['ref']);
                $redirectDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $redirectDomain .= ':' . $parsedUrl['port'];
                }
                $session_data['url'] = $redirectDomain . '/deposit/status/' . $rowData['txn_id'];
			}
			else {
				// If ref is a path, use BG_API_URL and set deposit/status route
				$bg_api_url = rtrim($bg_redirect_url_domain, '/');
				$session_data['url'] = $bg_api_url . '/deposit/status/' . $rowData['txn_id'];
			}

			if(str_contains(strtolower($rowData['payment_gateway']), 'phonepe')) {
	                  $session_data['payment_gateway'] = 'phonepe';
	                } else if(str_contains(strtolower($rowData['payment_gateway']), 'stripe')) {
	                  $session_data['payment_gateway'] = 'stripe';
	                }

			setcookie('bg_cart_data',  serialize($session_data), time()+(86400 * 30), "/");
			$_COOKIE['bg_cart_data'] = serialize($session_data);

			// Find or create a Bingeme-only product for this amount, add it now, go to WC checkout.
			$product_id = bg_get_or_create_amount_product( (float) $rowData['amount'] );
			if ( ! $product_id ) {
				error_log( 'BG Error: Failed to create dynamic product for amount: ' . $rowData['amount'] );
				wp_die( 'Failed to create product for payment. Please try again.' );
			}
			bg_add_product_and_redirect_to_checkout( $product_id );
        } else {
            wp_die('Payment initiation failed. The deposit details could not be retrieved from API. Please verify BG_API_URL/BG_API_KEY and that the txn_id exists.');
		}
	}
}

// Create a hidden, one-per-order product for a custom amount
function bg_create_dynamic_amount_product( $amount ) {
    try {
        $amount = (float) $amount;
        if ( $amount <= 0 ) {
            return false;
        }

        if ( class_exists('WC_Product_Simple') ) {
            $product = new WC_Product_Simple();
            $product->set_name( 'Digital Purchase #' . wp_generate_password(4, false) );
            $product->set_status( 'publish' );
            $product->set_catalog_visibility( 'hidden' );
            $product->set_manage_stock( false );
            $product->set_stock_status( 'instock' );
            $product->set_sold_individually( true );
            $product->set_virtual( true );
            $product->set_regular_price( wc_format_decimal( $amount, wc_get_price_decimals() ) );
            $product->set_price( wc_format_decimal( $amount, wc_get_price_decimals() ) );
            $product_id = $product->save();
            if ( $product_id ) {
                update_post_meta( $product_id, '_bg_dynamic_product', 1 );
                // Ensure product type taxonomy is set so WooCommerce treats it as purchasable.
                wp_set_object_terms( $product_id, 'simple', 'product_type', false );
                return (int) $product_id;
            }
        }

        $post_id = wp_insert_post(array(
            'post_title'   => 'Digital Purchase #' . wp_generate_password(4, false),
            'post_status'  => 'publish',
            'post_type'    => 'product',
        ));
        if ( $post_id && !is_wp_error($post_id) ) {
            update_post_meta($post_id, '_regular_price', (string)$amount);
            update_post_meta($post_id, '_price', (string)$amount);
            update_post_meta($post_id, '_manage_stock', 'no');
            update_post_meta($post_id, '_sold_individually', 'yes');
            update_post_meta($post_id, '_virtual', 'yes');
            update_post_meta($post_id, '_downloadable', 'no');
            update_post_meta($post_id, '_visibility', 'hidden');
            update_post_meta($post_id, '_bg_dynamic_product', 1 );
            return (int)$post_id;
        }
    } catch ( \Exception $e ) {
        error_log('BG dynamic product create error: ' . $e->getMessage());
    }
    return false;
}


function update_bg_deposit($status = 'pending', $data = []) {
    try {
        $apiKey = BG_API_KEY;
        $url = BG_API_URL;
        $cookie = [];

        if (BG_ENV != "prod") {
            $cookie = [
                'ckDevTest' => BG_COOKIE,
                'ckdev' => BG_COOKIE
            ];
        }

        // Get URL from cookie to determine API domain
        if (isset($_COOKIE['bg_cart_data'])) {
            $bg_data = maybe_unserialize(stripslashes($_COOKIE['bg_cart_data']));
            if (isset($bg_data['url']) && !empty($bg_data['url'])) {
                $cookieUrl = $bg_data['url'];

                // Check if cookie URL is a full URL
                if (strpos($cookieUrl, 'http://') === 0 || strpos($cookieUrl, 'https://') === 0) {
                    $parsedUrl = parse_url($cookieUrl);
                    if (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
                        $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/';
                    }
                }
            }
        }

        error_log('BG API: Using ref domain for API URL: ' . $url);
        $apiUrl = $url . "api/update/payment/deposit/status";

        $request_data = [
            'id'     => esc_sql($data['id']),
	    'status' => $status,
	    'paypal_txn_id' => $data['paypal_txn_id'] ?? null,
        ];

        $request_arguments = [
            'headers'   => ['X-API-key' => $apiKey],
            'body'      => json_encode($request_data),
            'cookies'   => $cookie,
            'sslverify' => (defined('BG_ENV') && BG_ENV === 'prod'),
        ];

        $response = wp_remote_post($apiUrl, $request_arguments);

        error_log(print_r($apiUrl, true));
        error_log(print_r($response, true));

    } catch (Exception $e) {
        trigger_error('Binge API Error: ' . $e->getMessage());
    }
}


function payment_complete($order_id) {
    error_log('BG Payment Complete Hook: order_id=' . $order_id);
    $order = wc_get_order($order_id);
    if (!$order) {
        error_log('BG Payment Complete Hook: Could not load order_id=' . $order_id);
        return;
    }

    // Check if already processed to prevent double processing
    $already_processed = get_post_meta($order_id, '_bg_payment_processed', true);
    if ($already_processed === 'yes') {
        error_log('BG Payment Complete Hook: Payment already processed for order_id=' . $order_id);
        return;
    }
    
    $bg_data = [];
    if(isset($_COOKIE['bg_cart_data'])) {
        $bg_data = unserialize(stripslashes($_COOKIE['bg_cart_data']));
    }
    error_log('BG Payment Complete Hook: bg_data=' . print_r($bg_data, true));
    
    if (!empty($bg_data['id'])) {
	error_log('BG Payment Complete Hook: Calling update_bg_deposit for payment completion');
	$paypal_txn_id = $order->get_transaction_id();
        if (!empty($paypal_txn_id)) {
            $paypal_txn_id = sanitize_text_field($paypal_txn_id);
            $bg_data['paypal_txn_id'] = $paypal_txn_id;
	}

        update_bg_deposit('active', $bg_data);
        
        // Get txn_id from cookie (stored during bgcart)
        $txn_id = '';
        if (!empty($bg_data['txn_id'])) {
            $txn_id = $bg_data['txn_id'];
        }
        
        // Store redirect info for all payments
        add_post_meta($order_id, '_bg_redirect_needed', 'yes');
        add_post_meta($order_id, '_bg_deposit_id', $bg_data['id']);
        if (!empty($txn_id)) {
            add_post_meta($order_id, '_bg_txn_id', $txn_id);
        }
        error_log('BG Payment Complete Hook: Set redirect meta for order_id=' . $order_id . ', txn_id=' . $txn_id);
        
        // Mark as processed to prevent double processing
        add_post_meta($order_id, '_bg_payment_processed', 'yes');
    } else {
        error_log('BG Payment Complete Hook: No bg_cart_data found');
    }
}

function order_status_changed($order_id, $old_status, $new_status) {
    error_log('BG Order Status Changed: order_id=' . $order_id . ', old_status=' . $old_status . ', new_status=' . $new_status);
    $order = wc_get_order($order_id);
    if (!$order) {
        error_log('BG Payment Complete Hook: Could not load order_id=' . $order_id);
        return;
    }

    // Check if already processed to prevent double processing
    $already_processed = get_post_meta($order_id, '_bg_payment_processed', true);
    if ($already_processed === 'yes') {
        error_log('BG Order Status Changed: Payment already processed for order_id=' . $order_id);
        return;
    }
    
    $order_details = get_post_meta($order_id);
    $bg_data = [];
    if (!empty($_COOKIE['bg_cart_data'])) {
        $bg_data = unserialize(stripslashes($_COOKIE['bg_cart_data']));
    }

    // Determine deposit ID
    $deposit_id = $order_details['_bg_deposit_id'][0] ?? ($bg_data['id'] ?? null);

    // Add deposit ID if not already in post meta
    if (empty($order_details['_bg_deposit_id']) && !empty($bg_data['id'])) {
        add_post_meta($order_id, '_bg_deposit_id', esc_sql($bg_data['id']));
        $deposit_id = $bg_data['id'];
    }

    if (!$deposit_id) {
        error_log('BG Order Status Changed: No deposit_id found for order ' . $order_id);
        return;
    }

    $data = ['id' => $deposit_id];

    // Handle status changes
    if (in_array($new_status, ['completed', 'processing'])) {
	error_log('BG Order Status Changed: Calling update_bg_deposit for completed status');
	$paypal_txn_id = $order->get_transaction_id();
        if (!empty($paypal_txn_id)) {
            $paypal_txn_id = sanitize_text_field($paypal_txn_id);
            $data['paypal_txn_id'] = $paypal_txn_id;
            $order->update_meta_data('_bg_paypal_txn_id', $paypal_txn_id);
	}

        update_bg_deposit('active', $data);
        
        // Store redirect info for all payments
        add_post_meta($order_id, '_bg_redirect_needed', 'yes');
        add_post_meta($order_id, '_bg_deposit_id', $deposit_id);
        
        // Mark as processed to prevent double processing
        add_post_meta($order_id, '_bg_payment_processed', 'yes');
    } elseif (in_array($new_status, ['cancelled', 'failed', 'refunded'])) {
        error_log('BG Order Status Changed: Calling update_bg_deposit_failed for failed status');
        update_bg_deposit_failed($data);
        
        // Mark as processed to prevent double processing
        add_post_meta($order_id, '_bg_payment_processed', 'yes');
    }
}

function bg_get_deposit_data($deposit_id) {
	try {
		$apiKey = BG_API_KEY;
		$url = BG_API_URL;
		$cookie = [];
		if(BG_ENV != "prod"){
			$cookie = [
				'ckDevTest' => BG_COOKIE,
				'ckdev' => BG_COOKIE
			];
		}
		
		$apiUrl = $url . "api/check-payment-type";
		$request_data = [
			'deposit_id' => $deposit_id
		];
		$request_arguments = array(
			'headers' => ['X-API-key' => $apiKey],
			'body' => json_encode($request_data),
			'cookies' => $cookie,
			'sslverify' => (defined('BG_ENV') && BG_ENV === 'prod') ? true : false,
		);

		$response = wp_remote_post($apiUrl, $request_arguments);
		$response_code = wp_remote_retrieve_response_code($response);
		
		if ($response_code === 200) {
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body, true);
			return $data;
		}
		
		return null;
		
	} catch(Exception $e) {
		error_log('BG Get Deposit Data Error: ' . $e->getMessage());
		return null;
	}
}

function update_bg_deposit_failed($data = []) {
	try {
		if (isset($data['user_id'])) {
			$apiKey =BG_API_KEY;
			$url = BG_API_URL;
			$cookie = [];
                if(BG_ENV != "prod"){
                        $cookie = [
                                'ckDevTest' => BG_COOKIE,
                                'ckdev' => BG_COOKIE
                        ];
                }
			$apiUrl = $url."api/set/deposits/fail";
			$request_data = [
					'deposit_id'     => esc_sql($data['id'])
			];
			$request_arguments = array(
							'headers'=>['X-API-key' => $apiKey],
							'body' => json_encode($request_data),
							'cookies' => $cookie
					);

			$response = wp_remote_post($apiUrl, $request_arguments);

			return true;

		}
	} catch(Exception $e) {
		error_log('BG Get Deposit Data Error: ' . $e->getMessage());
		return null;
	}

	return false;
}

function handle_thankyou_redirect($order_id) {
    error_log('BG Thank You Redirect: order_id=' . $order_id);
    
    // Check if this order needs a redirect
    $redirect_needed = get_post_meta($order_id, '_bg_redirect_needed', true);
    if ($redirect_needed !== 'yes') {
        error_log('BG Thank You Redirect: No redirect needed for order ' . $order_id);
        return;
    }
    
    $deposit_id = get_post_meta($order_id, '_bg_deposit_id', true);
    if (empty($deposit_id)) {
        error_log('BG Thank You Redirect: No deposit_id found for order ' . $order_id);
        return;
    }
    
    // Get redirect data from cookie (set during bgcart)
		$bg_data = [];
		if(isset($_COOKIE['bg_cart_data'])) {
			$bg_data = unserialize(stripslashes($_COOKIE['bg_cart_data']));
		}
    
    if (!empty($bg_data['url'])) {
        $redirectUrl = $bg_data['url'];
        error_log('BG Thank You Redirect: Using redirect URL from cookie: ' . $redirectUrl);
        
        // Add JavaScript to redirect after a delay to show success message
        echo '<script>
            setTimeout(function() {
                window.location.href = "' . esc_js($redirectUrl) . '";
            }, 5000); // 5 second delay
        </script>';
    } else {
        error_log('BG Thank You Redirect: No redirect URL found in cookie');
	}
}

/**
 * Proper way to enqueue scripts and styles
 */
function wpdocs_bg_checkout_scripts() {
	if ( ! function_exists( 'is_checkout' ) ) {
		return;
	}
	// Keep LMS pages clean; only load Bingeme checkout CSS on WooCommerce screens.
	if ( ! is_checkout() && ! is_cart() && ! is_shop() && ! is_account_page() ) {
		return;
	}
	wp_enqueue_style( 'widget-woocommerce', plugin_dir_url( __FILE__ ).'assets/css/widget-woocommerce.min.css' );
	wp_enqueue_style( 'widget-nav-menu', plugin_dir_url( __FILE__ ).'assets/css/widget-nav-menu.min.css' );
	wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ).'assets/css/fontawesome.min.css' );
	wp_enqueue_style( 'widget-theme-elements', plugin_dir_url( __FILE__ ).'assets/css/widget-theme-elements.min.css' );
	wp_enqueue_style( 'brands', plugin_dir_url( __FILE__ ).'assets/css/brands.min.css' );
	wp_enqueue_style( 'solid', plugin_dir_url( __FILE__ ).'assets/css/solid.min.css' );
	wp_enqueue_style( 'widget-icon-list', plugin_dir_url( __FILE__ ).'assets/css/widget-icon-list.min.css' );
	wp_enqueue_style( 'frontend-lite', plugin_dir_url( __FILE__ ).'assets/css/frontend-lite.min.css' );
	wp_enqueue_style( 'post-14', plugin_dir_url( __FILE__ ).'assets/css/post-14.css' );
	wp_enqueue_style( 'post-224', plugin_dir_url( __FILE__ ).'assets/css/post-224.css' );
	wp_enqueue_style( 'post-20', plugin_dir_url( __FILE__ ).'assets/css/post-20.css' );
	wp_enqueue_style( 'post-11', plugin_dir_url( __FILE__ ).'assets/css/post-11.css' );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_bg_checkout_scripts' );



// Register custom API endpoint
function custom_payment_methods_route() {
    register_rest_route( 'custom/v1', '/payment-methods', array(
        'methods'  => 'GET, POST',
        'callback' => 'enable_or_disable_payment_settings',
    ));
}
add_action( 'rest_api_init', 'custom_payment_methods_route' );

// Callback function to handle GET and POST requests
function enable_or_disable_payment_settings ( $request ) {
	if ( 'POST' === $request->get_method() ) {
		$params = $request->get_params();
		$provided_api_key = $request->get_header( 'x-api-key' );
		$expected_api_key = BG_API_KEY;

		if ( ! $provided_api_key || $provided_api_key !== $expected_api_key ) {
        		return new WP_Error( 'unauthorized', __( 'Unauthorized - Invalid API key.', 'text-domain' ), array( 'status' => 401 ) );
    		}

		// Ensure required parameters are present
		if ( !isset( $params['method_id'] ) || !isset( $params['enabled'] ) ) {
			return new WP_Error( 'invalid_params', esc_html__( 'Method ID and enabled status are required.', 'text-domain' ), array( 'status' => 400 ) );
		}

		$options = get_option($params['method_id']);
		$options['enabled'] = $params['enabled'];
		update_option($params['method_id'], $options);
		return rest_ensure_response( array( 'success' => true ) );

	}
}
