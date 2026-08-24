(function() {

    // Inject upsell box into Classic Editor sidebar
    const setupPanelClassic = () => {

        const sidebar = document.querySelector('#side-sortables');

        // Exit if sidebar not ready OR box already exists
        if (!sidebar || document.querySelector('#thinkup-panel-upgrade-classic')) return;

		// Get parent theme name
		let themeName = (typeof thinkupData !== 'undefined' && thinkupData.themeName) ? thinkupData.themeName : '';

		// Get theme name from body class if not passed in JS
		if( ! themeName ) {

			// Get body classes
			const bodyClasses = document.body.classList;

			// Find the class which contains the parent theme name
			bodyClasses.forEach( cls => {
				if ( cls.startsWith('theme-parent-') ) {

					// Extract parent theme name
					themeName = cls.replace('theme-parent-', '').trim().toLowerCase();
				}
			});
		}

		// Create upsell container
        const container = document.createElement('div');

		// Assign element tags
		container.id        = 'thinkup-panel-upgrade-classic';
		container.className = 'postbox thinkup-panel-upgrade-classic';

        container.innerHTML = `
			<p class = "block-title">Get ${themeName} Pro</p>
			<p class = "block-text">Build your site faster and easier with powerful theme options and dedicated premium support.</p>
			<a class = "block-link" href="https://www.thinkupthemes.com/themes/${themeName}" target="_blank">Upgrade Now</a>
        `;

        // Insert onto page
        sidebar.prepend(container);
    };

    // Watch for DOM changes
    const observer = new MutationObserver(setupPanelClassic);

    observer.observe(document.body, { childList: true, subtree: true });

    // Run once immediately
    setupPanelClassic();

})();
