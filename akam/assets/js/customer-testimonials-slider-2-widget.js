(function () {
    'use strict';

    function isRtlSlider(slider) {
        var root = slider.closest('[dir]');

        if (root && root.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function initCustomerTestimonialsSlider2(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = (scope || document).querySelectorAll('[data-webmz-cts2-slider]');

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                slider.swiper.destroy(true, true);
                delete slider.dataset.webmzCts2Ready;
            }

            if (slider.dataset.webmzCts2Ready === 'yes') {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-webmz-cts2-slider') || '{}');
            } catch (error) {
                config = {};
            }

            var shell = slider.closest('.webmz-cts2__shell');
            var main = slider.closest('.webmz-cts2__main');
            var slideCount = slider.querySelectorAll('.swiper-slide').length;
            var isRtl = isRtlSlider(slider);
            var wantsLoop = !!config.loop;
            var useLoop = wantsLoop && slideCount >= 3;
            var useRewind = wantsLoop && slideCount < 3;

            slider.dataset.webmzCts2Ready = 'yes';

            var options = {
                slidesPerView: config.slidesMobile || 1,
                spaceBetween: config.spaceBetween || 20,
                speed: 500,
                rtl: isRtl,
                watchOverflow: true,
                observer: true,
                observeParents: true,
                loop: useLoop,
                rewind: useRewind,
                breakpoints: {
                    576: {
                        slidesPerView: config.slidesTablet || 2,
                        spaceBetween: config.spaceBetween || 20
                    },
                    992: {
                        slidesPerView: config.slidesDesktop || 4,
                        spaceBetween: config.spaceBetween || 20
                    }
                }
            };

            if (config.autoplay) {
                options.autoplay = {
                    delay: Number(config.autoplayDelay || 5000),
                    disableOnInteraction: false
                };
            }

            if (config.navigation !== false && shell) {
                options.navigation = {
                    nextEl: shell.querySelector('.webmz-cts2__arrow--next'),
                    prevEl: shell.querySelector('.webmz-cts2__arrow--prev')
                };
            }

            if (config.pagination !== false && main) {
                options.pagination = {
                    el: main.querySelector('.webmz-cts2__pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initCustomerTestimonialsSlider2(document);
        });
    } else {
        initCustomerTestimonialsSlider2(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/webmz-customer-testimonials-slider-2.default',
                function ($scope) {
                    initCustomerTestimonialsSlider2($scope[0]);
                }
            );
        });
    }
})();
