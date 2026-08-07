(function () {
	'use strict';

	var openClass = 'is-open';
	var htmlLockClass = 'webmz-offcanvas-open';
	var editorPreviewClass = 'is-editor-preview';
	var mobileWidgetTypes = ['webmz-mobile-mini-cart', 'webmz-mini-cart', 'webmz-mobile-account', 'webmz-mobile-menu'];

	function isElementorEditMode() {
		return typeof window.elementorFrontend !== 'undefined'
			&& typeof window.elementorFrontend.isEditMode === 'function'
			&& window.elementorFrontend.isEditMode();
	}

	function characterLength(value) {
		return Array.from(value || '').length;
	}

	function getOpenPanels() {
		return document.querySelectorAll('.webmz-offcanvas.' + openClass);
	}

	function getFocusableElements(container) {
		if (!container) {
			return [];
		}

		return Array.prototype.slice.call(
			container.querySelectorAll(
				'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
			)
		).filter(function (el) {
			return el.getAttribute('aria-hidden') !== 'true'
				&& !el.hasAttribute('disabled')
				&& el.offsetWidth > 0
				&& el.offsetHeight > 0;
		});
	}

	function setPanelAccessibility(panel, isOpen) {
		if (!panel) {
			return;
		}

		panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
		panel.setAttribute('aria-modal', isOpen ? 'true' : 'false');

		if (!panel.getAttribute('role')) {
			panel.setAttribute('role', 'dialog');
		}

		if (isOpen) {
			panel.removeAttribute('inert');
		} else {
			panel.setAttribute('inert', '');
		}
	}

	function focusFirstInPanel(panel) {
		if (!panel) {
			return;
		}

		var preferred = panel.querySelector(
			'.webmz-offcanvas__close, [data-webmz-offcanvas-close], .webmz-msearch__input, a[href], button'
		);
		var focusables = getFocusableElements(panel);
		var target = preferred && focusables.indexOf(preferred) !== -1
			? preferred
			: focusables[0];

		if (target && typeof target.focus === 'function') {
			window.setTimeout(function () {
				target.focus();
			}, 0);
			return;
		}

		if (!panel.hasAttribute('tabindex')) {
			panel.setAttribute('tabindex', '-1');
		}

		panel.focus();
	}

	function closePanel(panel, options) {
		if (!panel) {
			return;
		}

		options = options || {};

		panel.classList.remove(openClass, editorPreviewClass);
		setPanelAccessibility(panel, false);

		var ownerDoc = panel.ownerDocument || document;
		var trigger = ownerDoc.querySelector('[aria-controls="' + panel.id + '"]');

		if (trigger) {
			trigger.setAttribute('aria-expanded', 'false');

			if (options.restoreFocus !== false && typeof trigger.focus === 'function') {
				trigger.focus();
			}
		}
	}

	function closeAll(except, options) {
		getOpenPanels().forEach(function (panel) {
			if (except && panel === except) {
				return;
			}

			closePanel(panel, options);
		});

		if (!except || !getOpenPanels().length) {
			document.documentElement.classList.remove(htmlLockClass);
		}
	}

	function openPanel(panel, trigger) {
		if (!panel) {
			return;
		}

		var ownerDoc = panel.ownerDocument || document;

		ownerDoc.querySelectorAll('.webmz-account.is-open, .webmz-hcart.is-open').forEach(function (widget) {
			widget.classList.remove(openClass);
			var commerceTrigger = widget.querySelector('[aria-expanded]');

			if (commerceTrigger) {
				commerceTrigger.setAttribute('aria-expanded', 'false');
			}
		});

		closeAll(panel, { restoreFocus: false });
		panel.classList.add(openClass);
		setPanelAccessibility(panel, true);

		if (isElementorEditMode()) {
			panel.classList.add(editorPreviewClass);
		} else {
			ownerDoc.documentElement.classList.add(htmlLockClass);
		}

		if (trigger) {
			trigger.setAttribute('aria-expanded', 'true');
		}

		focusFirstInPanel(panel);
	}

	function ensureRootId(root) {
		if (!root.id) {
			root.id = 'webmz-offcanvas-root-' + Math.random().toString(36).slice(2, 9);
		}

		return root.id;
	}

	function resolvePanel(root, trigger) {
		var panel = root.querySelector('[data-webmz-offcanvas-panel]');

		if (panel) {
			return panel;
		}

		if (trigger) {
			var panelId = trigger.getAttribute('aria-controls');

			if (panelId) {
				panel = document.getElementById(panelId);

				if (panel) {
					return panel;
				}
			}
		}

		var rootId = root.id || root.getAttribute('id');

		if (rootId) {
			return document.querySelector('[data-webmz-offcanvas-panel][data-webmz-offcanvas-for="' + rootId + '"]');
		}

		return null;
	}

	function portalPanel(panel, root) {
		if (!panel) {
			return;
		}

		panel.classList.remove('webmz-offcanvas--boot');

		if (panel.getAttribute('data-webmz-portaled') === 'yes' || isElementorEditMode()) {
			return;
		}

		if (root) {
			panel.setAttribute('data-webmz-offcanvas-for', ensureRootId(root));
		}

		panel.ownerDocument.body.appendChild(panel);
		panel.setAttribute('data-webmz-portaled', 'yes');
	}

	function initOffcanvas(root) {
		if (!root || root.getAttribute('data-webmz-offcanvas-ready') === 'yes') {
			return;
		}

		var trigger = root.querySelector('[data-webmz-offcanvas-trigger]');
		var panel = resolvePanel(root, trigger);

		if (!panel || !trigger) {
			return;
		}

		root.setAttribute('data-webmz-offcanvas-ready', 'yes');

		portalPanel(panel, root);

		if (!panel.id) {
			panel.id = 'webmz-offcanvas-' + Math.random().toString(36).slice(2, 9);
		}

		trigger.setAttribute('aria-controls', panel.id);
		trigger.setAttribute('aria-expanded', 'false');
		setPanelAccessibility(panel, false);
		panel.classList.remove(openClass, editorPreviewClass);

		var title = panel.querySelector('.webmz-offcanvas__title');

		if (title) {
			if (!title.id) {
				title.id = panel.id + '-title';
			}

			panel.setAttribute('aria-labelledby', title.id);
		} else if (!panel.getAttribute('aria-label')) {
			panel.setAttribute('aria-label', trigger.getAttribute('aria-label') || 'منو');
		}

		trigger.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();

			if (panel.classList.contains(openClass)) {
				closePanel(panel);
				(panel.ownerDocument || document).documentElement.classList.remove(htmlLockClass);
				return;
			}

			openPanel(panel, trigger);
		});

		panel.querySelectorAll('[data-webmz-offcanvas-close]').forEach(function (button) {
			button.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				closePanel(panel);
				(panel.ownerDocument || document).documentElement.classList.remove(htmlLockClass);
			});
		});

		var search = panel.querySelector('[data-webmz-msearch]');

		if (search) {
			initMobileSearch(search);
		}

		initMobileNav(panel);
		initGuestAccountTriggers(panel);
		initOpenAccountLinks(panel);
		initOpenCartLinks(panel);
	}

	function initMobileNav(scope) {
		scope.querySelectorAll('.webmz-mmenu-panel__nav .webmz-nav__toggle').forEach(function (button) {
			if (button.getAttribute('data-webmz-nav-ready') === 'yes') {
				return;
			}

			button.setAttribute('data-webmz-nav-ready', 'yes');

			button.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				var item = button.closest('.webmz-nav__item');

				if (!item) {
					return;
				}

				var opening = !item.classList.contains('is-submenu-open');
				item.classList.toggle('is-submenu-open', opening);
				button.setAttribute('aria-expanded', opening ? 'true' : 'false');
			});
		});
	}

	function showSearchBox(root) {
		var box = root.querySelector('[data-webmz-msearch-box]');

		if (box) {
			box.hidden = false;
		}
	}

	function hideMobileSearchBox(root) {
		var results = root.querySelector('[data-webmz-msearch-results]');
		var box = root.querySelector('[data-webmz-msearch-box]');
		var loading = root.querySelector('[data-webmz-msearch-loading]');
		var input = root.querySelector('.webmz-msearch__input');

		if (results) {
			results.hidden = true;
		}

		if (loading) {
			loading.hidden = true;
		}

		if (box) {
			box.hidden = true;
		}

		root.classList.remove('is-loading');
		root.setAttribute('aria-busy', 'false');

		if (input) {
			input.setAttribute('aria-busy', 'false');
		}
	}

	function setMobileSearchLoading(root, active) {
		var loading = root.querySelector('[data-webmz-msearch-loading]');
		var results = root.querySelector('[data-webmz-msearch-results]');
		var input = root.querySelector('.webmz-msearch__input');

		root.classList.toggle('is-loading', active);
		root.setAttribute('aria-busy', active ? 'true' : 'false');

		if (loading) {
			loading.hidden = !active;
		}

		if (active) {
			showSearchBox(root);

			if (results) {
				results.hidden = true;
			}
		} else if (loading) {
			loading.hidden = true;
		}

		if (input) {
			input.setAttribute('aria-busy', active ? 'true' : 'false');
		}
	}

	function resetMobileSearchResults(root) {
		hideMobileSearchBox(root);
	}

	function renderMobileSearchResults(root, data) {
		var products = root.querySelector('[data-webmz-msearch-products]');
		var posts = root.querySelector('[data-webmz-msearch-posts]');
		var results = root.querySelector('[data-webmz-msearch-results]');
		var loading = root.querySelector('[data-webmz-msearch-loading]');

		root.classList.remove('is-loading');

		if (loading) {
			loading.hidden = true;
		}

		showSearchBox(root);

		if (results) {
			results.hidden = false;
		}

		if (products) {
			products.innerHTML = data.products_html || '';
		}

		if (posts) {
			posts.innerHTML = data.posts_html || '';
		}
	}

	function requestMobileSearch(root, term) {
		if (typeof webmzMobileSearch === 'undefined') {
			return Promise.reject();
		}

		var productsLimit = root.getAttribute('data-products-limit') || '4';
		var postsLimit = root.getAttribute('data-posts-limit') || '4';

		var body = new window.FormData();
		body.append('action', 'webmz_live_search');
		body.append('nonce', webmzMobileSearch.nonce);
		body.append('term', term);
		body.append('mobile', '1');
		body.append('products_limit', productsLimit);
		body.append('posts_limit', postsLimit);

		return window.fetch(webmzMobileSearch.ajaxUrl, {
			method: 'POST',
			body: body,
			credentials: 'same-origin'
		}).then(function (response) {
			return response.json();
		}).then(function (payload) {
			if (!payload || !payload.success) {
				throw payload;
			}

			return payload.data;
		});
	}

	function ensureMobileSearchCloseButton(root) {
		var box = root.querySelector('[data-webmz-msearch-box]');

		if (!box || box.querySelector('[data-webmz-msearch-close]')) {
			return null;
		}

		var closeButton = document.createElement('button');
		closeButton.type = 'button';
		closeButton.className = 'webmz-msearch__close';
		closeButton.setAttribute('data-webmz-msearch-close', '');
		closeButton.setAttribute('aria-label', 'بستن نتایج');
		closeButton.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
		box.insertBefore(closeButton, box.firstChild);

		return closeButton;
	}

	function initMobileSearch(root) {
		if (!root || root.getAttribute('data-webmz-msearch-ready') === 'yes') {
			return;
		}

		root.setAttribute('data-webmz-msearch-ready', 'yes');

		var form = root.querySelector('.webmz-msearch__form');
		var input = root.querySelector('.webmz-msearch__input');
		var timer = null;

		if (!form || !input) {
			return;
		}

		function runSearch(term) {
			var value = String(term || '').trim();

			if (characterLength(value) < 3) {
				resetMobileSearchResults(root);
				return;
			}

			setMobileSearchLoading(root, true);

			requestMobileSearch(root, value).then(function (data) {
				renderMobileSearchResults(root, data);
			}).catch(function () {
				renderMobileSearchResults(root, {
					products_html: '<li class="webmz-msearch__empty">نتیجه‌ای یافت نشد.</li>',
					posts_html: '<li class="webmz-msearch__empty">نتیجه‌ای یافت نشد.</li>'
				});
			}).finally(function () {
				setMobileSearchLoading(root, false);
			});
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			runSearch(input.value);
		});

		input.addEventListener('input', function () {
			window.clearTimeout(timer);
			timer = window.setTimeout(function () {
				runSearch(input.value);
			}, 350);
		});

		input.addEventListener('blur', function () {
			window.setTimeout(function () {
				var value = String(input.value || '').trim();
				var results = root.querySelector('[data-webmz-msearch-results]');
				var hasResults = results && !results.hidden;

				if (characterLength(value) < 3 && !hasResults) {
					hideMobileSearchBox(root);
				}
			}, 180);
		});

		root.querySelectorAll('[data-webmz-msearch-term]').forEach(function (chip) {
			chip.addEventListener('click', function () {
				var term = chip.getAttribute('data-webmz-msearch-term') || chip.textContent;
				input.value = term;
				runSearch(term);
			});
		});

		var closeButton = root.querySelector('[data-webmz-msearch-close]') || ensureMobileSearchCloseButton(root);

		if (closeButton) {
			closeButton.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				hideMobileSearchBox(root);
			});
		}
	}

	function initGuestAccountTriggers(scope) {
		scope.querySelectorAll('[data-webmz-maccount-open-login]').forEach(function (trigger) {
			if (trigger.getAttribute('data-webmz-login-ready') === 'yes') {
				return;
			}

			trigger.setAttribute('data-webmz-login-ready', 'yes');

			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				closeAll();

				if (typeof window.webmzOtpOpen === 'function') {
					window.webmzOtpOpen();
				}
			});
		});
	}

	function initOpenAccountLinks(scope) {
		scope.querySelectorAll('[data-webmz-open-mobile-account]').forEach(function (link) {
			if (link.getAttribute('data-webmz-account-link-ready') === 'yes') {
				return;
			}

			link.setAttribute('data-webmz-account-link-ready', 'yes');

			link.addEventListener('click', function (event) {
				var ownerDoc = link.ownerDocument || document;
				var accountRoot = ownerDoc.querySelector('[data-webmz-mobile-account]');
				var accountTrigger = accountRoot ? accountRoot.querySelector('[data-webmz-offcanvas-trigger]') : null;

				if (!accountTrigger) {
					return;
				}

				event.preventDefault();
				closeAll();
				accountTrigger.click();
			});
		});
	}

	function getHeaderCartRoot(scope) {
		scope = scope || document;
		var carts = scope.querySelectorAll('[data-webmz-offcanvas-root][data-webmz-header-cart]');
		var visible = null;

		carts.forEach(function (cart) {
			var rect = cart.getBoundingClientRect();

			if (rect.width > 0 && rect.height > 0) {
				visible = cart;
			}
		});

		return visible || carts[0] || null;
	}

	function initOpenCartLinks(scope) {
		scope.querySelectorAll('[data-webmz-open-mobile-cart]').forEach(function (link) {
			if (link.getAttribute('data-webmz-cart-link-ready') === 'yes') {
				return;
			}

			link.setAttribute('data-webmz-cart-link-ready', 'yes');

			link.addEventListener('click', function (event) {
				var ownerDoc = link.ownerDocument || document;
				var cartRoot = getHeaderCartRoot(ownerDoc);
				var cartTrigger = cartRoot ? cartRoot.querySelector('[data-webmz-offcanvas-trigger]') : null;

				if (!cartTrigger) {
					return;
				}

				event.preventDefault();
				closeAll();
				cartTrigger.click();
			});
		});
	}

	function initScope(scope) {
		scope = scope || document;
		scope.querySelectorAll('[data-webmz-offcanvas-root]').forEach(initOffcanvas);
	}

	document.addEventListener('click', function (event) {
		if (event.target.closest('[data-webmz-offcanvas-trigger], [data-webmz-offcanvas-panel], .webmz-mmenu-panel__nav .webmz-nav__toggle')) {
			return;
		}

		getOpenPanels().forEach(function (panel) {
			if (isElementorEditMode() && panel.classList.contains(editorPreviewClass)) {
				return;
			}

			if (!panel.contains(event.target)) {
				closePanel(panel);
			}
		});

		if (!getOpenPanels().length) {
			document.documentElement.classList.remove(htmlLockClass);
		}
	});

	document.addEventListener('keydown', function (event) {
		var openPanels = getOpenPanels();

		if (!openPanels.length) {
			return;
		}

		if (event.key === 'Escape') {
			closeAll();
			return;
		}

		if (event.key !== 'Tab') {
			return;
		}

		var panel = openPanels[openPanels.length - 1];
		var focusables = getFocusableElements(panel);

		if (!focusables.length) {
			event.preventDefault();
			focusFirstInPanel(panel);
			return;
		}

		var first = focusables[0];
		var last = focusables[focusables.length - 1];
		var active = panel.ownerDocument.activeElement;

		if (event.shiftKey && active === first) {
			event.preventDefault();
			last.focus();
			return;
		}

		if (!event.shiftKey && active === last) {
			event.preventDefault();
			first.focus();
		}
	});

	document.addEventListener('DOMContentLoaded', function () {
		initScope(document);
	});

	document.addEventListener('webmz:open-mobile-cart', openMobileCart);

	function openMobileCart() {
		var cartRoot = getHeaderCartRoot(document);

		if (!cartRoot) {
			return;
		}

		var trigger = cartRoot.querySelector('[data-webmz-offcanvas-trigger]');
		var panel = resolvePanel(cartRoot, trigger);

		if (panel) {
			openPanel(panel, trigger);
			return;
		}

		if (trigger) {
			trigger.click();
		}
	}

	window.webmzOpenMobileCart = openMobileCart;

	if (typeof window.jQuery !== 'undefined' && window.jQuery(window).on) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
				return;
			}

			mobileWidgetTypes.forEach(function (widget) {
				window.elementorFrontend.hooks.addAction('frontend/element_ready/' + widget + '.default', function ($scope) {
					initScope($scope[0]);
				});
			});
		});
	}

	window.webmzCloseOffcanvas = closeAll;
})();
