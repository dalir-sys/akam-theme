(function ($) {
	'use strict';

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

		button.classList.toggle('is-loading', !!isLoading);

		if (isLoading) {
			button.disabled = true;
			button.setAttribute('aria-busy', 'true');
			return;
		}

		button.removeAttribute('aria-busy');
		button.disabled = false;
	}

	function removeViewCartLinks(button) {
		var actions = button.closest('.webmz-zpl__actions');

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

		setButtonLoading(button, true);

		$.ajax({
			url: cfg.addToCartUrl,
			method: 'POST',
			dataType: 'json',
			data: {
				product_id: button.getAttribute('data-product-id'),
				quantity: button.getAttribute('data-quantity') || 1
			}
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
				// Pass false so WooCommerce does not append "View cart" link.
				$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash || '', false, { immediateOpen: true }]);
				button.classList.remove('added');
				removeViewCartLinks(button);
				openMiniCart();
			}
		}).always(function () {
			setButtonLoading(button, false);
			removeViewCartLinks(button);
		});
	}

	function bindAddToCart(scope) {
		(scope || document).querySelectorAll('[data-webmz-zpl-add-to-cart]').forEach(function (button) {
			if (button.dataset.webmzZplCartReady === 'yes') {
				return;
			}

			button.dataset.webmzZplCartReady = 'yes';
			button.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				addProductToCart(button);
			});
		});
	}

	function isRtlSlider(slider) {
		var root = slider.closest('[dir]');

		if (root && root.getAttribute('dir') === 'rtl') {
			return true;
		}

		return document.documentElement.getAttribute('dir') === 'rtl';
	}

	function getSlidesPerView(config, width) {
		var viewport = typeof width === 'number' ? width : (window.innerWidth || 0);

		if (viewport >= 1025) {
			return Number(config.slidesDesktop || 4);
		}

		if (viewport >= 768) {
			return Number(config.slidesTablet || 2);
		}

		return Number(config.slidesMobile || 1);
	}

	function getSliderWidth(slider) {
		var viewport = slider.closest('.webmz-zpl__slider-viewport');

		if (viewport) {
			return viewport.clientWidth || viewport.getBoundingClientRect().width || 0;
		}

		return slider.clientWidth || slider.getBoundingClientRect().width || 0;
	}

	function isElementorPreview() {
		var body = document.body;

		return body.classList.contains('elementor-editor-active') ||
			body.classList.contains('elementor-editor-preview');
	}

	function getElementorDeviceSlides(config) {
		if (!window.elementorFrontend || typeof window.elementorFrontend.getCurrentDeviceMode !== 'function') {
			return null;
		}

		var mode = window.elementorFrontend.getCurrentDeviceMode();

		if (mode === 'mobile') {
			return Number(config.slidesMobile || 1);
		}

		if (mode === 'tablet') {
			return Number(config.slidesTablet || 2);
		}

		return Number(config.slidesDesktop || 4);
	}

	function initZhaketProductLoopSliders(scope) {
		if (typeof window.Swiper === 'undefined') {
			return;
		}

		var sliders = (scope || document).querySelectorAll('[data-webmz-zpl-slider]');

		sliders.forEach(function (slider) {
			if (slider.swiper) {
				slider.swiper.destroy(true, true);
				delete slider.dataset.webmzZplReady;
			}

			if (slider.dataset.webmzZplReady === 'yes') {
				return;
			}

			var config = {};

			try {
				config = JSON.parse(slider.getAttribute('data-webmz-zpl-slider') || '{}');
			} catch (error) {
				config = {};
			}

			var trackWrap = slider.closest('.webmz-zpl__track-wrap');
			var isRtl = isRtlSlider(slider);
			var slideCount = slider.querySelectorAll('.swiper-slide').length;
			var sliderWidth = getSliderWidth(slider);
			var slidesPerView = getSlidesPerView(
				config,
				sliderWidth > 0 ? sliderWidth : window.innerWidth
			);
			var wantsLoop = !!config.loop;
			var canLoop = wantsLoop && slideCount > slidesPerView;
			var editorSlides = isElementorPreview() ? getElementorDeviceSlides(config) : null;

			var options = {
				slidesPerView: editorSlides || Number(config.slidesMobile || 1),
				spaceBetween: Number(config.spaceBetween || 16),
				speed: 450,
				rtl: isRtl,
				watchOverflow: true,
				observer: true,
				observeParents: true,
				observeSlideChildren: true,
				resizeObserver: true,
				grabCursor: true,
				loop: editorSlides ? (wantsLoop && slideCount > editorSlides) : canLoop
			};

			if (!editorSlides) {
				options.breakpoints = {
					768: {
						slidesPerView: Number(config.slidesTablet || 2),
						spaceBetween: Number(config.spaceBetween || 16)
					},
					1025: {
						slidesPerView: Number(config.slidesDesktop || 4),
						spaceBetween: Number(config.spaceBetween || 16)
					}
				};
			}

			if (config.autoplay) {
				options.autoplay = {
					delay: Number(config.autoplayDelay || 4000),
					disableOnInteraction: false,
					pauseOnMouseEnter: true
				};
			}

			if (config.navigation && trackWrap) {
				var prevEl = trackWrap.querySelector('.webmz-zpl__nav--prev');
				var nextEl = trackWrap.querySelector('.webmz-zpl__nav--next');

				if (prevEl && nextEl) {
					options.navigation = {
						prevEl: prevEl,
						nextEl: nextEl
					};
				}
			}

			if (config.pagination && trackWrap) {
				var paginationEl = trackWrap.querySelector('.webmz-zpl__pagination');

				if (paginationEl) {
					options.pagination = {
						el: paginationEl,
						clickable: true
					};
				}
			}

			slider.dataset.webmzZplReady = 'yes';
			var swiper = new window.Swiper(slider, options);

			requestAnimationFrame(function () {
				if (!swiper || swiper.destroyed) {
					return;
				}

				swiper.update();
			});
		});
	}

	function init(scope) {
		initZhaketProductLoopSliders(scope);
		bindAddToCart(scope);
	}

	function scheduleInit(scope) {
		requestAnimationFrame(function () {
			init(scope);
		});
	}

	var elementorHookBound = false;

	function bindElementorHook() {
		if (elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		elementorHookBound = true;

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/webmz-zhaket-product-loop.default',
			function ($scope) {
				scheduleInit($scope[0]);
			}
		);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			init(document);
		});
	} else {
		init(document);
	}

	$(window).on('elementor/frontend/init', bindElementorHook);

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		bindElementorHook();
	}

	if (window.elementor && window.elementor.channels && window.elementor.channels.deviceMode) {
		window.elementor.channels.deviceMode.on('change', function () {
			if (!isElementorPreview()) {
				return;
			}

			initZhaketProductLoopSliders(document);
		});
	}
}(jQuery));
