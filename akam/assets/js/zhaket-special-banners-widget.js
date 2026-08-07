(function () {
    'use strict';

    var lightboxEl = null;
    var lastFocus = null;
    var lightboxKeydownBound = false;

    var CLOSE_SVG = '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';

    function ensureLightbox() {
        if (lightboxEl) {
            return lightboxEl;
        }

        lightboxEl = document.createElement('div');
        lightboxEl.className = 'webmz-zsb-lightbox';
        lightboxEl.hidden = true;
        lightboxEl.innerHTML =
            '<div class="webmz-zsb-lightbox__overlay" aria-hidden="true"></div>' +
            '<div class="webmz-zsb-lightbox__dialog" role="dialog" aria-modal="true">' +
                '<button type="button" class="webmz-zsb-lightbox__close" data-webmz-zsb-lightbox-close aria-label="بستن">' +
                    CLOSE_SVG +
                '</button>' +
                '<img class="webmz-zsb-lightbox__image" src="" alt="" />' +
            '</div>';

        document.body.appendChild(lightboxEl);

        lightboxEl.addEventListener('click', function (event) {
            if (event.target.closest('[data-webmz-zsb-lightbox-close]')) {
                event.preventDefault();
                closeLightbox();
            }
        });

        if (!lightboxKeydownBound) {
            lightboxKeydownBound = true;

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && lightboxEl && !lightboxEl.hidden) {
                    closeLightbox();
                }
            });
        }

        return lightboxEl;
    }

    function openLightbox(src, alt) {
        var lightbox = ensureLightbox();
        var image = lightbox.querySelector('.webmz-zsb-lightbox__image');
        var closeButton = lightbox.querySelector('.webmz-zsb-lightbox__close');

        if (!image) {
            return;
        }

        lastFocus = document.activeElement;
        image.src = src;
        image.alt = alt || '';
        lightbox.hidden = false;

        window.requestAnimationFrame(function () {
            lightbox.classList.add('is-open');
        });

        document.documentElement.classList.add('webmz-zsb-lightbox-open');

        if (closeButton && typeof closeButton.focus === 'function') {
            closeButton.focus();
        }
    }

    function closeLightbox() {
        if (!lightboxEl || lightboxEl.hidden) {
            return;
        }

        lightboxEl.classList.remove('is-open');
        lightboxEl.hidden = true;
        document.documentElement.classList.remove('webmz-zsb-lightbox-open');

        var image = lightboxEl.querySelector('.webmz-zsb-lightbox__image');

        if (image) {
            image.removeAttribute('src');
            image.alt = '';
        }

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }

        lastFocus = null;
    }

    function initZhaketSpecialBannersLightbox(scope) {
        var cards = (scope || document).querySelectorAll('.webmz-zsb__card[data-webmz-zsb-lightbox-src]');

        cards.forEach(function (card) {
            if (card.dataset.webmzZsbLightboxReady === 'yes') {
                return;
            }

            card.dataset.webmzZsbLightboxReady = 'yes';

            function openFromCard(event) {
                var src = card.getAttribute('data-webmz-zsb-lightbox-src');
                var alt = card.getAttribute('data-webmz-zsb-lightbox-alt') || '';

                if (!src) {
                    return;
                }

                event.preventDefault();
                openLightbox(src, alt);
            }

            card.addEventListener('click', openFromCard);

            card.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    openFromCard(event);
                }
            });
        });
    }

    function isRtlContext(root) {
        if (root && root.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function initZhaketSpecialBanners(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = (scope || document).querySelectorAll('[data-webmz-zhaket-special-banners]');

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                slider.swiper.destroy(true, true);
                delete slider.dataset.webmzZsbReady;
            }

            if (slider.dataset.webmzZsbReady === 'yes') {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-webmz-zhaket-special-banners') || '{}');
            } catch (error) {
                config = {};
            }

            var slideCount = slider.querySelectorAll('.swiper-slide').length;
            var widget = slider.closest('.webmz-zsb');
            var isRtl = isRtlContext(widget || slider);
            var wantsLoop = config.loop !== false;
            var useLoop = wantsLoop && slideCount > 1;

            var options = {
                slidesPerView: 'auto',
                spaceBetween: Number(config.spaceBetween || 20),
                speed: 500,
                rtl: isRtl,
                watchOverflow: true,
                loop: useLoop,
                grabCursor: config.grabCursor !== false,
                observer: true,
                observeParents: true,
                resistanceRatio: 0.75,
                freeMode: {
                    enabled: config.freeMode !== false,
                    momentum: true,
                    momentumRatio: 0.45,
                    momentumBounce: true,
                    sticky: false
                }
            };

            if (config.autoplay && slideCount > 1) {
                options.autoplay = {
                    delay: Number(config.autoplayDelay || 4500),
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                };
            }

            if (config.navigation && widget) {
                options.navigation = {
                    nextEl: widget.querySelector('.webmz-zsb__arrow--next'),
                    prevEl: widget.querySelector('.webmz-zsb__arrow--prev')
                };
            }

            if (config.pagination && widget) {
                var paginationEl = widget.querySelector('.webmz-zsb__pagination');

                if (paginationEl) {
                    options.pagination = {
                        el: paginationEl,
                        clickable: true
                    };
                }
            }

            slider.dataset.webmzZsbReady = 'yes';
            new window.Swiper(slider, options);
        });

        initZhaketSpecialBannersLightbox(scope);
    }

    var elementorHookBound = false;

    function bindElementorHook() {
        if (elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        elementorHookBound = true;

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-zhaket-special-banners.default',
            function ($scope) {
                initZhaketSpecialBanners($scope[0]);
            }
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initZhaketSpecialBanners(document);
        });
    } else {
        initZhaketSpecialBanners(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', bindElementorHook);

        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            bindElementorHook();
        }
    }
})();
