(function () {
    'use strict';

    function isRtlContext(root) {
        if (root && root.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function setActiveThumb(thumbsRoot, index) {
        if (!thumbsRoot) {
            return;
        }

        thumbsRoot.querySelectorAll('.webmz-instructor-slider__thumb').forEach(function (button) {
            var buttonIndex = parseInt(button.getAttribute('data-slide-index'), 10);
            var isActive = buttonIndex === index;

            button.classList.toggle('is-active', isActive);

            if (isActive) {
                button.setAttribute('aria-current', 'true');
            } else {
                button.removeAttribute('aria-current');
            }
        });
    }

    function goToSlide(mainSwiper, thumbsSwiper, thumbsEl, index, useLoop) {
        if (!mainSwiper || index < 0) {
            return;
        }

        if (useLoop) {
            mainSwiper.slideToLoop(index);
        } else {
            mainSwiper.slideTo(index);
        }

        if (thumbsSwiper) {
            thumbsSwiper.slideTo(index);
        }

        setActiveThumb(thumbsEl, index);
    }

    function createThumbClickHandler(mainSwiper, thumbsSwiper, thumbsEl, useLoop) {
        return function (event) {
            if (thumbsEl._webmzIgnoreClickUntil && Date.now() < thumbsEl._webmzIgnoreClickUntil) {
                event.preventDefault();
                event.stopPropagation();
                return;
            }

            var button = event.target.closest('.webmz-instructor-slider__thumb');

            if (!button || !thumbsEl.contains(button)) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            var index = parseInt(button.getAttribute('data-slide-index'), 10);

            if (Number.isNaN(index)) {
                return;
            }

            goToSlide(mainSwiper, thumbsSwiper, thumbsEl, index, useLoop);
        };
    }

    /**
     * Vertical/horizontal swipe on the thumb index when the list does not overflow,
     * so users can still change the active instructor by gesture.
     */
    function bindThumbsSwipeNav(thumbsEl, isStacked, onSwipe) {
        var startX = 0;
        var startY = 0;
        var tracking = false;
        var swiped = false;
        var threshold = 48;

        function onPointerDown(event) {
            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }

            tracking = true;
            swiped = false;
            startX = event.clientX;
            startY = event.clientY;
        }

        function onPointerMove(event) {
            if (!tracking) {
                return;
            }

            var dx = event.clientX - startX;
            var dy = event.clientY - startY;
            var primary = isStacked ? dx : dy;
            var secondary = isStacked ? dy : dx;

            if (Math.abs(primary) > 12 && Math.abs(primary) > Math.abs(secondary)) {
                swiped = true;
            }
        }

        function onPointerUp(event) {
            if (!tracking) {
                return;
            }

            tracking = false;

            var dx = event.clientX - startX;
            var dy = event.clientY - startY;
            var primary = isStacked ? dx : dy;
            var secondary = isStacked ? dy : dx;

            if (!swiped || Math.abs(primary) < threshold || Math.abs(primary) <= Math.abs(secondary)) {
                return;
            }

            // Up / left → next, down / right → previous (RTL thumbs stay visual-top-to-bottom).
            thumbsEl._webmzIgnoreClickUntil = Date.now() + 450;
            onSwipe(primary < 0 ? 1 : -1);
        }

        function onPointerCancel() {
            tracking = false;
            swiped = false;
        }

        if (thumbsEl._webmzThumbSwipeCleanup) {
            thumbsEl._webmzThumbSwipeCleanup();
        }

        thumbsEl.addEventListener('pointerdown', onPointerDown);
        thumbsEl.addEventListener('pointermove', onPointerMove);
        thumbsEl.addEventListener('pointerup', onPointerUp);
        thumbsEl.addEventListener('pointercancel', onPointerCancel);

        thumbsEl._webmzThumbSwipeCleanup = function () {
            thumbsEl.removeEventListener('pointerdown', onPointerDown);
            thumbsEl.removeEventListener('pointermove', onPointerMove);
            thumbsEl.removeEventListener('pointerup', onPointerUp);
            thumbsEl.removeEventListener('pointercancel', onPointerCancel);
            thumbsEl._webmzThumbSwipeCleanup = null;
        };
    }

    function initInstructorSlider(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var widgets = [];

        if (scope && scope.matches && scope.matches('[data-webmz-instructor-slider]')) {
            widgets = [scope];
        } else {
            widgets = Array.prototype.slice.call(
                (scope || document).querySelectorAll('[data-webmz-instructor-slider]')
            );
        }

        widgets.forEach(function (widget) {
            if (widget.dataset.webmzInstructorSliderReady === 'yes') {
                return;
            }

            var mainEl = widget.querySelector('.webmz-instructor-slider__main');
            var thumbsEl = widget.querySelector('.webmz-instructor-slider__thumbs');

            if (!mainEl || !thumbsEl) {
                return;
            }

            if (widget._webmzThumbClickHandler) {
                thumbsEl.removeEventListener('click', widget._webmzThumbClickHandler);
                widget._webmzThumbClickHandler = null;
            }

            if (thumbsEl._webmzThumbSwipeCleanup) {
                thumbsEl._webmzThumbSwipeCleanup();
            }

            if (mainEl.swiper) {
                mainEl.swiper.destroy(true, true);
            }

            if (thumbsEl.swiper) {
                thumbsEl.swiper.destroy(true, true);
            }

            var config = {};

            try {
                config = JSON.parse(widget.getAttribute('data-webmz-instructor-slider') || '{}');
            } catch (error) {
                config = {};
            }

            var slideCount = mainEl.querySelectorAll('.swiper-slide').length;
            var isRtl = isRtlContext(widget);
            var isStacked = window.matchMedia('(max-width: 991px)').matches;
            var useLoop = !!config.loop && slideCount > 1;

            widget.dataset.webmzInstructorSliderReady = 'yes';

            var thumbsSwiper = new window.Swiper(thumbsEl, {
                direction: isStacked ? 'horizontal' : 'vertical',
                slidesPerView: 'auto',
                spaceBetween: 12,
                watchSlidesProgress: true,
                rtl: isRtl,
                observer: true,
                observeParents: true,
                allowTouchMove: true,
                simulateTouch: true,
                grabCursor: true,
                slideToClickedSlide: false,
                threshold: 8,
                resistanceRatio: 0.85,
                mousewheel: {
                    forceToAxis: true,
                    releaseOnEdges: true,
                    sensitivity: 0.85
                },
                preventClicks: false,
                preventClicksPropagation: false
            });

            var mainOptions = {
                slidesPerView: 1,
                spaceBetween: 0,
                speed: 550,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                rtl: isRtl,
                watchOverflow: true,
                observer: true,
                observeParents: true,
                loop: useLoop,
                preventClicks: false,
                preventClicksPropagation: false,
                on: {
                    slideChange: function (swiper) {
                        setActiveThumb(thumbsEl, swiper.realIndex);

                        if (thumbsSwiper && thumbsSwiper.activeIndex !== swiper.realIndex) {
                            thumbsSwiper.slideTo(swiper.realIndex);
                        }
                    }
                }
            };

            if (config.autoplay && slideCount > 1) {
                mainOptions.autoplay = {
                    delay: Number(config.autoplayDelay || 5000),
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                };
            }

            var mainSwiper = new window.Swiper(mainEl, mainOptions);
            var thumbClickHandler = createThumbClickHandler(mainSwiper, thumbsSwiper, thumbsEl, useLoop);

            widget._webmzThumbClickHandler = thumbClickHandler;
            thumbsEl.addEventListener('click', thumbClickHandler);

            bindThumbsSwipeNav(thumbsEl, isStacked, function (direction) {
                if (slideCount < 2) {
                    return;
                }

                // When the thumb list can scroll, let Swiper own the gesture.
                if (thumbsSwiper && !thumbsSwiper.isLocked) {
                    return;
                }

                if (direction > 0) {
                    mainSwiper.slideNext();
                } else {
                    mainSwiper.slidePrev();
                }
            });

            setActiveThumb(thumbsEl, mainSwiper.realIndex);

            var resizeTimer;

            window.addEventListener('resize', function onResize() {
                if (!widget.isConnected) {
                    window.removeEventListener('resize', onResize);
                    return;
                }

                clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(function () {
                    var nextStacked = window.matchMedia('(max-width: 991px)').matches;

                    if (nextStacked !== isStacked) {
                        widget.dataset.webmzInstructorSliderReady = 'no';
                        initInstructorSlider(widget);
                    }
                }, 200);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initInstructorSlider(document);
        });
    } else {
        initInstructorSlider(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/webmz-instructor-slider.default',
                function ($scope) {
                    initInstructorSlider($scope[0]);
                }
            );
        });
    }
})();
