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
		document.querySelectorAll('.rh-site-top-bar__shell').forEach((shell) => {
			if (anyOpen) {
				shell.style.clipPath = 'none';
			}
		});
	};

	const restartMobileNavLinkAnimations = (nav) => {
		if (!nav || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}
		nav.querySelectorAll('.rh-hero-nav__menu li a').forEach((link) => {
			link.style.animation = 'none';
			// Force reflow so opening the menu replays the wipe stagger.
			void link.offsetWidth;
			link.style.removeProperty('animation');
		});
	};

	const setNavOpen = (targetNav, open) => {
		document.querySelectorAll('[data-rh-hero-nav]').forEach((n) => {
			n.classList.toggle('is-open', open && n === targetNav);
		});
		if (open && targetNav) {
			restartMobileNavLinkAnimations(targetNav);
		}
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

	document.querySelectorAll('[data-rh-hero-nav]').forEach((nav) => {
		nav.addEventListener('click', (e) => {
			const link = e.target.closest('a[href]');
			if (!link || !nav.contains(link) || !nav.classList.contains('is-open')) {
				return;
			}
			setNavOpen(nav, false);
		});
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			if (
				document.body.classList.contains('rh-contact-overlay-open') ||
				document.body.classList.contains('rh-section-overlay-open')
			) {
				return;
			}
			setNavOpen(null, false);
		}
	});
})();

/**
 * Home stats strip: after the home hero intro, cards stagger in, then count up left→right
 * (next stat starts when the previous count is 50% done; label wipe after each count).
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
	const HERO_SHELL_ANIM_MS = 2350;
	/** Match rh-home-hero-shell-in keyframes: shrink + frame begin at 27%. */
	const HERO_SHRINK_START_MS = Math.round(HERO_SHELL_ANIM_MS * 0.27);
	const STAT_CARD_STAGGER_MS = 200;
	const STAT_CARD_ANIM_MS = 520;
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
		strip.classList.remove('rh-home-stats-strip--pending', 'rh-home-stats-strip--active');
	}

	function beginStrip() {
		strip.classList.remove('rh-home-stats-strip--await-hero');
		strip.classList.add('rh-home-stats-strip--active');
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
		beginStrip();
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

	let heroIntroDone = prefersReduced;
	let stripVisible = false;
	let started = false;

	function startWhenReady() {
		if (started || !heroIntroDone || !stripVisible) {
			return;
		}
		started = true;
		observer.disconnect();

		if (prefersReduced) {
			runReduced();
			return;
		}

		beginStrip();
		window.setTimeout(() => {
			prepareLabels();
			runOverlapping();
		}, Math.round(STAT_CARD_ANIM_MS * 0.4));
	}

	const observer = new IntersectionObserver(
		(entries) => {
			if (entries.some((e) => e.isIntersecting)) {
				stripVisible = true;
				startWhenReady();
			}
		},
		{ root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
	);

	observer.observe(strip);

	const heroShell = document.querySelector('.rh-home-hero .rh-hero-home');
	if (!heroShell) {
		heroIntroDone = true;
		startWhenReady();
		return;
	}

	const markHeroIntroDone = () => {
		if (heroIntroDone) {
			return;
		}
		heroIntroDone = true;
		startWhenReady();
	};

	const onHeroAnimEnd = (e) => {
		if (e.animationName !== 'rh-home-hero-shell-in') {
			return;
		}
		heroShell.removeEventListener('animationend', onHeroAnimEnd);
		markHeroIntroDone();
	};

	heroShell.addEventListener('animationend', onHeroAnimEnd);
	window.setTimeout(markHeroIntroDone, HERO_SHRINK_START_MS);
	window.setTimeout(markHeroIntroDone, HERO_SHELL_ANIM_MS + 80);
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

/**
 * Projects archive: hover/touch card state matches the home project slider (--rh-card-active).
 */
(function () {
	const grid = document.querySelector('.rh-archive-projects__bento');
	if (!grid) {
		return;
	}

	let touchCard = null;

	const clearTouch = () => {
		if (touchCard) {
			touchCard.classList.remove('is-card-touch');
			touchCard = null;
		}
	};

	grid.addEventListener(
		'pointerdown',
		(e) => {
			if (e.pointerType !== 'touch') {
				return;
			}
			const card = e.target.closest('.rh-home-project-card.rh-bento-cell');
			if (!card || !grid.contains(card)) {
				return;
			}
			clearTouch();
			touchCard = card;
			card.classList.add('is-card-touch');
		},
		{ passive: true }
	);

	grid.addEventListener('pointerup', clearTouch, { passive: true });
	grid.addEventListener('pointercancel', clearTouch, { passive: true });
})();

	document.querySelectorAll('[data-rh-projects-carousel]').forEach((root) => {
		if (root instanceof HTMLElement) {
			initProjectsCarousel(root);
		}
	});
})();

/**
 * About / Services section overlays (#about, #services) on the current page.
 */
(function () {
	const overlays = new Map();
	document.querySelectorAll('[data-rh-section-overlay]').forEach((el) => {
		const id = el.getAttribute('data-rh-section-overlay');
		if (id) {
			overlays.set(id, el);
		}
	});
	if (!overlays.size) {
		return;
	}

	const CLOSE_MS = 420;
	let closeTimer = null;
	let activeId = '';

	const syncBodyLock = () => {
		const anyOpen = Array.from(overlays.values()).some((el) => el.classList.contains('is-open'));
		document.body.classList.toggle('rh-section-overlay-open', anyOpen);
		if (!anyOpen && !document.body.classList.contains('rh-contact-overlay-open')) {
			document.body.style.overflow = '';
		} else if (anyOpen) {
			document.body.style.overflow = 'hidden';
		}
	};

	const hashId = () => {
		const id = window.location.hash.replace(/^#/, '');
		return overlays.has(id) ? id : '';
	};

	const finishClose = (el) => {
		closeTimer = null;
		el.classList.remove('is-closing', 'is-open');
		el.setAttribute('aria-hidden', 'true');
		syncBodyLock();
	};

	const animateClose = (el) => {
		if (!el || !el.classList.contains('is-open')) {
			return;
		}
		el.classList.remove('is-open');
		el.classList.add('is-closing');
		window.clearTimeout(closeTimer);
		closeTimer = window.setTimeout(() => finishClose(el), CLOSE_MS);
	};

	const setOpen = (id, open) => {
		const el = overlays.get(id);
		if (!el) {
			return;
		}
		if (open) {
			window.clearTimeout(closeTimer);
			closeTimer = null;
			overlays.forEach((other, otherId) => {
				if (otherId === id) {
					return;
				}
				other.classList.remove('is-open', 'is-closing');
				other.setAttribute('aria-hidden', 'true');
			});
			activeId = id;
			el.classList.remove('is-closing');
			el.classList.add('is-open');
			el.setAttribute('aria-hidden', 'false');
			if (document.getElementById(id)) {
				window.scrollTo(0, 0);
			}
			syncBodyLock();
			const closeBtn = el.querySelector('[data-rh-section-overlay-close]');
			if (closeBtn && typeof closeBtn.focus === 'function') {
				window.requestAnimationFrame(() => closeBtn.focus());
			}
			return;
		}
		animateClose(el);
		if (activeId === id) {
			activeId = '';
		}
	};

	const closeActive = () => {
		const id = activeId || hashId();
		if (!id) {
			return;
		}
		try {
			const u = new URL(window.location.href);
			u.hash = '';
			const q = u.searchParams.toString();
			history.replaceState(null, '', u.pathname + (q ? '?' + q : '') + u.hash);
		} catch {
			history.replaceState(null, '', window.location.pathname + window.location.search);
		}
		setOpen(id, false);
	};

	const syncFromLocation = () => {
		const id = hashId();
		if (id) {
			setOpen(id, true);
			return;
		}
		if (activeId) {
			const el = overlays.get(activeId);
			if (el) {
				animateClose(el);
			}
			activeId = '';
			return;
		}
		overlays.forEach((el) => {
			if (el.classList.contains('is-open')) {
				animateClose(el);
			}
		});
	};

	const openFromLink = (id) => {
		const hash = '#' + id;
		if (window.location.hash !== hash) {
			try {
				const u = new URL(window.location.href);
				u.hash = id;
				history.pushState(null, '', u.pathname + u.search + u.hash);
			} catch {
				window.location.hash = hash;
			}
		}
		setOpen(id, true);
	};

	document.addEventListener('click', (e) => {
		const link = e.target.closest('a[href]');
		if (!link) {
			return;
		}
		const raw = link.getAttribute('href') || '';
		if (raw === '#about' || raw === '#services') {
			e.preventDefault();
			openFromLink(raw.slice(1));
			return;
		}
		try {
			const u = new URL(link.href, window.location.href);
			if (u.origin !== window.location.origin) {
				return;
			}
			const frag = u.hash.replace(/^#/, '');
			if (!overlays.has(frag)) {
				return;
			}
			if (u.pathname !== window.location.pathname || u.search !== window.location.search) {
				return;
			}
			e.preventDefault();
			openFromLink(frag);
		} catch {
			/* ignore malformed href */
		}
	});

	window.addEventListener('hashchange', syncFromLocation);
	window.addEventListener('pageshow', syncFromLocation);
	document.addEventListener('DOMContentLoaded', syncFromLocation);

	overlays.forEach((el) => {
		el.querySelectorAll('[data-rh-section-overlay-close]').forEach((btn) => {
			btn.addEventListener('click', () => closeActive());
		});
	});

	document.addEventListener(
		'keydown',
		(e) => {
			if (e.key !== 'Escape') {
				return;
			}
			const id = activeId || hashId();
			if (!id) {
				return;
			}
			const el = overlays.get(id);
			if (!el || (!el.classList.contains('is-open') && !el.classList.contains('is-closing'))) {
				return;
			}
			if (el.classList.contains('is-closing')) {
				window.clearTimeout(closeTimer);
				finishClose(el);
				e.preventDefault();
				return;
			}
			e.preventDefault();
			closeActive();
		},
		true
	);
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
		if (!document.body.classList.contains('rh-section-overlay-open')) {
			document.body.style.overflow = '';
		}
		document.body.classList.remove('rh-contact-overlay-open');
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
			if (!document.body.classList.contains('rh-section-overlay-open')) {
				document.body.style.overflow = '';
			}
		}
	};

	const showNoticeFromQuery = () => {
		if (!ajaxConfig || !ajaxConfig.messages) {
			return;
		}
		let status = '';
		try {
			status = new URL(window.location.href).searchParams.get('contact') || '';
		} catch {
			status = '';
		}
		if (!status) {
			return;
		}
		const message =
			typeof ajaxConfig.messages[status] === 'string' ? ajaxConfig.messages[status] : '';
		if (message) {
			showAjaxNotice(status, message);
		}
	};

	const syncFromLocation = () => {
		let contactParam = '';
		try {
			contactParam = new URL(window.location.href).searchParams.get('contact') || '';
		} catch {
			contactParam = '';
		}
		const open = window.location.hash === '#contact' || contactParam !== '';
		if (open) {
			setOpen(true);
			if (
				contactParam !== '' &&
				!overlay.querySelector('.rh-contact-overlay__notice--success, .rh-contact-overlay__notice--warn')
			) {
				showNoticeFromQuery();
			}
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

	const form = overlay.querySelector('[data-rh-contact-form]');
	const noticeSlot = overlay.querySelector('[data-rh-contact-notice-slot]');
	const noticeEl = overlay.querySelector('[data-rh-contact-notice]');
	const ajaxConfig = typeof rhContactForm === 'object' && rhContactForm ? rhContactForm : null;

	const clearAjaxNotice = () => {
		if (!noticeSlot || !noticeEl) {
			return;
		}
		noticeSlot.hidden = true;
		noticeEl.textContent = '';
		noticeEl.classList.remove('rh-contact-overlay__notice--success', 'rh-contact-overlay__notice--warn');
		noticeEl.removeAttribute('role');
	};

	const showAjaxNotice = (status, message) => {
		if (!noticeSlot || !noticeEl || !message) {
			return;
		}
		const isSuccess = status === 'sent';
		noticeEl.textContent = message;
		noticeEl.classList.toggle('rh-contact-overlay__notice--success', isSuccess);
		noticeEl.classList.toggle('rh-contact-overlay__notice--warn', !isSuccess);
		noticeEl.setAttribute('role', isSuccess ? 'status' : 'alert');
		noticeSlot.hidden = false;
		noticeEl.focus({ preventScroll: true });
	};

	if (form instanceof HTMLFormElement && ajaxConfig && ajaxConfig.ajaxUrl) {
		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			clearAjaxNotice();

			const submitBtn = form.querySelector('.rh-contact-overlay__submit');
			if (submitBtn instanceof HTMLButtonElement) {
				if (submitBtn.disabled) {
					return;
				}
				submitBtn.disabled = true;
				submitBtn.setAttribute('aria-busy', 'true');
			}

			const body = new FormData(form);
			body.set('action', ajaxConfig.action);

			try {
				const res = await fetch(ajaxConfig.ajaxUrl, {
					method: 'POST',
					body,
					credentials: 'same-origin',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
					},
				});

				let payload = null;
				try {
					payload = await res.json();
				} catch {
					payload = null;
				}

				const data = payload && typeof payload === 'object' ? payload.data : null;
				const status =
					data && typeof data.status === 'string'
						? data.status
						: payload && payload.success
							? 'sent'
							: 'failed';
				const message =
					data && typeof data.message === 'string'
						? data.message
						: status === 'sent'
							? ''
							: '';

				const fallback =
					ajaxConfig.messages && typeof ajaxConfig.messages[status] === 'string'
						? ajaxConfig.messages[status]
						: '';
				showAjaxNotice(status, message || fallback);

				if (status === 'sent') {
					form.reset();
				}
			} catch {
				const failedMsg =
					ajaxConfig.messages && typeof ajaxConfig.messages.failed === 'string'
						? ajaxConfig.messages.failed
						: '';
				showAjaxNotice('failed', failedMsg);
			} finally {
				if (submitBtn instanceof HTMLButtonElement) {
					submitBtn.disabled = false;
					submitBtn.removeAttribute('aria-busy');
				}
			}
		});
	}
})();

/**
 * Homepage section shells: simple fade-up on scroll, then reveal [data-rh-fx] inside.
 */
(function () {
	if (!document.body.classList.contains('rh-carpentry-home')) {
		return;
	}

	const shellSelectors = [
		'.rh-home-about-container',
		'.rh-home-section--projects .rh-clients-hero',
		'.rh-home-section--features .rh-home-section__inner',
		'.rh-home-section--testimonials .rh-clients-hero',
		'.rh-home-section--clients .rh-home-clients-container',
		'body.rh-carpentry-home .rh-site-footer__surface',
	];

	const shells = [];
	shellSelectors.forEach((selector) => {
		document.querySelectorAll(selector).forEach((el) => {
			if (!el.hasAttribute('data-rh-section-shell')) {
				el.setAttribute('data-rh-section-shell', '');
			}
			shells.push(el);
		});
	});

	if (!shells.length) {
		return;
	}

	const prefersReduced =
		typeof window.matchMedia === 'function' &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const SHELL_CONTENT_DELAY_MS = 160;

	const revealFxInShell = (shell) => {
		shell.querySelectorAll('[data-rh-fx-group]').forEach((group) => {
			group.querySelectorAll('[data-rh-fx]').forEach((el) => {
				el.classList.add('is-inview');
			});
		});
		shell.querySelectorAll('[data-rh-fx]').forEach((el) => {
			if (!el.closest('[data-rh-fx-group]')) {
				el.classList.add('is-inview');
			}
		});
	};

	const activateShell = (shell) => {
		if (shell.classList.contains('is-inview')) {
			return;
		}
		shell.classList.add('is-inview');
		if (prefersReduced) {
			revealFxInShell(shell);
			return;
		}
		window.setTimeout(() => {
			revealFxInShell(shell);
		}, SHELL_CONTENT_DELAY_MS);
	};

	if (prefersReduced || typeof IntersectionObserver === 'undefined') {
		shells.forEach((shell) => activateShell(shell));
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}
				activateShell(entry.target);
				observer.unobserve(entry.target);
			});
		},
		{ root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
	);

	shells.forEach((shell) => observer.observe(shell));
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

	/* Breadcrumbs: first crumb fades with the hero subtitle; each next crumb staggers after. */
	const syncBreadcrumbsWithHero = () => {
		const crumbsNav = document.querySelector('.rh-breadcrumbs[data-rh-fx-sync="hero-subtitle"]');
		if (!crumbsNav) {
			return;
		}
		const hero = document.querySelector(
			'.rh-page-hero-split[data-rh-fx-group], .rh-archive-projects__header[data-rh-fx-group], .rh-single-project__header[data-rh-fx-group]'
		);
		if (!hero) {
			return;
		}
		const anchor =
			hero.querySelector('.rh-archive-projects__subtitle[data-rh-fx]') ||
			hero.querySelector('.rh-single-project__title[data-rh-fx]') ||
			hero.querySelector('.rh-archive-projects__intro[data-rh-fx]') ||
			hero.querySelector('.page-title[data-rh-fx], h1[data-rh-fx]');
		if (!anchor) {
			return;
		}
		const baseMs = parseInt(anchor.style.getPropertyValue('--rh-fx-delay') || '0', 10) || 0;
		const stagger = Math.max(0, parseInt(crumbsNav.getAttribute('data-rh-fx-stagger') || '90', 10) || 0);
		const items = crumbsNav.querySelectorAll('.rh-breadcrumbs__item[data-rh-fx]');
		items.forEach((item, idx) => {
			item.style.setProperty('--rh-fx-delay', baseMs + idx * stagger + 'ms');
		});
	};
	syncBreadcrumbsWithHero();

	if (prefersReduced || typeof IntersectionObserver === 'undefined') {
		allItems.forEach((el) => el.classList.add('is-inview'));
		return;
	}

	/* Items inside a [data-rh-fx-group] are revealed together when the group
	   enters the viewport; items outside any group fire individually.
	   Homepage section shells reveal their own [data-rh-fx] after the shell fades up. */
	const itemInGroup = new WeakSet();
	groups.forEach((group) => {
		group.querySelectorAll('[data-rh-fx]').forEach((el) => itemInGroup.add(el));
	});

	const isInHomeSectionShell = (el) => !!el.closest('[data-rh-section-shell]');

	const standalone = allItems.filter((el) => !itemInGroup.has(el) && !isInHomeSectionShell(el));

	const groupsOutsideShells = groups.filter((group) => !isInHomeSectionShell(group));

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

	if (groupsOutsideShells.length) {
		/* Row headers on narrow viewports use display:contents so the title block,
		   body, and CTA can be reordered in CSS grid. That removes the group's
		   layout box, so observing the group never intersects — titles stay hidden. */
		const resolveFxGroupObserveTarget = (group) => {
			if (typeof window.getComputedStyle !== 'function') {
				return group;
			}
			if (window.getComputedStyle(group).display !== 'contents') {
				return group;
			}
			const titleBlock = group.querySelector(':scope > div');
			if (titleBlock) {
				return titleBlock;
			}
			const firstFx = group.querySelector('[data-rh-fx]');
			if (firstFx) {
				const parent = firstFx.parentElement;
				if (parent && parent !== group) {
					return parent;
				}
				return firstFx;
			}
			return group.firstElementChild || group;
		};

		const observeTargetToGroup = new Map();

		const groupObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				const group = observeTargetToGroup.get(entry.target);
				if (!group) return;
				group.querySelectorAll('[data-rh-fx]').forEach((el) => el.classList.add('is-inview'));
				groupObserver.unobserve(entry.target);
			});
		}, observerOptions);
		groupsOutsideShells.forEach((group) => {
			const target = resolveFxGroupObserveTarget(group);
			observeTargetToGroup.set(target, group);
			groupObserver.observe(target);
		});
	}

	/* Landing page heroes are above the fold — reveal immediately so kicker/title/subtitle show on load. */
	const aboveFoldHeroSelector =
		'.rh-page-hero-split[data-rh-fx-group], .rh-archive-projects__header[data-rh-fx-group], .rh-single-project__header[data-rh-fx-group]';
	const crumbsSynced = document.querySelector('.rh-breadcrumbs[data-rh-fx-sync="hero-subtitle"]');
	document.querySelectorAll(aboveFoldHeroSelector).forEach((group) => {
		const rect = group.getBoundingClientRect();
		if (rect.top < window.innerHeight && rect.bottom > 0) {
			group.querySelectorAll('[data-rh-fx]').forEach((el) => el.classList.add('is-inview'));
			if (crumbsSynced) {
				crumbsSynced.querySelectorAll('[data-rh-fx]').forEach((el) => el.classList.add('is-inview'));
			}
		}
	});
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
	const finishReveal = () => {
		shell.style.clipPath = 'none';
		shell.classList.add('rh-site-top-bar__shell--revealed');
	};
	const handle = (e) => {
		if (e.animationName !== 'rh-site-top-bar-shell-reveal') return;
		finishReveal();
		shell.removeEventListener('animationend', handle);
	};
	shell.addEventListener('animationend', handle);
	/* Safety net: if the animationend event is missed (e.g. throttled tab),
	   still clear the clip-path after a generous timeout matching the CSS
	   shell animation duration. */
	setTimeout(finishReveal, 2200);
})();

/**
 * Projects archive: sector filters + infinite scroll (rh/v1/projects).
 */
(function () {
	const grid = document.querySelector('[data-rh-archive-loader]');
	if (!grid || typeof window.fetch !== 'function') {
		return;
	}

	const filters = document.querySelector('[data-rh-archive-filters]');
	const sentinel = document.querySelector('[data-rh-archive-sentinel]');
	const statusEl = document.querySelector('[data-rh-archive-status]');
	const filterEmptyEl = document.querySelector('[data-rh-archive-filter-empty]');

	const perPage = parseInt(grid.getAttribute('data-per-page') || '0', 10) || 0;
	const restUrl = grid.getAttribute('data-rest-url') || '';
	const initialGridHtml = grid.innerHTML;
	const initialPage = parseInt(grid.getAttribute('data-page') || '1', 10) || 1;
	const initialTotalPages = parseInt(grid.getAttribute('data-total-pages') || '1', 10) || 1;

	let page = initialPage;
	let totalPages = initialTotalPages;
	let activeSector = '';
	let loading = false;
	let done = false;
	let observer = null;

	const STAGGER_MS = 110;
	const FILTER_ENTER_STAGGER_MS = 110;
	const prefersReduced =
		typeof window.matchMedia === 'function' &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const FILTER_EXIT_MS = prefersReduced ? 0 : 320;
	let filterAnimating = false;

	const getCards = () => Array.from(grid.querySelectorAll('.rh-archive-project-card'));

	const setStatus = (state, text) => {
		if (!statusEl) return;
		statusEl.classList.remove('is-loading', 'is-done', 'is-error');
		if (state) statusEl.classList.add(state);
		const textEl = statusEl.querySelector('.rh-archive-projects__status-text');
		if (textEl && typeof text === 'string') {
			textEl.textContent = text;
		}
	};

	const hideStatus = () => {
		if (!statusEl) return;
		statusEl.classList.remove('is-loading', 'is-done', 'is-error');
		statusEl.style.display = 'none';
	};

	const showStatus = () => {
		if (!statusEl) return;
		statusEl.style.display = '';
	};

	const setFilterEmptyVisible = (visible) => {
		if (!filterEmptyEl) return;
		filterEmptyEl.hidden = !visible;
	};

	const buildFetchUrl = (pageNum) => {
		let url;
		try {
			url = new URL(restUrl, window.location.origin);
		} catch {
			url = null;
		}
		if (url) {
			url.searchParams.set('page', String(pageNum));
			if (perPage > 0) url.searchParams.set('per_page', String(perPage));
			if (activeSector) url.searchParams.set('sector', activeSector);
			else url.searchParams.delete('sector');
			return url.toString();
		}
		const parts = [
			'page=' + encodeURIComponent(String(pageNum)),
			perPage > 0 ? 'per_page=' + encodeURIComponent(String(perPage)) : '',
			activeSector ? 'sector=' + encodeURIComponent(activeSector) : '',
		].filter(Boolean);
		return restUrl + (restUrl.indexOf('?') === -1 ? '?' : '&') + parts.join('&');
	};

	const mountCards = (html, append) => {
		if (!append) {
			grid.style.minHeight = '';
			grid.innerHTML = '';
		}
		if (html.trim() === '') {
			return [];
		}
		const tmp = document.createElement('div');
		tmp.innerHTML = html;
		const nodes = Array.from(tmp.children);
		nodes.forEach((node, i) => {
			node.removeAttribute('data-rh-fx');
			if (append) {
				node.classList.add('rh-is-appended');
				node.style.setProperty('--rh-card-stagger', i * STAGGER_MS + 'ms');
			}
			grid.appendChild(node);
		});
		return nodes;
	};

	const preserveGridHeight = () => {
		const h = grid.offsetHeight;
		if (h > 0) {
			grid.style.minHeight = h + 'px';
		}
	};

	const animateFilterOut = () =>
		new Promise((resolve) => {
			const cards = getCards();
			if (!cards.length || prefersReduced) {
				resolve();
				return;
			}
			preserveGridHeight();
			cards.forEach((card) => {
				card.classList.remove('rh-archive-card-enter', 'rh-is-appended');
				card.classList.add('rh-archive-card-exit');
			});
			window.setTimeout(resolve, FILTER_EXIT_MS);
		});

	const animateFilterIn = (nodes) => {
		grid.style.minHeight = '';
		const cards = nodes && nodes.length ? nodes : getCards();
		if (prefersReduced || !cards.length) {
			return;
		}
		cards.forEach((card, i) => {
			card.classList.remove('rh-archive-card-exit');
			card.style.setProperty('--rh-card-stagger', i * FILTER_ENTER_STAGGER_MS + 'ms');
			card.classList.remove('rh-archive-card-enter');
			void card.offsetWidth;
			card.classList.add('rh-archive-card-enter');
		});
	};

	const updatePaginationState = (json, pageNum) => {
		page = pageNum;
		grid.setAttribute('data-page', String(page));
		if (typeof json.total_pages === 'number' && json.total_pages > 0) {
			totalPages = json.total_pages;
			grid.setAttribute('data-total-pages', String(totalPages));
		}
		const hasMore = !!json.has_more && page < totalPages;
		done = !hasMore;
		if (sentinel) {
			sentinel.hidden = done;
		}
		if (done) {
			if (observer) observer.disconnect();
			hideStatus();
		} else {
			showStatus();
			hideStatus();
			startObserver();
		}
	};

	const fetchPage = (pageNum, { append = false, silent = false } = {}) => {
		if (!restUrl || loading) {
			return Promise.resolve(null);
		}
		loading = true;
		if (!silent) {
			setStatus('is-loading', append ? 'Loading more projects…' : 'Loading projects…');
		}

		return fetch(buildFetchUrl(pageNum), {
			credentials: 'same-origin',
			headers: { Accept: 'application/json' },
		})
			.then((res) => {
				if (!res.ok) throw new Error('HTTP ' + res.status);
				return res.json();
			})
			.then((json) => {
				const html = typeof json.html === 'string' ? json.html : '';
				const nodes = mountCards(html, append);
				const total = typeof json.total === 'number' ? json.total : 0;
				setFilterEmptyVisible(activeSector !== '' && total === 0);
				updatePaginationState(json, pageNum);
				return { json, nodes };
			})
			.catch(() => {
				setStatus('is-error', 'Could not load projects — please refresh');
			})
			.finally(() => {
				loading = false;
			});
	};

	const fetchNext = () => {
		if (loading || done) return;
		fetchPage(page + 1, { append: true });
	};

	const startObserver = () => {
		if (!sentinel || typeof window.IntersectionObserver !== 'function' || done) {
			return;
		}
		if (observer) {
			observer.disconnect();
		}
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
	};

	const setActiveFilterUi = (btn) => {
		if (!filters) return;
		filters.querySelectorAll('[data-rh-sector-filter]').forEach((el) => {
			const active = el === btn;
			el.classList.toggle('is-active', active);
			el.setAttribute('aria-pressed', active ? 'true' : 'false');
		});
	};

	const applySectorFilter = (slug) => {
		if (filterAnimating || loading) {
			return;
		}
		filterAnimating = true;
		activeSector = slug;
		setFilterEmptyVisible(false);
		done = false;

		if (observer) {
			observer.disconnect();
		}

		animateFilterOut().then(() => {
			if (slug === '') {
				grid.innerHTML = initialGridHtml;
				grid.style.minHeight = '';
				getCards().forEach((card) => card.removeAttribute('data-rh-fx'));
				page = initialPage;
				totalPages = initialTotalPages;
				grid.setAttribute('data-page', String(initialPage));
				grid.setAttribute('data-total-pages', String(initialTotalPages));
				done = page >= totalPages;
				if (sentinel) {
					sentinel.hidden = done;
				}
				hideStatus();
				animateFilterIn(getCards());
				filterAnimating = false;
				if (!done) {
					startObserver();
				}
				return;
			}

			fetchPage(1, { append: false, silent: true })
				.then((result) => {
					animateFilterIn(result && result.nodes ? result.nodes : []);
				})
				.finally(() => {
					filterAnimating = false;
				});
		});
	};

	if (filters) {
		filters.addEventListener('click', (e) => {
			const btn = e.target.closest('[data-rh-sector-filter]');
			if (!btn || filterAnimating) {
				return;
			}
			const slug = btn.getAttribute('data-rh-sector-filter') || '';
			if (slug === activeSector) {
				return;
			}
			setActiveFilterUi(btn);
			applySectorFilter(slug);
		});
	}

	if (sentinel && restUrl) {
		if (initialPage < initialTotalPages) {
			startObserver();
		} else {
			sentinel.hidden = true;
		}
	}
})();

