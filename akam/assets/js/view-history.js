(function () {
    'use strict';

    var config = window.webmzViewHistory || {};
    var timers = new WeakMap();
    var lastSaved = new WeakMap();
    var playedMedia = new WeakMap();

    function toNumber(value) {
        value = parseFloat(value);
        return Number.isFinite(value) ? value : 0;
    }

    function queryStart() {
        try {
            var params = new URLSearchParams(window.location.search || '');
            return Math.max(0, parseInt(params.get('webmz_start') || '0', 10));
        } catch (error) {
            return 0;
        }
    }

    function getPostId(media) {
        var direct = parseInt(media.getAttribute('data-webmz-history-post-id') || media.getAttribute('data-webmz-post-id') || '0', 10);
        if (direct > 0) {
            return direct;
        }

        var holder = media.closest('[data-tadris-post-id]');
        if (holder) {
            var fromHolder = parseInt(holder.getAttribute('data-tadris-post-id') || '0', 10);
            if (fromHolder > 0) {
                return fromHolder;
            }
        }

        if (config.contextPostId && document.body.classList.contains('single-post')) {
            return parseInt(config.contextPostId, 10) || 0;
        }

        return 0;
    }

    function saveProgress(media, force) {
        if (!config.ajaxUrl || !config.nonce || !media || media.readyState < 1) {
            return;
        }

        var postId = getPostId(media);
        if (!postId) {
            return;
        }

        var current = toNumber(media.currentTime);
        var duration = toNumber(media.duration);

        if (current < 1 || !playedMedia.get(media)) {
            return;
        }

        var previous = lastSaved.get(media) || 0;
        var now = Date.now();
        if (!force && now - previous < 9000) {
            return;
        }
        lastSaved.set(media, now);

        var data = new FormData();
        data.append('action', 'webmz_save_view_history');
        data.append('nonce', config.nonce);
        data.append('post_id', String(postId));
        data.append('current', String(Math.max(0, current)));
        data.append('duration', String(Math.max(0, duration)));
        data.append('type', media.getAttribute('data-webmz-history-type') || '');

        if (navigator.sendBeacon && force) {
            navigator.sendBeacon(config.ajaxUrl, data);
            return;
        }

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        }).catch(function () {});
    }

    function setStartTime(media) {
        var start = parseInt(media.getAttribute('data-webmz-history-start') || '0', 10);
        var fromUrl = queryStart();
        var postId = getPostId(media);

        if (fromUrl > 0 && (!config.contextPostId || parseInt(config.contextPostId, 10) === postId)) {
            start = fromUrl;
        }

        if (start <= 0) {
            return;
        }

        var apply = function () {
            var duration = toNumber(media.duration);
            try {
                media.currentTime = duration > 0 ? Math.min(start, Math.max(0, duration - 1)) : start;
            } catch (error) {}
        };

        if (media.readyState >= 1) {
            apply();
        } else {
            media.addEventListener('loadedmetadata', apply, { once: true });
        }
    }

    function initMedia(media) {
        if (!media || media.getAttribute('data-webmz-view-history-ready') === 'yes') {
            return;
        }

        if (!getPostId(media)) {
            return;
        }

        media.setAttribute('data-webmz-view-history-ready', 'yes');
        setStartTime(media);

        media.addEventListener('play', function () {
            playedMedia.set(media, true);
        });

        media.addEventListener('playing', function () {
            playedMedia.set(media, true);
        });

        media.addEventListener('timeupdate', function () {
            if (toNumber(media.currentTime) >= 1) {
                playedMedia.set(media, true);
            }
            saveProgress(media, false);
        });

        media.addEventListener('pause', function () {
            saveProgress(media, true);
        });

        media.addEventListener('ended', function () {
            saveProgress(media, true);
        });

    }

    function init(scope) {
        scope = scope || document;
        var medias = scope.querySelectorAll ? scope.querySelectorAll('video.tadris-player-tag, audio.tadris-player-tag, audio.tadris-sound-tag, [data-webmz-history-player]') : [];
        medias.forEach(initMedia);
    }

    window.addEventListener('beforeunload', function () {
        var medias = document.querySelectorAll('video[data-webmz-view-history-ready="yes"], audio[data-webmz-view-history-ready="yes"]');
        medias.forEach(function (media) {
            saveProgress(media, true);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        init(document);
    });

    document.querySelectorAll('[data-webmz-account-panel]').forEach(function (panel) {
        panel.addEventListener('webmzAccountLoaded', function () {
            init(panel);
        });
    });

    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            init($scope && $scope[0] ? $scope[0] : document);
        });
    }
}());
