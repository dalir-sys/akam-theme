(function () {
    'use strict';

    function closeItem(item) {
        if (!item) {
            return;
        }
        item.classList.remove('is-submenu-open');
        var toggle = item.querySelector(':scope > .webmz-nav__toggle');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
        item.querySelectorAll('.webmz-nav__item.is-submenu-open').forEach(function (child) {
            closeItem(child);
        });
    }

    function closeMenus(exceptRoot) {
        document.querySelectorAll('.webmz-nav[data-webmz-navigation]').forEach(function (root) {
            if (root !== exceptRoot) {
                root.querySelectorAll('.webmz-nav__item.is-submenu-open').forEach(closeItem);
            }
        });
    }

    function initNavigation(root) {
        if (!root || root.getAttribute('data-webmz-nav-ready') === 'yes') {
            return;
        }

        root.setAttribute('data-webmz-nav-ready', 'yes');
        var toggles = root.querySelectorAll('.webmz-nav__toggle');

        toggles.forEach(function (toggle) {
            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var item = toggle.closest('.webmz-nav__item');
                var willOpen = !item.classList.contains('is-submenu-open');
                var siblings = item.parentElement ? item.parentElement.children : [];

                Array.prototype.forEach.call(siblings, function (sibling) {
                    if (sibling !== item && sibling.classList.contains('webmz-nav__item')) {
                        closeItem(sibling);
                    }
                });

                item.classList.toggle('is-submenu-open', willOpen);
                toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        });

        root.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                root.querySelectorAll('.webmz-nav__item.is-submenu-open').forEach(closeItem);
            }
        });
    }

    function initScope(scope) {
        var roots = scope.querySelectorAll ? scope.querySelectorAll('.webmz-nav[data-webmz-navigation]') : [];
        roots.forEach(initNavigation);
        if (scope.matches && scope.matches('.webmz-nav[data-webmz-navigation]')) {
            initNavigation(scope);
        }
    }

    document.addEventListener('click', function (event) {
        var current = event.target.closest('.webmz-nav[data-webmz-navigation]');
        closeMenus(current);
        if (!current) {
            closeMenus(null);
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initScope(document);
        });
    } else {
        initScope(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
                return;
            }
            window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-navigation-menu.default', function ($scope) {
                initScope($scope[0]);
            });
            window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-icon-navigation-menu.default', function ($scope) {
                initScope($scope[0]);
            });
        });
    }
}());
