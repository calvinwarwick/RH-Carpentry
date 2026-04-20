/**
 * Optional second bundle — enqueued only on templates/template-with-interactive.php
 */

function initDemoIsland() {
	const btn = document.querySelector('[data-rh-base-demo]');
	const status = document.querySelector('[data-rh-base-demo-status]');
	const wrap = document.querySelector('.rh-demo-island');
	if (!btn || !status || !wrap) {
		return;
	}

	const setState = (active) => {
		wrap.classList.toggle('is-active', active);
		status.textContent = active
			? 'Demo: active (loaded from rh-base-interactive.js)'
			: 'Demo: idle';
	};

	let active = false;
	btn.addEventListener('click', () => {
		active = !active;
		setState(active);
	});

	setState(false);
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initDemoIsland);
} else {
	initDemoIsland();
}
