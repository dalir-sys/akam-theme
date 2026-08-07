(function ($) {
    'use strict';

    function widgetConfig() {
        return window.webmzTadrisWidgets || {};
    }

    function openMiniCart() {
        if (typeof window.webmzOpenHeaderCart === 'function') {
            window.webmzOpenHeaderCart();
        }
    }

    function setButtonLoading(button, isLoading) {
        if (!button) {
            return;
        }

        button.classList.toggle('is-loading', !!isLoading);

        if (isLoading) {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            return;
        }

        button.removeAttribute('aria-busy');

        if (button.classList.contains('is-variable') && !button.getAttribute('data-variation-id')) {
            button.disabled = true;
            return;
        }

        button.disabled = false;
    }

    function getSelectedAttributes(form) {
        var attributes = {};
        var rows = form.querySelectorAll('.webmz-spw-variation-options');

        rows.forEach(function (row) {
            var selected = row.querySelector('.webmz-spw-variation-option.is-selected');
            if (selected) {
                attributes['attribute_' + row.getAttribute('data-attribute')] = selected.getAttribute('data-value');
            }
        });

        return attributes;
    }

    function getAttributeRowCount(form) {
        return form.querySelectorAll('.webmz-spw-variation-options').length;
    }

    function findMatchingVariation(form) {
        var dataNode = form.querySelector('.webmz-spw-variations-data');
        if (!dataNode) {
            return null;
        }

        var variations;
        try {
            variations = JSON.parse(dataNode.textContent || '[]');
        } catch (error) {
            return null;
        }

        var selected = getSelectedAttributes(form);
        var keys = Object.keys(selected);
        var required = getAttributeRowCount(form);

        if (!keys.length || keys.length < required) {
            return null;
        }

        for (var i = 0; i < variations.length; i++) {
            var variation = variations[i];
            var attrs = variation.attributes || {};
            var match = true;

            for (var j = 0; j < keys.length; j++) {
                var key = keys[j];
                if (attrs[key] && attrs[key] !== '' && attrs[key] !== selected[key]) {
                    match = false;
                    break;
                }
            }

            if (match) {
                return variation;
            }
        }

        return null;
    }

    function formatPriceAmount(value) {
        var num = Math.round(Number(value) || 0);
        return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function toPersianDigits(value) {
        return String(value).replace(/\d/g, function (digit) {
            return '۰۱۲۳۴۵۶۷۸۹'[Number(digit)];
        });
    }

    function formatDiscountBadge(percent) {
        var value = Math.round(Number(percent) || 0);
        if (!(value > 0)) {
            return '';
        }

        return toPersianDigits(value) + '٪';
    }

    function getDiscountPercent(regularPrice, displayPrice) {
        if (!(regularPrice > displayPrice) || !(regularPrice > 0)) {
            return 0;
        }

        return Math.round(((regularPrice - displayPrice) / regularPrice) * 100);
    }

    function getCurrencyLabel() {
        var cfg = widgetConfig();
        if (cfg.currencySymbol) {
            return cfg.currencySymbol;
        }

        var existing = document.querySelector('.webmz-spw-advanced-atc__currency, .webmz-spw-purchase-box__currency, .tadris-price-currency');
        return existing ? existing.textContent.trim() : 'تومان';
    }

    function parseBasePrice(node) {
        var raw = node.getAttribute('data-webmz-spw-base-price');
        if (!raw) {
            return null;
        }

        try {
            return JSON.parse(raw);
        } catch (error) {
            return null;
        }
    }

    function buildPriceFromVariation(variation) {
        if (!variation) {
            return null;
        }

        var displayPrice = Number(variation.display_price);
        var regularPrice = Number(variation.display_regular_price);
        var currency = getCurrencyLabel();

        if (!variation.is_purchasable || !variation.is_in_stock) {
            return {
                state: 'unavailable',
                amount: '',
                currency: currency
            };
        }

        if (!(displayPrice > 0)) {
            return {
                state: 'free',
                amount: '',
                currency: currency
            };
        }

        return {
            state: 'priced',
            amount: formatPriceAmount(displayPrice),
            regular_amount: regularPrice > displayPrice ? formatPriceAmount(regularPrice) : '',
            discount_percent: getDiscountPercent(regularPrice, displayPrice),
            currency: currency
        };
    }

    function renderAdvancedPriceHtml(price) {
        if (!price || price.state === 'unavailable') {
            return '<span class="webmz-spw-advanced-atc__amount">ناموجود</span>';
        }

        if (price.state === 'free') {
            return '<span class="webmz-spw-advanced-atc__amount">رایگان</span>';
        }

        var html = '';
        var discountBadge = formatDiscountBadge(price.discount_percent || 0);

        if (price.regular_amount || discountBadge) {
            html += '<span class="webmz-spw-advanced-atc__sale-meta">';
            if (price.regular_amount) {
                html += '<span class="webmz-spw-advanced-atc__regular">' + price.regular_amount + '</span>';
            }
            if (discountBadge) {
                html += '<span class="webmz-spw-advanced-atc__discount">' + discountBadge + '</span>';
            }
            html += '</span>';
        }

        html += '<span class="webmz-spw-advanced-atc__current">' +
            '<span class="webmz-spw-advanced-atc__amount">' + price.amount + '</span>' +
            '<span class="webmz-spw-advanced-atc__currency">' + price.currency + '</span>' +
            '</span>';

        return html;
    }

    function renderPurchasePriceHtml(price) {
        if (!price || price.state === 'unavailable') {
            return 'ناموجود';
        }

        if (price.state === 'free') {
            return 'رایگان';
        }

        return '<span class="webmz-spw-purchase-box__amount">' + price.amount + '</span>' +
            '<span class="webmz-spw-purchase-box__currency">' + price.currency + '</span>';
    }

    function renderTadrisPriceHtml(price) {
        if (!price || price.state === 'unavailable') {
            return '<div class="tadris-not-for-sale">ناموجود</div>';
        }

        if (price.state === 'free') {
            return '<div class="tadris-free-price">رایگان</div>';
        }

        var html = '<div class="tadris-price-amount">' + price.amount + '</div><div class="tadris-price-row"><div class="tadris-price-currency">' + price.currency + '</div>';
        if (price.regular_amount) {
            html += '<div class="tadris-price-amount-del">' + price.regular_amount + '</div>';
        }
        html += '</div>';
        return html;
    }

    function updatePriceNode(node, price) {
        if (!node || !price) {
            return;
        }

        if (node.classList.contains('webmz-spw-advanced-atc__price') || node.closest('.webmz-spw-advanced-atc__price')) {
            var advancedTarget = node.classList.contains('webmz-spw-advanced-atc__price') ? node : node.closest('.webmz-spw-advanced-atc__price');
            advancedTarget.innerHTML = renderAdvancedPriceHtml(price);
            return;
        }

        if (node.classList.contains('webmz-spw-purchase-box__main-price') || node.closest('.webmz-spw-purchase-box__total')) {
            node.innerHTML = renderPurchasePriceHtml(price);
            return;
        }

        var tadris = node.querySelector ? node.querySelector('.tadris-price') : null;
        if (tadris || node.classList.contains('tadris-price')) {
            var target = tadris || node;
            target.innerHTML = renderTadrisPriceHtml(price);
            return;
        }

        if (node.hasAttribute('data-webmz-spw-price-display')) {
            if (node.closest('.webmz-spw-advanced-atc')) {
                node.innerHTML = renderAdvancedPriceHtml(price);
            } else {
                node.innerHTML = renderPurchasePriceHtml(price);
            }
        }
    }

    function updateProductPriceDisplays(productId, variation) {
        var price = buildPriceFromVariation(variation);
        var wraps = document.querySelectorAll('.webmz-spw[data-product-id="' + productId + '"]');

        wraps.forEach(function (wrap) {
            wrap.querySelectorAll('[data-webmz-spw-price-display]').forEach(function (node) {
                if (price) {
                    updatePriceNode(node, price);
                    return;
                }

                var base = parseBasePrice(node);
                if (base) {
                    updatePriceNode(node, base);
                }
            });
        });

        document.querySelectorAll('.webmz-spw-price').forEach(function (node) {
            var wrap = node.closest('.webmz-spw');
            var nodeProductId = wrap ? wrap.getAttribute('data-product-id') : null;

            if (nodeProductId && String(nodeProductId) !== String(productId)) {
                return;
            }

            if (price) {
                updatePriceNode(node, price);
            }
        });
    }

    function applyAttributesToForm(form, attributes) {
        if (!form || !attributes) {
            return;
        }

        form.querySelectorAll('.webmz-spw-variation-options').forEach(function (row) {
            var attr = row.getAttribute('data-attribute');
            var value = attributes['attribute_' + attr];

            row.querySelectorAll('.webmz-spw-variation-option').forEach(function (item) {
                var selected = value !== undefined && item.getAttribute('data-value') === value;
                item.classList.toggle('is-selected', selected);
                item.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
        });
    }

    function updateFormState(form) {
        var wrap = form.closest('.webmz-spw');
        var button = wrap ? wrap.querySelector('[data-webmz-spw-add]') : null;
        var variation = findMatchingVariation(form);
        var variationInput = form.querySelector('input[name="variation_id"]');
        var productId = form.getAttribute('data-product-id');

        if (variationInput) {
            variationInput.value = variation ? variation.variation_id : '';
        }

        if (button) {
            if (variation && variation.is_in_stock && variation.is_purchasable) {
                button.disabled = false;
                button.setAttribute('data-variation-id', variation.variation_id);
            } else {
                button.disabled = true;
                button.removeAttribute('data-variation-id');
            }
        }

        if (productId) {
            updateProductPriceDisplays(productId, variation);
        }

        return variation;
    }

    function syncVariationForms(sourceForm) {
        if (!sourceForm) {
            return;
        }

        var productId = sourceForm.getAttribute('data-product-id');
        var selected = getSelectedAttributes(sourceForm);
        var forms;

        if (productId) {
            forms = document.querySelectorAll('.webmz-spw-variation-form[data-product-id="' + productId + '"]');
        } else {
            forms = [sourceForm];
        }

        forms.forEach(function (form) {
            if (form !== sourceForm) {
                applyAttributesToForm(form, selected);
            }
            updateFormState(form);
        });
    }

    function initVariationForms(scope) {
        var options = (scope || document).querySelectorAll('.webmz-spw-variation-option');

        options.forEach(function (option) {
            if (option.getAttribute('data-webmz-spw-variation-ready') === 'yes' || option.disabled) {
                return;
            }

            option.setAttribute('data-webmz-spw-variation-ready', 'yes');
            option.addEventListener('click', function () {
                var group = option.closest('.webmz-spw-variation-options');
                var form = option.closest('.webmz-spw-variation-form');
                if (!group || !form) {
                    return;
                }

                group.querySelectorAll('.webmz-spw-variation-option').forEach(function (item) {
                    item.classList.remove('is-selected');
                    item.setAttribute('aria-pressed', 'false');
                });
                option.classList.add('is-selected');
                option.setAttribute('aria-pressed', 'true');
                syncVariationForms(form);
            });
        });

        var forms = (scope || document).querySelectorAll('.webmz-spw-variation-form');

        forms.forEach(function (form) {
            updateFormState(form);
        });
    }

    function getVariationAttributesForCart(variation, form) {
        var selected = form ? getSelectedAttributes(form) : {};
        var attributes = {};

        if (variation && variation.attributes) {
            Object.keys(variation.attributes).forEach(function (key) {
                var value = variation.attributes[key];
                if (value !== undefined && value !== null && String(value) !== '') {
                    attributes[key] = value;
                } else if (selected[key] !== undefined) {
                    attributes[key] = selected[key];
                }
            });
        }

        Object.keys(selected).forEach(function (key) {
            if (attributes[key] === undefined) {
                attributes[key] = selected[key];
            }
        });

        return attributes;
    }

    function addProductToCart(button) {
        var cfg = widgetConfig();
        if (!cfg.addToCartUrl || button.classList.contains('is-loading')) {
            return;
        }

        var parentId = button.getAttribute('data-product-id');
        var form = button.closest('.webmz-spw') ? button.closest('.webmz-spw').querySelector('.webmz-spw-variation-form') : null;
        var variation = form ? findMatchingVariation(form) : null;
        var variationId = variation && variation.variation_id
            ? String(variation.variation_id)
            : (button.getAttribute('data-variation-id') || '');

        /*
         * WooCommerce WC_AJAX::add_to_cart ignores POST variation_id.
         * It only resolves variations when product_id itself is a variation.
         */
        var payload = {
            product_id: variationId || parentId,
            quantity: button.getAttribute('data-quantity') || 1
        };

        if (variationId) {
            Object.assign(payload, getVariationAttributesForCart(variation, form));
        }

        setButtonLoading(button, true);

        $.ajax({
            url: cfg.addToCartUrl,
            method: 'POST',
            dataType: 'json',
            data: payload
        }).done(function (response) {
            if (response && response.error && response.already_in_cart) {
                if (typeof window.webmzShowAlreadyInCartAlert === 'function') {
                    window.webmzShowAlreadyInCartAlert(response);
                }
                return;
            }

            if (response && response.error && response.product_url) {
                window.location.href = response.product_url;
                return;
            }

            if (response && response.fragments) {
                // Pass false instead of $button so WooCommerce does not append "View cart" link.
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash || '', false, { immediateOpen: true }]);
                button.classList.remove('added');

                var actionWrap = button.closest('.webmz-spw-add-to-cart-wrap, .webmz-spw-purchase-box__action, .webmz-spw-advanced-atc__action');
                if (actionWrap) {
                    actionWrap.querySelectorAll('.added_to_cart').forEach(function (link) {
                        link.remove();
                    });
                }

                openMiniCart();
            }
        }).always(function () {
            setButtonLoading(button, false);
        });
    }

    function resolveScrollTarget(selector) {
        if (selector) {
            var target = document.querySelector(selector);
            if (target) {
                return target;
            }
        }

        return document.querySelector('#reviews, .webmz-wc-reviews-widget');
    }

    function initScrollTargets(scope) {
        (scope || document).querySelectorAll('[data-webmz-spw-scroll]').forEach(function (button) {
            if (button.getAttribute('data-webmz-spw-scroll-ready') === 'yes') {
                return;
            }

            button.setAttribute('data-webmz-spw-scroll-ready', 'yes');
            button.addEventListener('click', function () {
                var selector = button.getAttribute('data-webmz-spw-scroll');
                var target = resolveScrollTarget(selector);

                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    function initReadMore(scope) {
        $(scope || document).find('[data-webmz-spw-read-more]').each(function () {
            var $wrap = $(this);
            var $btn = $wrap.find('[data-webmz-spw-read-more-btn]');

            if (!$btn.length || $btn.attr('data-webmz-spw-read-more-ready') === 'yes') {
                return;
            }

            $btn.attr('data-webmz-spw-read-more-ready', 'yes');

            function updateReadMoreState() {
                if ($btn.is('[hidden]')) {
                    return;
                }

                var $inner = $wrap.find('.webmz-spw-product-content__inner');
                if (!$inner.length) {
                    return;
                }

                var maxHeight = parseInt(window.getComputedStyle($wrap[0]).getPropertyValue('--webmz-spw-content-max'), 10) || 320;
                var contentHeight = $inner[0].scrollHeight;

                if (contentHeight <= maxHeight) {
                    $wrap.removeClass('has-overflow is-collapsed');
                    return;
                }

                $wrap.addClass('has-overflow is-collapsed');
            }

            updateReadMoreState();

            $wrap.find('.webmz-spw-product-content__inner img').on('load', updateReadMoreState);

            $btn.on('click', function () {
                $wrap.removeClass('is-collapsed');
                $btn.attr('hidden', 'hidden');
            });
        });
    }

    function showCurriculumRegisterAlert(message) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({
                text: message,
                icon: 'info',
                confirmButtonText: 'متوجه شدم',
                customClass: {
                    popup: 'webmz-swal-popup',
                    confirmButton: 'webmz-swal-confirm'
                }
            });
        }

        window.alert(message);
        return Promise.resolve();
    }

    function initCurriculumRegister(scope) {
        (scope || document).querySelectorAll('[data-webmz-spw-curriculum-register]').forEach(function (button) {
            if (button.getAttribute('data-webmz-spw-curriculum-register-ready') === 'yes') {
                return;
            }

            button.setAttribute('data-webmz-spw-curriculum-register-ready', 'yes');
            button.addEventListener('click', function (event) {
                event.preventDefault();

                if (button.disabled) {
                    return;
                }

                var curriculum = button.closest('[data-webmz-spw-curriculum]');
                var message = curriculum && curriculum.getAttribute('data-webmz-spw-curriculum-alert')
                    ? curriculum.getAttribute('data-webmz-spw-curriculum-alert')
                    : 'برای مشاهده جلسات قفل باید در دوره ثبت نام نمایید';

                showCurriculumRegisterAlert(message);
            });
        });
    }

    function initCurriculum(scope) {
        (scope || document).querySelectorAll('[data-webmz-spw-curriculum]').forEach(function (curriculum) {
            if (curriculum.getAttribute('data-webmz-spw-curriculum-ready') === 'yes') {
                return;
            }

            curriculum.setAttribute('data-webmz-spw-curriculum-ready', 'yes');

            curriculum.querySelectorAll('.webmz-spw-curriculum__section').forEach(function (section) {
                var panel = section.querySelector('.webmz-spw-curriculum__panel');
                if (panel) {
                    panel.setAttribute('aria-hidden', section.classList.contains('is-open') ? 'false' : 'true');
                }
            });

            curriculum.querySelectorAll('.webmz-spw-curriculum__header').forEach(function (header) {
                header.addEventListener('click', function () {
                    var section = header.closest('.webmz-spw-curriculum__section');
                    var panel = section ? section.querySelector('.webmz-spw-curriculum__panel') : null;

                    if (!section || !panel) {
                        return;
                    }

                    var isOpen = section.classList.contains('is-open');

                    if (isOpen) {
                        section.classList.remove('is-open');
                        panel.setAttribute('aria-hidden', 'true');
                        header.setAttribute('aria-expanded', 'false');
                        return;
                    }

                    section.classList.add('is-open');
                    panel.setAttribute('aria-hidden', 'false');
                    header.setAttribute('aria-expanded', 'true');
                });
            });
        });
    }

    function initFaq(scope) {
        (scope || document).querySelectorAll('[data-webmz-spw-faq]').forEach(function (faq) {
            if (faq.getAttribute('data-webmz-spw-faq-ready') === 'yes') {
                return;
            }

            faq.setAttribute('data-webmz-spw-faq-ready', 'yes');

            faq.querySelectorAll('.webmz-spw-faq__item').forEach(function (item) {
                var answer = item.querySelector('.webmz-spw-faq__answer');
                if (answer) {
                    answer.setAttribute('aria-hidden', item.classList.contains('is-open') ? 'false' : 'true');
                }
            });

            faq.querySelectorAll('.webmz-spw-faq__question').forEach(function (question) {
                question.addEventListener('click', function () {
                    var item = question.closest('.webmz-spw-faq__item');
                    var answer = item ? item.querySelector('.webmz-spw-faq__answer') : null;
                    var isOpen = item && item.classList.contains('is-open');

                    faq.querySelectorAll('.webmz-spw-faq__item').forEach(function (node) {
                        node.classList.remove('is-open');
                        var nodeAnswer = node.querySelector('.webmz-spw-faq__answer');
                        var nodeQuestion = node.querySelector('.webmz-spw-faq__question');
                        if (nodeAnswer) {
                            nodeAnswer.setAttribute('aria-hidden', 'true');
                        }
                        if (nodeQuestion) {
                            nodeQuestion.setAttribute('aria-expanded', 'false');
                        }
                    });

                    if (!isOpen && item && answer) {
                        item.classList.add('is-open');
                        answer.setAttribute('aria-hidden', 'false');
                        question.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        });
    }

    function initAddToCart(scope) {
        (scope || document).querySelectorAll('[data-webmz-spw-add]').forEach(function (button) {
            if (button.getAttribute('data-webmz-spw-add-ready') === 'yes') {
                return;
            }

            button.setAttribute('data-webmz-spw-add-ready', 'yes');
            button.addEventListener('click', function (event) {
                event.preventDefault();
                if (button.disabled) {
                    return;
                }
                addProductToCart(button);
            });
        });
    }

    function scrollToCartArea(cartArea) {
        if (!cartArea) {
            return;
        }

        cartArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function syncStickyCartPrice(bar) {
        var priceSlot = bar.querySelector('[data-webmz-spw-sticky-price]');
        if (!priceSlot) {
            return;
        }

        var priceEl = document.querySelector('.webmz-spw-price .tadris-price');

        if (priceEl) {
            priceSlot.innerHTML = priceEl.outerHTML;
            priceSlot.removeAttribute('aria-hidden');
            return;
        }

        var fallbackEl = document.querySelector('.webmz-spw-advanced-atc__price, .webmz-spw-purchase-box__main-price, [data-webmz-spw-price-display], .webmz-spw-price');
        if (!fallbackEl) {
            priceSlot.innerHTML = '';
            priceSlot.setAttribute('aria-hidden', 'true');
            return;
        }

        priceSlot.innerHTML = fallbackEl.innerHTML;
        priceSlot.removeAttribute('aria-hidden');
    }

    function syncStickyCartButton(stickyBtn, mainBtn) {
        if (!stickyBtn || !mainBtn) {
            return;
        }

        var mainLabel = mainBtn.querySelector('span:last-child');
        var stickyLabel = stickyBtn.querySelector('span');

        if (mainLabel && stickyLabel) {
            stickyLabel.textContent = mainLabel.textContent;
        }

        stickyBtn.classList.toggle('is-variable', mainBtn.classList.contains('is-variable'));
        stickyBtn.classList.toggle('is-disabled', mainBtn.classList.contains('is-disabled'));

        if (mainBtn.classList.contains('is-variable')) {
            var variationId = mainBtn.getAttribute('data-variation-id');

            if (variationId) {
                stickyBtn.setAttribute('data-variation-id', variationId);
                stickyBtn.setAttribute('data-product-id', mainBtn.getAttribute('data-product-id') || '');
                stickyBtn.setAttribute('data-webmz-spw-add', '');
            } else {
                stickyBtn.removeAttribute('data-variation-id');
                stickyBtn.removeAttribute('data-webmz-spw-add');
            }
        }

        stickyBtn.disabled = mainBtn.disabled && !mainBtn.classList.contains('is-loading');
    }

    function canStickyAddDirectly(stickyBtn, mainBtn) {
        if (!stickyBtn || stickyBtn.disabled || stickyBtn.classList.contains('is-disabled')) {
            return false;
        }

        if (stickyBtn.hasAttribute('data-product-id') && !stickyBtn.classList.contains('is-variable')) {
            return true;
        }

        return false;
    }

    function resolveCartArea() {
        return document.querySelector('#cart-area-scroll, [data-webmz-spw-cart-anchor], .webmz-spw-advanced-atc, .webmz-spw-purchase-box, .webmz-spw-add-to-cart-wrap');
    }

    function isStickyCartMobileViewport() {
        return window.matchMedia('(max-width: 767px)').matches;
    }

    function initStickyAddToCart(scope) {
        var bar = document.querySelector('[data-webmz-spw-sticky-cart]');

        if (!bar || bar.getAttribute('data-webmz-spw-sticky-ready') === 'yes') {
            return;
        }

        // Keep the bar in DOM on desktop so resize to mobile can still use it.
        if (!isStickyCartMobileViewport()) {
            return;
        }

        var cartArea = resolveCartArea();

        if (!cartArea) {
            return;
        }

        bar.setAttribute('data-webmz-spw-sticky-ready', 'yes');

        var scrollOnly = bar.getAttribute('data-webmz-spw-sticky-scroll-only') === 'yes';
        var stickyBtn = bar.querySelector('[data-webmz-spw-sticky-add]');
        var mainBtn = cartArea.querySelector('[data-webmz-spw-add]');

        if (scrollOnly) {
            // Variable products: sticky button scrolls to variation picker.
            mainBtn = null;
        }

        syncStickyCartPrice(bar);

        if (mainBtn && stickyBtn) {
            syncStickyCartButton(stickyBtn, mainBtn);

            var buttonObserver = new MutationObserver(function () {
                syncStickyCartButton(stickyBtn, mainBtn);
            });

            buttonObserver.observe(mainBtn, {
                attributes: true,
                attributeFilter: ['disabled', 'data-variation-id', 'data-product-id', 'class']
            });
        }

        var priceObserverTarget = document.querySelector('.webmz-spw-price .tadris-price, .webmz-spw-advanced-atc__price, .webmz-spw-purchase-box__main-price, [data-webmz-spw-price-display], .webmz-spw-price');

        if (priceObserverTarget) {
            var priceObserver = new MutationObserver(function () {
                syncStickyCartPrice(bar);
            });

            priceObserver.observe(priceObserverTarget, {
                childList: true,
                subtree: true,
                characterData: true
            });
        }

        var setStickyVisible = function (isVisible) {
            if (!isStickyCartMobileViewport()) {
                isVisible = false;
            }

            if (isVisible) {
                bar.setAttribute('aria-hidden', 'false');
                bar.classList.add('is-animating-in');
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () {
                        bar.classList.add('is-visible');
                        bar.classList.remove('is-animating-in');
                        document.documentElement.style.setProperty('--webmz-spw-sticky-cart-height', bar.offsetHeight + 'px');
                    });
                });
            } else {
                bar.classList.remove('is-visible', 'is-animating-in');
                bar.setAttribute('aria-hidden', 'true');
                document.documentElement.style.removeProperty('--webmz-spw-sticky-cart-height');
            }

            document.documentElement.classList.toggle('webmz-spw-sticky-cart-active', isVisible);
        };

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                var entry = entries[0];

                if (!entry) {
                    return;
                }

                // Show whenever the cart area is out of view (above or below).
                setStickyVisible(!entry.isIntersecting);
            }, {
                threshold: 0,
                rootMargin: '0px'
            });

            observer.observe(cartArea);
        } else {
            var onScroll = function () {
                var rect = cartArea.getBoundingClientRect();
                var inView = rect.top < window.innerHeight && rect.bottom > 0;
                setStickyVisible(!inView);
            };

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }

        window.addEventListener('resize', function () {
            if (!isStickyCartMobileViewport()) {
                setStickyVisible(false);
                return;
            }

            // Re-init visibility check after switching to mobile.
            if (bar.getAttribute('data-webmz-spw-sticky-ready') === 'yes') {
                var rect = cartArea.getBoundingClientRect();
                var inView = rect.top < window.innerHeight && rect.bottom > 0;
                setStickyVisible(!inView);
            }

            if (bar.classList.contains('is-visible')) {
                document.documentElement.style.setProperty('--webmz-spw-sticky-cart-height', bar.offsetHeight + 'px');
            }
        }, { passive: true });

        if (!stickyBtn) {
            return;
        }

        stickyBtn.addEventListener('click', function (event) {
            event.preventDefault();

            if (scrollOnly) {
                scrollToCartArea(cartArea);
                return;
            }

            if (stickyBtn.disabled || stickyBtn.classList.contains('is-disabled')) {
                return;
            }

            if (mainBtn) {
                syncStickyCartButton(stickyBtn, mainBtn);
            }

            if (canStickyAddDirectly(stickyBtn, mainBtn)) {
                addProductToCart(stickyBtn);
                return;
            }

            scrollToCartArea(cartArea);
        });
    }

    function pauseMediaVideos(root) {
        if (!root) {
            return;
        }

        root.querySelectorAll('video').forEach(function (video) {
            try {
                video.pause();
            } catch (e) {
                // Ignore pause errors.
            }

            if (video.plyr && typeof video.plyr.pause === 'function') {
                video.plyr.pause();
            }
        });
    }

    function activateMediaItem(root, index) {
        if (!root) {
            return;
        }

        var items = root.querySelectorAll('[data-webmz-spw-media-item]');
        var thumbs = root.querySelectorAll('[data-webmz-spw-media-thumb]');
        var currentIndex = typeof root._webmzSpwActiveIndex === 'number'
            ? root._webmzSpwActiveIndex
            : Array.prototype.findIndex.call(items, function (item) {
                return item.classList.contains('is-active');
            });

        if (index < 0 || index >= items.length || index === currentIndex) {
            return;
        }

        var current = items[currentIndex];
        var next = items[index];
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        thumbs.forEach(function (thumb) {
            var thumbIndex = parseInt(thumb.getAttribute('data-index'), 10);
            var isActive = thumbIndex === index;
            thumb.classList.toggle('is-active', isActive);
            thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
        });

        pauseMediaVideos(root);
        root._webmzSpwActiveIndex = index;

        if (reduceMotion || !current || !next) {
            items.forEach(function (item, itemIndex) {
                var isActive = itemIndex === index;
                item.classList.toggle('is-active', isActive);
                item.classList.remove('is-leaving');
                item.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });

            if (next && typeof window.webmzTadrisInitScope === 'function') {
                window.webmzTadrisInitScope(next);
            }
            return;
        }

        if (root._webmzSpwLeaveTimer) {
            window.clearTimeout(root._webmzSpwLeaveTimer);
            root._webmzSpwLeaveTimer = null;
        }

        items.forEach(function (item) {
            item.classList.remove('is-leaving');
            if (item !== next) {
                item.classList.remove('is-active');
                item.setAttribute('aria-hidden', 'true');
            }
        });

        current.classList.add('is-leaving');
        next.classList.add('is-active');
        next.setAttribute('aria-hidden', 'false');

        // Force reflow so the enter transition always runs.
        void next.offsetWidth;

        root._webmzSpwLeaveTimer = window.setTimeout(function () {
            current.classList.remove('is-leaving');
            root._webmzSpwLeaveTimer = null;
        }, 400);

        if (typeof window.webmzTadrisInitScope === 'function') {
            window.webmzTadrisInitScope(next);
        }
    }

    function initProductMediaGalleries(scope) {
        var roots = (scope || document).querySelectorAll('[data-webmz-spw-media]');

        roots.forEach(function (root) {
            if (root.getAttribute('data-webmz-spw-media-ready') === 'yes') {
                return;
            }

            var thumbsEl = root.querySelector('[data-webmz-spw-media-thumbs]');
            var thumbs = root.querySelectorAll('[data-webmz-spw-media-thumb]');
            var activeItem = root.querySelector('.webmz-spw-media__item.is-active');
            var items = root.querySelectorAll('[data-webmz-spw-media-item]');

            root._webmzSpwActiveIndex = activeItem
                ? Array.prototype.indexOf.call(items, activeItem)
                : 0;

            if (!thumbs.length) {
                root.setAttribute('data-webmz-spw-media-ready', 'yes');
                return;
            }

            root.setAttribute('data-webmz-spw-media-ready', 'yes');

            if (thumbsEl && typeof window.Swiper !== 'undefined' && !thumbsEl.swiper) {
                root._webmzSpwThumbsSwiper = new window.Swiper(thumbsEl, {
                    slidesPerView: 'auto',
                    spaceBetween: 10,
                    watchOverflow: true,
                    freeMode: true,
                    resistanceRatio: 0.65
                });
            }

            if (!root._webmzSpwThumbClickBound) {
                root._webmzSpwThumbClickBound = true;
                root.addEventListener('click', function (event) {
                    var thumb = event.target.closest('[data-webmz-spw-media-thumb]');
                    if (!thumb || !root.contains(thumb)) {
                        return;
                    }

                    event.preventDefault();
                    var index = parseInt(thumb.getAttribute('data-index'), 10);
                    if (isNaN(index)) {
                        return;
                    }

                    activateMediaItem(root, index);

                    if (root._webmzSpwThumbsSwiper && typeof root._webmzSpwThumbsSwiper.slideTo === 'function') {
                        root._webmzSpwThumbsSwiper.slideTo(index);
                    }
                });
            }
        });
    }

    function initScope(scope) {
        initProductMediaGalleries(scope);
        initVariationForms(scope);
        initScrollTargets(scope);
        initReadMore(scope);
        initFaq(scope);
        initCurriculum(scope);
        initCurriculumRegister(scope);
        initAddToCart(scope);
        // Always resolve sticky cart from document (bar lives in footer).
        initStickyAddToCart(document);
    }

    document.addEventListener('click', function (event) {
        var add = event.target.closest('[data-webmz-spw-add]');
        if (add && add.getAttribute('data-webmz-spw-add-ready') !== 'yes') {
            initAddToCart(add.closest('.webmz-spw') || document);
        }
    });

    $(function () {
        initScope(document);

        // Retry after layout widgets paint (Elementor / late ATC markup).
        window.setTimeout(function () {
            initStickyAddToCart(document);
        }, 300);
    });

    $(window).on('elementor/frontend/init', function () {
        [
            'webmz-spw-product-media.default',
            'webmz-spw-participants.default',
            'webmz-spw-rating.default',
            'webmz-spw-product-title.default',
            'webmz-spw-short-description.default',
            'webmz-spw-product-price.default',
            'webmz-spw-add-to-cart.default',
            'webmz-spw-advanced-add-to-cart.default',
            'webmz-spw-course-features.default',
            'webmz-spw-purchase-box.default',
            'webmz-spw-instructor-box.default',
            'webmz-spw-product-content.default',
            'webmz-spw-faq.default',
            'webmz-spw-section-heading.default',
            'webmz-spw-course-curriculum.default'
        ].forEach(function (name) {
            elementorFrontend.hooks.addAction('frontend/element_ready/' + name, function ($scope) {
                initScope($scope[0]);
            });
        });
    });
}(jQuery));
