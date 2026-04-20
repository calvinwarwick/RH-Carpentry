/**
 * Global theme script (mobile nav, small enhancements).
 */

function initMobileNav() {
	const toggle = document.querySelector('[data-rh-base-menu-toggle]');
	const nav = document.querySelector('[data-rh-base-menu]');
	if (!toggle || !nav) {
		return;
	}

	const setOpen = (open) => {
		nav.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
	};

	toggle.addEventListener('click', () => {
		setOpen(!nav.classList.contains('is-open'));
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			setOpen(false);
		}
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initMobileNav);
} else {
	initMobileNav();
}
