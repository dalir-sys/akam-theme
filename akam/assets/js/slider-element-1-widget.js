(function () {
    'use strict';

    function isRtlSlider(slider) {
        if (slider.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function setupAutoplayProgress(slider, swiper, config) {
        var progress = slider.querySelector('.webmz-slider-element-1__autoplay-progress');
        var ring = progress ? progress.querySelector('.webmz-slider-element-1__autoplay-ring') : null;

        if (!progress || !ring || !config.autoplay || !swiper || !swiper.autoplay) {
            if (progress) {
                progress.hidden = true;
            }

            slider.classList.remove('has-autoplay-progress');
            return;
        }

        var radius = 15;
        var circumference = 2 * Math.PI * radius;

        ring.style.strokeDasharray = String(circumference);
        ring.style.strokeDashoffset = String(circumference);

        function setProgress(percentage) {
            var elapsed = Math.max(0, Math.min(1, Number(percentage) || 0));

            ring.style.strokeDashoffset = String(circumference * (1 - elapsed));
        }

        function showProgress() {
            progress.hidden = false;
            slider.classList.add('has-autoplay-progress');
        }

        function hideProgress() {
            progress.hidden = true;
            slider.classList.remove('has-autoplay-progress');
        }

        swiper.on('autoplayTimeLeft', function (swiperInstance, timeLeft, percentage) {
            showProgress();
            setProgress(percentage);
        });

        swiper.on('autoplayStart', function () {
            showProgress();
            setProgress(0);
        });

        swiper.on('autoplayResume', showProgress);
        swiper.on('autoplayStop', hideProgress);

        swiper.on('slideChangeTransitionStart', function () {
            setProgress(0);
        });

        if (swiper.autoplay.running) {
            showProgress();
            setProgress(0);
        } else {
            hideProgress();
        }
    }

    function initSliderElement1(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = (scope || document).querySelectorAll('[data-webmz-slider-element-1]');

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                slider.swiper.destroy(true, true);
                delete slider.dataset.webmzSe1Ready;
                slider.classList.remove('has-autoplay-progress');
            }

            if (slider.dataset.webmzSe1Ready === 'yes') {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-webmz-slider-element-1') || '{}');
            } catch (error) {
                config = {};
            }

            var slideCount = slider.querySelectorAll('.swiper-slide').length;

            if (slideCount < 2) {
                slider.dataset.webmzSe1Ready = 'yes';
                return;
            }

            var isRtl = isRtlSlider(slider);
            var wantsLoop = config.loop !== false;
            var useLoop = wantsLoop && slideCount >= 3;
            var useRewind = wantsLoop && slideCount < 3;

            var options = {
                slidesPerView: 1,
                spaceBetween: 0,
                speed: 600,
                rtl: isRtl,
                watchOverflow: true,
                loop: useLoop,
                rewind: useRewind,
                loopedSlides: slideCount,
                autoplay: config.autoplay ? {
                    delay: Number(config.delay || 5000),
                    disableOnInteraction: false
                } : false,
                navigation: {
                    nextEl: slider.querySelector('.webmz-slider-element-1__arrow--next'),
                    prevEl: slider.querySelector('.webmz-slider-element-1__arrow--prev')
                },
                pagination: {
                    el: slider.querySelector('.webmz-slider-element-1__pagination'),
                    clickable: true
                },
                on: {
                    init: function (swiper) {
                        swiper.update();
                        setupAutoplayProgress(slider, swiper, config);
                    },
                    resize: function (swiper) {
                        swiper.update();
                    }
                }
            };

            slider.dataset.webmzSe1Ready = 'yes';
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
            'frontend/element_ready/webmz-slider-element-1.default',
            function ($scope) {
                initSliderElement1($scope[0]);
            }
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initSliderElement1(document);
        });
    } else {
        initSliderElement1(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', bindElementorHook);

        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            bindElementorHook();
        }
    }
})();
