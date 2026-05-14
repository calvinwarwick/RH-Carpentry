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
	const LABEL_REVEAL_MS = 920;
	/** Start label swipe before count completes (0.8 = 80% through count). */
	const LABEL_REVEAL_AT_COUNT_PROGRESS = 0.8;
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
			charsEl.textContent = text;
		});
	}

	function revealLabel(labelWrapper, onComplete) {
		const chars = labelWrapper.querySelector('.rh-home-stats-strip__label-chars');
		if (!chars || !(chars.textContent || '').trim()) {
			onComplete();
			return;
		}
		labelWrapper.classList.add('is-in');
		window.setTimeout(onComplete, LABEL_REVEAL_MS + 36);
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
				label.classList.add('is-in');
			}
		});
		finalizeStrip();
	}

	function animateValue(el, onLabelStart, onComplete) {
		const c = readConfig(el);
		el.classList.add('is-started');
		const startTime = performance.now();
		let labelStarted = false;
		const tick = (now) => {
			const t = Math.min(1, (now - startTime) / DURATION_MS);
			const eased = 1 - Math.pow(1 - t, 3);
			render(el, c.target * eased);
			if (!labelStarted && t >= LABEL_REVEAL_AT_COUNT_PROGRESS) {
				labelStarted = true;
				onLabelStart();
			}
			if (t < 1) {
				requestAnimationFrame(tick);
			} else {
				if (!labelStarted) {
					onLabelStart();
				}
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
				let labelDone = false;
				let countDone = false;
				const maybeFinalizeItem = () => {
					if (labelDone && countDone) {
						tryFinalize();
					}
				};
				animateValue(
					el,
					() => {
						revealLabel(label, () => {
							labelDone = true;
							maybeFinalizeItem();
						});
					},
					() => {
						countDone = true;
						maybeFinalizeItem();
					}
				);
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
	/** Soft pause while pointer is hovering or actively touching the carousel — resumes when the pointer leaves / touch ends. */
	let hoverPaused = false;
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

	/**
	 * Soft-pause the autoplay timer while a user is hovering with a mouse or
	 * actively touching the carousel. Independent of the user-pause state so
	 * a manually-paused slider stays paused after the pointer leaves.
	 */
	function setHoverPaused(paused) {
		if (paused === hoverPaused) {
			return;
		}
		if (paused) {
			if (!isPaused) {
				pausedRemainingMs =
					autoplayDeadline !== null
						? Math.max(0, autoplayDeadline - performance.now())
						: intervalMs;
			}
			hoverPaused = true;
			clearSchedule();
			if (pauseBtn) {
				pauseBtn.classList.add('is-hover-paused');
			}
		} else {
			hoverPaused = false;
			if (pauseBtn) {
				pauseBtn.classList.remove('is-hover-paused');
			}
			if (!isPaused) {
				scheduleNext(pausedRemainingMs);
			}
		}
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

	/**
	 * Scroll-linked opacity: each slide's opacity tracks its distance to the
	 * viewport centre so a manual scroll continuously cross-fades between
	 * slides. Cleared once the scroll settles (clearScrollLinkedOpacity).
	 */
	function updateScrollLinkedOpacity() {
		if (prefersReduced || !slides.length) {
			return;
		}
		const viewRect = viewport.getBoundingClientRect();
		const viewMid = viewRect.left + viewRect.width / 2;
		const slideWidth = slides[0].offsetWidth || viewport.clientWidth || 1;
		const ramp = Math.max(1, slideWidth);
		const opacityFloor = 0.35;
		slides.forEach((slide) => {
			const r = slide.getBoundingClientRect();
			const mid = r.left + r.width / 2;
			const d = Math.abs(mid - viewMid);
			const t = Math.min(1, d / ramp);
			const op = 1 - t * (1 - opacityFloor);
			slide.style.opacity = op.toFixed(3);
		});
	}

	function clearScrollLinkedOpacity() {
		slides.forEach((slide) => {
			if (slide.style.opacity !== '') {
				slide.style.opacity = '';
			}
		});
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
			setHoverPaused(false);
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
			/* Drop inline opacity from scroll-linked cross-fade so .is-active
			   controls the settled slide (no scroll event when scrollLeft is
			   unchanged — without this the first slide can stay faded). */
			clearScrollLinkedOpacity();
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
		/* Pointer can stay over a slide that just became inactive — clear soft pause. */
		setHoverPaused(false);
		/* Blocks spurious closestSlideIndex() overrides from snap/settle after scrollTo; cleared on user scroll. */
		navIntentUntil = performance.now() + 520;

		/* Drop any stale inline opacities from a previous manual scroll so the CSS
		   transition handles the programmatic active-state change cleanly. */
		clearScrollLinkedOpacity();
		syncActiveUi();

		scrollActiveSlideIntoView();
		isFirstGo = false;

		scheduleNext();
	}

	function scheduleNext(delayMs) {
		const delay = typeof delayMs === 'number' ? delayMs : intervalMs;
		clearSchedule();
		if (!multi || prefersReduced || document.hidden || isPaused || hoverPaused) {
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
			if (!isPaused && !hoverPaused) {
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
			} else if (!hoverPaused) {
				scheduleNext(pausedRemainingMs);
			}
		});
	}

	/* Soft pause: only while the pointer / touch is on the active slide (not
	   the whole carousel chrome). Passive touch listeners so vertical scroll
	   is never blocked. */
	slides.forEach((slide) => {
		slide.addEventListener('mouseenter', () => {
			if (slide.classList.contains('is-active')) {
				setHoverPaused(true);
			}
		});
		slide.addEventListener('mouseleave', () => setHoverPaused(false));
	});
	root.addEventListener(
		'touchstart',
		(e) => {
			const slide = e.target.closest('[data-rh-testimonial-slide]');
			if (slide && root.contains(slide) && slide.classList.contains('is-active')) {
				setHoverPaused(true);
			}
		},
		{ passive: true }
	);
	root.addEventListener('touchend', () => setHoverPaused(false), { passive: true });
	root.addEventListener('touchcancel', () => setHoverPaused(false), { passive: true });

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
			/* Live cross-fade while the user is manually scrolling. */
			updateScrollLinkedOpacity();
			window.clearTimeout(scrollSyncTimer);
			scrollSyncTimer = window.setTimeout(() => {
				/* Release inline opacities so the CSS .is-active rule + transition take over. */
				clearScrollLinkedOpacity();
				onScrollSettled();
			}, 140);
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
 * Supports multiple roots (e.g. home #projects + single-project gallery).
 */
(function () {
	/**
	 * @param {HTMLElement} root
	 */
	function initProjectsCarousel(root) {
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
	/** Soft pause while pointer is hovering or actively touching the carousel — resumes when the pointer leaves / touch ends. */
	let hoverPaused = false;
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

	/**
	 * Soft-pause the autoplay timer while a user is hovering with a mouse or
	 * actively touching the carousel. Does not change the user-pause state set
	 * by the pause button, so a manually-paused slider stays paused even after
	 * the pointer leaves.
	 */
	function setHoverPaused(paused) {
		if (paused === hoverPaused) {
			return;
		}
		if (paused) {
			if (!isPaused) {
				pausedRemainingMs =
					autoplayDeadline !== null
						? Math.max(0, autoplayDeadline - performance.now())
						: intervalMs;
			}
			hoverPaused = true;
			clearSchedule();
			if (pauseBtn) {
				pauseBtn.classList.add('is-hover-paused');
			}
		} else {
			hoverPaused = false;
			if (pauseBtn) {
				pauseBtn.classList.remove('is-hover-paused');
			}
			if (!isPaused) {
				scheduleNext(pausedRemainingMs);
			}
		}
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
	 * Scroll-linked card state: each slide's --rh-card-active (0..1) and
	 * opacity are computed continuously from its distance to the viewport
	 * centre. Drives BG zoom, overlay strength and the live cross-fade so
	 * every card animation tracks scroll position 1:1 instead of snapping
	 * when .is-active toggles. Inline values are cleared by
	 * clearScrollLinkedOpacity() once the scroll settles; CSS rules + @property
	 * transition on --rh-card-active take over for programmatic state changes.
	 */
	function updateScrollLinkedOpacity() {
		if (prefersReduced || !slides.length) {
			return;
		}
		const viewRect = viewport.getBoundingClientRect();
		const viewMid = viewRect.left + viewRect.width / 2;
		const slideWidth = slides[0].offsetWidth || viewport.clientWidth || 1;
		/* Ramp distance: a full slide width — neighbours reach the faded floor
		   as the active slide reaches centre. */
		const ramp = Math.max(1, slideWidth);
		const opacityFloor = 0.35;
		slides.forEach((slide) => {
			const r = slide.getBoundingClientRect();
			const mid = r.left + r.width / 2;
			const d = Math.abs(mid - viewMid);
			const t = Math.min(1, d / ramp);
			const op = 1 - t * (1 - opacityFloor);
			const active = 1 - t;
			slide.style.opacity = op.toFixed(3);
			slide.style.setProperty('--rh-card-active', active.toFixed(3));
		});
	}

	function clearScrollLinkedOpacity() {
		slides.forEach((slide) => {
			if (slide.style.opacity !== '') {
				slide.style.opacity = '';
			}
			if (slide.style.getPropertyValue('--rh-card-active') !== '') {
				slide.style.removeProperty('--rh-card-active');
			}
		});
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
			setHoverPaused(false);
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
			/* Inline --rh-card-active / opacity override .is-active until
			   cleared; if scrollLeft does not change (no scroll events),
			   stale values can leave the first card looking inactive. */
			clearScrollLinkedOpacity();
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
		/* Pointer can stay over a slide that just became inactive — clear soft pause. */
		setHoverPaused(false);
		/* Block scroll-sync until snap settles; subsumes debounce + snap after programmatic scroll. */
		navIntentUntil = performance.now() + 2000;

		syncActiveUi();

		/* Prime --rh-card-active / opacity from current scroll position BEFORE
		   the scrollTo() begins. This way the first paint after the class
		   toggle shows the cards in their position-correct (pre-scroll) state
		   rather than briefly snapping to the new class's full active/inactive
		   values. Subsequent scroll events then continuously update inline
		   values as the smooth scroll progresses so the BG zoom + opacity
		   track scroll position frame-by-frame. */
		updateScrollLinkedOpacity();

		scrollActiveSlideIntoView();
		isFirstGo = false;

		scheduleNext();
	}

	function scheduleNext(delayMs) {
		const delay = typeof delayMs === 'number' ? delayMs : intervalMs;
		clearSchedule();
		if (!multi || prefersReduced || document.hidden || isPaused || hoverPaused) {
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
			if (!isPaused && !hoverPaused) {
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
			} else if (!hoverPaused) {
				scheduleNext(pausedRemainingMs);
			}
		});
	}

	/* Soft pause: only while the pointer / touch is on the active slide (not
	   the whole carousel chrome). Passive touch listeners so vertical page
	   scrolling on mobile is not blocked. */
	slides.forEach((slide) => {
		slide.addEventListener('mouseenter', () => {
			if (slide.classList.contains('is-active')) {
				setHoverPaused(true);
			}
		});
		slide.addEventListener('mouseleave', () => setHoverPaused(false));
	});
	root.addEventListener(
		'touchstart',
		(e) => {
			const slide = e.target.closest('[data-rh-project-slide]');
			if (slide && root.contains(slide) && slide.classList.contains('is-active')) {
				setHoverPaused(true);
			}
		},
		{ passive: true }
	);
	root.addEventListener('touchend', () => setHoverPaused(false), { passive: true });
	root.addEventListener('touchcancel', () => setHoverPaused(false), { passive: true });

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
		if (card.classList.contains('is-active')) {
			const url = card.getAttribute('data-rh-project-url');
			if (url && String(url).trim() !== '') {
				window.location.assign(url);
				return;
			}
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
			/* Always run the visual update so BG zoom (--rh-card-active) and
			   opacity track scroll position during BOTH manual scrolls and
			   programmatic smooth scrolls triggered by goTo()/autoplay. The
			   suppressScrollSync flag only guards the index/UI settle below,
			   not the per-frame visual sync. */
			updateScrollLinkedOpacity();
			updateEdgeState();
			if (suppressScrollSync) {
				return;
			}
			navIntentUntil = 0;
			window.clearTimeout(scrollSyncTimer);
			scrollSyncTimer = window.setTimeout(() => {
				/* Release inline opacities/--rh-card-active so the CSS
				   .is-active rule + @property transition take over. */
				clearScrollLinkedOpacity();
				onScrollSettled();
			}, 140);
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
	}

	document.querySelectorAll('[data-rh-projects-carousel]').forEach((root) => {
		if (root instanceof HTMLElement) {
			initProjectsCarousel(root);
		}
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
	).filter((el) => !el.querySelector('[data-rh-fx]'));
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

/**
 * Generic [data-rh-fx] reveal observer.
 *
 * Adds entry animations to titles, subtitles, paragraphs, buttons and lists
 * across all pages. Pairs with the CSS in home-hero.css (search "data-rh-fx").
 *
 * - For every element with [data-rh-fx], the observer adds `.is-inview` when
 *   the element scrolls into the viewport, which triggers its keyframe.
 * - When a parent has [data-rh-fx-group], all descendant [data-rh-fx] items
 *   are revealed together when the group enters the viewport, each with a
 *   staggered animation-delay (default 80ms between items, override via
 *   data-rh-fx-stagger="120"). Explicit inline `--rh-fx-delay` always wins.
 * - Respects prefers-reduced-motion (immediately marks all as in-view).
 */
(function () {
	const allItems = Array.from(document.querySelectorAll('[data-rh-fx]'));
	const groups = Array.from(document.querySelectorAll('[data-rh-fx-group]'));
	if (!allItems.length && !groups.length) return;

	/* Flag <html> so CSS knows JS booted and may apply the hidden initial
	   state. Without this flag, elements stay at their natural opacity so
	   pages remain readable with JS disabled. */
	document.documentElement.classList.add('rh-fx-js');

	const prefersReduced =
		typeof window.matchMedia === 'function' &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Assign cascading --rh-fx-delay to grouped items. Items with their own
	   inline --rh-fx-delay are left alone so callers can override individuals. */
	groups.forEach((group) => {
		const stagger = Math.max(0, parseInt(group.getAttribute('data-rh-fx-stagger') || '80', 10) || 0);
		const base = Math.max(0, parseInt(group.getAttribute('data-rh-fx-base') || '0', 10) || 0);
		const items = group.querySelectorAll('[data-rh-fx]');
		let idx = 0;
		items.forEach((item) => {
			if (item.style.getPropertyValue('--rh-fx-delay')) {
				idx++;
				return;
			}
			item.style.setProperty('--rh-fx-delay', base + idx * stagger + 'ms');
			idx++;
		});
	});

	if (prefersReduced || typeof IntersectionObserver === 'undefined') {
		allItems.forEach((el) => el.classList.add('is-inview'));
		return;
	}

	/* Items inside a [data-rh-fx-group] are revealed together when the group
	   enters the viewport; items outside any group fire individually. */
	const itemInGroup = new WeakSet();
	groups.forEach((group) => {
		group.querySelectorAll('[data-rh-fx]').forEach((el) => itemInGroup.add(el));
	});

	const standalone = allItems.filter((el) => !itemInGroup.has(el));

	const observerOptions = { root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 };

	if (standalone.length) {
		const standaloneObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				entry.target.classList.add('is-inview');
				standaloneObserver.unobserve(entry.target);
			});
		}, observerOptions);
		standalone.forEach((el) => standaloneObserver.observe(el));
	}

	if (groups.length) {
		const groupObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				entry.target.querySelectorAll('[data-rh-fx]').forEach((el) => el.classList.add('is-inview'));
				groupObserver.unobserve(entry.target);
			});
		}, observerOptions);
		groups.forEach((group) => groupObserver.observe(group));
	}
})();

/**
 * Inner-page header intro animation cleanup.
 *
 * The header shell uses an animated `clip-path` to sweep open from the
 * top-left. With CSS `animation-fill-mode: both` the end-state clip-path
 * persists, which would also clip the absolutely-positioned mobile
 * dropdown (it lives inside the shell but sits below it). After the
 * animation ends, clear the inline clip-path so the dropdown can render.
 */
(function () {
	if (
		typeof window.matchMedia === 'function' &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches
	) {
		return;
	}
	const shell = document.querySelector('.rh-site-top-bar__shell');
	if (!shell) return;
	const handle = (e) => {
		if (e.animationName !== 'rh-site-top-bar-shell-reveal') return;
		shell.style.clipPath = 'none';
		shell.removeEventListener('animationend', handle);
	};
	shell.addEventListener('animationend', handle);
	/* Safety net: if the animationend event is missed (e.g. throttled tab),
	   still clear the clip-path after a generous timeout matching the CSS
	   shell animation duration. */
	setTimeout(() => {
		if (shell.style.clipPath !== 'none') {
			shell.style.clipPath = 'none';
		}
	}, 2200);
})();

/**
 * Projects archive infinite scroll.
 *
 * Fetches subsequent pages from the rh/v1/projects REST endpoint when the
 * sentinel approaches the viewport, then appends the returned cards into
 * the bento grid. Status messages live in [data-rh-archive-status].
 */
(function () {
	const grid = document.querySelector('[data-rh-archive-loader]');
	const sentinel = document.querySelector('[data-rh-archive-sentinel]');
	const statusEl = document.querySelector('[data-rh-archive-status]');
	if (
		!grid ||
		!sentinel ||
		typeof window.IntersectionObserver !== 'function' ||
		typeof window.fetch !== 'function'
	) {
		return;
	}

	let page = parseInt(grid.getAttribute('data-page') || '1', 10) || 1;
	let totalPages = parseInt(grid.getAttribute('data-total-pages') || '1', 10) || 1;
	const perPage = parseInt(grid.getAttribute('data-per-page') || '0', 10) || 0;
	const restUrl = grid.getAttribute('data-rest-url') || '';
	if (!restUrl || page >= totalPages) {
		return;
	}

	let loading = false;
	let done = false;
	let observer = null;

	const setStatus = (state, text) => {
		if (!statusEl) return;
		statusEl.classList.remove('is-loading', 'is-done', 'is-error');
		if (state) statusEl.classList.add(state);
		const textEl = statusEl.querySelector('.rh-archive-projects__status-text');
		if (textEl && typeof text === 'string') {
			textEl.textContent = text;
		}
	};

	/* Per-card stagger so appended cards pop in one after another. */
	const STAGGER_MS = 110;

	const hideStatus = () => {
		if (!statusEl) return;
		statusEl.classList.remove('is-loading', 'is-done', 'is-error');
		statusEl.style.display = 'none';
	};

	const fetchNext = () => {
		if (loading || done) return;
		loading = true;
		setStatus('is-loading', 'Loading more projects…');

		const nextPage = page + 1;
		let url;
		try {
			url = new URL(restUrl, window.location.origin);
		} catch (e) {
			url = null;
		}
		const fetchUrl = url
			? (() => {
					url.searchParams.set('page', String(nextPage));
					if (perPage > 0) url.searchParams.set('per_page', String(perPage));
					return url.toString();
				})()
			: restUrl +
				(restUrl.indexOf('?') === -1 ? '?' : '&') +
				'page=' +
				encodeURIComponent(String(nextPage)) +
				(perPage > 0 ? '&per_page=' + encodeURIComponent(String(perPage)) : '');

		fetch(fetchUrl, {
			credentials: 'same-origin',
			headers: { Accept: 'application/json' },
		})
			.then((res) => {
				if (!res.ok) throw new Error('HTTP ' + res.status);
				return res.json();
			})
			.then((json) => {
				const html = typeof json.html === 'string' ? json.html : '';
				if (html.trim() !== '') {
					const tmp = document.createElement('div');
					tmp.innerHTML = html;
					Array.from(tmp.children).forEach((node, i) => {
						node.classList.add('rh-is-appended');
						node.style.setProperty('--rh-card-stagger', i * STAGGER_MS + 'ms');
						/* Initial cards use [data-rh-fx] in a delayed group; appended nodes miss
						   group init and would stay opacity:0 — rely on rh-archive-card-in only. */
						node.removeAttribute('data-rh-fx');
						grid.appendChild(node);
					});
				}
				page = nextPage;
				grid.setAttribute('data-page', String(page));
				if (typeof json.total_pages === 'number' && json.total_pages > 0) {
					totalPages = json.total_pages;
					grid.setAttribute('data-total-pages', String(totalPages));
				}
				const hasMore = !!json.has_more && page < totalPages;
				if (!hasMore) {
					done = true;
					if (observer) observer.disconnect();
					hideStatus();
				} else {
					hideStatus();
				}
			})
			.catch(() => {
				setStatus('is-error', 'Could not load more projects — please refresh');
			})
			.finally(() => {
				loading = false;
			});
	};

	observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					fetchNext();
				}
			});
		},
		{ root: null, rootMargin: '0px 0px 200px 0px', threshold: 0 }
	);
	observer.observe(sentinel);
})();
