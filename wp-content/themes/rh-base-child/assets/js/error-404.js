/**
 * 404 — gentle plank wobble on hover (optional delight).
 */
(function () {
	const scene = document.querySelector('.rh-error-404__scene');
	if (!scene) {
		return;
	}

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	const planks = scene.querySelectorAll('.rh-error-404__plank');
	planks.forEach((plank) => {
		plank.addEventListener('mouseenter', () => {
			plank.style.transition = 'transform 0.35s cubic-bezier(0.34, 1.46, 0.64, 1)';
			const tilt = plank.classList.contains('rh-error-404__plank--1')
				? 'rotate(-7deg) translateY(-4px)'
				: plank.classList.contains('rh-error-404__plank--3')
					? 'rotate(6deg) translateY(-4px)'
					: 'translateY(-6px) scale(1.06)';
			plank.style.transform = tilt;
		});
		plank.addEventListener('mouseleave', () => {
			plank.style.transition = 'transform 0.45s cubic-bezier(0.34, 1.46, 0.64, 1)';
			plank.style.transform = '';
		});
	});
})();
