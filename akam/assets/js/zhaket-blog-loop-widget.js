(function () {
	'use strict';

	function isRtlContext(root) {
		if (root && root.getAttribute('dir') === 'rtl') {
			return true;
		}

		return document.documentElement.getAttribute('dir') === 'rtl';
	}

	function initZhaketBlogLoop(scope) {
		if (typeof window.Swiper === 'undefined') {
			return;
		}

		var sliders = (scope || document).querySelectorAll('[data-webmz-zhaket-blog-loop]');

		sliders.forEach(function (slider) {
			if (slider.swiper) {
				slider.swiper.destroy(true, true);
				delete slider.dataset.webmzZblReady;
			}

			if (slider.dataset.webmzZblReady === 'yes') {
				return;
			}

			var config = {};

			try {
				config = JSON.parse(slider.getAttribute('data-webmz-zhaket-blog-loop') || '{}');
			} catch (error) {
				config = {};
			}

			var wrap = slider.closest('.webmz-zbl__slider-wrap');
			var slideCount = slider.querySelectorAll('.swiper-slide').length;
			var isRtl = isRtlContext(slider.closest('.webmz-zbl') || slider);
			var wantsLoop = config.loop !== false;
			var useLoop = wantsLoop && slideCount > 1;

			var options = {
				slidesPerView: config.slidesMobile || 1,
				spaceBetween: Number(config.spaceBetween || 20),
				speed: 500,
				rtl: isRtl,
				watchOverflow: true,
				loop: useLoop,
				grabCursor: config.grabCursor !== false,
				observer: true,
				observeParents: true,
				breakpoints: {
					576: {
						slidesPerView: config.slidesTablet || 2,
						spaceBetween: Number(config.spaceBetween || 20)
					},
					992: {
						slidesPerView: config.slidesDesktop || 4,
						spaceBetween: Number(config.spaceBetween || 20)
					}
				}
			};

			if (config.autoplay && slideCount > 1) {
				options.autoplay = {
					delay: Number(config.autoplayDelay || 4000),
					disableOnInteraction: false,
					pauseOnMouseEnter: true
				};
			}

			if (config.navigation && wrap) {
				options.navigation = {
					nextEl: wrap.querySelector('.webmz-zbl__arrow--next'),
					prevEl: wrap.querySelector('.webmz-zbl__arrow--prev')
				};
			}

			if (config.pagination && wrap) {
				var paginationEl = wrap.querySelector('.webmz-zbl__pagination');

				if (paginationEl) {
					options.pagination = {
						el: paginationEl,
						clickable: true
					};
				}
			}

			slider.dataset.webmzZblReady = 'yes';
			new window.Swiper(slider, options);
		});
	}

	var elementorHookBound = false;

	function bindElementorHook() {
		if (elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		elementorHookBound = true;

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/webmz-zhaket-blog-loop.default',
			function ($scope) {
				initZhaketBlogLoop($scope[0]);
			}
		);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initZhaketBlogLoop(document);
		});
	} else {
		initZhaketBlogLoop(document);
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', bindElementorHook);

		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			bindElementorHook();
		}
	}
})();
