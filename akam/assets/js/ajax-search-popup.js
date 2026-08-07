(function () {
	'use strict';

	var ANIMATION_MS = 580;

	function isElementorEditMode() {
		return typeof window.elementorFrontend !== 'undefined'
			&& typeof window.elementorFrontend.isEditMode === 'function'
			&& window.elementorFrontend.isEditMode();
	}

	function ensureRootId(root) {
		if (root.id) {
			return root.id;
		}

		root.id = 'webmz-search-popup-root-' + Math.random().toString(36).slice(2, 9);
		return root.id;
	}

	function getOverlay(root) {
		var rootId = root.id || root.getAttribute('id');

		if (rootId) {
			var portaled = document.querySelector(
				'[data-webmz-search-popup-overlay][data-webmz-search-popup-for="' + rootId + '"]'
			);

			if (portaled) {
				return portaled;
			}
		}

		return root.querySelector('[data-webmz-search-popup-overlay]');
	}

	function portalOverlay(overlay, root) {
		if (!overlay) {
			return;
		}

		if (overlay.getAttribute('data-webmz-portaled') === 'yes' || isElementorEditMode()) {
			return;
		}

		overlay.setAttribute('data-webmz-search-popup-for', ensureRootId(root));
		overlay.ownerDocument.body.appendChild(overlay);
		overlay.setAttribute('data-webmz-portaled', 'yes');
	}

	function getEventElement(target) {
		if (!target) {
			return null;
		}

		return target.nodeType === 1 ? target : target.parentElement;
	}

	function isOverlayVisible(overlay) {
		return overlay.classList.contains('is-open') || overlay.classList.contains('is-closing');
	}

	function finishBoot(root, overlay) {
		root.classList.remove('webmz-search-popup--boot');

		if (overlay) {
			overlay.removeAttribute('style');
		}
	}

	function getSearchRoot(root) {
		var overlay = getOverlay(root);
		var searchRoot;

		if (overlay) {
			searchRoot = overlay.querySelector('.webmz-ajax-search');

			if (searchRoot) {
				return searchRoot;
			}
		}

		return root.querySelector('.webmz-ajax-search');
	}

	function resetSearchState(root) {
		var searchRoot = getSearchRoot(root);
		var panel;
		var input;

		if (!searchRoot) {
			return;
		}

		panel = searchRoot.querySelector('.webmz-ajax-search__panel');
		input = searchRoot.querySelector('.webmz-ajax-search__input');

		if (searchRoot.classList.contains('webmz-ajax-search--zhaket')) {
			if (panel) {
				panel.hidden = false;
			}

			searchRoot.classList.remove('is-searching');

			if (input) {
				input.value = '';
				input.blur();
			}

			var idle = searchRoot.querySelector('[data-webmz-zhaket-idle]');
			var results = searchRoot.querySelector('[data-webmz-zhaket-results]');
			var banner = searchRoot.querySelector('[data-webmz-zhaket-banner]');

			if (idle) {
				idle.hidden = false;
			}

			if (results) {
				results.hidden = true;
			}

			if (banner) {
				banner.hidden = false;
			}

			var products = searchRoot.querySelector('[data-webmz-results="products"]');
			var posts = searchRoot.querySelector('[data-webmz-results="posts"]');

			if (products) {
				products.innerHTML = '';
			}

			if (posts) {
				posts.innerHTML = '';
			}

			searchRoot.webmzEnterTerm = '';
			searchRoot.webmzEnterCount = 0;
			return;
		}

		if (panel) {
			panel.hidden = true;
		}

		if (input) {
			input.value = '';
			input.blur();
		}

		searchRoot.webmzEnterTerm = '';
		searchRoot.webmzEnterCount = 0;
	}

	function openPopup(root) {
		var overlay = getOverlay(root);
		var searchRoot = getSearchRoot(root);
		var panel;
		var input;

		if (!overlay || isOverlayVisible(overlay)) {
			return;
		}

		finishBoot(root, overlay);
		overlay.hidden = false;
		document.body.classList.add('webmz-search-popup-open');

		window.requestAnimationFrame(function () {
			window.requestAnimationFrame(function () {
				overlay.classList.add('is-open');
			});
		});

		if (searchRoot) {
			panel = searchRoot.querySelector('.webmz-ajax-search__panel');
			input = searchRoot.querySelector('.webmz-ajax-search__input');

			if (panel) {
				panel.hidden = false;
			}

			if (input) {
				window.setTimeout(function () {
					input.focus();
				}, ANIMATION_MS * 0.45);
			}
		}
	}

	function closePopup(root) {
		var overlay = getOverlay(root);

		if (!overlay || !overlay.classList.contains('is-open') || overlay.classList.contains('is-closing')) {
			return;
		}

		overlay.classList.remove('is-open');
		overlay.classList.add('is-closing');

		window.setTimeout(function () {
			overlay.classList.remove('is-closing');
			overlay.hidden = true;
			document.body.classList.remove('webmz-search-popup-open');
			resetSearchState(root);
		}, ANIMATION_MS);
	}

	function initPopup(root) {
		if (!root || root.dataset.webmzPopupReady === 'yes') {
			return;
		}

		root.dataset.webmzPopupReady = 'yes';

		var trigger = root.querySelector('[data-webmz-search-popup-trigger]');
		var overlay = root.querySelector('[data-webmz-search-popup-overlay]');
		var dialog;
		var closeButton;

		if (!trigger || !overlay) {
			return;
		}

		finishBoot(root, overlay);
		portalOverlay(overlay, root);

		dialog = overlay.querySelector('[data-webmz-search-popup-dialog]');
		closeButton = overlay.querySelector('[data-webmz-search-popup-close]');

		trigger.addEventListener('click', function () {
			openPopup(root);
		});

		if (closeButton) {
			closeButton.addEventListener('click', function () {
				closePopup(root);
			});
		}

		overlay.addEventListener('click', function (event) {
			var target = getEventElement(event.target);

			if (!target || target === overlay) {
				closePopup(root);
			}
		});

		if (dialog) {
			dialog.addEventListener('click', function (event) {
				event.stopPropagation();
			});
		}

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
				closePopup(root);
			}
		});
	}

	function initScope(scope) {
		if (!scope || !scope.querySelectorAll) {
			return;
		}

		scope.querySelectorAll('[data-webmz-search-popup]').forEach(initPopup);

		if (scope.dataset && scope.dataset.webmzSearchPopup !== undefined) {
			initPopup(scope);
		}
	}

	function observePopups() {
		if (!window.MutationObserver || !document.body) {
			return;
		}

		new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				mutation.addedNodes.forEach(function (node) {
					if (node.nodeType === 1) {
						initScope(node);
					}
				});
			});
		}).observe(document.body, {
			childList: true,
			subtree: true
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initScope(document);
			observePopups();
		});
	} else {
		initScope(document);
		observePopups();
	}
}());
