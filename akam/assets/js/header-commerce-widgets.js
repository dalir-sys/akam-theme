(function ($) {
    'use strict';

    function closeAll(except) {
        if (typeof window.webmzCloseOffcanvas === 'function') {
            window.webmzCloseOffcanvas();
        }

        document.querySelectorAll('.webmz-account.is-open, .webmz-hcart.is-open, [data-webmz-header-cart].is-open').forEach(function (widget) {
            if (except && widget === except) {
                return;
            }

            widget.classList.remove('is-open');
            var trigger = widget.querySelector('[aria-expanded]');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function initToggleWidget(widget) {
        if (!widget || widget.getAttribute('data-webmz-commerce-ready') === 'yes') {
            return;
        }

        widget.setAttribute('data-webmz-commerce-ready', 'yes');
        var trigger = widget.querySelector('[aria-expanded]');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            var opening = !widget.classList.contains('is-open');
            closeAll(widget);
            widget.classList.toggle('is-open', opening);
            trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
        });

        widget.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                widget.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                trigger.focus();
            }
        });
    }

    function initScope(scope) {
        var widgets = scope.querySelectorAll
            ? scope.querySelectorAll('.webmz-account, .webmz-hcart:not([data-webmz-mobile-cart]):not([data-webmz-offcanvas-root])')
            : [];
        widgets.forEach(initToggleWidget);
    }

    function openOffcanvasCart() {
        if (typeof window.webmzOpenMobileCart === 'function') {
            window.webmzOpenMobileCart();
            return;
        }

        document.dispatchEvent(new CustomEvent('webmz:open-mobile-cart'));
    }

    function getCartPanel(cart) {
        var trigger = cart.querySelector('[data-webmz-offcanvas-trigger]');

        if (!trigger) {
            return null;
        }

        var panelId = trigger.getAttribute('aria-controls');

        return panelId ? document.getElementById(panelId) : null;
    }

    function queryCartScopes(cart, selector) {
        var nodes = [];

        cart.querySelectorAll(selector).forEach(function (node) {
            nodes.push(node);
        });

        var panel = getCartPanel(cart);

        if (panel) {
            panel.querySelectorAll(selector).forEach(function (node) {
                if (nodes.indexOf(node) === -1) {
                    nodes.push(node);
                }
            });
        }

        return nodes;
    }

    function setUpdating(active) {
        document.querySelectorAll('[data-webmz-header-cart]').forEach(function (cart) {
            cart.classList.toggle('is-updating', active);

            var panel = getCartPanel(cart);

            if (panel) {
                panel.classList.toggle('is-updating', active);
            }
        });
    }

    function applySnapshot(snapshot, openAfter) {
        document.querySelectorAll('[data-webmz-header-cart]').forEach(function (cart) {
            if (typeof snapshot.content_html === 'string') {
                queryCartScopes(cart, '[data-webmz-hcart-body]').forEach(function (body) {
                    body.innerHTML = snapshot.content_html;
                });
            }

            queryCartScopes(cart, '[data-webmz-hcart-total]').forEach(function (total) {
                total.textContent = snapshot.total_text || '';
            });

            cart.classList.toggle('is-empty', !!snapshot.is_empty);
        });

        document.querySelectorAll('[data-webmz-hcart-count]').forEach(function (count) {
            count.textContent = String(snapshot.count || 0);
        });

        if (openAfter) {
            closeAll();
            openOffcanvasCart();
        }
    }

    function ajaxRequest(action, data, openAfter) {
        if (typeof webmzHeaderCommerce === 'undefined') {
            return $.Deferred().reject().promise();
        }

        setUpdating(true);

        return $.ajax({
            url: webmzHeaderCommerce.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: $.extend({
                action: action,
                nonce: webmzHeaderCommerce.nonce
            }, data || {})
        }).done(function (response) {
            if (response && response.success && response.data) {
                applySnapshot(response.data, openAfter);
            }
        }).always(function () {
            setUpdating(false);
        });
    }

    function openHeaderCart() {
        closeAll();
        openOffcanvasCart();
    }

    function refreshCart(openAfter) {
        return ajaxRequest('webmz_header_cart_snapshot', {}, !!openAfter);
    }

    window.webmzOpenHeaderCart = openHeaderCart;
    window.webmzRefreshHeaderCart = function () {
        return refreshCart(false);
    };

    document.addEventListener('click', function (event) {
        var removeButton = event.target.closest('[data-webmz-remove-item]');

        if (removeButton) {
            event.preventDefault();
            if (removeButton.disabled) {
                return;
            }

            removeButton.disabled = true;
            ajaxRequest(
                'webmz_header_cart_remove_item',
                { cart_item_key: removeButton.getAttribute('data-webmz-remove-item') },
                false
            ).fail(function () {
                removeButton.disabled = false;
            });
            return;
        }

        if (!event.target.closest('.webmz-account, .webmz-hcart, [data-webmz-offcanvas-trigger], [data-webmz-offcanvas-panel], .webmz-mtrigger')) {
            closeAll();
        }
    });

    $(function () {
        initScope(document);
    });

    $(window).on('elementor/frontend/init', function () {
        ['webmz-account.default', 'webmz-mini-cart.default', 'webmz-mobile-mini-cart.default'].forEach(function (widgetName) {
            window.elementorFrontend.hooks.addAction('frontend/element_ready/' + widgetName, function ($scope) {
                initScope($scope[0]);
            });
        });
    });

    /* WooCommerce AJAX add-to-cart; update the isolated cart through our own endpoint. */
    $(document.body).on('adding_to_cart', function () {
        setUpdating(true);
    });

    $(document.body).on('added_to_cart', function (event, fragments, cartHash, $button, options) {
        options = options || {};

        if (options.immediateOpen) {
            refreshCart(false);
            return;
        }

        refreshCart(true);
    });

    /* Classic cart and fragments-compatible events initiated elsewhere in WooCommerce. */
    $(document.body).on('removed_from_cart updated_wc_div wc_fragments_refreshed', function () {
        refreshCart(false);
    });
}(jQuery));
