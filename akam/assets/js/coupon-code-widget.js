/**
 * Copy-to-clipboard for the coupon code widget.
 *
 * Delegated from document so it also works for widgets inside Elementor popups
 * that are injected after page load.
 */
(function () {
	'use strict';

	function fallbackCopy(text) {
		var field = document.createElement('textarea');
		field.value = text;
		field.setAttribute('readonly', '');
		field.style.position = 'fixed';
		field.style.opacity = '0';
		document.body.appendChild(field);
		field.select();

		var ok = false;
		try {
			ok = document.execCommand('copy');
		} catch (error) {
			ok = false;
		}

		document.body.removeChild(field);
		return ok;
	}

	function copy(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text).then(function () {
				return true;
			}, function () {
				return fallbackCopy(text);
			});
		}

		return Promise.resolve(fallbackCopy(text));
	}

	function showCopied(button) {
		var label = button.querySelector('.webmz-coupon__copy-label');

		if (!label) {
			return;
		}

		if (!button.hasAttribute('data-default-text')) {
			button.setAttribute('data-default-text', label.textContent);
		}

		window.clearTimeout(button.webmzCouponTimer);
		button.classList.add('is-copied');
		label.textContent = button.getAttribute('data-copied-text') || label.textContent;

		button.webmzCouponTimer = window.setTimeout(function () {
			button.classList.remove('is-copied');
			label.textContent = button.getAttribute('data-default-text');
		}, 2000);
	}

	document.addEventListener('click', function (event) {
		var button = event.target.closest ? event.target.closest('[data-webmz-coupon-copy]') : null;

		if (!button) {
			return;
		}

		event.preventDefault();

		copy(button.getAttribute('data-webmz-coupon-copy') || '').then(function (ok) {
			if (ok) {
				showCopied(button);
			}
		});
	});
}());
