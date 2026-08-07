(function ($) {
    'use strict';

    if (typeof wc_cart_params === 'undefined') {
        return;
    }

    var updateTimer = null;
    var DEBOUNCE_MS = 500;

    function syncQtySnapshots() {
        $('.woocommerce-cart-form .cart_item input.qty').each(function () {
            $(this).attr('data-webmz-prev-qty', $(this).val());
        });
    }

    function requestCartUpdate($input) {
        var $form = $input.closest('form.woocommerce-cart-form');

        if (!$form.length || $form.hasClass('processing')) {
            return;
        }

        var currentValue = String($input.val());
        var previousValue = String($input.attr('data-webmz-prev-qty') || '');

        if (previousValue === currentValue) {
            return;
        }

        $input.attr('data-webmz-prev-qty', currentValue);

        var $updateBtn = $form.find(':input[name="update_cart"]');

        $form.find(':input[type=submit]').removeAttr('clicked');
        $updateBtn.prop('disabled', false).attr('clicked', 'true');
        $form.trigger('submit');
    }

    function scheduleCartUpdate($input) {
        clearTimeout(updateTimer);
        updateTimer = setTimeout(function () {
            requestCartUpdate($input);
        }, DEBOUNCE_MS);
    }

    $(function () {
        syncQtySnapshots();
    });

    $(document.body).on('change', '.woocommerce-cart-form .cart_item input.qty', function () {
        clearTimeout(updateTimer);
        requestCartUpdate($(this));
    });

    $(document.body).on('input', '.woocommerce-cart-form .cart_item input.qty', function () {
        scheduleCartUpdate($(this));
    });

    $(document.body).on('updated_wc_div', function () {
        syncQtySnapshots();
    });
}(jQuery));
