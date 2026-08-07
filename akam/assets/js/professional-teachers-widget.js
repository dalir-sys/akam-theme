(function () {
    'use strict';

    var modal = null;
    var activePlayer = null;
    var lastFocus = null;

    function config() {
        return window.webmzProfessionalTeachers || {};
    }

    function isRtlSlider(slider) {
        var root = slider.closest('[dir]');

        if (root && root.getAttribute('dir') === 'rtl') {
            return true;
        }

        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function getPlyrOptions() {
        var options = {};
        var cfg = config();
        var iconUrl = cfg.plyrIconUrl || (window.webmzTadrisWidgets && window.webmzTadrisWidgets.plyrIconUrl);

        if (iconUrl) {
            options.iconUrl = iconUrl;
            options.loadSprite = true;
        }

        return options;
    }

    function parseYoutubeId(url) {
        var match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|shorts\/|watch\?v=|watch\?.+&v=))([^?&/]+)/i);
        return match ? match[1] : '';
    }

    function parseVimeoId(url) {
        var match = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
        return match ? match[1] : '';
    }

    function getVideoType(url) {
        if (!url) {
            return 'html5';
        }

        if (parseYoutubeId(url)) {
            return 'youtube';
        }

        if (parseVimeoId(url)) {
            return 'vimeo';
        }

        return 'html5';
    }

    function escapeAttr(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;');
    }

    function buildPlayerMarkup(url, poster) {
        var type = getVideoType(url);
        var origin = window.location.origin || '';
        var safeUrl = escapeAttr(url);
        var safePoster = escapeAttr(poster);

        if (type === 'youtube') {
            var youtubeId = parseYoutubeId(url);
            return '<div class="plyr__video-embed">' +
                '<iframe src="https://www.youtube.com/embed/' + youtubeId + '?origin=' + encodeURIComponent(origin) + '&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" allowfullscreen allowtransparency allow="autoplay"></iframe>' +
                '</div>';
        }

        if (type === 'vimeo') {
            var vimeoId = parseVimeoId(url);
            return '<div class="plyr__video-embed">' +
                '<iframe src="https://player.vimeo.com/video/' + vimeoId + '?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media" allowfullscreen allowtransparency allow="autoplay"></iframe>' +
                '</div>';
        }

        var posterAttr = safePoster ? ' poster="' + safePoster + '" data-poster="' + safePoster + '"' : '';

        return '<video class="tadris-player-tag webmz-pro-teacher-video-modal__video" playsinline controls preload="metadata"' + posterAttr + '>' +
            '<source src="' + safeUrl + '" type="video/mp4">' +
            '</video>';
    }

    function ensureModal() {
        if (modal) {
            return modal;
        }

        var cfg = config();

        modal = document.createElement('div');
        modal.className = 'webmz-pro-teacher-video-modal';
        modal.hidden = true;
        modal.innerHTML =
            '<div class="webmz-pro-teacher-video-modal__overlay" data-webmz-pro-teacher-video-close></div>' +
            '<div class="webmz-pro-teacher-video-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="webmz-pro-teacher-video-modal-title">' +
                '<button type="button" class="webmz-pro-teacher-video-modal__close" data-webmz-pro-teacher-video-close aria-label="' + (cfg.closeLabel || 'بستن') + '">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<h3 class="webmz-pro-teacher-video-modal__title" id="webmz-pro-teacher-video-modal-title"></h3>' +
                '<div class="webmz-pro-teacher-video-modal__player webmz-plyr-widget webmz-plyr-widget--video" data-webmz-pro-teacher-video-player></div>' +
            '</div>';

        document.body.appendChild(modal);

        modal.addEventListener('click', function (event) {
            if (event.target.closest('[data-webmz-pro-teacher-video-close]')) {
                event.preventDefault();
                closeVideoModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal && !modal.hidden) {
                closeVideoModal();
            }
        });

        return modal;
    }

    function destroyActivePlayer() {
        if (!activePlayer) {
            return;
        }

        try {
            activePlayer.destroy();
        } catch (error) {
            // Player may already be destroyed.
        }

        activePlayer = null;
    }

    function closeVideoModal() {
        if (!modal) {
            return;
        }

        destroyActivePlayer();

        var playerWrap = modal.querySelector('[data-webmz-pro-teacher-video-player]');
        if (playerWrap) {
            playerWrap.innerHTML = '';
        }

        modal.classList.remove('is-open');
        modal.hidden = true;
        document.documentElement.classList.remove('webmz-pro-teacher-video-open');

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }

        lastFocus = null;
    }

    function openVideoModal(button) {
        var url = button.getAttribute('data-webmz-pro-teacher-video');
        var title = button.getAttribute('data-webmz-pro-teacher-video-title') || '';
        var poster = button.getAttribute('data-webmz-pro-teacher-video-poster') || '';

        if (!url || typeof window.Plyr === 'undefined') {
            return;
        }

        var modalEl = ensureModal();
        var titleEl = modalEl.querySelector('.webmz-pro-teacher-video-modal__title');
        var playerWrap = modalEl.querySelector('[data-webmz-pro-teacher-video-player]');

        destroyActivePlayer();
        playerWrap.innerHTML = buildPlayerMarkup(url, poster);

        if (titleEl) {
            titleEl.textContent = title;
            titleEl.hidden = !title;
        }

        lastFocus = document.activeElement;
        modalEl.hidden = false;

        window.requestAnimationFrame(function () {
            modalEl.classList.add('is-open');
            document.documentElement.classList.add('webmz-pro-teacher-video-open');

            var target = playerWrap.querySelector('video, .plyr__video-embed');

            if (target) {
                activePlayer = new window.Plyr(target, getPlyrOptions());

                if (typeof activePlayer.play === 'function') {
                    activePlayer.play().catch(function () {
                        // Autoplay may be blocked until user interacts.
                    });
                }
            }

            var closeButton = modalEl.querySelector('.webmz-pro-teacher-video-modal__close');
            if (closeButton) {
                closeButton.focus();
            }
        });
    }

    function initProfessionalTeacherVideos(scope) {
        (scope || document).querySelectorAll('[data-webmz-pro-teacher-video]').forEach(function (button) {
            if (button.getAttribute('data-webmz-pro-teacher-video-ready') === 'yes') {
                return;
            }

            button.setAttribute('data-webmz-pro-teacher-video-ready', 'yes');
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                openVideoModal(button);
            });
        });
    }

    function initProfessionalTeachersSliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = (scope || document).querySelectorAll('[data-webmz-pro-teachers-slider]');

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                slider.swiper.destroy(true, true);
            }

            var sliderConfig = {};

            try {
                sliderConfig = JSON.parse(slider.getAttribute('data-webmz-pro-teachers-slider') || '{}');
            } catch (error) {
                sliderConfig = {};
            }

            var shell = slider.closest('.webmz-pro-teachers');
            var slideCount = slider.querySelectorAll('.swiper-slide').length;
            var isRtl = isRtlSlider(slider);
            var wantsLoop = !!sliderConfig.loop;
            var useLoop = wantsLoop && slideCount > (sliderConfig.slidesDesktop || 4);

            var options = {
                slidesPerView: sliderConfig.slidesMobile || 1,
                spaceBetween: sliderConfig.spaceBetween || 24,
                speed: 500,
                rtl: isRtl,
                watchOverflow: true,
                observer: true,
                observeParents: true,
                loop: useLoop,
                breakpoints: {
                    576: {
                        slidesPerView: sliderConfig.slidesTablet || 2,
                        spaceBetween: sliderConfig.spaceBetween || 24
                    },
                    992: {
                        slidesPerView: sliderConfig.slidesDesktop || 4,
                        spaceBetween: sliderConfig.spaceBetween || 24
                    }
                }
            };

            if (sliderConfig.autoplay) {
                options.autoplay = {
                    delay: Number(sliderConfig.autoplayDelay || 3500),
                    disableOnInteraction: false
                };
            }

            if (sliderConfig.navigation && shell) {
                options.navigation = {
                    nextEl: shell.querySelector('.webmz-pro-teachers__arrow--next'),
                    prevEl: shell.querySelector('.webmz-pro-teachers__arrow--prev')
                };
            }

            if (sliderConfig.pagination) {
                options.pagination = {
                    el: slider.querySelector('.webmz-pro-teachers__pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }

    function initScope(scope) {
        initProfessionalTeachersSliders(scope);
        initProfessionalTeacherVideos(scope);
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
                'frontend/element_ready/webmz-professional-teachers.default',
                function ($scope) {
                    initScope($scope[0]);
                }
            );
        });
    }
})();
