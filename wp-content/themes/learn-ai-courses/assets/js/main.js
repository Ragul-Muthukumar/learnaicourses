/**
 * Learn AI Courses theme front-end behavior.
 * What this file does: mobile nav toggle and scroll reveal motion.
 * Process:
 * 1) Bind the menu button to open/close navigation on small screens
 * 2) Observe [data-reveal] nodes and add is-visible when they enter view
 */
(function () {
	// Wait until the DOM is ready before querying controls.
	document.addEventListener('DOMContentLoaded', function () {
		// Cache the mobile menu toggle button.
		const nav_toggle = document.querySelector('[data-nav-toggle]');
		// Cache the navigation panel controlled by the toggle.
		const site_nav = document.querySelector('[data-site-nav]');

		// Only wire the toggle when both elements exist.
		if (nav_toggle && site_nav) {
			nav_toggle.addEventListener('click', function () {
				// Flip the open state class on the nav panel.
				const is_open = site_nav.classList.toggle('is-open');
				// Mirror expanded state for accessibility tools.
				nav_toggle.setAttribute('aria-expanded', is_open ? 'true' : 'false');
			});
		}

		// Collect elements that should animate into view.
		const reveal_nodes = document.querySelectorAll('[data-reveal]');
		// Gracefully show everything if IntersectionObserver is unavailable.
		if (!('IntersectionObserver' in window)) {
			reveal_nodes.forEach(function (node) {
				node.classList.add('is-visible');
			});
			return;
		}

		// Create an observer that reveals nodes near the viewport.
		const reveal_observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						reveal_observer.unobserve(entry.target);
					}
				});
			},
			{
				threshold: 0.16,
				rootMargin: '0px 0px -8% 0px',
			}
		);

		// Observe each reveal candidate.
		reveal_nodes.forEach(function (node) {
			reveal_observer.observe(node);
		});
	});
})();
