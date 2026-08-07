(function ($) {
	'use strict';

	var globalConfig = window.webmzZhaketProductTabs || {};

	function parseConfig(root) {
		try {
			return JSON.parse(root.getAttribute('data-webmz-zhaket-product-tabs') || '{}');
		} catch (error) {
			return {};
		}
	}

	function widgetConfig() {
		return window.webmzTadrisWidgets || {};
	}

	function openMiniCart() {
		if (typeof window.webmzOpenHeaderCart === 'function') {
			window.webmzOpenHeaderCart();
		}
	}

	function setButtonLoading(button, isLoading) {
		if (!button) {
			return;
		}

		var label = button.querySelector('.webmz-zpt__btn-label');
		var spinner = button.querySelector('.webmz-zpt__btn-spinner');

		button.classList.toggle('is-loading', !!isLoading);

		if (label) {
			label.hidden = !!isLoading;
			label.setAttribute('aria-hidden', isLoading ? 'true' : 'false');
		}

		if (spinner) {
			spinner.hidden = !isLoading;
			spinner.setAttribute('aria-hidden', isLoading ? 'false' : 'true');
		}

		if (isLoading) {
			button.disabled = true;
			button.setAttribute('aria-busy', 'true');
			return;
		}

		button.removeAttribute('aria-busy');
		button.disabled = false;
	}

	function ensureCartButtonStructure(button) {
		if (!button || button.querySelector('.webmz-zpt__btn-label')) {
			return;
		}

		var text = (button.textContent || '').trim();

		button.textContent = '';

		var label = document.createElement('span');
		label.className = 'webmz-zpt__btn-label';
		label.textContent = text;

		var spinner = document.createElement('span');
		spinner.className = 'webmz-zpt__btn-spinner';
		spinner.setAttribute('aria-hidden', 'true');
		spinner.hidden = true;

		button.appendChild(label);
		button.appendChild(spinner);
	}

	function removeViewCartLinks(button) {
		var actions = button.closest('.webmz-zpt__card-actions');

		if (!actions) {
			return;
		}

		actions.querySelectorAll('.added_to_cart').forEach(function (link) {
			link.remove();
		});
	}

	function addProductToCart(button) {
		var cfg = widgetConfig();

		if (!cfg.addToCartUrl || button.classList.contains('is-loading')) {
			return;
		}

		var payload = {
			product_id: button.getAttribute('data-product-id'),
			quantity: button.getAttribute('data-quantity') || 1
		};

		setButtonLoading(button, true);

		$.ajax({
			url: cfg.addToCartUrl,
			method: 'POST',
			dataType: 'json',
			data: payload
		}).done(function (response) {
			if (response && response.error && response.already_in_cart) {
				if (typeof window.webmzShowAlreadyInCartAlert === 'function') {
					window.webmzShowAlreadyInCartAlert(response);
				}
				return;
			}

			if (response && response.error && response.product_url) {
				window.location.href = response.product_url;
				return;
			}

			if (response && response.fragments) {
				$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash || '', false, { immediateOpen: true }]);
				openMiniCart();
			}
		}).always(function () {
			setButtonLoading(button, false);
			removeViewCartLinks(button);
		});
	}

	function setLoading(panel, isLoading) {
		if (!panel) {
			return;
		}

		panel.classList.toggle('is-loading', !!isLoading);

		var loader = panel.querySelector('.webmz-zpt__loading');

		if (loader) {
			if (isLoading) {
				loader.removeAttribute('hidden');
				loader.setAttribute('aria-hidden', 'false');
			} else {
				loader.setAttribute('hidden', 'hidden');
				loader.setAttribute('aria-hidden', 'true');
			}
		}
	}

	function isRtlSlider(slider) {
		var root = slider.closest('[dir]');

		if (root && root.getAttribute('dir') === 'rtl') {
			return true;
		}

		return document.documentElement.getAttribute('dir') === 'rtl';
	}

	function getGapFromRoot(slider, fallback) {
		var root = slider.closest('.webmz-zpt');

		if (!root) {
			return fallback;
		}

		var computed = window.getComputedStyle(root).getPropertyValue('--webmz-zpt-gap').trim();

		if (!computed) {
			return fallback;
		}

		var parsed = parseInt(computed, 10);

		return Number.isFinite(parsed) ? parsed : fallback;
	}

	function initSliders(scope) {
		if (typeof window.Swiper === 'undefined') {
			return;
		}

		(scope || document).querySelectorAll('[data-webmz-zpt-slider]').forEach(function (slider) {
			if (slider.swiper) {
				slider.swiper.destroy(true, true);
				delete slider.dataset.webmzZptReady;
			}

			var settings = {};

			try {
				settings = JSON.parse(slider.getAttribute('data-webmz-zpt-slider') || '{}');
			} catch (error) {
				settings = {};
			}

			var trackWrap = slider.closest('.webmz-zpt__track-wrap');
			var prevEl = trackWrap ? trackWrap.querySelector('.webmz-zpt__nav--prev') : null;
			var nextEl = trackWrap ? trackWrap.querySelector('.webmz-zpt__nav--next') : null;
			var paginationEl = trackWrap ? trackWrap.querySelector('.webmz-zpt__pagination') : null;
			var isRtl = isRtlSlider(slider);
			var gap = getGapFromRoot(slider, settings.spaceBetween || 20);

			var options = {
				slidesPerView: settings.slidesMobile || 1,
				spaceBetween: gap,
				speed: 500,
				rtl: isRtl,
				watchOverflow: true,
				observer: true,
				observeParents: true,
				grabCursor: true,
				resistanceRatio: 0.75,
				breakpoints: {
					768: {
						slidesPerView: settings.slidesTablet || 3,
						spaceBetween: gap
					},
					992: {
						slidesPerView: settings.slidesDesktop || 4,
						spaceBetween: gap
					}
				}
			};

			if (settings.loop) {
				options.loop = true;
			}

			if (settings.autoplay) {
				options.autoplay = {
					delay: Number(settings.autoplayDelay || 4000),
					disableOnInteraction: false,
					pauseOnMouseEnter: true
				};
			}

			if (settings.navigation !== false && prevEl && nextEl) {
				options.navigation = {
					prevEl: prevEl,
					nextEl: nextEl
				};
			}

			if (settings.pagination !== false && paginationEl) {
				options.pagination = {
					el: paginationEl,
					clickable: true
				};
			}

			slider.dataset.webmzZptReady = 'yes';
			new window.Swiper(slider, options);
		});
	}

	function initPanelContent(scope) {
		initSliders(scope);
		initAddToCart(scope);
		initCardHover(scope);
	}

	function loadTabPanel(root, panel, tabIndex, config) {
		var inner = panel.querySelector('.webmz-zpt__panel-inner');

		if (!inner || inner.dataset.webmzZptLoaded === 'yes') {
			return Promise.resolve();
		}

		if (!globalConfig.ajaxUrl || !globalConfig.nonce) {
			inner.innerHTML = '<div class="webmz-zpt__empty">' + (globalConfig.errorText || 'خطایی رخ داد.') + '</div>';
			inner.dataset.webmzZptLoaded = 'yes';
			return Promise.resolve();
		}

		setLoading(panel, true);

		var body = new FormData();
		body.append('action', 'webmz_zhaket_product_tabs_load');
		body.append('nonce', globalConfig.nonce);
		body.append('tab_index', String(tabIndex));
		body.append('config', JSON.stringify(config));

		return window.fetch(globalConfig.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		}).then(function (response) {
			return response.json();
		}).then(function (response) {
			if (response && response.success && response.data && response.data.html) {
				inner.innerHTML = response.data.html;
				inner.dataset.webmzZptLoaded = 'yes';
				initPanelContent(inner);
				return;
			}

			inner.innerHTML = '<div class="webmz-zpt__empty">' + ((response && response.data && response.data.message) || globalConfig.errorText || 'خطایی رخ داد.') + '</div>';
			inner.dataset.webmzZptLoaded = 'yes';
		}).catch(function () {
			inner.innerHTML = '<div class="webmz-zpt__empty">' + (globalConfig.errorText || 'خطایی رخ داد.') + '</div>';
			inner.dataset.webmzZptLoaded = 'yes';
		}).finally(function () {
			setLoading(panel, false);
		});
	}

	function activateTab(root, tabIndex) {
		var tabs = root.querySelectorAll('[data-webmz-zpt-tab]');
		var panels = root.querySelectorAll('[data-webmz-zpt-panel]');
		var config = parseConfig(root);
		var targetPanel = null;

		tabs.forEach(function (tab) {
			var active = tab.getAttribute('data-webmz-zpt-tab') === String(tabIndex);
			tab.classList.toggle('is-active', active);
			tab.setAttribute('aria-selected', active ? 'true' : 'false');
		});

		panels.forEach(function (panel) {
			var active = panel.getAttribute('data-webmz-zpt-panel') === String(tabIndex);
			panel.classList.toggle('is-active', active);

			if (active) {
				panel.removeAttribute('hidden');
				targetPanel = panel;
			} else {
				panel.setAttribute('hidden', 'hidden');
			}
		});

		if (targetPanel) {
			loadTabPanel(root, targetPanel, tabIndex, config);
		}
	}

	function initAddToCart(scope) {
		(scope || document).querySelectorAll('[data-webmz-zpt-add-to-cart]').forEach(function (button) {
			ensureCartButtonStructure(button);

			if (button.dataset.webmzZptCartReady === 'yes') {
				return;
			}

			button.dataset.webmzZptCartReady = 'yes';
			button.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				if (button.disabled) {
					return;
				}

				addProductToCart(button);
			});
		});
	}

	function initCardHover(scope) {
		(scope || document).querySelectorAll('.webmz-zpt__card').forEach(function (card) {
			if (card.dataset.webmzZptHoverReady === 'yes') {
				return;
			}

			card.dataset.webmzZptHoverReady = 'yes';

			var wrap = card.closest('.webmz-zpt__card-wrap');

			function setHovered(isHovered) {
				card.classList.toggle('is-hovered', isHovered);

				if (wrap) {
					wrap.classList.toggle('is-hovered-wrap', isHovered);
				}
			}

			card.addEventListener('mouseenter', function () {
				setHovered(true);
			});

			card.addEventListener('mouseleave', function () {
				setHovered(false);
			});

			card.addEventListener('focusin', function () {
				setHovered(true);
			});

			card.addEventListener('focusout', function (event) {
				if (!card.contains(event.relatedTarget)) {
					setHovered(false);
				}
			});
		});
	}

	function initRoot(root) {
		if (!root || root.dataset.webmzZptReady === 'yes') {
			return;
		}

		root.dataset.webmzZptReady = 'yes';

		var firstPanel = root.querySelector('.webmz-zpt__panel.is-active .webmz-zpt__panel-inner');

		if (firstPanel) {
			firstPanel.dataset.webmzZptLoaded = 'yes';
			initPanelContent(firstPanel);
		}

		root.addEventListener('click', function (event) {
			var tab = event.target.closest('[data-webmz-zpt-tab]');

			if (!tab || !root.contains(tab)) {
				return;
			}

			event.preventDefault();
			activateTab(root, tab.getAttribute('data-webmz-zpt-tab'));
		});
	}

	function initScope(scope) {
		(scope || document).querySelectorAll('[data-webmz-zhaket-product-tabs]').forEach(initRoot);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initScope(document);
		});
	} else {
		initScope(document);
	}

	$(window).on('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/webmz-zhaket-product-tabs.default',
			function ($scope) {
				initScope($scope[0]);
			}
		);
	});
}(jQuery));
