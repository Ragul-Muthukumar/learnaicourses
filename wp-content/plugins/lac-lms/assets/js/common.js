/**
 * Learn AI Courses LMS front-end script.
 * What this file does: handles enroll button clicks via the REST API.
 * Process:
 * 1) Bind click listeners on .lac-enroll-button
 * 2) Redirect guests to login
 * 3) POST /enroll with encrypted course_id and REST nonce
 * 4) Update button label and message on success/failure
 */
(function () {
	// Abort when the localized config object is missing.
	if (typeof window.lac_lms_config === 'undefined') {
		return;
	}

	// Cache the localized REST configuration for this page.
	const lac_config = window.lac_lms_config;

	/**
	 * Show a short status message under the enroll button.
	 * @param {HTMLElement} message_el Target paragraph element.
	 * @param {string} text Message to display.
	 * @param {boolean} is_error Whether to style as an error.
	 */
	function lac_show_message(message_el, text, is_error) {
		// Reveal the message element and set its copy.
		message_el.hidden = false;
		message_el.textContent = text;
		// Toggle an error class for visual feedback.
		message_el.classList.toggle('is-error', Boolean(is_error));
	}

	/**
	 * Handle an enroll button click for one course.
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
			button_el.textContent = 'Continue learning';
			// Confirm success under the button.
			lac_show_message(message_el, payload.message || 'Enrolled successfully.', false);
		} catch (error) {
			// Show a readable failure message.
			lac_show_message(message_el, error.message || 'Something went wrong.', true);
		} finally {
			// Re-enable the button after the request settles.
			button_el.disabled = false;
		}
	}

	// Bind listeners once the DOM is interactive.
	document.addEventListener('DOMContentLoaded', function () {
		// Find every enroll CTA on the page.
		const buttons = document.querySelectorAll('.lac-enroll-button');
		// Attach an async click handler to each CTA.
		buttons.forEach(function (button_el) {
			button_el.addEventListener('click', function () {
				lac_handle_enroll_click(button_el);
			});
		});
	});
})();
