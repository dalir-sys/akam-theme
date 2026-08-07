(function () {
    'use strict';

    var config = window.webmzAccountPanel || {};

    if (!config.ajaxEnabled) {
        return;
    }

    function closest(element, selector) {
        while (element && element.nodeType === 1) {
            if (element.matches(selector)) {
                return element;
            }
            element = element.parentElement;
        }
        return null;
    }

    function setActive(panel, endpoint) {
        var items = panel.querySelectorAll('.webmz-account-nav__item');
        items.forEach(function (item) {
            item.classList.remove('is-active');
        });

        var link = panel.querySelector('[data-webmz-account-endpoint="' + endpoint + '"]');
        var item = link ? closest(link, '.webmz-account-nav__item') : null;
        if (item) {
            item.classList.add('is-active');
        }
    }

    function loadUrl(panel, url, endpoint, push) {
        var content = panel.querySelector('[data-webmz-account-content]');
        if (!content || !url) {
            return;
        }

        panel.classList.add('is-loading');

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.text();
            })
            .then(function (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newContent = doc.querySelector('[data-webmz-account-content]') || doc.querySelector('.woocommerce-MyAccount-content');
                var newTitle = doc.querySelector('title');

                if (!newContent) {
                    window.location.href = url;
                    return;
                }

                content.innerHTML = newContent.innerHTML;
                setActive(panel, endpoint);

                if (push) {
                    window.history.pushState({ webmzAccountUrl: url, endpoint: endpoint }, '', url);
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }
                }

                panel.dispatchEvent(new CustomEvent('webmzAccountLoaded', {
                    bubbles: true,
                    detail: {
                        endpoint: endpoint,
                        url: url
                    }
                }));
            })
            .catch(function () {
                content.innerHTML = '<div class="webmz-account-empty">' + (config.errorMessage || 'خطا در بارگذاری محتوا') + '</div>';
            })
            .finally(function () {
                panel.classList.remove('is-loading');
            });
    }

    document.addEventListener('click', function (event) {
        var link = closest(event.target, '.webmz-account-nav__link');
        if (!link) {
            return;
        }

        var panel = closest(link, '[data-webmz-account-panel]');
        if (!panel || link.getAttribute('data-webmz-account-no-ajax') === '1') {
            return;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank') {
            return;
        }

        event.preventDefault();
        loadUrl(panel, link.href, link.getAttribute('data-webmz-account-endpoint') || 'dashboard', true);
    });

    window.addEventListener('popstate', function () {
        var panel = document.querySelector('[data-webmz-account-panel]');
        if (!panel) {
            return;
        }
        loadUrl(panel, window.location.href, panel.getAttribute('data-current-endpoint') || 'dashboard', false);
    });
}());
