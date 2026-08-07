(function () {
	'use strict';

	function activateItem(item) {
		if (!item || item.classList.contains('is-active')) {
			return;
		}

		var root = item.closest('[data-webmz-mega-content]');
		if (!root) {
			return;
		}

		root.querySelectorAll('.webmz-mega-content__item.is-active').forEach(function (activeItem) {
			activeItem.classList.remove('is-active');
		});
		item.classList.add('is-active');
	}

	function initMegaContent(root) {
		if (!root || root.getAttribute('data-webmz-mega-ready') === 'yes') {
			return;
		}

		root.setAttribute('data-webmz-mega-ready', 'yes');
		root.querySelectorAll('.webmz-mega-content__item').forEach(function (item) {
			item.addEventListener('mouseenter', function () {
				activateItem(item);
			});
			item.addEventListener('focusin', function () {
				activateItem(item);
			});
		});
	}

	function isDesktopMegaMenu() {
		return window.matchMedia('(min-width: 768px)').matches;
	}

	function isIconNavMegaMenu(menu) {
		return !!(menu && menu.closest('.webmz-icon-nav[data-webmz-icon-nav]'));
	}

	function getMegaMenuWidth(menu) {
		if (isIconNavMegaMenu(menu)) {
			return Math.max(320, window.innerWidth - 48);
		}

		return Math.min(960, window.innerWidth - 48);
	}

	function getAdminBarOffset() {
		var bar = document.getElementById('wpadminbar');
		return bar ? bar.offsetHeight : 0;
	}

	function syncAdminBarOffsetVar() {
		document.documentElement.style.setProperty('--webmz-admin-bar-offset', getAdminBarOffset() + 'px');
	}

	function getFixedContainingBlock(element) {
		var current = element.parentElement;

		while (current && current !== document.documentElement) {
			var style = window.getComputedStyle(current);

			if (
				(style.transform && style.transform !== 'none') ||
				(style.webkitTransform && style.webkitTransform !== 'none') ||
				(style.filter && style.filter !== 'none') ||
				(style.perspective && style.perspective !== 'none')
			) {
				return current;
			}

			current = current.parentElement;
		}

		return null;
	}

	function toContainingBlockCoords(viewportX, viewportY, containingBlock) {
		if (!containingBlock) {
			return {
				left: viewportX,
				top: viewportY,
			};
		}

		var blockRect = containingBlock.getBoundingClientRect();

		return {
			left: viewportX - blockRect.left,
			top: viewportY - blockRect.top,
		};
	}

	function getMegaMenuAnchorRect(item) {
		var rootList = item.closest('.webmz-nav__list--root');
		if (rootList) {
			return rootList.getBoundingClientRect();
		}

		var nav = item.closest('.webmz-nav');
		return nav ? nav.getBoundingClientRect() : item.getBoundingClientRect();
	}
	function getMegaMenuAlignmentRect(menu) {
		if (isIconNavMegaMenu(menu)) {
			return {
				left: 0,
				width: window.innerWidth,
			};
		}

		var container = menu.closest('.webmz-container, .elementor-container, .e-con > .e-con-inner');
		if (container) {
			return container.getBoundingClientRect();
		}

		return {
			left: 0,
			width: window.innerWidth,
		};
	}

	function positionNavMegaMenu(menu) {
		if (!menu) {
			return;
		}

		var item = menu.closest('.webmz-nav__item.has-mega-menu');
		if (!item) {
			return;
		}

		if (!isDesktopMegaMenu()) {
			menu.style.removeProperty('--webmz-mega-menu-fixed-left');
			menu.style.removeProperty('--webmz-mega-menu-fixed-top');
			menu.style.removeProperty('--webmz-mega-menu-width');
			return;
		}

		var anchorRect = getMegaMenuAnchorRect(item);
		var alignRect = getMegaMenuAlignmentRect(menu);
		var menuWidth = getMegaMenuWidth(menu);
		var centerX = alignRect.left + (alignRect.width / 2);
		var viewportLeft = centerX - (menuWidth / 2);
		var edgePadding = 24;
		var containingBlock = getFixedContainingBlock(menu);
		var coords = toContainingBlockCoords(
			Math.max(edgePadding, Math.min(viewportLeft, window.innerWidth - menuWidth - edgePadding)),
			anchorRect.bottom,
			containingBlock
		);

		menu.style.setProperty('--webmz-mega-menu-fixed-left', coords.left + 'px');
		menu.style.setProperty('--webmz-mega-menu-fixed-top', coords.top + 'px');
		menu.style.setProperty('--webmz-mega-menu-width', menuWidth + 'px');
	}

	function openNavMegaItem(item, menu) {
		if (!item) {
			return;
		}

		window.clearTimeout(item.webmzMegaCloseTimer);

		var siblings = item.parentElement ? item.parentElement.querySelectorAll(':scope > .webmz-nav__item.has-mega-menu.is-mega-menu-open') : [];
		siblings.forEach(function (sibling) {
			if (sibling !== item) {
				window.clearTimeout(sibling.webmzMegaCloseTimer);
				sibling.classList.remove('is-mega-menu-open');
			}
		});

		positionNavMegaMenu(menu);
		item.classList.add('is-mega-menu-open');
		window.requestAnimationFrame(function () {
			positionNavMegaMenu(menu);
		});
	}

	function scheduleCloseNavMegaItem(item) {
		if (!item) {
			return;
		}

		window.clearTimeout(item.webmzMegaCloseTimer);
		item.webmzMegaCloseTimer = window.setTimeout(function () {
			item.classList.remove('is-mega-menu-open');
		}, 500);
	}

	function initNavMegaMenus(root) {
		var menus = root.querySelectorAll ? root.querySelectorAll('.webmz-nav__mega-menu') : [];
		menus.forEach(function (menu) {
			positionNavMegaMenu(menu);

			var item = menu.closest('.webmz-nav__item.has-mega-menu');
			if (!item || item.getAttribute('data-webmz-mega-position-ready') === 'yes') {
				return;
			}

			item.setAttribute('data-webmz-mega-position-ready', 'yes');
			item.addEventListener('mouseenter', function () {
				openNavMegaItem(item, menu);
			});
			item.addEventListener('mouseleave', function () {
				scheduleCloseNavMegaItem(item);
			});
			item.addEventListener('focusin', function () {
				openNavMegaItem(item, menu);
			});
			item.addEventListener('focusout', function () {
				scheduleCloseNavMegaItem(item);
			});
		});
	}

	function initScope(scope) {
		var roots = scope.querySelectorAll ? scope.querySelectorAll('[data-webmz-mega-content]') : [];
		roots.forEach(initMegaContent);
		if (scope.matches && scope.matches('[data-webmz-mega-content]')) {
			initMegaContent(scope);
		}
		initNavMegaMenus(scope);
	}

	function refreshNavMegaMenus() {
		syncAdminBarOffsetVar();
		initNavMegaMenus(document);
		document.querySelectorAll('.webmz-nav__mega-menu').forEach(positionNavMegaMenu);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			syncAdminBarOffsetVar();
			initScope(document);
			window.setTimeout(refreshNavMegaMenus, 0);
		});
	} else {
		syncAdminBarOffsetVar();
		initScope(document);
		window.setTimeout(refreshNavMegaMenus, 0);
	}

	window.addEventListener('load', refreshNavMegaMenus);

	window.addEventListener('resize', refreshNavMegaMenus);
	window.addEventListener('scroll', refreshNavMegaMenus, { passive: true });

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
				return;
			}
			window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-mega-menu-content.default', function ($scope) {
				initScope($scope[0]);
			});
			window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-navigation-menu.default', function ($scope) {
				initScope($scope[0]);
			});
			window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-icon-navigation-menu.default', function ($scope) {
				initScope($scope[0]);
			});
		});
	}
}());
