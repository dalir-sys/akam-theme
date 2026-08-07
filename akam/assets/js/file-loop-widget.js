(function () {
	'use strict';

	var cache = new Map();
	var POPUP_HIDE_MS = 220;
	var POPUP_LOADER_MIN_MS = 60;
	var MOBILE_MQ = window.matchMedia('(max-width: 767px)');

	function getConfig() {
		return window.webmzFileLoop || {};
	}

	function isMobilePopupMode() {
		return MOBILE_MQ.matches;
	}

	function escapeHtml(value) {
		return String(value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function escapeAttr(value) {
		return escapeHtml(value);
	}

	function lockBodyScroll() {
		document.documentElement.classList.add('webmz-fl-popup-open');
	}

	function unlockBodyScroll() {
		document.documentElement.classList.remove('webmz-fl-popup-open');
	}

	function isRtlSlider(slider) {
		var root = slider.closest('[dir]');

		if (root && root.getAttribute('dir') === 'rtl') {
			return true;
		}

		return document.documentElement.getAttribute('dir') === 'rtl';
	}

	function initFileLoopSliders(scope) {
		if (typeof window.Swiper === 'undefined') {
			return;
		}

		var sliders = (scope || document).querySelectorAll('[data-webmz-fl-slider]');

		sliders.forEach(function (slider) {
			if (slider.swiper) {
				slider.swiper.destroy(true, true);
				delete slider.dataset.webmzFlReady;
			}

			if (slider.dataset.webmzFlReady === 'yes') {
				return;
			}

			var settings = {};

			try {
				settings = JSON.parse(slider.getAttribute('data-webmz-fl-slider') || '{}');
			} catch (error) {
				settings = {};
			}

			var widget = slider.closest('.webmz-fl');
			var trackWrap = slider.closest('.webmz-fl__track-wrap');
			var isRtl = isRtlSlider(slider);

			slider.dataset.webmzFlReady = 'yes';

			var options = {
				slidesPerView: 'auto',
				spaceBetween: settings.spaceBetween || 16,
				speed: 450,
				rtl: isRtl,
				watchOverflow: true,
				observer: true,
				observeParents: true,
				grabCursor: true,
				resistanceRatio: 0.75,
				freeMode: {
					enabled: settings.freeMode !== false,
					momentum: true,
					momentumRatio: 0.45,
					momentumBounce: true,
					sticky: false
				}
			};

			if (settings.autoplay) {
				options.autoplay = {
					delay: Number(settings.autoplayDelay || 4000),
					disableOnInteraction: false
				};
			}

			if (settings.pagination !== false && trackWrap) {
				var paginationEl = trackWrap.querySelector('.webmz-fl__pagination');
				if (paginationEl) {
					options.pagination = {
						el: paginationEl,
						clickable: true
					};
				}
			}

			new window.Swiper(slider, options);
		});
	}

	function ensureWidgetId(widget) {
		if (!widget.dataset.webmzFlId) {
			widget.dataset.webmzFlId = 'webmz-fl-' + Math.random().toString(36).slice(2, 9);
		}

		return widget.dataset.webmzFlId;
	}

	function getPopup(widget) {
		var ownerId = ensureWidgetId(widget);
		var portaled = document.querySelector('.webmz-fl__popup[data-webmz-fl-owner="' + ownerId + '"]');

		if (portaled) {
			return portaled;
		}

		return widget.querySelector('.webmz-fl__popup');
	}

	function ensurePopupPortaled(widget) {
		var popup = getPopup(widget);

		if (!popup) {
			return null;
		}

		var ownerId = ensureWidgetId(widget);

		if (popup.dataset.webmzFlPortaled !== 'yes') {
			popup.classList.add('webmz-fl__popup--fixed');
			popup.dataset.webmzFlOwner = ownerId;
			popup.dataset.webmzFlPortaled = 'yes';
			syncPopupThemeVars(widget, popup);
			document.body.appendChild(popup);
		}

		return popup;
	}

	function syncPopupThemeVars(widget, popup) {
		if (!widget || !popup) {
			return;
		}

		var styles = window.getComputedStyle(widget);
		popup.style.setProperty('--webmz-fl-accent', styles.getPropertyValue('--webmz-fl-accent').trim() || 'var(--webmz-color-primary, #0878f9)');
	}

	function getLoadingMarkup() {
		var config = getConfig();
		var loadingText = config.loadingText || 'در حال بارگذاری...';

		return (
			'<div class="webmz-fl__popup-inner webmz-fl__popup-inner--loading" role="status" aria-live="polite">' +
			'<div class="webmz-fl__popup-loading">' +
			'<div class="webmz-fl__popup-loader" aria-hidden="true"></div>' +
			'<span class="webmz-fl__popup-loading-text">' + escapeHtml(loadingText) + '</span>' +
			'</div>' +
			'</div>'
		);
	}

	function getMobilePopupShell(innerHtml, productUrl) {
		var config = getConfig();
		var closeLabel = config.closeLabel || 'بستن';
		var viewLabel = config.viewProductLabel || 'مشاهده محصول';
		var popupTitle = config.popupTitle || 'جزئیات محصول';
		var viewLink = productUrl
			? '<a class="webmz-fl__popup-view" href="' + escapeAttr(productUrl) + '">' + escapeHtml(viewLabel) + '</a>'
			: '';

		return (
			'<div class="webmz-fl__popup-overlay" data-webmz-fl-popup-close tabindex="-1" aria-hidden="true"></div>' +
			'<div class="webmz-fl__popup-dialog" role="dialog" aria-modal="true" aria-label="' + escapeAttr(popupTitle) + '">' +
			'<button type="button" class="webmz-fl__popup-close" data-webmz-fl-popup-close aria-label="' + escapeAttr(closeLabel) + '">' +
			'<span aria-hidden="true">&times;</span>' +
			'</button>' +
			'<div class="webmz-fl__popup-content">' + innerHtml + '</div>' +
			viewLink +
			'</div>'
		);
	}

	function setPopupMarkup(popup, html, productUrl) {
		if (isMobilePopupMode()) {
			popup.classList.add('webmz-fl__popup--mobile');
			popup.innerHTML = getMobilePopupShell(html, productUrl);
			popup.style.left = '';
			popup.style.top = '';
			popup.style.display = '';
			return;
		}

		popup.classList.remove('webmz-fl__popup--mobile');
		popup.innerHTML = html;
	}

	function positionPopup(card, popup) {
		if (isMobilePopupMode()) {
			return;
		}

		var cardRect = card.getBoundingClientRect();
		var gap = 12;
		var left;
		var top;
		var popupRect;

		popup.style.display = 'block';
		popup.style.visibility = 'hidden';
		popup.style.left = '-9999px';
		popup.style.top = '0';

		popupRect = popup.getBoundingClientRect();
		left = cardRect.left + (cardRect.width / 2) - (popupRect.width / 2);
		top = cardRect.top - popupRect.height - gap;

		left = Math.max(12, Math.min(left, window.innerWidth - popupRect.width - 12));

		if (top < 12) {
			top = cardRect.bottom + gap;
		}

		popup.style.left = left + 'px';
		popup.style.top = top + 'px';
		popup.style.visibility = '';
	}

	function hidePopup(widget, immediate) {
		var popup = getPopup(widget);
		var activeCard = widget.querySelector('.webmz-fl__card.is-hover');

		if (!popup) {
			return;
		}

		if (!popup.classList.contains('is-visible') && popup.hidden) {
			return;
		}

		popup.classList.remove('is-visible', 'is-loading');

		if (popup.classList.contains('webmz-fl__popup--mobile')) {
			unlockBodyScroll();
		}

		if (immediate) {
			popup.classList.remove('is-hiding', 'webmz-fl__popup--mobile');
			popup.hidden = true;
			popup.setAttribute('aria-hidden', 'true');
			popup.innerHTML = '';
			popup.style.display = '';
		} else {
			popup.classList.add('is-hiding');

			window.setTimeout(function () {
				if (!popup.classList.contains('is-hiding')) {
					return;
				}

				popup.classList.remove('is-hiding', 'webmz-fl__popup--mobile');
				popup.hidden = true;
				popup.setAttribute('aria-hidden', 'true');
				popup.innerHTML = '';
				popup.style.display = '';
				unlockBodyScroll();
			}, POPUP_HIDE_MS);
		}

		if (activeCard) {
			activeCard.classList.remove('is-hover');
		}
	}

	function openPopupShell(widget, card) {
		var popup = ensurePopupPortaled(widget);
		var prevCard = widget.querySelector('.webmz-fl__card.is-hover');

		if (!popup) {
			return null;
		}

		if (prevCard && prevCard !== card) {
			prevCard.classList.remove('is-hover');
		}

		card.classList.add('is-hover');
		syncPopupThemeVars(widget, popup);
		popup.classList.remove('is-hiding');
		setPopupMarkup(popup, getLoadingMarkup(), card.getAttribute('href') || '');
		popup.hidden = false;
		popup.removeAttribute('hidden');
		popup.setAttribute('aria-hidden', 'false');
		popup.classList.add('is-loading', 'is-visible');
		popup.style.display = isMobilePopupMode() ? 'flex' : 'block';

		if (isMobilePopupMode()) {
			lockBodyScroll();
		} else {
			positionPopup(card, popup);
		}

		return popup;
	}

	function showPopup(widget, card, html) {
		var popup = getPopup(widget);

		if (!popup || popup.hidden) {
			popup = openPopupShell(widget, card);
		}

		if (!popup) {
			return;
		}

		card.classList.add('is-hover');
		setPopupMarkup(popup, html, card.getAttribute('href') || '');
		popup.classList.remove('is-loading', 'is-hiding');
		popup.classList.add('is-visible');
		popup.hidden = false;
		popup.removeAttribute('hidden');
		popup.setAttribute('aria-hidden', 'false');
		popup.style.display = isMobilePopupMode() ? 'flex' : 'block';

		if (isMobilePopupMode()) {
			lockBodyScroll();
		} else {
			positionPopup(card, popup);
		}
	}

	function getCacheKey(productId, badgeFallback) {
		return String(productId) + '|' + badgeFallback;
	}

	function getCachedPopupHtml(productId, badgeFallback) {
		var cacheKey = getCacheKey(productId, badgeFallback);
		return cache.has(cacheKey) ? cache.get(cacheKey) : '';
	}

	function fetchPopupHtml(productId, badgeFallback, signal) {
		var config = getConfig();
		var cacheKey = getCacheKey(productId, badgeFallback);

		if (cache.has(cacheKey)) {
			return Promise.resolve(cache.get(cacheKey));
		}

		if (!config.ajaxUrl || !config.nonce) {
			return Promise.reject(new Error('missing config'));
		}

		var body = new URLSearchParams();
		body.append('action', 'webmz_file_loop_hover');
		body.append('nonce', config.nonce);
		body.append('product_id', String(productId));
		body.append('badge_fallback', badgeFallback || '');

		return fetch(config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: body.toString(),
			signal: signal
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (payload) {
				if (!payload || !payload.success || !payload.data || !payload.data.html) {
					throw new Error((payload && payload.data && payload.data.message) || config.errorText || 'error');
				}

				cache.set(cacheKey, payload.data.html);
				return payload.data.html;
			});
	}

	function prefetchCardPopup(productId, badgeFallback) {
		if (!productId || getCachedPopupHtml(productId, badgeFallback)) {
			return;
		}

		fetchPopupHtml(productId, badgeFallback).catch(function () {
			// Silent prefetch failure.
		});
	}

	function prefetchVisibleCardPopups(widget) {
		var badgeFallback = widget.getAttribute('data-webmz-fl-badge-fallback') || '';
		var cards = widget.querySelectorAll('.webmz-fl__card');
		var limit = Math.min(cards.length, 8);
		var index;

		for (index = 0; index < limit; index += 1) {
			prefetchCardPopup(cards[index].getAttribute('data-product-id'), badgeFallback);
		}
	}

	function schedulePrefetchVisibleCardPopups(widget) {
		var run = function () {
			if (!widget.isConnected) {
				return;
			}

			prefetchVisibleCardPopups(widget);
		};

		if (typeof window.requestIdleCallback === 'function') {
			window.requestIdleCallback(run, { timeout: 1200 });
			return;
		}

		window.setTimeout(run, 600);
	}

	function initFileLoopHover(scope) {
		var widgets = (scope || document).querySelectorAll('.webmz-fl');

		widgets.forEach(function (widget) {
			if (widget.dataset.webmzFlHoverReady === 'yes') {
				return;
			}

			widget.dataset.webmzFlHoverReady = 'yes';
			ensureWidgetId(widget);

			var hoverTimer = null;
			var loaderTimer = null;
			var requestToken = 0;
			var activeCard = null;
			var abortController = null;

			function clearPendingTimers() {
				if (hoverTimer) {
					clearTimeout(hoverTimer);
					hoverTimer = null;
				}

				if (loaderTimer) {
					clearTimeout(loaderTimer);
					loaderTimer = null;
				}
			}

			function abortActiveFetch() {
				if (abortController) {
					abortController.abort();
					abortController = null;
				}
			}

			function invalidateSession() {
				requestToken += 1;
				clearPendingTimers();
				abortActiveFetch();
			}

			function terminateHover(immediateHide) {
				invalidateSession();

				if (activeCard) {
					activeCard.classList.remove('is-hover');
					activeCard = null;
				}

				hidePopup(widget, immediateHide !== false);
			}

			function isSessionActive(token, card) {
				return token === requestToken && activeCard === card;
			}

			function loadCardPopup(card) {
				var productId = card.getAttribute('data-product-id');
				var badgeFallback = widget.getAttribute('data-webmz-fl-badge-fallback') || '';
				var token = requestToken;
				var cachedHtml = getCachedPopupHtml(productId, badgeFallback);

				if (!productId) {
					return;
				}

				if (cachedHtml) {
					showPopup(widget, card, cachedHtml);
					return;
				}

				var loadStartedAt = Date.now();

				if (!openPopupShell(widget, card)) {
					return;
				}

				abortActiveFetch();
				abortController = typeof AbortController !== 'undefined' ? new AbortController() : null;

				fetchPopupHtml(productId, badgeFallback, abortController ? abortController.signal : undefined)
					.then(function (html) {
						if (!isSessionActive(token, card)) {
							return;
						}

						var elapsed = Date.now() - loadStartedAt;
						var remain = elapsed < POPUP_LOADER_MIN_MS ? (POPUP_LOADER_MIN_MS - elapsed) : 0;

						loaderTimer = window.setTimeout(function () {
							loaderTimer = null;

							if (!isSessionActive(token, card)) {
								return;
							}

							showPopup(widget, card, html);
						}, remain);
					})
					.catch(function (error) {
						if (error && error.name === 'AbortError') {
							return;
						}

						if (!isSessionActive(token, card)) {
							return;
						}

						terminateHover(true);
					});
			}

			function enterCard(card) {
				var popup = getPopup(widget);
				var isSameCard = activeCard === card;
				var popupVisible = popup && popup.classList.contains('is-visible') && !popup.hidden;

				if (isSameCard && popupVisible) {
					positionPopup(card, popup);
					return;
				}

				invalidateSession();

				if (activeCard && activeCard !== card) {
					activeCard.classList.remove('is-hover');
				}

				activeCard = card;
				loadCardPopup(card);
			}

			widget.addEventListener('mouseover', function (event) {
				if (isMobilePopupMode()) {
					return;
				}

				var card = event.target.closest('.webmz-fl__card');

				if (!card || !widget.contains(card)) {
					return;
				}

				var from = event.relatedTarget;
				if (from && card.contains(from)) {
					return;
				}

				enterCard(card);
			});

			widget.addEventListener('mouseleave', function (event) {
				if (isMobilePopupMode()) {
					return;
				}

				if (event.relatedTarget && widget.contains(event.relatedTarget)) {
					return;
				}

				terminateHover(true);
			});

			widget.addEventListener('click', function (event) {
				if (!isMobilePopupMode()) {
					return;
				}

				var card = event.target.closest('.webmz-fl__card');

				if (!card || !widget.contains(card)) {
					return;
				}

				event.preventDefault();
				enterCard(card);
			});

			document.addEventListener('click', function (event) {
				if (!isMobilePopupMode()) {
					return;
				}

				var closeTrigger = event.target.closest('[data-webmz-fl-popup-close]');

				if (!closeTrigger) {
					return;
				}

				var popup = closeTrigger.closest('.webmz-fl__popup');

				if (!popup || popup.dataset.webmzFlOwner !== ensureWidgetId(widget) || !popup.classList.contains('is-visible')) {
					return;
				}

				event.preventDefault();
				terminateHover(true);
			});

			document.addEventListener('keydown', function (event) {
				if (!isMobilePopupMode() || event.key !== 'Escape') {
					return;
				}

				var popup = getPopup(widget);

				if (!popup || popup.hidden || !popup.classList.contains('is-visible')) {
					return;
				}

				event.preventDefault();
				terminateHover(true);
			});

			window.addEventListener('resize', function () {
				var popup = getPopup(widget);
				var card = widget.querySelector('.webmz-fl__card.is-hover');

				if (!popup || !popup.classList.contains('is-visible') || !card) {
					return;
				}

				if (isMobilePopupMode()) {
					popup.style.display = 'flex';
					popup.style.left = '';
					popup.style.top = '';
					return;
				}

				popup.classList.remove('webmz-fl__popup--mobile');
				popup.style.display = 'block';
				positionPopup(card, popup);
			});

			window.addEventListener('scroll', function () {
				if (isMobilePopupMode()) {
					return;
				}

				var popup = getPopup(widget);
				var card = widget.querySelector('.webmz-fl__card.is-hover');

				if (popup && popup.classList.contains('is-visible') && card) {
					positionPopup(card, popup);
				}
			}, true);

			if (typeof MOBILE_MQ.addEventListener === 'function') {
				MOBILE_MQ.addEventListener('change', function () {
					if (getPopup(widget) && getPopup(widget).classList.contains('is-visible')) {
						terminateHover(true);
					}
				});
			} else if (typeof MOBILE_MQ.addListener === 'function') {
				MOBILE_MQ.addListener(function () {
					if (getPopup(widget) && getPopup(widget).classList.contains('is-visible')) {
						terminateHover(true);
					}
				});
			}

			schedulePrefetchVisibleCardPopups(widget);
		});
	}

	function initScope(scope) {
		initFileLoopSliders(scope);
		initFileLoopHover(scope);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initScope(document);
		});
	} else {
		initScope(document);
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/webmz-file-loop.default',
				function ($scope) {
					initScope($scope[0]);
				}
			);
		});
	}
})();
