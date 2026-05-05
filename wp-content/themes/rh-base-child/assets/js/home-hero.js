/**
 * Mobile navigation toggle (home hero).
 */
(function () {
	const toggles = document.querySelectorAll('[data-rh-hero-nav-toggle]');
	if (!toggles.length) {
		return;
	}

	const syncBodyClass = () => {
		const anyOpen = Array.from(document.querySelectorAll('[data-rh-hero-nav]')).some((n) =>
			n.classList.contains('is-open')
		);
		document.body.classList.toggle('rh-hero-nav-open', anyOpen);
	};

	const setNavOpen = (targetNav, open) => {
		document.querySelectorAll('[data-rh-hero-nav]').forEach((n) => {
			n.classList.toggle('is-open', open && n === targetNav);
		});
		document.querySelectorAll('[data-rh-hero-nav-toggle]').forEach((t) => {
			const id = t.getAttribute('aria-controls');
			const nav = id ? document.getElementById(id) : null;
			const isOpen = !!(nav && nav.classList.contains('is-open'));
			t.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
		syncBodyClass();
	};

	toggles.forEach((toggle) => {
		toggle.addEventListener('click', () => {
			const id = toggle.getAttribute('aria-controls');
			const nav = id ? document.getElementById(id) : null;
			if (!nav || !nav.matches('[data-rh-hero-nav]')) {
				return;
			}
			setNavOpen(nav, !nav.classList.contains('is-open'));
		});
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			if (document.body.classList.contains('rh-contact-overlay-open')) {
				return;
			}
			setNavOpen(null, false);
		}
	});
})();

/**
 * Home stats strip: count up on scroll, left→right; next stat starts when the previous count is 50% done; label letters after each count (below About).
 */
(function () {
	const strip = document.querySelector('.rh-home-stats-strip');
	if (!strip) {
		return;
	}

	const items = strip.querySelectorAll('[data-rh-stat-item]');
	if (!items.length) {
		return;
	}

	const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const DURATION_MS = 900;
	const LETTER_STAGGER_MS = 25;
	const LETTER_FADE_MS = 132;
	/** Delay before starting stat N+1: previous stat’s count-up is this far along (0.5 = 50%). */
	const NEXT_STAT_AT_COUNT_PROGRESS = 0.5;
	const NEXT_STAT_DELAY_MS = DURATION_MS * NEXT_STAT_AT_COUNT_PROGRESS;

	function formatNum(n, grouping) {
		const r = Math.round(n);
		if (grouping) {
			return new Intl.NumberFormat('en-GB', { maximumFractionDigits: 0 }).format(r);
		}
		return String(r);
	}

	function readConfig(el) {
		return {
			target: parseFloat(el.getAttribute('data-target') || '0', 10),
			prefix: el.getAttribute('data-prefix') || '',
			suffix: el.getAttribute('data-suffix') || '',
			grouping: el.getAttribute('data-group') === '1',
		};
	}

	function render(el, n) {
		const c = readConfig(el);
		el.textContent = c.prefix + formatNum(n, c.grouping) + c.suffix;
	}

	function finalizeStrip() {
		strip.classList.remove('rh-home-stats-strip--pending');
	}

	function prepareLabels() {
		strip.querySelectorAll('.rh-home-stats-strip__label').forEach((label) => {
			const sr = label.querySelector('.screen-reader-text');
			const charsEl = label.querySelector('.rh-home-stats-strip__label-chars');
			if (!sr || !charsEl || charsEl.dataset.rhPrepared === '1') {
				return;
			}
			charsEl.dataset.rhPrepared = '1';
			const text = sr.textContent || '';
			Array.from(text).forEach((ch) => {
				const span = document.createElement('span');
				span.className = 'rh-home-stats-strip__letter';
				if (ch === ' ') {
					span.classList.add('rh-home-stats-strip__letter--space');
					span.textContent = '\u00a0';
				} else {
					span.textContent = ch;
				}
				charsEl.appendChild(span);
			});
		});
	}

	function revealLabelLetters(labelWrapper, onComplete) {
		const letters = labelWrapper.querySelectorAll('.rh-home-stats-strip__letter');
		if (!letters.length) {
			onComplete();
			return;
		}
		letters.forEach((letter, i) => {
			window.setTimeout(() => letter.classList.add('is-in'), i * LETTER_STAGGER_MS);
		});
		const settleMs = (letters.length - 1) * LETTER_STAGGER_MS + LETTER_FADE_MS + 36;
		window.setTimeout(onComplete, settleMs);
	}

	function runReduced() {
		prepareLabels();
		items.forEach((item) => {
			const el = item.querySelector('[data-rh-stat-value]');
			const label = item.querySelector('.rh-home-stats-strip__label');
			if (!el) {
				return;
			}
			const c = readConfig(el);
			el.textContent = c.prefix + formatNum(c.target, c.grouping) + c.suffix;
			el.classList.add('is-started');
			if (label) {
				label.querySelectorAll('.rh-home-stats-strip__letter').forEach((letter) => letter.classList.add('is-in'));
			}
		});
		finalizeStrip();
	}

	function animateValue(el, onComplete) {
		const c = readConfig(el);
		el.classList.add('is-started');
		const startTime = performance.now();
		const tick = (now) => {
			const t = Math.min(1, (now - startTime) / DURATION_MS);
			const eased = 1 - Math.pow(1 - t, 3);
			render(el, c.target * eased);
			if (t < 1) {
				requestAnimationFrame(tick);
			} else {
				render(el, c.target);
				onComplete();
			}
		};
		requestAnimationFrame(tick);
	}

	function runOverlapping() {
		let finished = 0;
		const tryFinalize = () => {
			finished += 1;
			if (finished >= items.length) {
				finalizeStrip();
			}
		};

		items.forEach((item, index) => {
			window.setTimeout(() => {
				const el = item.querySelector('[data-rh-stat-value]');
				const label = item.querySelector('.rh-home-stats-strip__label');
				if (!el || !label) {
					tryFinalize();
					return;
				}
				animateValue(el, () => {
					revealLabelLetters(label, tryFinalize);
				});
			}, index * NEXT_STAT_DELAY_MS);
		});
	}

	const observer = new IntersectionObserver(
		(entries) => {
			const hit = entries.some((e) => e.isIntersecting);
			if (!hit) {
				return;
			}
			observer.disconnect();

			if (prefersReduced) {
				runReduced();
				return;
			}

			prepareLabels();
			runOverlapping();
		},
		{ root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
	);

	observer.observe(strip);
})();

/**
 * Testimonials carousel: auto-advance, pause control (ring progress), prev/next.
 */
(function () {
	const root = document.querySelector('[data-rh-testimonials-carousel]');
	if (!root) {
		return;
	}

	const viewport = root.querySelector('.rh-home-testimonials-carousel__viewport');
	const slides = Array.from(root.querySelectorAll('[data-rh-testimonial-slide]'));
	const pauseBtn = root.querySelector('[data-rh-testimonial-autoplay-toggle]');

	if (!slides.length || !viewport) {
		return;
	}

	const multi = slides.length > 1;
	const prefersReduced =
		typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const mqWide =
		typeof window.matchMedia === 'function' ? window.matchMedia('(min-width: 900px)') : null;
	const intervalMs = Math.max(3000, parseInt(String(root.getAttribute('data-interval') || '5000'), 10) || 5000);

	root.style.setProperty('--rh-testimonial-interval', `${intervalMs}ms`);

	let index = slides.findIndex((el) => el.classList.contains('is-active'));
	if (index < 0) {
		index = 0;
	}

	let timerId = null;
	/** When a slide-advance timeout is scheduled, wall-clock time when it should fire (performance.now()). */
	let autoplayDeadline = null;
	/** Remaining ms for the current slide when user pauses (used on resume so the timer does not reset). */
	let pausedRemainingMs = intervalMs;
	let isFirstGo = true;
	let isPaused = false;
	/** Ignore debounced scroll sync while scrollTo() from goTo() is in flight (avoids wrong index mid-smooth-scroll / snap settle). */
	let suppressScrollSync = false;
	let scrollUnsuppressTimer = null;
	let scrollEndFallbackTimer = null;
	/** After goTo(), do not let closestSlideIndex() override `index` until scroll + snap have settled. */
	let navIntentUntil = 0;

	function clearSchedule() {
		if (timerId !== null) {
			window.clearTimeout(timerId);
			timerId = null;
		}
		autoplayDeadline = null;
	}

	function updateEdgeState() {
		root.setAttribute('data-at-start', index === 0 ? 'true' : 'false');
		root.setAttribute('data-at-end', index === slides.length - 1 ? 'true' : 'false');

		const sw = viewport.scrollWidth;
		const cw = viewport.clientWidth;
		const maxScroll = Math.max(0, sw - cw);
		const sl = viewport.scrollLeft;
		const hasOverflow = maxScroll > 1;
		root.setAttribute('data-scroll-overflow', hasOverflow ? 'true' : 'false');

		/* Masks: only when content is clipped on that side (scroll-based). Right edge uses a small inset; left uses a larger inset so the left feather does not appear while the first card is still largely visible. At max scroll, always allow the left hint so the last slide still shows it even when the scroll range is short. */
		const maskRightPx = 10;
		const maskLeftPx = 40;
		const atScrollEnd = maxScroll > 0 && sl >= maxScroll - 1;
		const canMaskLeft = hasOverflow && (sl > maskLeftPx || atScrollEnd);
		const canMaskRight = hasOverflow && sl < maxScroll - maskRightPx;
		root.setAttribute('data-mask-left', canMaskLeft ? 'true' : 'false');
		root.setAttribute('data-mask-right', canMaskRight ? 'true' : 'false');
	}

	function restartProgress() {
		if (!pauseBtn || prefersReduced || !multi) {
			return;
		}
		if (isPaused) {
			return;
		}
		pauseBtn.classList.remove('is-timing');
		const prog = pauseBtn.querySelector('.rh-home-testimonials-carousel__pause-progress');
		if (prog) {
			void prog.getBoundingClientRect();
		}
		pauseBtn.classList.add('is-timing');
	}

	function syncActiveUi() {
		slides.forEach((el, j) => {
			el.classList.toggle('is-active', j === index);
		});
		restartProgress();
		updateEdgeState();
	}

	/** Slide whose centre is nearest the viewport centre (best match while user scrolls). */
	function closestSlideIndex() {
		const vr = viewport.getBoundingClientRect();
		const mid = vr.left + vr.width / 2;
		let bestI = 0;
		let bestD = Infinity;
		slides.forEach((slide, i) => {
			const r = slide.getBoundingClientRect();
			const c = r.left + r.width / 2;
			const d = Math.abs(c - mid);
			if (d < bestD) {
				bestD = d;
				bestI = i;
			}
		});
		return bestI;
	}

	let scrollSyncTimer = null;

	function onScrollSettled() {
		const ci = closestSlideIndex();
		if (ci !== index) {
			if (performance.now() < navIntentUntil) {
				updateEdgeState();
				return;
			}
			index = ci;
			syncActiveUi();
			scheduleNext();
		} else {
			updateEdgeState();
		}
	}

	/**
	 * Scroll only the carousel viewport — never use scrollIntoView() on slides (it scrolls the page too).
	 * Centres the active slide when possible (track has no left padding; scrollLeft never < 0).
	 */
	function clearScrollUnsuppress() {
		if (scrollUnsuppressTimer !== null) {
			window.clearTimeout(scrollUnsuppressTimer);
			scrollUnsuppressTimer = null;
		}
		if (scrollEndFallbackTimer !== null) {
			window.clearTimeout(scrollEndFallbackTimer);
			scrollEndFallbackTimer = null;
		}
	}

	function scrollActiveSlideIntoView() {
		const el = slides[index];
		if (!el || !viewport) {
			return;
		}

		const slideRect = el.getBoundingClientRect();
		const viewRect = viewport.getBoundingClientRect();
		const slideMid = slideRect.left + slideRect.width / 2;
		const viewMid = viewRect.left + viewRect.width / 2;
		const delta = slideMid - viewMid;

		const maxScroll = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
		const nextLeft = Math.max(0, Math.min(viewport.scrollLeft + delta, maxScroll));
		const behavior = prefersReduced || isFirstGo ? 'auto' : 'smooth';

		suppressScrollSync = true;
		window.clearTimeout(scrollSyncTimer);
		scrollSyncTimer = null;
		clearScrollUnsuppress();

		viewport.scrollTo({
			left: nextLeft,
			behavior,
		});

		let settleDone = false;
		const afterScrollEnd = () => {
			if (settleDone) {
				return;
			}
			settleDone = true;
			if (scrollEndFallbackTimer !== null) {
				window.clearTimeout(scrollEndFallbackTimer);
				scrollEndFallbackTimer = null;
			}
			scrollUnsuppressTimer = window.setTimeout(() => {
				scrollUnsuppressTimer = null;
				suppressScrollSync = false;
			}, 80);
		};

		if (behavior === 'auto') {
			window.requestAnimationFrame(() => {
				window.requestAnimationFrame(afterScrollEnd);
			});
			return;
		}

		const onScrollEnd = () => {
			viewport.removeEventListener('scrollend', onScrollEnd);
			if (scrollEndFallbackTimer !== null) {
				window.clearTimeout(scrollEndFallbackTimer);
				scrollEndFallbackTimer = null;
			}
			afterScrollEnd();
		};

		viewport.addEventListener('scrollend', onScrollEnd, { once: true });
		scrollEndFallbackTimer = window.setTimeout(() => {
			viewport.removeEventListener('scrollend', onScrollEnd);
			afterScrollEnd();
		}, 700);
	}

	function goTo(nextIndex) {
		index = (nextIndex + slides.length) % slides.length;
		/* Blocks spurious closestSlideIndex() overrides from snap/settle after scrollTo; cleared on user scroll. */
		navIntentUntil = performance.now() + 520;

		syncActiveUi();

		scrollActiveSlideIntoView();
		isFirstGo = false;

		scheduleNext();
	}

	function scheduleNext(delayMs) {
		const delay = typeof delayMs === 'number' ? delayMs : intervalMs;
		clearSchedule();
		if (!multi || prefersReduced || document.hidden || isPaused) {
			return;
		}
		const ms = Math.max(0, delay);
		autoplayDeadline = performance.now() + ms;
		timerId = window.setTimeout(() => {
			timerId = null;
			autoplayDeadline = null;
			goTo(index + 1);
		}, ms);
	}

	if (pauseBtn && multi) {
		const labelPause = pauseBtn.getAttribute('data-label-pause') || pauseBtn.getAttribute('aria-label') || 'Pause';
		const labelPlay = pauseBtn.getAttribute('data-label-play') || 'Play';

		pauseBtn.addEventListener('click', () => {
			if (!isPaused) {
				pausedRemainingMs =
					autoplayDeadline !== null ? Math.max(0, autoplayDeadline - performance.now()) : intervalMs;
			}
			isPaused = !isPaused;
			pauseBtn.classList.toggle('is-paused', isPaused);
			pauseBtn.setAttribute('aria-pressed', isPaused ? 'true' : 'false');
			const icon = pauseBtn.querySelector('.rh-home-testimonials-carousel__pause-icon-wrap i');
			if (icon) {
				icon.className = isPaused ? 'fa-solid fa-play' : 'fa-solid fa-pause';
			}
			pauseBtn.setAttribute('aria-label', isPaused ? labelPlay : labelPause);
			if (isPaused) {
				clearSchedule();
				/* Keep .is-timing so the ring stays at the current progress (CSS animation-play-state: paused). */
			} else {
				scheduleNext(pausedRemainingMs);
			}
		});
	}

	const prevBtn = root.querySelector('[data-rh-testimonial-prev]');
	const nextBtn = root.querySelector('[data-rh-testimonial-next]');
	if (prevBtn) {
		prevBtn.addEventListener('click', () => goTo(index - 1));
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', () => goTo(index + 1));
	}

	root.addEventListener('click', (e) => {
		const article = e.target.closest('[data-rh-testimonial-slide]');
		if (!article || !root.contains(article)) {
			return;
		}
		const raw = article.getAttribute('data-rh-testimonial-index');
		if (raw === null) {
			return;
		}
		const i = parseInt(raw, 10);
		if (Number.isNaN(i)) {
			return;
		}
		goTo(i);
	});

	viewport.addEventListener(
		'pointerdown',
		() => {
			navIntentUntil = 0;
		},
		{ passive: true }
	);

	viewport.addEventListener(
		'wheel',
		() => {
			navIntentUntil = 0;
		},
		{ passive: true }
	);

	viewport.addEventListener(
		'scroll',
		() => {
			updateEdgeState();
			if (suppressScrollSync) {
				return;
			}
			window.clearTimeout(scrollSyncTimer);
			scrollSyncTimer = window.setTimeout(onScrollSettled, 140);
		},
		{ passive: true }
	);

	viewport.addEventListener('keydown', (e) => {
		if (e.key === 'ArrowLeft') {
			e.preventDefault();
			goTo(index - 1);
		} else if (e.key === 'ArrowRight') {
			e.preventDefault();
			goTo(index + 1);
		}
	});

	document.addEventListener('visibilitychange', () => {
		if (document.hidden) {
			clearSchedule();
			/* Do not remove .is-timing while user-paused — ring position must stay frozen. */
			if (pauseBtn && !isPaused) {
				pauseBtn.classList.remove('is-timing');
			}
		} else if (!prefersReduced && multi && !isPaused) {
			restartProgress();
			scheduleNext();
		}
	});

	if (mqWide) {
		mqWide.addEventListener('change', () => {
			goTo(index);
		});
	}

	const track = root.querySelector('.rh-home-testimonials-carousel__track');
	if (typeof ResizeObserver !== 'undefined') {
		const ro = new ResizeObserver(() => {
			updateEdgeState();
		});
		ro.observe(viewport);
		if (track) {
			ro.observe(track);
		}
	}

	let winResizeTimer = null;
	window.addEventListener('resize', () => {
		window.clearTimeout(winResizeTimer);
		winResizeTimer = window.setTimeout(() => {
			winResizeTimer = null;
			updateEdgeState();
		}, 100);
	});

	goTo(index);

	if (typeof document !== 'undefined' && document.fonts && typeof document.fonts.ready !== 'undefined') {
		document.fonts.ready.then(() => {
			updateEdgeState();
		});
	}
	window.requestAnimationFrame(() => {
		updateEdgeState();
	});
})();

/**
 * Projects carousel: auto-advance, pause control (ring progress), prev/next.
 */
(function () {
	const root = document.querySelector('[data-rh-projects-carousel]');
	if (!root) {
		return;
	}

	const viewport = root.querySelector('.rh-home-projects-carousel__viewport');
	const slides = Array.from(root.querySelectorAll('[data-rh-project-slide]'));
	const pauseBtn = root.querySelector('[data-rh-project-autoplay-toggle]');

	if (!slides.length || !viewport) {
		return;
	}

	const multi = slides.length > 1;
	const prefersReduced =
		typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const mqWide =
		typeof window.matchMedia === 'function' ? window.matchMedia('(min-width: 900px)') : null;
	const intervalMs = Math.max(3000, parseInt(String(root.getAttribute('data-interval') || '5000'), 10) || 5000);

	root.style.setProperty('--rh-project-interval', `${intervalMs}ms`);

	let index = slides.findIndex((el) => el.classList.contains('is-active'));
	if (index < 0) {
		index = 0;
	}

	let timerId = null;
	/** When a slide-advance timeout is scheduled, wall-clock time when it should fire (performance.now()). */
	let autoplayDeadline = null;
	/** Remaining ms for the current slide when user pauses (used on resume so the timer does not reset). */
	let pausedRemainingMs = intervalMs;
	let isFirstGo = true;
	let isPaused = false;
	/** Ignore debounced scroll sync while scrollTo() from goTo() is in flight (avoids wrong index mid-smooth-scroll / snap settle). */
	let suppressScrollSync = false;
	let scrollUnsuppressTimer = null;
	let scrollEndFallbackTimer = null;
	/** After goTo(), do not let scroll sync override `index` until scroll + snap have settled. */
	let navIntentUntil = 0;

	function clearSchedule() {
		if (timerId !== null) {
			window.clearTimeout(timerId);
			timerId = null;
		}
		autoplayDeadline = null;
	}

	function updateEdgeState() {
		root.setAttribute('data-at-start', index === 0 ? 'true' : 'false');
		root.setAttribute('data-at-end', index === slides.length - 1 ? 'true' : 'false');

		const sw = viewport.scrollWidth;
		const cw = viewport.clientWidth;
		const maxScroll = Math.max(0, sw - cw);
		const sl = viewport.scrollLeft;
		const hasOverflow = maxScroll > 1;
		root.setAttribute('data-scroll-overflow', hasOverflow ? 'true' : 'false');

		/* Masks: only when content is clipped on that side (scroll-based). Right edge uses a small inset; left uses a larger inset so the left feather does not appear while the first card is still largely visible. At max scroll, always allow the left hint so the last slide still shows it even when the scroll range is short. */
		const maskRightPx = 10;
		const maskLeftPx = 40;
		const atScrollEnd = maxScroll > 0 && sl >= maxScroll - 1;
		const canMaskLeft = hasOverflow && (sl > maskLeftPx || atScrollEnd);
		const canMaskRight = hasOverflow && sl < maxScroll - maskRightPx;
		root.setAttribute('data-mask-left', canMaskLeft ? 'true' : 'false');
		root.setAttribute('data-mask-right', canMaskRight ? 'true' : 'false');
	}

	function restartProgress() {
		if (!pauseBtn || prefersReduced || !multi) {
			return;
		}
		if (isPaused) {
			return;
		}
		pauseBtn.classList.remove('is-timing');
		const prog = pauseBtn.querySelector('.rh-home-projects-carousel__pause-progress');
		if (prog) {
			void prog.getBoundingClientRect();
		}
		pauseBtn.classList.add('is-timing');
	}

	function syncActiveUi() {
		slides.forEach((el, j) => {
			el.classList.toggle('is-active', j === index);
		});
		restartProgress();
		updateEdgeState();
	}

	/**
	 * Map scrollLeft to slide index: nearest ideal scroll position per slide (same math as scrollActiveSlideIntoView).
	 * Avoids wrong picks when multiple cards are partly visible (viewport-centre vs slide-centre distance).
	 */
	function indexFromScrollLayout() {
		const maxScroll = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
		const sl = viewport.scrollLeft;
		const vw = viewport.clientWidth;
		let bestI = 0;
		let bestD = Infinity;
		slides.forEach((slide, i) => {
			const idealLeft = slide.offsetLeft + slide.offsetWidth / 2 - vw / 2;
			const clamped = Math.max(0, Math.min(idealLeft, maxScroll));
			const d = Math.abs(sl - clamped);
			if (d < bestD) {
				bestD = d;
				bestI = i;
			}
		});
		return bestI;
	}

	let scrollSyncTimer = null;

	function onScrollSettled() {
		const ci = indexFromScrollLayout();
		if (ci !== index) {
			if (performance.now() < navIntentUntil) {
				updateEdgeState();
				return;
			}
			index = ci;
			syncActiveUi();
			scheduleNext();
		} else {
			updateEdgeState();
		}
	}

	/**
	 * Scroll only the carousel viewport — never use scrollIntoView() on slides (it scrolls the page too).
	 * Centres the active slide when possible (track has no left padding; scrollLeft never < 0).
	 */
	function clearScrollUnsuppress() {
		if (scrollUnsuppressTimer !== null) {
			window.clearTimeout(scrollUnsuppressTimer);
			scrollUnsuppressTimer = null;
		}
		if (scrollEndFallbackTimer !== null) {
			window.clearTimeout(scrollEndFallbackTimer);
			scrollEndFallbackTimer = null;
		}
	}

	function scrollActiveSlideIntoView() {
		const el = slides[index];
		if (!el || !viewport) {
			return;
		}

		const slideRect = el.getBoundingClientRect();
		const viewRect = viewport.getBoundingClientRect();
		const slideMid = slideRect.left + slideRect.width / 2;
		const viewMid = viewRect.left + viewRect.width / 2;
		const delta = slideMid - viewMid;

		const maxScroll = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
		const startLeft = viewport.scrollLeft;
		const nextLeft = Math.max(0, Math.min(startLeft + delta, maxScroll));
		const scrollDist = Math.abs(nextLeft - startLeft);
		/* Long smooth scroll + mandatory snap can settle on an intermediate slide; use instant scroll for big jumps. */
		const largeJump = scrollDist > viewport.clientWidth * 0.4;
		const behavior = prefersReduced || isFirstGo || largeJump ? 'auto' : 'smooth';
		/* Extend nav intent so scroll-snap settle cannot fire sync before goTo’s window (set in goTo). */
		const extend = behavior === 'auto' ? 900 : 2200;
		navIntentUntil = Math.max(navIntentUntil, performance.now() + extend);

		suppressScrollSync = true;
		window.clearTimeout(scrollSyncTimer);
		scrollSyncTimer = null;
		clearScrollUnsuppress();

		viewport.scrollTo({
			left: nextLeft,
			behavior,
		});

		const unsuppressMs = behavior === 'auto' ? 200 : 320;

		let settleDone = false;
		const afterScrollEnd = () => {
			if (settleDone) {
				return;
			}
			settleDone = true;
			if (scrollEndFallbackTimer !== null) {
				window.clearTimeout(scrollEndFallbackTimer);
				scrollEndFallbackTimer = null;
			}
			scrollUnsuppressTimer = window.setTimeout(() => {
				scrollUnsuppressTimer = null;
				suppressScrollSync = false;
			}, unsuppressMs);
		};

		if (behavior === 'auto') {
			window.requestAnimationFrame(() => {
				window.requestAnimationFrame(afterScrollEnd);
			});
			return;
		}

		const onScrollEnd = () => {
			viewport.removeEventListener('scrollend', onScrollEnd);
			if (scrollEndFallbackTimer !== null) {
				window.clearTimeout(scrollEndFallbackTimer);
				scrollEndFallbackTimer = null;
			}
			afterScrollEnd();
		};

		viewport.addEventListener('scrollend', onScrollEnd, { once: true });
		scrollEndFallbackTimer = window.setTimeout(() => {
			viewport.removeEventListener('scrollend', onScrollEnd);
			afterScrollEnd();
		}, 900);
	}

	function goTo(nextIndex) {
		index = (nextIndex + slides.length) % slides.length;
		/* Block scroll-sync until snap settles; subsumes debounce + snap after programmatic scroll. */
		navIntentUntil = performance.now() + 2000;

		syncActiveUi();

		scrollActiveSlideIntoView();
		isFirstGo = false;

		scheduleNext();
	}

	function scheduleNext(delayMs) {
		const delay = typeof delayMs === 'number' ? delayMs : intervalMs;
		clearSchedule();
		if (!multi || prefersReduced || document.hidden || isPaused) {
			return;
		}
		const ms = Math.max(0, delay);
		autoplayDeadline = performance.now() + ms;
		timerId = window.setTimeout(() => {
			timerId = null;
			autoplayDeadline = null;
			goTo(index + 1);
		}, ms);
	}

	if (pauseBtn && multi) {
		const labelPause = pauseBtn.getAttribute('data-label-pause') || pauseBtn.getAttribute('aria-label') || 'Pause';
		const labelPlay = pauseBtn.getAttribute('data-label-play') || 'Play';

		pauseBtn.addEventListener('click', () => {
			if (!isPaused) {
				pausedRemainingMs =
					autoplayDeadline !== null ? Math.max(0, autoplayDeadline - performance.now()) : intervalMs;
			}
			isPaused = !isPaused;
			pauseBtn.classList.toggle('is-paused', isPaused);
			pauseBtn.setAttribute('aria-pressed', isPaused ? 'true' : 'false');
			const icon = pauseBtn.querySelector('.rh-home-projects-carousel__pause-icon-wrap i');
			if (icon) {
				icon.className = isPaused ? 'fa-solid fa-play' : 'fa-solid fa-pause';
			}
			pauseBtn.setAttribute('aria-label', isPaused ? labelPlay : labelPause);
			if (isPaused) {
				clearSchedule();
				/* Keep .is-timing so the ring stays at the current progress (CSS animation-play-state: paused). */
			} else {
				scheduleNext(pausedRemainingMs);
			}
		});
	}

	const prevBtn = root.querySelector('[data-rh-project-prev]');
	const nextBtn = root.querySelector('[data-rh-project-next]');
	if (prevBtn) {
		prevBtn.addEventListener('click', () => goTo(index - 1));
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', () => goTo(index + 1));
	}

	root.addEventListener('click', (e) => {
		const card = e.target.closest('[data-rh-project-slide]');
		if (!card || !root.contains(card)) {
			return;
		}
		const raw = card.getAttribute('data-rh-project-index');
		if (raw === null) {
			return;
		}
		const i = parseInt(raw, 10);
		if (Number.isNaN(i)) {
			return;
		}
		goTo(i);
	});

	viewport.addEventListener(
		'wheel',
		() => {
			navIntentUntil = 0;
		},
		{ passive: true }
	);

	viewport.addEventListener(
		'scroll',
		() => {
			if (!suppressScrollSync) {
				navIntentUntil = 0;
			}
			updateEdgeState();
			if (suppressScrollSync) {
				return;
			}
			window.clearTimeout(scrollSyncTimer);
			scrollSyncTimer = window.setTimeout(onScrollSettled, 140);
		},
		{ passive: true }
	);

	viewport.addEventListener('keydown', (e) => {
		if (e.key === 'ArrowLeft') {
			e.preventDefault();
			goTo(index - 1);
		} else if (e.key === 'ArrowRight') {
			e.preventDefault();
			goTo(index + 1);
		}
	});

	document.addEventListener('visibilitychange', () => {
		if (document.hidden) {
			clearSchedule();
			/* Do not remove .is-timing while user-paused — ring position must stay frozen. */
			if (pauseBtn && !isPaused) {
				pauseBtn.classList.remove('is-timing');
			}
		} else if (!prefersReduced && multi && !isPaused) {
			restartProgress();
			scheduleNext();
		}
	});

	if (mqWide) {
		mqWide.addEventListener('change', () => {
			goTo(index);
		});
	}

	const track = root.querySelector('.rh-home-projects-carousel__track');
	if (typeof ResizeObserver !== 'undefined') {
		const ro = new ResizeObserver(() => {
			updateEdgeState();
		});
		ro.observe(viewport);
		if (track) {
			ro.observe(track);
		}
	}

	let winResizeTimer = null;
	window.addEventListener('resize', () => {
		window.clearTimeout(winResizeTimer);
		winResizeTimer = window.setTimeout(() => {
			winResizeTimer = null;
			updateEdgeState();
		}, 100);
	});

	goTo(index);

	if (typeof document !== 'undefined' && document.fonts && typeof document.fonts.ready !== 'undefined') {
		document.fonts.ready.then(() => {
			updateEdgeState();
		});
	}
	window.requestAnimationFrame(() => {
		updateEdgeState();
	});
})();

/**
 * Full-screen #contact overlay: hash, close control, Escape, scroll lock.
 */
(function () {
	const overlay = document.querySelector('[data-rh-contact-overlay]');
	if (!overlay) {
		return;
	}

	const HERO_OUT_MS = 450;
	/* After contact hero-in + earlier form stagger; keep in sync with --rh-contact-content-t0 */
	const FOCUS_IN_MS = 2400;
	let closeTimer = null;
	let focusTimer = null;

	const finishClose = () => {
		closeTimer = null;
		overlay.classList.remove('is-closing');
		overlay.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('rh-contact-overlay-open');
		document.body.style.overflow = '';
	};

	const animateClose = () => {
		window.clearTimeout(focusTimer);
		focusTimer = null;
		if (!overlay.classList.contains('is-open')) {
			return;
		}
		overlay.classList.remove('is-open');
		overlay.classList.add('is-closing');
		window.clearTimeout(closeTimer);
		closeTimer = window.setTimeout(finishClose, HERO_OUT_MS);
	};

	const setOpen = (open) => {
		if (open) {
			window.clearTimeout(focusTimer);
			focusTimer = null;
			window.clearTimeout(closeTimer);
			closeTimer = null;
			overlay.classList.remove('is-closing');
			overlay.classList.add('is-open');
			overlay.setAttribute('aria-hidden', 'false');
			document.body.classList.add('rh-contact-overlay-open');
			document.body.style.overflow = 'hidden';
			return;
		}
		overlay.classList.remove('is-open');
		if (!overlay.classList.contains('is-closing')) {
			overlay.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('rh-contact-overlay-open');
			document.body.style.overflow = '';
		}
	};

	const syncFromLocation = () => {
		const open = window.location.hash === '#contact';
		if (open) {
			setOpen(true);
			window.clearTimeout(focusTimer);
			focusTimer = window.setTimeout(() => {
				focusTimer = null;
				const focusable = overlay.querySelector(
					'button[data-rh-contact-close], input:not([type="hidden"]), textarea, select, a[href]'
				);
				if (focusable && typeof focusable.focus === 'function') {
					focusable.focus();
				}
			}, FOCUS_IN_MS);
			return;
		}
		animateClose();
	};

	const closeOverlay = () => {
		try {
			const u = new URL(window.location.href);
			u.hash = '';
			u.searchParams.delete('contact');
			const q = u.searchParams.toString();
			const next = u.pathname + (q ? '?' + q : '');
			history.replaceState(null, '', next || '/');
		} catch {
			history.replaceState(null, '', window.location.pathname);
		}
		animateClose();
	};

	window.addEventListener('hashchange', syncFromLocation);
	window.addEventListener('pageshow', syncFromLocation);
	document.addEventListener('DOMContentLoaded', syncFromLocation);

	overlay.querySelectorAll('[data-rh-contact-close]').forEach((btn) => {
		btn.addEventListener('click', () => {
			closeOverlay();
		});
	});

	document.addEventListener(
		'keydown',
		(e) => {
			if (e.key !== 'Escape') {
				return;
			}
			if (overlay.classList.contains('is-closing')) {
				window.clearTimeout(closeTimer);
				finishClose();
				e.preventDefault();
				return;
			}
			if (!overlay.classList.contains('is-open')) {
				return;
			}
			e.preventDefault();
			closeOverlay();
		},
		true
	);
})();

/**
 * Home hero: per-line link-style wipe when title/lede wrap to multiple lines (front page).
 */
(function () {
	if (!document.body.classList.contains('rh-carpentry-home')) {
		return;
	}

	const root = document.querySelector('.rh-home-hero');
	const copy = root && root.querySelector('.rh-hero-copy');
	const title = copy && copy.querySelector('.rh-hero-title');
	const lede = copy && copy.querySelector('.rh-hero-lede');
	if (!root || !copy || !title || !lede) {
		return;
	}

	const prefersReduced =
		typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (prefersReduced) {
		return;
	}

	const TITLE_T0 = 1.16;
	const LINE_STAGGER = 0.18;
	const LEDE_AFTER_TITLE = 0.06;
	const BTN_AFTER_LEDE = 0.36;
	const BTN_STAGGER = 0.14;
	const WIPE_S = 0.92;
	const LINE_TOP_THRESHOLD = 6;

	function ensurePlain(el, dataKey) {
		const stored = (el.dataset[dataKey] || '').trim();
		if (stored) {
			return stored;
		}
		const t = (el.textContent || '').replace(/\s+/g, ' ').trim();
		el.dataset[dataKey] = t;
		return t;
	}

	function splitIntoLines(el, lineClass, plain) {
		const text = plain.replace(/\s+/g, ' ').trim();
		if (!text) {
			el.textContent = '';
			return 0;
		}

		const words = text.split(/\s+/);
		el.textContent = '';
		const frag = document.createDocumentFragment();
		const wordSpans = [];
		words.forEach((w, i) => {
			const s = document.createElement('span');
			s.className = 'rh-hero-copy-word';
			s.textContent = w;
			frag.appendChild(s);
			if (i < words.length - 1) {
				frag.appendChild(document.createTextNode(' '));
			}
			wordSpans.push(s);
		});
		el.appendChild(frag);

		const lineGroups = [];
		let group = [];
		let lineTop = null;

		wordSpans.forEach((span) => {
			const top = span.getBoundingClientRect().top;
			if (!group.length) {
				group.push(span);
				lineTop = top;
				return;
			}
			if (Math.abs(top - lineTop) <= LINE_TOP_THRESHOLD) {
				group.push(span);
			} else {
				lineGroups.push(group);
				group = [span];
				lineTop = top;
			}
		});
		if (group.length) {
			lineGroups.push(group);
		}

		el.textContent = '';
		lineGroups.forEach((group, li) => {
			const line = document.createElement('span');
			line.className = lineClass;
			line.style.setProperty('--rh-hero-line-i', String(li));
			line.textContent = group.map((n) => n.textContent).join(' ');
			el.appendChild(line);
		});

		return lineGroups.length;
	}

	let settleTimer = 0;
	let resizeTimer = 0;

	function applyTiming(titleLines, ledeLines) {
		window.clearTimeout(settleTimer);
		const tl = Math.max(1, titleLines);
		const ml = Math.max(0, ledeLines);

		const ledeStart = TITLE_T0 + tl * LINE_STAGGER + LEDE_AFTER_TITLE;
		let btn1;
		if (ml > 0) {
			btn1 = ledeStart + (ml - 1) * LINE_STAGGER + BTN_AFTER_LEDE;
		} else {
			btn1 = TITLE_T0 + (tl - 1) * LINE_STAGGER + BTN_AFTER_LEDE;
		}
		const btn2 = btn1 + BTN_STAGGER;

		copy.style.setProperty('--rh-hero-title-line-count', String(Math.max(1, titleLines)));
		copy.style.setProperty('--rh-hero-lede-line-count', String(ml));
		copy.style.setProperty('--rh-hero-btn-1-delay', `${btn1}s`);
		copy.style.setProperty('--rh-hero-btn-2-delay', `${btn2}s`);

		const lastLineStart =
			ml > 0 ? ledeStart + (ml - 1) * LINE_STAGGER : TITLE_T0 + (tl - 1) * LINE_STAGGER;
		settleTimer = window.setTimeout(() => {
			copy.classList.add('rh-hero-lines-settled');
		}, (lastLineStart + WIPE_S) * 1000 + 180);
	}

	function run() {
		const titlePlain = ensurePlain(title, 'rhHeroTitlePlain');
		const ledePlain = ensurePlain(lede, 'rhHeroLedePlain');

		let n = 0;
		let m = 0;

		if (titlePlain) {
			title.classList.add('rh-hero-title--lines-applied');
			n = splitIntoLines(title, 'rh-hero-title-line', titlePlain);
		} else {
			title.textContent = '';
			title.classList.remove('rh-hero-title--lines-applied');
		}

		if (ledePlain) {
			lede.classList.add('rh-hero-lede--lines-applied');
			m = splitIntoLines(lede, 'rh-hero-lede-line', ledePlain);
		} else {
			lede.textContent = '';
			lede.classList.remove('rh-hero-lede--lines-applied');
		}

		if (n === 0 && titlePlain) {
			title.textContent = titlePlain;
			title.classList.remove('rh-hero-title--lines-applied');
		}
		if (m === 0 && ledePlain) {
			lede.textContent = ledePlain;
			lede.classList.remove('rh-hero-lede--lines-applied');
		}

		if (
			!title.classList.contains('rh-hero-title--lines-applied') &&
			!lede.classList.contains('rh-hero-lede--lines-applied')
		) {
			return;
		}

		applyTiming(n || 1, m);
	}

	function scheduleRun() {
		window.requestAnimationFrame(() => {
			window.requestAnimationFrame(run);
		});
	}

	function onResize() {
		if (!copy.classList.contains('rh-hero-lines-settled')) {
			return;
		}
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(() => {
			window.clearTimeout(settleTimer);
			const tp = title.dataset.rhHeroTitlePlain || '';
			const lp = lede.dataset.rhHeroLedePlain || '';
			title.classList.remove('rh-hero-title--lines-applied');
			lede.classList.remove('rh-hero-lede--lines-applied');
			title.textContent = tp;
			lede.textContent = lp;
			scheduleRun();
		}, 120);
	}

	function bootstrap() {
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(scheduleRun).catch(scheduleRun);
		} else {
			scheduleRun();
		}
	}

	window.addEventListener('resize', onResize, { passive: true });

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bootstrap);
	} else {
		bootstrap();
	}
})();

/**
 * Homepage sections/footer: fade/slide in when entering viewport.
 */
(function () {
	if (!document.body.classList.contains('rh-carpentry-home')) {
		return;
	}

	const targets = Array.from(
		document.querySelectorAll(
			'.site-main--front > .rh-home-section, .site-main--front > .rh-bento-page, body.rh-carpentry-home .rh-site-footer'
		)
	);
	if (!targets.length) {
		return;
	}

	const prefersReduced =
		typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	targets.forEach((el) => {
		el.setAttribute('data-rh-reveal', '');
	});

	if (prefersReduced || typeof IntersectionObserver === 'undefined') {
		targets.forEach((el) => el.classList.add('is-inview'));
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}
				entry.target.classList.add('is-inview');
				observer.unobserve(entry.target);
			});
		},
		{ root: null, rootMargin: '0px 0px -10% 0px', threshold: 0.16 }
	);

	targets.forEach((el, index) => {
		if (index <= 1) {
			/* Avoid hiding the first visible bands on initial paint. */
			el.classList.add('is-inview');
			return;
		}
		observer.observe(el);
	});
})();
