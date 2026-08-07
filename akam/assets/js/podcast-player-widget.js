(function ($) {
    'use strict';

    var config = function () {
        return window.webmzPodcastPlayer || {};
    };

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });
    }

    function formatDuration(seconds) {
        var total = Math.max(0, Math.floor(Number(seconds) || 0));
        var mins = Math.floor(total / 60);
        var secs = total % 60;
        var label = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

        try {
            return label.replace(/\d/g, function (digit) {
                return String.fromCharCode(digit.charCodeAt(0) + 1728);
            });
        } catch (error) {
            return label;
        }
    }

    function getPlyrOptions() {
        var options = {
            controls: ['play', 'progress', 'current-time', 'duration']
        };

        if (window.webmzTadrisWidgets && window.webmzTadrisWidgets.plyrIconUrl) {
            options.iconUrl = window.webmzTadrisWidgets.plyrIconUrl;
            options.loadSprite = true;
        }

        return options;
    }

    function getPlyrInstance(audio) {
        if (!audio) {
            return null;
        }

        if (audio.plyr && typeof audio.plyr.destroy === 'function') {
            return audio.plyr;
        }

        if (typeof window.Plyr !== 'undefined' && typeof window.Plyr.get === 'function') {
            return window.Plyr.get(audio) || null;
        }

        return null;
    }

    function destroyPlyr(audio) {
        var instance = getPlyrInstance(audio);

        if (instance) {
            try {
                instance.destroy();
            } catch (error) {
                // Ignore destroy errors when Plyr is already torn down.
            }
        }

        if (!audio) {
            return;
        }

        audio.plyr = null;
        audio.removeAttribute('data-tadris-player-ready');
        audio.removeAttribute('data-webmz-pp-plyr-ready');
    }

    function setAudioElementSource(audio, url) {
        var source = audio.querySelector('source');

        if (source) {
            source.src = url;
        } else {
            audio.innerHTML = '<source src="' + escapeHtml(url) + '" type="audio/mp3">';
        }

        audio.load();
    }

    function initPlyr(audio) {
        if (!audio || typeof window.Plyr === 'undefined') {
            return null;
        }

        if (audio.getAttribute('data-webmz-pp-plyr-ready') === 'yes' && getPlyrInstance(audio)) {
            return getPlyrInstance(audio);
        }

        destroyPlyr(audio);
        audio.setAttribute('data-webmz-pp-plyr-ready', 'yes');
        audio.plyr = new window.Plyr(audio, getPlyrOptions());

        return audio.plyr;
    }

    function updatePlayerSource(audio, data, shouldPlay) {
        if (!audio || !data || !data.audio_url) {
            return null;
        }

        var url = data.audio_url;
        var player = getPlyrInstance(audio);
        var media = player && player.media ? player.media : audio;

        audio.setAttribute('data-webmz-history-post-id', String(data.id || ''));

        if (player && typeof player.pause === 'function') {
            player.pause();
        }

        setAudioElementSource(media, url);

        if (!player) {
            player = initPlyr(audio);
        }

        if (shouldPlay && player && typeof player.play === 'function') {
            var playTrack = function () {
                media.removeEventListener('loadeddata', playTrack);
                media.removeEventListener('canplay', playTrack);
                player.play().catch(function () {
                    // Autoplay may be blocked until the next user gesture.
                });
            };

            if (media.readyState >= 2) {
                player.play().catch(function () {});
            } else {
                media.addEventListener('loadeddata', playTrack);
                media.addEventListener('canplay', playTrack);
            }
        }

        return player;
    }

    function setLoading(item, isLoading) {
        if (!item) {
            return;
        }

        item.classList.toggle('is-loading', !!isLoading);
        item.setAttribute('aria-busy', isLoading ? 'true' : 'false');
    }

    function setDurationLabel(node, seconds) {
        if (!node || !seconds) {
            return;
        }

        node.textContent = formatDuration(seconds);
        node.setAttribute('data-duration-ready', 'yes');
    }

    function loadItemDuration(item) {
        var durationNode = item.querySelector('.webmz-pp__item-duration');
        var audioUrl = item.getAttribute('data-audio-url') || '';
        var knownDuration = parseInt(item.getAttribute('data-duration') || '0', 10);

        if (!durationNode || durationNode.getAttribute('data-duration-ready') === 'yes') {
            return;
        }

        if (knownDuration > 0) {
            setDurationLabel(durationNode, knownDuration);
            return;
        }

        if (!audioUrl) {
            return;
        }

        var probe = new Audio();
        probe.preload = 'metadata';
        probe.src = audioUrl;

        var onMeta = function () {
            if (probe.duration && isFinite(probe.duration)) {
                setDurationLabel(durationNode, probe.duration);
                item.setAttribute('data-duration', String(Math.round(probe.duration)));
            }

            probe.removeEventListener('loadedmetadata', onMeta);
            probe.src = '';
        };

        probe.addEventListener('loadedmetadata', onMeta);
    }

    function loadPlaylistDurations(root) {
        root.querySelectorAll('.webmz-pp__item').forEach(loadItemDuration);
    }

    function renderThumb(thumbWrap, data) {
        if (!thumbWrap) {
            return;
        }

        if (data.image_html) {
            thumbWrap.innerHTML = data.image_html;
            return;
        }

        if (data.image_url) {
            thumbWrap.innerHTML = '<img class="webmz-pp__thumb-img" src="' + escapeHtml(data.image_url) + '" alt="' + escapeHtml(data.title) + '">';
            return;
        }

        var initial = (data.title || '').trim().charAt(0) || '•';
        thumbWrap.innerHTML = '<div class="webmz-pp__thumb-fallback" aria-hidden="true">' + escapeHtml(initial) + '</div>';
    }

    function setActiveItem(root, postId) {
        root.querySelectorAll('.webmz-pp__item').forEach(function (item) {
            var isActive = item.getAttribute('data-post-id') === String(postId);
            var trigger = item.querySelector('.webmz-pp__item-trigger');

            item.classList.toggle('is-active', isActive);

            if (trigger) {
                trigger.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            }
        });
    }

    function isItemActionTarget(target) {
        return !!(target && target.closest && target.closest('.webmz-pp__item-actions, [data-webmz-favorite-post]'));
    }

    function getItemFromEventTarget(target) {
        if (!target || !target.closest) {
            return null;
        }

        return target.closest('.webmz-pp__item');
    }

    function switchPodcast(root, item) {
        var cfg = config();
        var postId = item.getAttribute('data-post-id');
        var audio = root.querySelector('.webmz-pp__audio');
        var titleNode = root.querySelector('[data-webmz-pp-title]');
        var thumbWrap = root.querySelector('[data-webmz-pp-thumb]');
        var player = getPlyrInstance(audio);

        if (!postId || item.classList.contains('is-active') || item.classList.contains('is-loading')) {
            return;
        }

        setLoading(item, true);

        if (player && typeof player.pause === 'function') {
            player.pause();
        }

        $.ajax({
            url: cfg.ajaxUrl || '',
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'webmz_podcast_player_load',
                nonce: cfg.nonce || '',
                post_id: postId
            }
        }).done(function (response) {
            if (!response || !response.success || !response.data) {
                return;
            }

            var data = response.data;

            if (titleNode) {
                titleNode.textContent = data.title || '';
            }

            renderThumb(thumbWrap, data);
            updatePlayerSource(audio, data, true);

            if (data.duration) {
                var durationNode = item.querySelector('.webmz-pp__item-duration');
                setDurationLabel(durationNode, data.duration);
            }

            setActiveItem(root, postId);
        }).fail(function () {
            if (window.console && typeof window.console.error === 'function') {
                window.console.error(cfg.errorText || 'خطایی رخ داد.');
            }
        }).always(function () {
            setLoading(item, false);
        });
    }

    function setupWidget(root) {
        if (!root || root.getAttribute('data-webmz-pp-ready') === 'yes') {
            return;
        }

        root.setAttribute('data-webmz-pp-ready', 'yes');

        var audio = root.querySelector('.webmz-pp__audio');

        if (audio) {
            initPlyr(audio);
        }

        loadPlaylistDurations(root);

        root.addEventListener('click', function (event) {
            if (isItemActionTarget(event.target)) {
                return;
            }

            var item = getItemFromEventTarget(event.target);

            if (!item || !root.contains(item)) {
                return;
            }

            event.preventDefault();
            switchPodcast(root, item);
        });

        root.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            if (isItemActionTarget(event.target)) {
                return;
            }

            var item = getItemFromEventTarget(event.target);

            if (!item || !root.contains(item)) {
                return;
            }

            event.preventDefault();
            switchPodcast(root, item);
        });
    }

    function initScope(scope) {
        var context = scope && scope.querySelectorAll ? scope : document;
        context.querySelectorAll('.webmz-pp').forEach(setupWidget);
    }

    $(function () {
        initScope(document);
    });

    $(window).on('elementor/frontend/init', function () {
        if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-podcast-player.default',
            function ($scope) {
                initScope($scope[0]);
            }
        );
    });
}(jQuery));
