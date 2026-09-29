/**
 * Shared video helpers: direct files, Aparat and YouTube links.
 *
 * Mirrors inc/video-embeds.php so players swapped in JavaScript (playlist,
 * popup player) render the same markup as server-rendered ones.
 */
(function () {
	'use strict';

	var cfg = window.webmzVideoEmbeds || {};
	var modal = null;
	var modalPlayer = null;
	var lastFocus = null;

	function parse(url) {
		var value = String(url || '').trim();
		var match;

		if (!value) {
			return { type: '', id: '', url: '', embedUrl: '' };
		}

		match = value.match(/aparat\.com\/(?:v\/|video\/video\/embed\/videohash\/|embed\/)([A-Za-z0-9]+)/i);
		if (match) {
			return {
				type: 'aparat',
				id: match[1],
				url: value,
				embedUrl: 'https://www.aparat.com/video/video/embed/videohash/' + encodeURIComponent(match[1]) + '/vt/frame'
			};
		}

		match = value.match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:embed\/|shorts\/|live\/|v\/|watch\?(?:.*&)?v=))([A-Za-z0-9_-]{6,})/i);
		if (match) {
			return {
				type: 'youtube',
				id: match[1],
				url: value,
				embedUrl: 'https://www.youtube.com/embed/' + encodeURIComponent(match[1])
			};
		}

		return { type: 'file', id: '', url: value, embedUrl: '' };
	}

	function isEmbed(url) {
		var type = parse(url).type;
		return type === 'aparat' || type === 'youtube';
	}

	function escapeAttr(value) {
		return String(value || '')
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}

	function fileMime(url) {
		var path = String(url || '').split(/[?#]/)[0].toLowerCase();
		if (/\.webm$/.test(path)) {
			return 'video/webm';
		}
		if (/\.(ogv|ogg)$/.test(path)) {
			return 'video/ogg';
		}
		return 'video/mp4';
	}

	/**
	 * Build player markup.
	 *
	 * @param {string} url
	 * @param {{poster?:string, videoClass?:string, title?:string}} options
	 * @return {string}
	 */
	function buildMarkup(url, options) {
		var opts = options || {};
		var video = parse(url);
		var title = escapeAttr(opts.title || cfg.videoLabel || 'video');
		var videoClass = escapeAttr(opts.videoClass || 'tadris-player-tag');

		if (video.type === 'aparat') {
			return '<div class="webmz-video-embed webmz-video-embed--aparat" data-webmz-video-type="aparat">' +
				'<iframe src="' + escapeAttr(video.embedUrl) + '" title="' + title + '" loading="lazy" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" allow="autoplay; fullscreen; picture-in-picture"></iframe>' +
				'</div>';
		}

		if (video.type === 'youtube') {
			var origin = encodeURIComponent(window.location.origin || '');
			return '<div class="webmz-video-embed webmz-video-embed--youtube" data-webmz-video-type="youtube">' +
				'<div class="plyr__video-embed ' + videoClass + '">' +
				'<iframe src="' + escapeAttr(video.embedUrl) + '?origin=' + origin + '&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" title="' + title + '" allowfullscreen allowtransparency allow="autoplay; fullscreen; picture-in-picture"></iframe>' +
				'</div></div>';
		}

		if (video.type === 'file') {
			var poster = opts.poster ? ' poster="' + escapeAttr(opts.poster) + '" data-poster="' + escapeAttr(opts.poster) + '"' : '';
			return '<video class="' + videoClass + '" playsinline controls preload="metadata"' + poster + '>' +
				'<source src="' + escapeAttr(video.url) + '" type="' + fileMime(video.url) + '">' +
				'</video>';
		}

		return '';
	}

	function plyrOptions() {
		var options = {};
		var widgets = window.webmzTadrisWidgets || {};

		if (widgets.plyrIconUrl) {
			options.iconUrl = widgets.plyrIconUrl;
			options.loadSprite = true;
		}

		return options;
	}

	/**
	 * Render a player into a container and enhance it with Plyr when possible.
	 * Aparat embeds are left as plain iframes (no player API).
	 *
	 * @return {object|null} Plyr instance, if one was created.
	 */
	function mount(container, url, options) {
		if (!container) {
			return null;
		}

		container.innerHTML = buildMarkup(url, options);

		var target = container.querySelector('video, .plyr__video-embed');
		if (!target || typeof window.Plyr === 'undefined') {
			return null;
		}

		// Stop the generic tadris-widgets initializer from wrapping it a second time.
		target.setAttribute('data-tadris-player-ready', 'yes');

		return new window.Plyr(target, plyrOptions());
	}

	function destroyModalPlayer() {
		if (modalPlayer && typeof modalPlayer.destroy === 'function') {
			try {
				modalPlayer.destroy();
			} catch (error) {
				// Player already torn down.
			}
		}
		modalPlayer = null;
	}

	function ensureModal() {
		if (modal) {
			return modal;
		}

		modal = document.createElement('div');
		modal.className = 'webmz-video-modal';
		modal.hidden = true;
		modal.setAttribute('role', 'dialog');
		modal.setAttribute('aria-modal', 'true');
		modal.innerHTML =
			'<div class="webmz-video-modal__dialog">' +
			'<button type="button" class="webmz-video-modal__close" aria-label="' + escapeAttr(cfg.closeLabel || 'Close') + '">&times;</button>' +
			'<p class="webmz-video-modal__title"></p>' +
			'<div class="webmz-video-modal__player"></div>' +
			'</div>';

		modal.addEventListener('click', function (event) {
			if (event.target === modal || event.target.closest('.webmz-video-modal__close')) {
				closeModal();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && modal && !modal.hidden) {
				closeModal();
			}
		});

		document.body.appendChild(modal);
		return modal;
	}

	function openModal(url, options) {
		var opts = options || {};

		if (!parse(url).type) {
			return;
		}

		ensureModal();
		lastFocus = document.activeElement;
		destroyModalPlayer();

		modal.querySelector('.webmz-video-modal__title').textContent = opts.title || '';
		modal.hidden = false;
		document.body.classList.add('webmz-video-modal-open');

		modalPlayer = mount(modal.querySelector('.webmz-video-modal__player'), url, {
			poster: opts.poster || '',
			title: opts.title || '',
			videoClass: 'webmz-video-modal__video'
		});

		if (modalPlayer && typeof modalPlayer.play === 'function') {
			var play = modalPlayer.play();
			if (play && typeof play.catch === 'function') {
				play.catch(function () {});
			}
		}

		window.requestAnimationFrame(function () {
			modal.classList.add('is-open');
			modal.querySelector('.webmz-video-modal__close').focus();
		});
	}

	function closeModal() {
		if (!modal || modal.hidden) {
			return;
		}

		destroyModalPlayer();
		// Removing the markup also stops Aparat iframes from playing in the background.
		modal.querySelector('.webmz-video-modal__player').innerHTML = '';
		modal.classList.remove('is-open');
		modal.hidden = true;
		document.body.classList.remove('webmz-video-modal-open');

		if (lastFocus && typeof lastFocus.focus === 'function') {
			lastFocus.focus();
		}
	}

	document.addEventListener('click', function (event) {
		var trigger = event.target.closest ? event.target.closest('[data-webmz-video-modal]') : null;

		if (!trigger || trigger.getAttribute('data-webmz-download-guard') === 'yes') {
			return;
		}

		var url = trigger.getAttribute('data-webmz-video-url') || trigger.getAttribute('href') || '';
		if (!parse(url).type || url === '#') {
			return;
		}

		event.preventDefault();
		openModal(url, {
			title: trigger.getAttribute('data-webmz-video-title') || '',
			poster: trigger.getAttribute('data-webmz-video-poster') || ''
		});
	});

	window.webmzVideo = {
		parse: parse,
		isEmbed: isEmbed,
		buildMarkup: buildMarkup,
		mount: mount,
		openModal: openModal,
		closeModal: closeModal
	};
}());
