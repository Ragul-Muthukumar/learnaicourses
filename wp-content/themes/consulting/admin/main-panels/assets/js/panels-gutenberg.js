(function() {

	// Output upsell button
    const setupPanelGutenberg = () => {

		// Confirm panels sidebar exists
        const sidebarHeader = document.querySelector('.interface-complementary-area-header');

		// Exit early if panel sidebar doesn't exist
        if (!sidebarHeader || document.querySelector('.thinkup-panel-upgrade-gutenberg')) return;

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
        container.id        = 'thinkup-panel-upgrade-gutenberg';
        container.className = 'thinkup-panel-upgrade-gutenberg';

		// Create content for upsell
        container.innerHTML = `
			<p class = "block-title">Get ${themeName} Pro</p>
			<p class = "block-text">Build your site faster and easier with powerful theme options and dedicated premium support.</p>
			<a class = "block-link" href="https://www.thinkupthemes.com/themes/${themeName}" target="_blank">Upgrade Now</a>
        `;

        sidebarHeader.insertAdjacentElement('afterend', container);
    };

	// Watch for DOM changes and inject button when panels load
    const observer = new MutationObserver(setupPanelGutenberg);

	// Observe all new elements in the editor
    observer.observe(document.body, { childList: true, subtree: true });

	// Run once in case the panel is already present
    setupPanelGutenberg(); // Run once immediately

})();
