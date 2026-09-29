(function ($) {
    'use strict';

    var STORAGE_KEY = 'webmz_ytp_playlist_progress';

    function config() {
        return window.webmzYoutubePlaylist || {};
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });
    }

    function getPlaylistId(root) {
        return root ? (root.getAttribute('data-webmz-ytp-playlist-id') || '') : '';
    }

    function getPlaylistMeta(root) {
        if (!root) {
            return [];
        }

        try {
            return JSON.parse(root.getAttribute('data-webmz-ytp-items') || '[]');
        } catch (error) {
            return [];
        }
    }

    function readAllStorage() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch (error) {
            return {};
        }
    }

    function writeAllStorage(data) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch (error) {
            // Ignore quota or privacy mode errors.
        }
    }

    function getPlaylistStorage(playlistId) {
        if (!playlistId) {
            return {};
        }

        var all = readAllStorage();
        return all[playlistId] || {};
    }

    function setPlaylistStorage(playlistId, playlistData) {
        if (!playlistId) {
            return;
        }

        var all = readAllStorage();
        all[playlistId] = playlistData;
        writeAllStorage(all);
    }

    function formatPercent(percent) {
        return String(Math.round(percent)).replace(/\d/g, function (digit) {
            return '۰۱۲۳۴۵۶۷۸۹'[digit];
        }) + '%';
    }

    function isWatchCompleted(current, duration) {
        if (!duration || duration <= 0 || !current || current <= 0) {
            return false;
        }

        var threshold = Math.max(duration - 3, duration * 0.95);
        return current >= threshold;
    }

    function isPostCompleted(root, postId) {
        var store = getPlaylistStorage(getPlaylistId(root));
        var stored = store[String(postId)];
        var duration = 0;
        var current = 0;

        if (stored) {
            duration = parseFloat(stored.duration) || 0;
            current = parseFloat(stored.current) || 0;
        }

        if (!duration) {
            getPlaylistMeta(root).forEach(function (item) {
                if (String(item.id) === String(postId)) {
                    duration = parseFloat(item.duration) || 0;
                    if (!current && item.progress) {
                        current = parseFloat(item.progress) || 0;
                    }
                }
            });
        }

        return isWatchCompleted(current, duration);
    }

    function updateItemStates(root) {
        if (!root) {
            return;
        }

        root.querySelectorAll('.webmz-ytp__item[data-post-id]').forEach(function (item) {
            var postId = item.getAttribute('data-post-id');
            item.classList.toggle('is-completed', isPostCompleted(root, postId));
        });
    }

    function updateItemDurationMeta(root, postId, duration) {
        if (!root || !postId || !duration || duration < 1) {
            return;
        }

        var items = getPlaylistMeta(root);
        var changed = false;
        var matchedUrl = '';

        items.forEach(function (item) {
            if (String(item.id) === String(postId) && item.video_url) {
                matchedUrl = normalizeMediaUrl(item.video_url);
            }
        });

        items.forEach(function (item) {
            var sameItem = String(item.id) === String(postId);
            var sameUrl = matchedUrl && item.video_url && normalizeMediaUrl(item.video_url) === matchedUrl;

            if (!sameItem && !sameUrl) {
                return;
            }

            if (!item.duration || item.duration < duration) {
                item.duration = duration;
                changed = true;
            }
        });

        if (changed) {
            root.setAttribute('data-webmz-ytp-items', JSON.stringify(items));
        }
    }

    function saveProgress(root, postId, current, duration) {
        var playlistId = getPlaylistId(root);
        if (!playlistId || !postId) {
            return;
        }

        var store = getPlaylistStorage(playlistId);
        var key = String(postId);
        var prev = store[key] || {};
        var nextCurrent = Math.max(prev.current || 0, current || 0);
        var nextDuration = Math.max(duration || 0, prev.duration || 0);

        if (nextDuration > 0 && nextCurrent > nextDuration) {
            nextCurrent = nextDuration;
        }

        if (nextCurrent < 1 && nextDuration < 1) {
            return;
        }

        store[key] = {
            current: nextCurrent,
            duration: nextDuration,
            updated: Date.now()
        };
        setPlaylistStorage(playlistId, store);

        if (nextDuration > 0) {
            updateItemDurationMeta(root, postId, nextDuration);
        }

        updateProgressUI(root);
    }

    function calculateProgressPercent(root) {
        var items = getPlaylistMeta(root);
        var store = getPlaylistStorage(getPlaylistId(root));
        var totalDuration = 0;
        var totalCurrent = 0;

        items.forEach(function (item) {
            var id = String(item.id);
            var duration = parseFloat(item.duration) || 0;
            var stored = store[id];

            if (stored && stored.duration > duration) {
                duration = stored.duration;
            }

            if (duration <= 0) {
                return;
            }

            var current = stored ? Math.min(duration, Math.max(0, stored.current || 0)) : 0;
            totalDuration += duration;
            totalCurrent += current;
        });

        if (totalDuration <= 0) {
            return 0;
        }

        return Math.round(Math.min(100, Math.max(0, (totalCurrent / totalDuration) * 100)));
    }

    function updateProgressUI(root) {
        if (!root) {
            return;
        }

        var percent = calculateProgressPercent(root);
        var text = root.querySelector('[data-webmz-ytp-progress-text]');
        var bar = root.querySelector('[data-webmz-ytp-progress-bar]');

        if (text) {
            text.textContent = formatPercent(percent);
        }

        if (bar) {
            bar.style.width = percent + '%';
        }

        updateItemStates(root);
    }

    function seedStorageFromServer(root) {
        var playlistId = getPlaylistId(root);
        var items = getPlaylistMeta(root);
        var store = getPlaylistStorage(playlistId);
        var changed = false;

        items.forEach(function (item) {
            var id = String(item.id);
            var serverProgress = parseFloat(item.progress) || 0;
            var duration = parseFloat(item.duration) || 0;
            var prev = store[id] || {};
            var nextCurrent = Math.max(prev.current || 0, serverProgress);
            var nextDuration = Math.max(duration, prev.duration || 0);

            if (serverProgress < 1 && !prev.current) {
                return;
            }

            if (!store[id] || nextCurrent > (prev.current || 0) || nextDuration > (prev.duration || 0)) {
                store[id] = {
                    current: nextCurrent,
                    duration: nextDuration,
                    updated: Date.now()
                };
                changed = true;
            }
        });

        if (changed) {
            setPlaylistStorage(playlistId, store);
        }
    }

    function isProgressSuspended(root) {
        return !!(root && root.getAttribute('data-webmz-ytp-progress-suspend') === 'yes');
    }

    function setProgressSuspended(root, suspended) {
        if (!root) {
            return;
        }

        if (suspended) {
            root.setAttribute('data-webmz-ytp-progress-suspend', 'yes');
        } else {
            root.removeAttribute('data-webmz-ytp-progress-suspend');
        }
    }

    function getStoredProgress(root, postId) {
        if (!root || !postId) {
            return { current: 0, duration: 0 };
        }

        var store = getPlaylistStorage(getPlaylistId(root));
        var stored = store[String(postId)] || {};
        var current = parseFloat(stored.current) || 0;
        var duration = parseFloat(stored.duration) || 0;

        if (!duration || !current) {
            getPlaylistMeta(root).forEach(function (item) {
                if (String(item.id) !== String(postId)) {
                    return;
                }

                if (!duration) {
                    duration = parseFloat(item.duration) || 0;
                }

                if (!current && item.progress) {
                    current = parseFloat(item.progress) || 0;
                }
            });
        }

        return {
            current: current,
            duration: duration
        };
    }

    function saveCurrentVideoProgress(root) {
        var video = root ? root.querySelector('.webmz-ytp__video') : null;
        if (!video || isProgressSuspended(root)) {
            return;
        }

        var postId = video.getAttribute('data-webmz-history-post-id');
        var current = video.currentTime || 0;
        var duration = video.duration || 0;

        if (postId && current >= 1) {
            saveProgress(root, postId, current, isFinite(duration) ? duration : 0);
        }
    }

    function setupProgressTracking(root) {
        var video = root.querySelector('.webmz-ytp__video');
        if (!video || video.getAttribute('data-webmz-ytp-progress-ready') === 'yes') {
            return;
        }

        video.setAttribute('data-webmz-ytp-progress-ready', 'yes');

        var lastSave = 0;

        function persist(force) {
            if (isProgressSuspended(root)) {
                return;
            }

            var postId = video.getAttribute('data-webmz-history-post-id');
            var current = video.currentTime || 0;
            var duration = video.duration || 0;
            var now = Date.now();

            if (!postId || current < 1) {
                return;
            }

            if (!force && now - lastSave < 3000) {
                return;
            }

            lastSave = now;
            saveProgress(root, postId, current, isFinite(duration) ? duration : 0);
        }

        video.addEventListener('timeupdate', function () {
            persist(false);
        });
        video.addEventListener('pause', function () {
            persist(true);
        });
        video.addEventListener('ended', function () {
            if (isProgressSuspended(root)) {
                return;
            }

            var duration = video.duration || 0;
            var postId = video.getAttribute('data-webmz-history-post-id');

            if (postId && duration > 0) {
                saveProgress(root, postId, duration, duration);
            }
        });

        if (!root.getAttribute('data-webmz-ytp-unload-bound')) {
            root.setAttribute('data-webmz-ytp-unload-bound', 'yes');
            window.addEventListener('beforeunload', function () {
                saveCurrentVideoProgress(root);
            });
        }
    }

    function getPlyrOptions() {
        var options = {};

        if (window.webmzTadrisWidgets && window.webmzTadrisWidgets.plyrIconUrl) {
            options.iconUrl = window.webmzTadrisWidgets.plyrIconUrl;
            options.loadSprite = true;
        }

        return options;
    }

    function getPlyrInstance(video) {
        if (!video) {
            return null;
        }

        if (video.plyr && typeof video.plyr.destroy === 'function') {
            return video.plyr;
        }

        if (typeof window.Plyr !== 'undefined' && typeof window.Plyr.get === 'function') {
            return window.Plyr.get(video) || null;
        }

        return null;
    }

    function destroyPlyr(video) {
        var instance = getPlyrInstance(video);

        if (instance) {
            try {
                instance.destroy();
            } catch (error) {
                // Ignore destroy errors when Plyr is already torn down.
            }
        }

        if (!video) {
            return;
        }

        video.plyr = null;
        video.removeAttribute('data-webmz-ytp-plyr-ready');
    }

    function normalizeMediaUrl(url) {
        if (!url) {
            return '';
        }

        try {
            var resolved = new URL(String(url), window.location.href);
            resolved.hash = '';
            return resolved.href;
        } catch (error) {
            return String(url).split('#')[0];
        }
    }

    function getMediaCurrentUrl(media) {
        if (!media) {
            return '';
        }

        var source = media.querySelector('source');
        var raw = (source && source.getAttribute('src')) || media.getAttribute('src') || media.currentSrc || '';

        return normalizeMediaUrl(raw);
    }

    function setVideoSource(video, url) {
        if (!video) {
            return;
        }

        var nextUrl = String(url || '');
        var source = video.querySelector('source');
        var sameUrl = normalizeMediaUrl(nextUrl) !== '' && getMediaCurrentUrl(video) === normalizeMediaUrl(nextUrl);

        // Same URL does not always reload in browsers; clear first so each lesson starts clean.
        if (sameUrl) {
            if (source) {
                source.removeAttribute('src');
            }

            video.removeAttribute('src');
            video.load();
        }

        if (source) {
            source.src = nextUrl;
        } else {
            video.innerHTML = '<source src="' + escapeHtml(nextUrl) + '" type="video/mp4">';
        }

        video.load();
    }

    function seekMediaToProgress(media, startAt) {
        if (!media) {
            return;
        }

        var target = Math.max(0, parseFloat(startAt) || 0);

        var apply = function () {
            var duration = isFinite(media.duration) ? media.duration : 0;

            try {
                if (target > 0) {
                    media.currentTime = duration > 0 ? Math.min(target, Math.max(0, duration - 1)) : target;
                } else {
                    media.currentTime = 0;
                }
            } catch (error) {
                // Ignore seek errors while media is still switching.
            }
        };

        if (media.readyState >= 1) {
            apply();
        } else {
            media.addEventListener('loadedmetadata', apply, { once: true });
        }
    }

    function initPlyr(video) {
        if (!video || typeof window.Plyr === 'undefined') {
            return null;
        }

        if (video.getAttribute('data-webmz-ytp-plyr-ready') === 'yes' && getPlyrInstance(video)) {
            return getPlyrInstance(video);
        }

        destroyPlyr(video);
        video.setAttribute('data-webmz-ytp-plyr-ready', 'yes');
        video.plyr = new window.Plyr(video, getPlyrOptions());

        return video.plyr;
    }

    function clearEmbed(root) {
        var slot = root.querySelector('[data-webmz-ytp-embed]');
        var wrap = root.querySelector('.webmz-ytp__player-wrap');

        if (slot && slot.webmzEmbedPlayer && typeof slot.webmzEmbedPlayer.destroy === 'function') {
            try {
                slot.webmzEmbedPlayer.destroy();
            } catch (error) {
                // Ignore destroy errors when the embed is already gone.
            }
        }

        if (slot) {
            slot.webmzEmbedPlayer = null;
            slot.innerHTML = '';
        }

        if (wrap) {
            wrap.classList.remove('is-embed');
        }
    }

    /**
     * Play an Aparat/YouTube lesson in the embed slot. The MP4 player is paused and
     * detached from history so no progress is recorded against the embed lesson.
     */
    function showEmbed(root, video, data, shouldPlay) {
        var slot = root.querySelector('[data-webmz-ytp-embed]');
        var wrap = root.querySelector('.webmz-ytp__player-wrap');
        var player = getPlyrInstance(video);

        if (!slot || !wrap || !window.webmzVideo) {
            return;
        }

        if (player && typeof player.pause === 'function') {
            player.pause();
        } else if (video && typeof video.pause === 'function') {
            video.pause();
        }

        video.removeAttribute('data-webmz-history-post-id');
        clearEmbed(root);
        wrap.classList.add('is-embed');
        slot.webmzEmbedPlayer = window.webmzVideo.mount(slot, data.video_url, { title: data.title || '' });

        if (shouldPlay && slot.webmzEmbedPlayer && typeof slot.webmzEmbedPlayer.play === 'function') {
            var play = slot.webmzEmbedPlayer.play();
            if (play && typeof play.catch === 'function') {
                play.catch(function () {});
            }
        }
    }

    function updatePlayerSource(root, video, data, shouldPlay) {
        if (!video || !data || !data.video_url) {
            return null;
        }

        if (window.webmzVideo && window.webmzVideo.isEmbed(data.video_url)) {
            showEmbed(root, video, data, shouldPlay);
            return null;
        }

        clearEmbed(root);

        var url = data.video_url;
        var player = getPlyrInstance(video);
        var media = player && player.media ? player.media : video;
        var postId = String(data.id || '');
        var saved = getStoredProgress(root, postId);
        var resumeAt = saved.current > 0 && !isWatchCompleted(saved.current, saved.duration || 0)
            ? saved.current
            : 0;

        setProgressSuspended(root, true);

        // Pause while the old post id is still set so history saves against the previous lesson.
        if (player && typeof player.pause === 'function') {
            player.pause();
        } else if (typeof media.pause === 'function') {
            media.pause();
        }

        // Drop post id during source swap so shared-URL reloads cannot leak progress into the next lesson.
        video.removeAttribute('data-webmz-history-post-id');

        if (data.image_url) {
            media.setAttribute('poster', data.image_url);
        } else {
            media.removeAttribute('poster');
        }

        // Plyr draws its own poster layer; the attribute alone leaves the previous lesson's image.
        if (player) {
            try {
                player.poster = data.image_url || '';
            } catch (error) {
                // Older Plyr builds without a poster setter keep the attribute value.
            }
        }

        setVideoSource(media, url);

        if (!player) {
            player = initPlyr(video);
            media = player && player.media ? player.media : video;
        }

        var attached = false;
        var attachPostId = function () {
            if (attached) {
                return;
            }

            attached = true;
            seekMediaToProgress(media, resumeAt);
            video.setAttribute('data-webmz-history-post-id', postId);
            setProgressSuspended(root, false);
        };

        var onReady = function () {
            media.removeEventListener('loadeddata', onReady);
            media.removeEventListener('canplay', onReady);
            attachPostId();
        };

        if (media.readyState >= 1) {
            attachPostId();
        } else {
            media.addEventListener('loadeddata', onReady);
            media.addEventListener('canplay', onReady);
            window.setTimeout(attachPostId, 800);
        }

        if (shouldPlay && player && typeof player.play === 'function') {
            var playTrack = function () {
                media.removeEventListener('loadeddata', playTrack);
                media.removeEventListener('canplay', playTrack);
                attachPostId();
                player.play().catch(function () {
                    // Autoplay may be blocked until the next user gesture.
                });
            };

            if (media.readyState >= 2) {
                attachPostId();
                player.play().catch(function () {});
            } else {
                media.addEventListener('loadeddata', playTrack);
                media.addEventListener('canplay', playTrack);
            }
        }

        return player;
    }

    function setLoadingState(root, item, isLoading) {
        if (!root) {
            return;
        }

        root.classList.toggle('is-switching', !!isLoading);

        if (item) {
            item.classList.toggle('is-loading', !!isLoading);
            item.setAttribute('aria-busy', isLoading ? 'true' : 'false');
        }
    }

    function updateFavoriteButton(root, data) {
        var button = root.querySelector('[data-webmz-favorite-post]');
        var label = button ? button.querySelector('.webmz-favorite-label') : null;
        var isSaved = !!(data && data.is_saved);

        if (!button) {
            return;
        }

        button.setAttribute('data-webmz-favorite-post', String(data.id || ''));
        button.classList.toggle('is-saved', isSaved);
        button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');

        if (label) {
            label.textContent = isSaved
                ? (button.getAttribute('data-remove-label') || 'حذف از ذخیره')
                : (button.getAttribute('data-save-label') || 'ذخیره');
        }
    }

    function setActiveItem(root, postId) {
        root.querySelectorAll('.webmz-ytp__item').forEach(function (item) {
            var active = item.getAttribute('data-post-id') === String(postId);
            item.classList.toggle('is-active', active);
        });
    }

    function switchVideo(root, item) {
        var cfg = config();
        var postId = item.getAttribute('data-post-id');
        var video = root.querySelector('.webmz-ytp__video');
        var title = root.querySelector('[data-webmz-ytp-title]');
        var level = root.querySelector('[data-webmz-ytp-level]');
        var excerpt = root.querySelector('[data-webmz-ytp-excerpt]');
        var duration = root.querySelector('[data-webmz-ytp-duration]');
        var levelText = root.querySelector('[data-webmz-ytp-level-text]');
        var author = root.querySelector('[data-webmz-ytp-author]');
        var authorAvatar = root.querySelector('[data-webmz-ytp-author-avatar]');
        var lessonBadge = root.querySelector('[data-webmz-ytp-lesson-badge]');
        var shareBtn = root.querySelector('.webmz-ytp__action-btn.video-action-share');
        var downloadBtn = root.querySelector('.webmz-ytp__action-btn--download');

        if (!postId || !video || item.classList.contains('is-active') || item.classList.contains('is-loading')) {
            return;
        }

        saveCurrentVideoProgress(root);

        setLoadingState(root, item, true);

        $.ajax({
            url: cfg.ajaxUrl || '',
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'webmz_youtube_playlist_load',
                nonce: cfg.nonce || '',
                post_id: postId
            }
        }).done(function (response) {
            if (!response || !response.success || !response.data) {
                return;
            }

            var data = response.data;

            if (title) {
                title.textContent = data.title || '';
                title.setAttribute('href', data.permalink || '#');
            }

            if (level) {
                level.textContent = data.level_label || '';
                level.hidden = !data.level_label;
            }
            if (levelText) {
                levelText.textContent = data.level_label || '—';
            }
            if (excerpt) {
                excerpt.textContent = data.excerpt || '';
            }
            if (duration) {
                duration.textContent = data.duration_label || '--:--';
            }
            if (author) {
                author.textContent = data.author_name || '';
            }
            if (authorAvatar) {
                if (data.author_avatar) {
                    authorAvatar.src = data.author_avatar;
                    authorAvatar.hidden = false;
                } else {
                    authorAvatar.hidden = true;
                }
            }
            if (lessonBadge) {
                var list = root.querySelectorAll('.webmz-ytp__item');
                var index = Array.prototype.indexOf.call(list, item);
                lessonBadge.textContent = 'درس ' + String(index + 1);
            }
            if (shareBtn) {
                shareBtn.setAttribute('data-webmz-share-toggle', String(data.id || ''));
                shareBtn.setAttribute('data-share-url', data.permalink || '#');
                shareBtn.setAttribute('data-share-title', data.title || '');
            }
            if (downloadBtn) {
                if (data.downloads && data.downloads.length && data.downloads[0].link) {
                    if (root.getAttribute('data-require-login-download') === 'yes') {
                        downloadBtn.setAttribute('href', '#');
                        downloadBtn.setAttribute('data-webmz-download-guard', 'yes');
                    } else {
                        downloadBtn.setAttribute('href', data.downloads[0].link);
                        downloadBtn.removeAttribute('data-webmz-download-guard');
                    }
                } else {
                    downloadBtn.setAttribute('href', '#');
                }
            }

            updatePlayerSource(root, video, data, true);
            updateFavoriteButton(root, data);
            setActiveItem(root, postId);
        }).fail(function () {
            if (window.console && typeof window.console.error === 'function') {
                window.console.error(cfg.errorText || 'خطا در بارگذاری ویدیو.');
            }
        }).always(function () {
            setLoadingState(root, item, false);
        });
    }

    function setupWidget(root) {
        if (!root || root.getAttribute('data-webmz-ytp-ready') === 'yes') {
            return;
        }

        root.setAttribute('data-webmz-ytp-ready', 'yes');
        seedStorageFromServer(root);
        updateProgressUI(root);
        initPlyr(root.querySelector('.webmz-ytp__video'));
        setupProgressTracking(root);

        // First lesson rendered server-side as a YouTube embed still needs Plyr.
        var embedSlot = root.querySelector('[data-webmz-ytp-embed]');
        var embedTarget = embedSlot ? embedSlot.querySelector('.plyr__video-embed') : null;
        if (embedTarget && typeof window.Plyr !== 'undefined') {
            embedSlot.webmzEmbedPlayer = new window.Plyr(embedTarget, getPlyrOptions());
        }

        root.addEventListener('click', function (event) {
            var trigger = event.target.closest('.webmz-ytp__item-trigger');
            if (!trigger) {
                return;
            }

            event.preventDefault();
            var item = trigger.closest('.webmz-ytp__item');
            if (item) {
                switchVideo(root, item);
            }
        });
    }

    function initScope(scope) {
        var context = scope && scope.querySelectorAll ? scope : document;
        context.querySelectorAll('.webmz-ytp').forEach(setupWidget);
    }

    $(function () {
        initScope(document);
    });

    $(window).on('elementor/frontend/init', function () {
        if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-youtube-playlist.default',
            function ($scope) {
                initScope($scope[0]);
            }
        );
    });
}(jQuery));
