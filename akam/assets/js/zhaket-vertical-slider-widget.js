(function () {
    'use strict';

    function isRtlContext(root) {
        if (root && root.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function parseConfig(slider) {
        try {
            return JSON.parse(slider.getAttribute('data-webmz-zhaket-vertical-slider') || '{}');
        } catch (error) {
            return {};
        }
    }

    function buildOptions(slider, configData) {
        var slideCount = slider.querySelectorAll('.swiper-slide').length;
        var wantsLoop = configData.loop !== false;
        var canLoop = wantsLoop && slideCount >= 3;
        var peek = Number(configData.slidesPerView || 1.38);

        if (Number.isNaN(peek) || peek < 1.1) {
            peek = 1.38;
        }

        return {
            direction: 'horizontal',
            slidesPerView: peek,
            centeredSlides: true,
            spaceBetween: Number(configData.spaceBetween || 18),
            speed: Number(configData.speed || 550),
            rtl: isRtlContext(slider.closest('[dir]') || slider),
            loop: canLoop,
            rewind: wantsLoop && !canLoop,
            watchOverflow: true,
            autoHeight: true,
            observer: true,
            observeParents: true,
            grabCursor: false,
            preventClicks: false,
            preventClicksPropagation: false,
            slideToClickedSlide: true,
            threshold: 10,
            resistanceRatio: 0.85,
            autoplay: configData.autoplay ? {
                delay: Number(configData.delay || 3500),
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            } : false
        };
    }

    function bindSlideLinks(slider, swiper) {
        var moved = false;

        slider.addEventListener('touchstart', function () {
            moved = false;
        }, { passive: true });

        slider.addEventListener('touchmove', function () {
            moved = true;
        }, { passive: true });

        slider.addEventListener('click', function (event) {
            var link = event.target.closest('.webmz-zvs__slide-link');

            if (!link || !slider.contains(link)) {
                return;
            }

            var slide = link.closest('.swiper-slide');

            if (!slide || !slide.classList.contains('swiper-slide-active')) {
                event.preventDefault();
                return;
            }

            if (moved || (swiper && (swiper.animating || swiper.touches && swiper.touches.diff !== 0))) {
                event.preventDefault();
            }
        });
    }

    function initSlider(slider) {
        if (slider.swiper) {
            slider.swiper.destroy(true, true);
            delete slider.dataset.webmzZvsReady;
        }

        if (slider.dataset.webmzZvsReady === 'yes') {
            return;
        }

        var slideCount = slider.querySelectorAll('.swiper-slide').length;

        if (slideCount < 2) {
            slider.dataset.webmzZvsReady = 'yes';
            slider.classList.add('webmz-zvs__swiper--single');
            return;
        }

        var swiper = new window.Swiper(slider, buildOptions(slider, parseConfig(slider)));

        bindSlideLinks(slider, swiper);
        slider.dataset.webmzZvsReady = 'yes';
    }

    function initZhaketVerticalSlider(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = [];

        if (scope && scope.matches && scope.matches('[data-webmz-zhaket-vertical-slider]')) {
            sliders = [scope];
        } else {
            sliders = Array.prototype.slice.call(
                (scope || document).querySelectorAll('[data-webmz-zhaket-vertical-slider]')
            );
        }

        sliders.forEach(initSlider);
    }

    var elementorHookBound = false;

    function bindElementorHook() {
        if (elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        elementorHookBound = true;

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-zhaket-vertical-slider.default',
            function ($scope) {
                initZhaketVerticalSlider($scope[0]);
            }
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initZhaketVerticalSlider(document);
        });
    } else {
        initZhaketVerticalSlider(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', bindElementorHook);

        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            bindElementorHook();
        }
    }
})();
