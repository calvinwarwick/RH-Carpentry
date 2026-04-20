/**
 * Desktop custom cursor: translucent white ring, grows over links / controls.
 */
(function () {
	const mq = window.matchMedia('(hover: hover) and (pointer: fine)');
	if (!mq.matches) {
		return;
	}
	const heroInner = document.querySelector('.rh-hero-home__inner');
	if (!heroInner) {
		return;
	}
	const heroShell = heroInner.closest('.rh-hero-home');
	if (!heroShell) {
		return;
	}

	const interactive =
		'a[href], button, input[type="submit"], input[type="button"], input[type="reset"], label[for], [role="link"], [role="button"], summary, select, textarea';

	let el = document.querySelector('.rh-custom-cursor');
	if (!el) {
		el = document.createElement('div');
		el.className = 'rh-custom-cursor';
		el.setAttribute('aria-hidden', 'true');
		document.body.appendChild(el);
	}
	let shape = el.querySelector('.rh-custom-cursor__shape');
	if (!shape) {
		shape = document.createElement('div');
		shape.className = 'rh-custom-cursor__shape';
		el.appendChild(shape);
	}
	document.body.classList.add('rh-custom-cursor--on');
	el.classList.add('is-visible');
	let active = true;
	let wasInteractive = false;
	let rotationDeg = 0;

	const setActive = (isActive) => {
		active = isActive;
		document.body.classList.toggle('rh-custom-cursor--on', isActive);
		el.classList.toggle('is-visible', isActive);
		if (!isActive) {
			el.classList.remove('is-expanded', 'is-link');
		}
	};

	const setPos = (clientX, clientY) => {
		el.style.transform = `translate3d(${clientX}px, ${clientY}px, 0) translate(-50%, -50%)`;
	};

	const setExpanded = (target) => {
		const hit = target && typeof target.closest === 'function' && target.closest(interactive);
		el.classList.toggle('is-expanded', Boolean(hit));
		el.classList.toggle('is-link', Boolean(hit));
		if (hit !== wasInteractive) {
			rotationDeg += 45;
			shape.style.transform = `rotate(${rotationDeg}deg)`;
			wasInteractive = hit;
		}
	};

	document.addEventListener(
		'mousemove',
		(e) => {
			const inHeroInner = e.target && typeof e.target.closest === 'function' && e.target.closest('.rh-hero-home__inner');
			if (!inHeroInner) {
				setActive(false);
				return;
			}
			if (!active) {
				setActive(true);
			}
			setPos(e.clientX, e.clientY);
			setExpanded(e.target);
		},
		{ passive: true }
	);

	heroInner.addEventListener('mouseleave', () => {
		setActive(false);
		el.classList.add('is-hidden');
	});

	heroInner.addEventListener('mouseenter', () => {
		setActive(true);
		el.classList.remove('is-hidden');
	});

	mq.addEventListener('change', (e) => {
		if (!e.matches) {
			setActive(false);
			el.classList.remove('is-visible', 'is-expanded', 'is-hidden', 'is-pressed');
		} else {
			setActive(false);
		}
	});
})();
