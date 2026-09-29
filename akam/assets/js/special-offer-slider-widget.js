(function () {
    'use strict';

    var videoModal = null;
    var activePlayer = null;
    var lastFocus = null;
    var playButtonsBound = false;

    function config() {
        return window.webmzSpecialOfferSlider || {};
    }

    function toPersianDigits(value) {
        return String(value).replace(/\d/g, function (digit) {
            return '۰۱۲۳۴۵۶۷۸۹'[digit];
        });
    }

    function shouldUsePersianDigits() {
        var lang = document.documentElement.getAttribute('lang') || '';

        return lang.indexOf('fa') === 0 || document.documentElement.getAttribute('dir') === 'rtl';
    }

    function padTime(value) {
        var output = String(Math.max(0, value));

        if (output.length < 2) {
            output = '0' + output;
        }

        return shouldUsePersianDigits() ? toPersianDigits(output) : output;
    }

    function normalizeSaleStart(start, end, now) {
        var remaining = Math.max(0, end - now);

        if (!end || end <= now) {
            return 0;
        }

        if (start && start > 0 && start <= now && end > start) {
            return start;
        }

        return Math.max(0, now - (30 * 86400));
    }

    function updateTimerProgress(timer, end, start, now) {
        var progressBar = timer.querySelector('.webmz-sos__timer-progress-bar');
        var progressWrap = timer.querySelector('.webmz-sos__timer-progress');

        if (!progressBar || !progressWrap) {
            return;
        }

        if (!end || end <= now) {
            progressBar.style.width = '0%';
            return;
        }

        start = normalizeSaleStart(start, end, now);

        if (!start || end <= start) {
            progressBar.style.width = '0%';
            return;
        }

        var remaining = Math.max(0, end - now);
        var total = end - start;
        var percent = Math.min(100, Math.max(0, (remaining / total) * 100));

        progressBar.style.width = percent + '%';
        progressWrap.hidden = false;
    }

    function updateTimer(timer) {
        var end = parseInt(timer.getAttribute('data-end'), 10);
        var start = parseInt(timer.getAttribute('data-start'), 10);
        var endedLabel = timer.getAttribute('data-ended-label') || '';
        var activeLabel = timer.getAttribute('data-timer-label') || '';
        var labelEl = timer.querySelector('.webmz-sos__timer-label');
        var digitsEl = timer.querySelector('.webmz-sos__timer-digits');
        var progressWrap = timer.querySelector('.webmz-sos__timer-progress');
        var now = Math.floor(Date.now() / 1000);

        if (!end || !labelEl) {
            return;
        }

        var diff = end - now;

        if (diff <= 0) {
            timer.classList.add('is-ended');
            labelEl.textContent = endedLabel;

            if (digitsEl) {
                digitsEl.hidden = true;
            }

            if (progressWrap) {
                progressWrap.hidden = true;
            }

            updateTimerProgress(timer, end, start, now);
            return;
        }

        timer.classList.remove('is-ended');

        if (activeLabel) {
            labelEl.textContent = activeLabel;
        }

        if (digitsEl) {
            digitsEl.hidden = false;
        }

        if (progressWrap) {
            progressWrap.hidden = false;
        }

        updateTimerProgress(timer, end, start, now);

        var daysEl = timer.querySelector('[data-unit="days"]');
        var hoursEl = timer.querySelector('[data-unit="hours"]');
        var minutesEl = timer.querySelector('[data-unit="minutes"]');
        var secondsEl = timer.querySelector('[data-unit="seconds"]');
        var days = Math.floor(diff / 86400);
        var hours = Math.floor((diff % 86400) / 3600);
        var minutes = Math.floor((diff % 3600) / 60);
        var seconds = diff % 60;

        if (daysEl) {
            daysEl.textContent = padTime(days);
        }

        if (hoursEl) {
            hoursEl.textContent = padTime(hours);
        }

        if (minutesEl) {
            minutesEl.textContent = padTime(minutes);
        }

        if (secondsEl) {
            secondsEl.textContent = padTime(seconds);
        }
    }

    function initCountdownTimers(scope) {
        var timers = (scope || document).querySelectorAll('[data-webmz-sos-timer]');

        timers.forEach(function (timer) {
            if (timer.dataset.webmzSosTimerReady === 'yes') {
                return;
            }

            updateTimer(timer);

            window.setInterval(function () {
                updateTimer(timer);
            }, 1000);

            timer.dataset.webmzSosTimerReady = 'yes';
        });
    }

    function isRtlSlider(slider) {
        if (slider.getAttribute('dir') === 'rtl') {
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

    function parseAparatId(url) {
        var match = url.match(/aparat\.com\/(?:v\/|video\/video\/embed\/videohash\/|embed\/)([A-Za-z0-9]+)/i);
        return match ? match[1] : '';
    }

    function getVideoType(url) {
        if (!url) {
            return 'html5';
        }

        if (parseAparatId(url)) {
            return 'aparat';
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

        if (type === 'aparat') {
            // Aparat has no player API, so it stays a plain iframe (Plyr skips it).
            return '<div class="webmz-video-embed webmz-video-embed--aparat" style="position:relative;width:100%;aspect-ratio:16/9;background:#0f172a;">' +
                '<iframe src="https://www.aparat.com/video/video/embed/videohash/' + encodeURIComponent(parseAparatId(url)) + '/vt/frame" style="position:absolute;inset:0;width:100%;height:100%;border:0;" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" allow="autoplay; fullscreen; picture-in-picture"></iframe>' +
                '</div>';
        }

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

        return '<video class="tadris-player-tag webmz-sos-video-modal__video" playsinline controls preload="metadata"' + posterAttr + '>' +
            '<source src="' + safeUrl + '" type="video/mp4">' +
            '</video>';
    }

    function ensureVideoModal() {
        if (videoModal) {
            return videoModal;
        }

        var cfg = config();

        videoModal = document.createElement('div');
        videoModal.className = 'webmz-sos-video-modal';
        videoModal.hidden = true;
        videoModal.innerHTML =
            '<div class="webmz-sos-video-modal__overlay" data-webmz-sos-video-close></div>' +
            '<div class="webmz-sos-video-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="webmz-sos-video-modal-title">' +
                '<button type="button" class="webmz-sos-video-modal__close" data-webmz-sos-video-close aria-label="' + escapeAttr(cfg.closeLabel || 'بستن') + '">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<h3 class="webmz-sos-video-modal__title" id="webmz-sos-video-modal-title"></h3>' +
                '<div class="webmz-sos-video-modal__player webmz-plyr-widget webmz-plyr-widget--video" data-webmz-sos-video-player></div>' +
            '</div>';

        document.body.appendChild(videoModal);

        videoModal.addEventListener('click', function (event) {
            if (event.target.closest('[data-webmz-sos-video-close]')) {
                event.preventDefault();
                closeVideoModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && videoModal && !videoModal.hidden) {
                closeVideoModal();
            }
        });

        return videoModal;
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
        if (!videoModal) {
            return;
        }

        destroyActivePlayer();

        var playerWrap = videoModal.querySelector('[data-webmz-sos-video-player]');
        if (playerWrap) {
            playerWrap.innerHTML = '';
        }

        videoModal.classList.remove('is-open');
        videoModal.hidden = true;
        document.documentElement.classList.remove('webmz-sos-video-open');

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }

        lastFocus = null;
    }

    function initModalPlayer(playerWrap) {
        if (typeof window.Plyr === 'undefined') {
            return;
        }

        var target = playerWrap.querySelector('video, .plyr__video-embed');

        if (!target) {
            return;
        }

        activePlayer = new window.Plyr(target, getPlyrOptions());
    }

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.textContent = value || '';
        return div.innerHTML;
    }

    function getLoadingMarkup() {
        var cfg = config();

        return '<div class="webmz-sos-video-modal__status">' +
            '<span class="webmz-sos-video-modal__spinner" aria-hidden="true"></span>' +
            '<span class="webmz-sos-video-modal__status-text">' + escapeHtml(cfg.loadingLabel || 'در حال بارگذاری...') + '</span>' +
            '</div>';
    }

    function getErrorMarkup(message) {
        return '<div class="webmz-sos-video-modal__status webmz-sos-video-modal__status--error">' +
            '<span class="webmz-sos-video-modal__status-text">' + escapeHtml(message) + '</span>' +
            '</div>';
    }

    function openVideoModalShell(title) {
        var modalEl = ensureVideoModal();
        var cfg = config();
        var titleEl = modalEl.querySelector('.webmz-sos-video-modal__title');
        var playerWrap = modalEl.querySelector('[data-webmz-sos-video-player]');

        destroyActivePlayer();

        if (playerWrap) {
            playerWrap.innerHTML = getLoadingMarkup();
        }

        if (titleEl) {
            titleEl.textContent = title || cfg.videoLabel || '';
            titleEl.hidden = !titleEl.textContent;
        }

        modalEl.hidden = false;

        window.requestAnimationFrame(function () {
            modalEl.classList.add('is-open');
            document.documentElement.classList.add('webmz-sos-video-open');

            var closeButton = modalEl.querySelector('.webmz-sos-video-modal__close');
            if (closeButton) {
                closeButton.focus();
            }
        });

        return modalEl;
    }

    function showVideoError(message) {
        var playerWrap = videoModal ? videoModal.querySelector('[data-webmz-sos-video-player]') : null;

        if (playerWrap) {
            playerWrap.innerHTML = getErrorMarkup(message);
        }
    }

    function showVideoModal(title, url, poster) {
        var modalEl = videoModal || ensureVideoModal();
        var titleEl = modalEl.querySelector('.webmz-sos-video-modal__title');
        var playerWrap = modalEl.querySelector('[data-webmz-sos-video-player]');

        destroyActivePlayer();
        playerWrap.innerHTML = buildPlayerMarkup(url, poster);

        if (titleEl) {
            titleEl.textContent = title;
            titleEl.hidden = !title;
        }

        modalEl.hidden = false;

        window.requestAnimationFrame(function () {
            modalEl.classList.add('is-open');
            document.documentElement.classList.add('webmz-sos-video-open');
            initModalPlayer(playerWrap);

            var closeButton = modalEl.querySelector('.webmz-sos-video-modal__close');
            if (closeButton) {
                closeButton.focus();
            }
        });
    }

    function fetchIntroVideo(productId) {
        var cfg = config();
        var body = new FormData();

        body.append('action', 'webmz_sos_intro_video');
        body.append('nonce', cfg.nonce || '');
        body.append('product_id', String(productId));

        return fetch(cfg.ajaxUrl, {
            method: 'POST',
            body: body,
            credentials: 'same-origin'
        }).then(function (response) {
            return response.json();
        });
    }

    function openVideoModal(button) {
        var productId = button.getAttribute('data-product-id');
        var cfg = config();

        if (!productId || !cfg.ajaxUrl) {
            return;
        }

        lastFocus = document.activeElement;
        openVideoModalShell('');

        fetchIntroVideo(productId)
            .then(function (payload) {
                if (!payload || !payload.success || !payload.data || !payload.data.video_url) {
                    showVideoError((payload && payload.data && payload.data.message) || cfg.errorLabel || 'ویدیو یافت نشد.');
                    return;
                }

                showVideoModal(
                    payload.data.title || cfg.videoLabel || '',
                    payload.data.video_url,
                    payload.data.poster || ''
                );
            })
            .catch(function () {
                showVideoError(cfg.errorLabel || 'خطا در بارگذاری ویدیو.');
            });
    }

    function bindVideoPlayButtons() {
        if (playButtonsBound) {
            return;
        }

        playButtonsBound = true;

        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-webmz-sos-play]');

            if (!button || button.disabled) {
                return;
            }

            event.preventDefault();
            openVideoModal(button);
        });
    }

    function shouldUseAutoHeight() {
        return window.matchMedia('(max-width: 991px)').matches;
    }

    function initSpecialOfferSlider(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        bindVideoPlayButtons();

        var sliders = (scope || document).querySelectorAll('[data-webmz-special-offer-slider]');

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                slider.swiper.destroy(true, true);
                delete slider.dataset.webmzSosSliderReady;
            }

            if (slider.dataset.webmzSosSliderReady === 'yes') {
                return;
            }

            var configData = {};

            try {
                configData = JSON.parse(slider.getAttribute('data-webmz-special-offer-slider') || '{}');
            } catch (error) {
                configData = {};
            }

            var slideCount = slider.querySelectorAll('.swiper-slide').length;

            if (slideCount < 2) {
                slider.dataset.webmzSosSliderReady = 'yes';
                initCountdownTimers(slider);
                return;
            }

            var isRtl = isRtlSlider(slider);
            var wantsLoop = slideCount >= 2;
            var options = {
                slidesPerView: 1,
                spaceBetween: 0,
                speed: 650,
                rtl: isRtl,
                watchOverflow: true,
                autoHeight: shouldUseAutoHeight(),
                observer: true,
                observeParents: true,
                loop: wantsLoop && slideCount >= 3,
                rewind: wantsLoop && slideCount < 3,
                autoplay: configData.autoplay ? {
                    delay: Number(configData.delay || 6000),
                    disableOnInteraction: false
                } : false,
                navigation: configData.showArrows === false ? undefined : {
                    nextEl: slider.querySelector('.webmz-sos__arrow--next'),
                    prevEl: slider.querySelector('.webmz-sos__arrow--prev')
                },
                pagination: {
                    el: slider.querySelector('.webmz-sos__pagination'),
                    clickable: true
                },
                on: {
                    init: function () {
                        initCountdownTimers(slider);
                    },
                    resize: function (swiper) {
                        swiper.params.autoHeight = shouldUseAutoHeight();
                        swiper.update();
                    },
                    slideChangeTransitionEnd: function () {
                        initCountdownTimers(slider);
                    }
                }
            };

            slider.dataset.webmzSosSliderReady = 'yes';
            new window.Swiper(slider, options);

            if (!slider.dataset.webmzSosResizeBound) {
                slider.dataset.webmzSosResizeBound = 'yes';

                window.addEventListener('resize', function () {
                    if (!slider.swiper) {
                        return;
                    }

                    slider.swiper.params.autoHeight = shouldUseAutoHeight();
                    slider.swiper.update();
                });
            }
        });

        initCountdownTimers(scope);
    }

    var elementorHookBound = false;

    function bindElementorHook() {
        if (elementorHookBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        elementorHookBound = true;

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-special-offer-slider.default',
            function ($scope) {
                initSpecialOfferSlider($scope[0]);
            }
        );
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initSpecialOfferSlider(document);
        });
    } else {
        initSpecialOfferSlider(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', bindElementorHook);

        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            bindElementorHook();
        }
    }
})();
