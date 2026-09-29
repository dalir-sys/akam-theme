/**
 * Akam hover effects helper.
 *
 * Cards are prepared lazily on first hover (works for cards loaded by AJAX too):
 * - image zoom is enabled only when a wrapper between the image and the card
 *   already clips its content, so zoomed images never spill over badges;
 * - in "vivid" mode the spotlight glow follows the cursor.
 */
(function () {
	'use strict';

	var body = document.body;
	if (!body || !body.classList.contains('webmz-fx')) {
		return;
	}

	var CARD_SELECTOR = [
		'.tadris-product-type-1',
		'.tadris-product-type-2',
		'.tadris-product-type-3',
		'.tadris-blog-tile-card',
		'.webmz-blog-archive-card',
		'.webmz-post-card',
		'.webmz-teacher-card',
		'.webmz-pro-teacher-card',
		'.webmz-cc__card',
		'.webmz-cts2__card',
		'.webmz-idb__item',
		'.webmz-service-box',
		'.webmz-zpl__card',
		'.webmz-zbl__card',
		'.webmz-zpt__card',
		'.tadris-icon-box-1',
		'[data-webmz-fx-card]'
	].join(',');

	var vivid = body.classList.contains('webmz-fx--vivid');
	var finePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	function clips(element) {
		var style = window.getComputedStyle(element);
		return /(hidden|clip)/.test(style.overflow + ' ' + style.overflowX + ' ' + style.overflowY);
	}

	function prepareZoom(card) {
		card.querySelectorAll('img').forEach(function (img) {
			// Skip icons and avatars; only real cover images zoom.
			if (img.hasAttribute('data-webmz-fx-zoom') || img.clientWidth < 120) {
				return;
			}

			for (var node = img.parentElement; node && node !== card.parentElement; node = node.parentElement) {
				if (clips(node)) {
					img.setAttribute('data-webmz-fx-zoom', '');
					return;
				}
				if (node === card) {
					return;
				}
			}
		});
	}

	/**
	 * A static card can become position:relative only if none of its absolutely positioned
	 * descendants is currently placed against an ancestor outside the card.
	 */
	function canHostOverlay(card) {
		if (window.getComputedStyle(card).position !== 'static') {
			return true;
		}

		var escapes = Array.prototype.some.call(card.querySelectorAll('*'), function (child) {
			var position = window.getComputedStyle(child).position;
			return (position === 'absolute' || position === 'fixed') && !(child.offsetParent && card.contains(child.offsetParent));
		});

		if (escapes) {
			return false;
		}

		card.style.position = 'relative';
		return true;
	}

	function prepare(card) {
		if (card.hasAttribute('data-webmz-fx-card-root')) {
			return;
		}

		card.setAttribute('data-webmz-fx-card-root', '');
		prepareZoom(card);

		// The glow is its own overlay because cards paint their own backgrounds.
		if (vivid && finePointer && canHostOverlay(card)) {
			var spot = document.createElement('span');
			spot.className = 'webmz-fx-spot';
			spot.setAttribute('aria-hidden', 'true');
			card.appendChild(spot);
			card.setAttribute('data-webmz-fx-spotlight', '');
		}
	}

	document.addEventListener('pointerover', function (event) {
		var card = event.target.closest ? event.target.closest(CARD_SELECTOR) : null;
		if (card) {
			prepare(card);
		}
	}, { passive: true });

	if (!vivid || !finePointer) {
		return;
	}

	var pending = null;
	var lastEvent = null;

	document.addEventListener('pointermove', function (event) {
		lastEvent = event;

		if (pending) {
			return;
		}

		pending = window.requestAnimationFrame(function () {
			pending = null;
			var card = lastEvent.target.closest ? lastEvent.target.closest('[data-webmz-fx-spotlight]') : null;

			if (!card) {
				return;
			}

			var rect = card.getBoundingClientRect();
			card.style.setProperty('--webmz-fx-x', (lastEvent.clientX - rect.left) + 'px');
			card.style.setProperty('--webmz-fx-y', (lastEvent.clientY - rect.top) + 'px');
		});
	}, { passive: true });
}());
