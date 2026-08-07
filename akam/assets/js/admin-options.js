(function ($) {
    'use strict';
    var $form = $('#webmz-options-form');
    var $status = $('.webmz-status');

    var normalizeHexColor = function (value) {
        value = String(value || '').trim();

        if (value.charAt(0) !== '#') {
            value = '#' + value;
        }

        if (/^#([A-Fa-f0-9]{6})$/.test(value)) {
            return value.toLowerCase();
        }

        return '';
    };

    var initColorFields = function ($scope) {
        var $root = $scope && $scope.length ? $scope : $(document);

        $root.find('.webmz-color-field').each(function () {
            var $field = $(this);

            if ($field.data('webmzColorReady')) {
                return;
            }

            $field.data('webmzColorReady', true);

            var $native = $field.find('.webmz-color-field__native');
            var $hex = $field.find('.webmz-color-field__hex');

            $native.on('input change', function () {
                $hex.val($native.val());
            });

            $hex.on('input change blur', function () {
                var normalized = normalizeHexColor($hex.val());

                if (normalized) {
                    $hex.val(normalized);
                    $native.val(normalized);
                }
            });
        });
    };

    initColorFields();

    var initLucideIcons = function () {
        if (!window.lucide || typeof window.lucide.createIcons !== 'function') {
            return;
        }

        window.lucide.createIcons({
            attrs: {
                class: 'webmz-tab__svg',
                'stroke-width': '1.75'
            },
            nameAttr: 'data-lucide'
        });

        $('.webmz-savebar__submit svg').attr('stroke', '#ffffff');
    };

    var updateAccountEndpointOrder = function () {
        $('#webmz-account-endpoint-list .webmz-account-endpoint-row').each(function (index) {
            $(this).find('.webmz-account-endpoint-order-input').val(index + 1);
        });
    };

    var initAccountEndpointSortable = function () {
        var $endpointList = $('#webmz-account-endpoint-list');

        if (!$endpointList.length || !$.fn.sortable) {
            return;
        }

        if ($endpointList.hasClass('ui-sortable')) {
            $endpointList.sortable('destroy');
        }

        $endpointList.sortable({
            items: '> .webmz-account-endpoint-row',
            handle: '.webmz-account-endpoint-drag',
            axis: 'y',
            tolerance: 'pointer',
            cursor: 'grabbing',
            scroll: true,
            forcePlaceholderSize: true,
            placeholder: 'webmz-account-endpoint-row webmz-account-endpoint-row--placeholder',
            start: function (event, ui) {
                ui.placeholder.height(ui.item.outerHeight());
                ui.helper.width(ui.item.outerWidth());
            },
            update: updateAccountEndpointOrder
        });

        updateAccountEndpointOrder();
    };

    var updateFloatingContactOrder = function () {
        $('#webmz-floating-contact-items .webmz-floating-contact-row').each(function (index) {
            $(this).find('.webmz-floating-contact-order-input').val(index + 1);
        });
    };

    var initFloatingContactSortable = function () {
        var $holder = $('#webmz-floating-contact-items');

        if (!$holder.length || !$.fn.sortable) {
            return;
        }

        if ($holder.hasClass('ui-sortable')) {
            $holder.sortable('destroy');
        }

        $holder.sortable({
            items: '> .webmz-floating-contact-row',
            handle: '.webmz-floating-contact-drag',
            axis: 'y',
            tolerance: 'pointer',
            cursor: 'grabbing',
            scroll: true,
            forcePlaceholderSize: true,
            placeholder: 'webmz-floating-contact-row webmz-floating-contact-row--placeholder',
            start: function (event, ui) {
                ui.placeholder.height(ui.item.outerHeight());
                ui.helper.width(ui.item.outerWidth());
            },
            update: updateFloatingContactOrder
        });

        updateFloatingContactOrder();
    };

    var getTabFromUrl = function () {
        var params = new URLSearchParams(window.location.search);
        return params.get('tab') || (webmzAdmin.defaultTab || 'appearance');
    };

    var setTabInUrl = function (panel, replace) {
        var url = new URL(window.location.href);
        url.searchParams.set('tab', panel);
        window.history[replace ? 'replaceState' : 'pushState']({ tab: panel }, '', url.toString());
    };

    var activateTab = function (panel, updateUrl) {
        var $tab = $('.webmz-tab[data-panel="' + panel + '"]');

        if (!$tab.length) {
            panel = webmzAdmin.defaultTab || 'appearance';
            $tab = $('.webmz-tab[data-panel="' + panel + '"]');
        }

        $('.webmz-tab').removeClass('is-active');
        $tab.addClass('is-active');
        $('.webmz-panel').removeClass('is-active');
        $('.webmz-panel[data-panel="' + panel + '"]').addClass('is-active');

        if (updateUrl !== false) {
            setTabInUrl(panel, updateUrl === 'replace');
        }

        if (panel === 'floating-contact') {
            window.setTimeout(initFloatingContactSortable, 0);
        }

        if (panel === 'account') {
            window.setTimeout(initAccountEndpointSortable, 0);
        }
    };

    $('.webmz-tab').on('click', function () {
        activateTab($(this).data('panel'));
    });

    window.addEventListener('popstate', function () {
        activateTab(getTabFromUrl(), false);
    });

    activateTab(getTabFromUrl(), 'replace');
    initLucideIcons();

    var updateLayoutFieldState = function ($field) {
        var hasTemplate = parseInt($field.find('select').val(), 10) > 0;
        $field.toggleClass('is-layout-assigned', hasTemplate);
    };

    $('.webmz-layout-field').each(function () {
        updateLayoutFieldState($(this));
    });

    $(document).on('change', '.webmz-layout-field select', function () {
        updateLayoutFieldState($(this).closest('.webmz-layout-field'));
    });

    $(document).on('click', '.webmz-media-upload', function (event) {
        event.preventDefault();

        var $button = $(this);
        var target = $button.data('target');
        var preview = $button.data('preview');
        var frame = wp.media({
            title: $button.data('title') || webmzAdmin.mediaTitle,
            button: { text: $button.data('button') || webmzAdmin.mediaButton },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
            $(target).val(attachment.id);
            $(preview).removeClass('is-empty').html('<img src="' + url + '" alt="">');
        });

        frame.open();
    });

    $(document).on('click', '.webmz-media-remove', function (event) {
        event.preventDefault();

        var target = $(this).data('target');
        var preview = $(this).data('preview');
        $(target).val('0');
        $(preview).addClass('is-empty').html('<span>بدون آیکون</span>');
    });

    $(document).on('click', '.webmz-account-endpoint-up, .webmz-account-endpoint-down', function (event) {
        event.preventDefault();

        var $row = $(this).closest('.webmz-account-endpoint-row');
        if ($(this).hasClass('webmz-account-endpoint-up')) {
            $row.prev('.webmz-account-endpoint-row').before($row);
        } else {
            $row.next('.webmz-account-endpoint-row').after($row);
        }

        updateAccountEndpointOrder();
    });

    $(document).on('click', '.webmz-floating-contact-up, .webmz-floating-contact-down', function (event) {
        event.preventDefault();

        var $row = $(this).closest('.webmz-floating-contact-row');
        if ($(this).hasClass('webmz-floating-contact-up')) {
            $row.prev('.webmz-floating-contact-row').before($row);
        } else {
            $row.next('.webmz-floating-contact-row').after($row);
        }

        updateFloatingContactOrder();
    });

    $('#webmz-add-floating-contact-item').on('click', function (event) {
        event.preventDefault();

        var $holder = $('#webmz-floating-contact-items');
        var template = $('#webmz-floating-contact-template').html();
        var index = Date.now().toString();

        if (!template) {
            return;
        }

        var $row = $(template.replace(/__INDEX__/g, index));
        $holder.append($row);
        initColorFields($row);
        updateFloatingContactOrder();
    });

    $(document).on('click', '.webmz-remove-floating-contact-item', function (event) {
        event.preventDefault();
        $(this).closest('.webmz-floating-contact-row').remove();
        updateFloatingContactOrder();
    });

    $('#webmz-add-custom-endpoint').on('click', function (event) {
        event.preventDefault();

        var $holder = $('#webmz-custom-endpoints');
        var template = $('#webmz-custom-endpoint-template').html();
        var index = Date.now().toString();

        if (!template) {
            return;
        }

        $holder.append(template.replace(/__INDEX__/g, index));
    });

    $(document).on('click', '.webmz-remove-custom-endpoint', function (event) {
        event.preventDefault();
        $(this).closest('.webmz-custom-endpoint-row').remove();
    });

    var parseAjaxResponse = function (response) {
        if (typeof response === 'string') {
            try {
                return JSON.parse(response);
            } catch (error) {
                return null;
            }
        }

        return response;
    };

    var applyChildThemeStatus = function ($button, $childStatus, data) {
        var status = (data && data.status) || {};
        var message = (data && data.message) || webmzAdmin.saved;

        $status.removeClass('is-error').addClass('is-success').text(message);
        $childStatus
            .removeClass('webmz-child-theme-status--not-installed webmz-child-theme-status--installed webmz-child-theme-status--active')
            .addClass('webmz-child-theme-status--' + (status.status || 'active'))
            .text(status.label || message);

        if (status.button_text) {
            $button.text(status.button_text);
        }

        $button.prop('disabled', !!status.is_active);
    };

    $('#webmz-install-child-theme').on('click', function (event) {
        event.preventDefault();

        var $button = $(this);
        var $childStatus = $('#webmz-child-theme-status');

        $button.prop('disabled', true);
        $status.removeClass('is-success is-error').text(webmzAdmin.installingChild);
        $childStatus.removeClass('webmz-child-theme-status--not-installed webmz-child-theme-status--installed webmz-child-theme-status--active').text(webmzAdmin.installingChild);

        $.ajax({
            url: webmzAdmin.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'webmz_install_child_theme',
                nonce: webmzAdmin.childNonce
            }
        })
            .done(function (response) {
                response = parseAjaxResponse(response);

                if (response && response.success) {
                    applyChildThemeStatus($button, $childStatus, response.data || {});
                    return;
                }

                var message = (response && response.data && response.data.message) || webmzAdmin.childInstallError;
                $status.addClass('is-error').text(message);
                $childStatus.addClass('webmz-child-theme-status--not-installed').text(message);
                $button.prop('disabled', false);
            })
            .fail(function (xhr) {
                var response = xhr.responseJSON || parseAjaxResponse(xhr.responseText);

                if (response && response.success) {
                    applyChildThemeStatus($button, $childStatus, response.data || {});
                    return;
                }

                var message = response && response.data && response.data.message ? response.data.message : webmzAdmin.childInstallError;
                $status.addClass('is-error').text(message);
                $childStatus.addClass('webmz-child-theme-status--not-installed').text(message);
                $button.prop('disabled', false);
            });
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        var data = $form.serializeArray();
        data.push({ name: 'action', value: 'webmz_save_options' });
        data.push({ name: 'nonce', value: webmzAdmin.nonce });
        $status.removeClass('is-success is-error').text(webmzAdmin.saving);
        $.post(webmzAdmin.ajaxUrl, data)
            .done(function (response) {
                if (response && response.success) {
                    $status.addClass('is-success').text(response.data.message || webmzAdmin.saved);
                } else {
                    $status.addClass('is-error').text(webmzAdmin.error);
                }
                initLucideIcons();
            })
            .fail(function () {
                $status.addClass('is-error').text(webmzAdmin.error);
                initLucideIcons();
            });
    });
}(jQuery));
