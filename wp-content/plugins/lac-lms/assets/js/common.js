/**
 * Learn AI Courses LMS front-end script.
 * What this file does: handles free enroll, mock purchase, and PayPal checkout.
 * Process:
 * 1) Bind enroll / Buy now buttons
 * 2) Mount PayPal Smart Buttons when configured
 * 3) POST /enroll, /purchase, or /paypal/* as needed
 * 4) Update CTA state on success
 */
(function () {
	// Abort when the localized config object is missing.
	if (typeof window.lac_lms_config === 'undefined') {
		return;
	}

	// Cache the localized REST configuration for this page.
	const lac_config = window.lac_lms_config;

	/**
	 * Show a short status message under the purchase / enroll control.
	 * @param {HTMLElement|null} message_el Target paragraph element.
	 * @param {string} text Message to display.
	 * @param {boolean} is_error Whether to style as an error.
	 */
	function lac_show_message(message_el, text, is_error) {
		// Ignore calls when no message node exists.
		if (!message_el) {
			return;
		}
		// Reveal the message element and set its copy.
		message_el.hidden = false;
		message_el.textContent = text;
		// Toggle an error class for visual feedback.
		message_el.classList.toggle('is-error', Boolean(is_error));
	}

	/**
	 * Render a checkout status panel (progress / success / error).
	 * @param {HTMLElement} target_el Element to replace or fill.
	 * @param {'progress'|'success'|'error'} kind Status kind.
	 * @param {string} title Heading text.
	 * @param {string} text Supporting text.
	 */
	function lac_render_payment_status(target_el, kind, title, text) {
		if (!target_el) {
			return;
		}
		const panel = document.createElement('div');
		panel.className = 'lac-payment-status';
		if (kind === 'progress') {
			const spinner = document.createElement('div');
			spinner.className = 'lac-payment-status__spinner';
			panel.appendChild(spinner);
		}
		const title_el = document.createElement('p');
		title_el.className = 'lac-payment-status__title is-' + kind;
		title_el.textContent = title;
		panel.appendChild(title_el);
		const text_el = document.createElement('p');
		text_el.className = 'lac-payment-status__text';
		text_el.textContent = text;
		panel.appendChild(text_el);
		target_el.innerHTML = '';
		target_el.appendChild(panel);
	}

	/**
	 * Redirect to continue learning or swap the CTA after success.
	 * @param {HTMLElement} source_el Button or PayPal wrap element.
	 * @param {string} course_id Encrypted course id.
	 * @param {string} success_message Confirmation text.
	 * @param {string} [continue_url] Optional learning-page URL from the REST response.
	 * @param {Object} [extra] Optional extras such as redirect_delay_ms.
	 */
	function lac_mark_purchased(source_el, course_id, success_message, continue_url, extra) {
		// Prefer replacing the outer paypal wrap when present.
		const wrap_el = source_el.classList.contains('lac-paypal-wrap')
			? source_el
			: source_el.closest('.lac-product__buy-actions, .lac-product__hero-actions, .lac-paypal-wrap, [data-lac-checkout-actions]') || source_el.parentElement;
		// Prefer the REST continue URL, then the checkout data attribute.
		const checkout_actions_el = wrap_el && wrap_el.matches('[data-lac-checkout-actions]')
			? wrap_el
			: (wrap_el ? wrap_el.closest('[data-lac-checkout-actions]') : null);
		const dest_url = continue_url || (checkout_actions_el ? checkout_actions_el.dataset.continue_url : '');
		const delay_ms = extra && Number(extra.redirect_delay_ms) > 0 ? Number(extra.redirect_delay_ms) : 0;
		const status_host = checkout_actions_el || wrap_el;

		// Bingeme (and any delayed redirect): show success on this site first.
		if (dest_url && delay_ms > 0 && status_host) {
			lac_render_payment_status(
				status_host,
				'success',
				'Payment successful',
				(success_message || 'Your payment was completed.') + ' Redirecting you back…'
			);
			setTimeout(function () {
				window.location.href = dest_url;
			}, delay_ms);
			return;
		}

		if (dest_url) {
			window.location.href = dest_url;
			return;
		}
		// Build a continue button matching the enrolled CTA style.
		const button_el = document.createElement('a');
		button_el.className = 'lac-enroll-button lac-enroll-link is-enrolled';
		button_el.href = dest_url || '#';
		const label_el = document.createElement('span');
		label_el.className = 'lac-enroll-button__label';
		label_el.textContent = 'Continue learning';
		button_el.appendChild(label_el);
		// Keep a message element for post-purchase feedback.
		let message_el = wrap_el ? wrap_el.querySelector('.lac-enroll-message') : null;
		if (!message_el) {
			message_el = document.createElement('p');
			message_el.className = 'lac-enroll-message';
		}
		// Clear the old CTA and insert the continue button.
		if (wrap_el) {
			wrap_el.innerHTML = '';
			wrap_el.appendChild(button_el);
			wrap_el.appendChild(message_el);
		}
		lac_show_message(message_el, success_message || 'You are enrolled.', false);
	}

	/**
	 * Handle an enroll button click for a free course.
	 * @param {HTMLButtonElement} button_el Clicked enroll control.
	 */
	async function lac_handle_enroll_click(button_el) {
		// Locate the sibling message element for feedback.
		const message_el = button_el.parentElement.querySelector('.lac-enroll-message');
		// Send guests to the login screen before enrollment.
		if (!Number(lac_config.is_logged_in)) {
			window.location.href = lac_config.login_url;
			return;
		}
		// Skip network work when already enrolled.
		if (button_el.classList.contains('is-enrolled')) {
			lac_show_message(message_el, 'You are already enrolled. Open a lesson to continue.', false);
			return;
		}
		// Disable the button while the request is in flight.
		button_el.disabled = true;
		try {
			// POST the encrypted course id to the enroll endpoint.
			const response = await fetch(lac_config.rest_url + 'enroll', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': lac_config.rest_nonce,
				},
				body: JSON.stringify({
					course_id: button_el.dataset.course_id,
				}),
			});
			// Parse the JSON body from the REST response.
			const payload = await response.json();
			// Surface API errors to the learner.
			if (!response.ok) {
				throw new Error(payload.message || 'Enrollment failed.');
			}
			// Mark the button as enrolled and update its label.
			button_el.classList.add('is-enrolled');
			button_el.classList.remove('is-available');
			const label_el = button_el.querySelector('.lac-enroll-button__label');
			if (label_el) {
				label_el.textContent = 'Continue learning';
			} else {
				button_el.textContent = 'Continue learning';
			}
			// Confirm success under the button.
			lac_show_message(message_el, payload.message || 'Enrolled successfully.', false);
			// Redirect checkout enrollments to the first lesson after a successful enroll.
			const checkout_actions_el = button_el.closest('[data-lac-checkout-actions]');
			const dest_url = payload.continue_url || (checkout_actions_el ? checkout_actions_el.dataset.continue_url : '');
			if (dest_url) {
				window.location.href = dest_url;
			}
		} catch (error) {
			// Show a readable failure message.
			lac_show_message(message_el, error.message || 'Something went wrong.', true);
		} finally {
			// Re-enable the button after the request settles.
			button_el.disabled = false;
		}
	}

	/**
	 * Handle a mock / local Buy now click for a paid course.
	 * @param {HTMLButtonElement} button_el Clicked purchase control.
	 */
	async function lac_handle_purchase_click(button_el) {
		// Locate the sibling message element for feedback.
		const message_el = button_el.parentElement.querySelector('.lac-enroll-message');
		// Send guests to the login screen before purchase.
		if (!Number(lac_config.is_logged_in)) {
			window.location.href = lac_config.login_url;
			return;
		}
		// Disable the button while checkout runs.
		button_el.disabled = true;
		try {
			// POST the encrypted course id to the mock purchase endpoint.
			const response = await fetch(lac_config.rest_url + 'purchase', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': lac_config.rest_nonce,
				},
				body: JSON.stringify({
					course_id: button_el.dataset.course_id,
				}),
			});
			// Parse the JSON body from the REST response.
			const payload = await response.json();
			// Surface API errors to the learner.
			if (!response.ok) {
				throw new Error(payload.message || 'Purchase failed.');
			}
			// Replace every matching purchase CTA on the page with continue.
			document.querySelectorAll('.lac-enroll-button.is-purchase, .lac-paypal-wrap').forEach(function (el) {
				if (el.dataset.course_id === button_el.dataset.course_id || el === button_el) {
					lac_mark_purchased(el, button_el.dataset.course_id, payload.message, payload.continue_url, {
						redirect_delay_ms: payload.redirect_delay_ms,
					});
				}
			});
			// Also mark the clicked button if it remains.
			if (document.body.contains(button_el)) {
				lac_mark_purchased(button_el, button_el.dataset.course_id, payload.message, payload.continue_url, {
					redirect_delay_ms: payload.redirect_delay_ms,
				});
			}
		} catch (error) {
			// Show a readable failure message.
			lac_show_message(message_el, error.message || 'Something went wrong.', true);
			button_el.disabled = false;
		}
	}

	/**
	 * Mount PayPal Smart Buttons for one paid course wrap.
	 * @param {HTMLElement} wrap_el Wrapper with course_id and price data.
	 */
	function lac_mount_paypal_buttons(wrap_el) {
		// Locate the mount node and message element inside the wrap.
		const container_el = wrap_el.querySelector('.lac-paypal-button-container');
		const message_el = wrap_el.querySelector('.lac-enroll-message');
		// Abort when the SDK or container is unavailable.
		if (!container_el || typeof window.paypal === 'undefined' || !window.paypal.Buttons) {
			if (message_el && Number(lac_config.paypal_configured)) {
				lac_show_message(message_el, 'PayPal button failed to load. Refresh and try again.', true);
			}
			return;
		}
		// Render the official PayPal Buttons into the container.
		window.paypal.Buttons({
			style: {
				layout: 'vertical',
				color: 'gold',
				shape: 'rect',
				label: 'pay',
			},
			/**
			 * Ask our server to create a PayPal order for this course.
			 * @returns {Promise<string>} PayPal order id.
			 */
			createOrder: async function () {
				// Guests must sign in before starting checkout.
				if (!Number(lac_config.is_logged_in)) {
					window.location.href = lac_config.login_url;
					throw new Error('Login required');
				}
				// Create the remote order through the LMS REST API.
				const response = await fetch(lac_config.rest_url + 'paypal/create-order', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': lac_config.rest_nonce,
					},
					body: JSON.stringify({
						course_id: wrap_el.dataset.course_id,
					}),
				});
				// Parse and validate the create-order response.
				const payload = await response.json();
				if (!response.ok) {
					throw new Error(payload.message || 'Could not start PayPal checkout.');
				}
				// Already-enrolled responses should stop the button flow.
				if (payload.status === 'already_enrolled') {
					lac_mark_purchased(wrap_el, wrap_el.dataset.course_id, payload.message, payload.continue_url);
					throw new Error(payload.message);
				}
				// Hand the PayPal order id back to the Smart Buttons SDK.
				return payload.paypal_order_id;
			},
			/**
			 * Capture payment after the buyer approves in the PayPal popup.
			 * @param {Object} data PayPal approval data with orderID.
			 */
			onApprove: async function (data) {
				const actions_el = wrap_el.closest('[data-lac-checkout-actions]') || wrap_el;
				lac_render_payment_status(
					actions_el,
					'progress',
					'Processing payment…',
					'Please wait while we confirm your PayPal payment.'
				);
				try {
					// Ask our server to capture funds and enroll the learner.
					const response = await fetch(lac_config.rest_url + 'paypal/capture-order', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': lac_config.rest_nonce,
						},
						body: JSON.stringify({
							paypal_order_id: data.orderID,
						}),
					});
					// Parse and validate the capture response.
					const payload = await response.json();
					if (!response.ok) {
						throw new Error(payload.message || 'Payment capture failed.');
					}
					// Swap the PayPal UI for a continue-learning button / success panel.
					lac_mark_purchased(
						wrap_el,
						wrap_el.dataset.course_id,
						payload.message,
						payload.continue_url,
						{
							redirect_delay_ms: payload.redirect_delay_ms,
						}
					);
				} catch (error) {
					// Surface capture failures under the button area.
					lac_render_payment_status(
						actions_el,
						'error',
						'Payment failed',
						(error && error.message) || 'Payment failed. Please try again.'
					);
				}
			},
			/**
			 * Surface PayPal SDK or buyer-cancel errors.
			 * @param {Error} error SDK error object.
			 */
			onError: function (error) {
				// Ignore intentional login redirects thrown from createOrder.
				if (error && error.message === 'Login required') {
					return;
				}
				const actions_el = wrap_el.closest('[data-lac-checkout-actions]') || wrap_el;
				lac_render_payment_status(
					actions_el,
					'error',
					'Payment failed',
					(error && error.message) || 'PayPal checkout error.'
				);
			},
			onCancel: function () {
				const message_el = wrap_el.querySelector('.lac-enroll-message');
				lac_show_message(message_el, 'Payment cancelled. You can try again when ready.', true);
			},
		}).render(container_el);
	}

	// Bind listeners once the DOM is interactive.
	document.addEventListener('DOMContentLoaded', function () {
		// Checkout-only flows: course pages now link to /checkout/ instead.
		const checkout_root = document.querySelector('.lac-checkout, [data-lac-checkout-actions]');
		if (!checkout_root) {
			return;
		}
		// Bind free enroll and mock purchase buttons inside checkout.
		checkout_root.querySelectorAll('.lac-enroll-button').forEach(function (button_el) {
			button_el.addEventListener('click', function (event) {
				if (button_el.dataset.action === 'purchase' || button_el.classList.contains('is-purchase')) {
					event.preventDefault();
					lac_handle_purchase_click(button_el);
					return;
				}
				if (button_el.dataset.action === 'enroll' || button_el.classList.contains('is-available')) {
					event.preventDefault();
					lac_handle_enroll_click(button_el);
				}
			});
		});
		// Mount PayPal buttons when the SDK and wraps are present on checkout.
		const paypal_wraps = checkout_root.querySelectorAll('.lac-paypal-wrap');
		if (paypal_wraps.length && Number(lac_config.paypal_configured) && !Number(lac_config.paypal_mock)) {
			paypal_wraps.forEach(function (wrap_el) {
				lac_mount_paypal_buttons(wrap_el);
			});
		}
	});
})();
