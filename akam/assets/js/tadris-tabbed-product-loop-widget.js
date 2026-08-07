(function () {
    'use strict';

    var globalConfig = window.webmzTabbedProductLoop || {};

    function parseConfig(root) {
        var raw = root.getAttribute('data-webmz-tabbed-product-loop') || '{}';

        try {
            return JSON.parse(raw);
        } catch (error) {
            return {};
        }
    }

    function setLoading(panel, isLoading) {
        if (!panel) {
            return;
        }

        panel.classList.toggle('is-loading', !!isLoading);

        var loader = panel.querySelector('.webmz-tpl__loading');

        if (loader) {
            if (isLoading) {
                loader.removeAttribute('hidden');
                loader.setAttribute('aria-hidden', 'false');
            } else {
                loader.setAttribute('hidden', 'hidden');
                loader.setAttribute('aria-hidden', 'true');
            }
        }
    }

    function initSliders(scope) {
        if (typeof window.webmzTadrisInitScope === 'function') {
            window.webmzTadrisInitScope(scope);
        }
    }

    function loadTabPanel(root, panel, tabIndex, config) {
        var inner = panel.querySelector('.webmz-tpl__panel-inner');

        if (!inner || inner.dataset.webmzTplLoaded === 'yes') {
            return Promise.resolve();
        }

        if (!globalConfig.ajaxUrl || !globalConfig.nonce) {
            inner.innerHTML = '<div class="webmz-tpl__empty">' + (globalConfig.errorText || 'خطایی رخ داد.') + '</div>';
            inner.dataset.webmzTplLoaded = 'yes';
            return Promise.resolve();
        }

        setLoading(panel, true);

        var body = new FormData();
        body.append('action', 'webmz_tabbed_product_loop_load');
        body.append('nonce', globalConfig.nonce);
        body.append('tab_index', String(tabIndex));
        body.append('config', JSON.stringify(config));

        return window.fetch(globalConfig.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        }).then(function (response) {
            return response.json();
        }).then(function (response) {
            if (response && response.success && response.data && response.data.html) {
                inner.innerHTML = response.data.html;
                inner.dataset.webmzTplLoaded = 'yes';
                initSliders(inner);
                return;
            }

            inner.innerHTML = '<div class="webmz-tpl__empty">' + ((response && response.data && response.data.message) || globalConfig.errorText || 'خطایی رخ داد.') + '</div>';
            inner.dataset.webmzTplLoaded = 'yes';
        }).catch(function () {
            inner.innerHTML = '<div class="webmz-tpl__empty">' + (globalConfig.errorText || 'خطایی رخ داد.') + '</div>';
            inner.dataset.webmzTplLoaded = 'yes';
        }).finally(function () {
            setLoading(panel, false);
        });
    }

    function activateTab(root, tabIndex) {
        var tabs = root.querySelectorAll('[data-webmz-tpl-tab]');
        var panels = root.querySelectorAll('[data-webmz-tpl-panel]');
        var config = parseConfig(root);
        var targetPanel = null;

        tabs.forEach(function (tab) {
            var active = tab.getAttribute('data-webmz-tpl-tab') === String(tabIndex);
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        panels.forEach(function (panel) {
            var active = panel.getAttribute('data-webmz-tpl-panel') === String(tabIndex);
            panel.classList.toggle('is-active', active);

            if (active) {
                panel.removeAttribute('hidden');
                targetPanel = panel;
            } else {
                panel.setAttribute('hidden', 'hidden');
            }
        });

        if (targetPanel) {
            loadTabPanel(root, targetPanel, tabIndex, config);
        }
    }

    function initRoot(root) {
        if (!root || root.dataset.webmzTplReady === 'yes') {
            return;
        }

        root.dataset.webmzTplReady = 'yes';

        var firstPanel = root.querySelector('.webmz-tpl__panel.is-active .webmz-tpl__panel-inner');
        if (firstPanel) {
            firstPanel.dataset.webmzTplLoaded = 'yes';
            initSliders(firstPanel);
        }

        root.addEventListener('click', function (event) {
            var tab = event.target.closest('[data-webmz-tpl-tab]');

            if (!tab || !root.contains(tab)) {
                return;
            }

            event.preventDefault();
            activateTab(root, tab.getAttribute('data-webmz-tpl-tab'));
        });
    }

    function initScope(scope) {
        var roots = (scope || document).querySelectorAll('[data-webmz-tabbed-product-loop]');
        roots.forEach(initRoot);
    }

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

            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/webmz-tadris-tabbed-product-loop.default',
                function ($scope) {
                    initScope($scope[0]);
                }
            );
        });
    }
})();
