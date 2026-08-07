(function () {
    'use strict';

    function isRtlSlider(slider) {
        if (slider.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function getBreakpoint() {
        var width = window.innerWidth || document.documentElement.clientWidth || 0;

        if (width < 768) {
            return 'mobile';
        }

        if (width < 1025) {
            return 'tablet';
        }

        return 'desktop';
    }

    function getSlidesPerView(config) {
        var perView = config.slidesPerView || {};
        var bp = getBreakpoint();

        if (bp === 'mobile' && perView.mobile) {
            return Number(perView.mobile) || 2;
        }

        if (bp === 'tablet' && perView.tablet) {
            return Number(perView.tablet) || 3;
        }

        return Number(perView.desktop) || 4;
    }

    function initZhaketTopDeveloper(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = [];

        if (scope && scope.matches && scope.matches('[data-webmz-zhaket-top-developer]')) {
            sliders = [scope];
        } else {
            sliders = Array.prototype.slice.call(
                (scope || document).querySelectorAll('[data-webmz-zhaket-top-developer]')
            );
        }

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                slider.swiper.destroy(true, true);
                delete slider.dataset.webmzZtdReady;
            }

            if (slider.dataset.webmzZtdReady === 'yes') {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-webmz-zhaket-top-developer') || '{}');
            } catch (error) {
                config = {};
            }

            var slideCount = slider.querySelectorAll('.swiper-slide').length;

            if (slideCount < 2) {
                slider.dataset.webmzZtdReady = 'yes';
                return;
            }

            var wrap = slider.closest('.webmz-ztd__products-wrap');
            var prevBtn = wrap ? wrap.querySelector('.webmz-ztd__nav-btn--prev') : null;
            var nextBtn = wrap ? wrap.querySelector('.webmz-ztd__nav-btn--next') : null;
            var rtl = isRtlSlider(slider);
            var wantsLoop = config.loop !== false;
            var canLoop = wantsLoop && slideCount > 2;
            var slidesPerView = getSlidesPerView(config);

            var options = {
                slidesPerView: slidesPerView,
                spaceBetween: Number(config.spaceBetween || 14),
                speed: 550,
                watchOverflow: true,
                observer: true,
                observeParents: true,
                loop: canLoop,
                rewind: wantsLoop && !canLoop,
                autoplay: config.autoplay ? {
                    delay: Number(config.autoplayDelay || 4500),
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                } : false
            };

            if (prevBtn && nextBtn) {
                options.navigation = {
                    prevEl: prevBtn,
                    nextEl: nextBtn
                };
            }

            if (rtl) {
                options.rtl = true;
            }

            slider.dataset.webmzZtdReady = 'yes';
            var swiper = new window.Swiper(slider, options);

            if (wrap) {
                var resizeTimer;

                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        if (!swiper || swiper.destroyed) {
                            return;
                        }

                        swiper.params.slidesPerView = getSlidesPerView(config);
                        swiper.update();
                    }, 150);
                });
            }
        });
    }

    var elementorHookBound = false;

    function bindElementorHook() {
        if (elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        elementorHookBound = true;

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-zhaket-top-developer.default',
            function ($scope) {
                initZhaketTopDeveloper($scope[0]);
            }
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initZhaketTopDeveloper(document);
        });
    } else {
        initZhaketTopDeveloper(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', bindElementorHook);

        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            bindElementorHook();
        }
    }
})();
