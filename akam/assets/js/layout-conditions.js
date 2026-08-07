(function ($) {
    'use strict';

    var config = window.webmzLayoutConditions || {};
    var $type = $('#webmz-layout-type-select');
    var $settings = $('#webmz-layout-settings');
    var $body = $settings.find('.webmz-layout-settings__body');
    var $root = $('#webmz-layout-rules');
    var ruleIndex = $root.find('.webmz-layout-rule').length;
    var searchTimer = null;

    if (!$type.length || !$settings.length || !$root.length) {
        return;
    }

    var refreshLockState = function () {
        var hasType = !!$type.val();

        $settings.toggleClass('is-locked', !hasType);
        $body.prop('disabled', !hasType);
        $type.prop('disabled', false);

        var $notice = $settings.find('.webmz-layout-settings__lock-notice');

        if (!hasType && !$notice.length) {
            $settings.prepend('<div class="webmz-layout-settings__lock-notice">' + (config.i18n.lockNotice || '') + '</div>');
        }

        if (hasType) {
            $notice.remove();
        }
    };

    $type.on('change', refreshLockState);

    var reindexRules = function () {
        $root.find('.webmz-layout-rule').each(function (index) {
            var $rule = $(this);
            $rule.attr('data-index', index);
            $rule.find('[name^="webmz_layout_conditions["]').each(function () {
                var $input = $(this);
                var field = $input.attr('name').replace(/^webmz_layout_conditions\[[^\]]+\]/, '');
                $input.attr('name', 'webmz_layout_conditions[' + index + ']' + field);
            });
        });
        ruleIndex = $root.find('.webmz-layout-rule').length;
    };

    var closeResults = function ($rule) {
        $rule.find('.webmz-layout-rule__results').removeClass('is-open').empty();
    };

    var refreshRuleState = function ($rule) {
        var isGlobal = 'general' === $rule.find('.webmz-layout-rule__name').val();
        var $picker = $rule.find('.webmz-layout-rule__picker');

        $picker.toggleClass('is-hidden', isGlobal);

        if (isGlobal) {
            $rule.find('.webmz-layout-rule__choices').empty();
            $rule.find('.webmz-layout-rule__search').val('');
            closeResults($rule);
        }
    };

    var renderResults = function ($rule, items) {
        var $results = $rule.find('.webmz-layout-rule__results');
        var html = '';

        if (!items.length) {
            html = '<button type="button" class="webmz-layout-rule__result is-empty" disabled>' + (config.i18n.noResult || '') + '</button>';
        } else {
            $.each(items, function (_, item) {
                html += '<button type="button" class="webmz-layout-rule__result" data-id="' + item.id + '" data-label="' + $('<div/>').text(item.label).html() + '">' + item.label + '</button>';
            });
        }

        $results.html(html).addClass('is-open');
    };

    var getSelectedIds = function ($rule) {
        var ids = [];
        $rule.find('.webmz-layout-rule__choice input:checked').each(function () {
            ids.push(parseInt($(this).val(), 10));
        });
        return ids;
    };

    var addChoice = function ($rule, id, label) {
        id = parseInt(id, 10);
        if (!id) {
            return;
        }

        var selected = getSelectedIds($rule);
        if (selected.indexOf(id) !== -1) {
            return;
        }

        var index = $rule.attr('data-index');
        var $choice = $(
            '<label class="webmz-layout-rule__choice">' +
            '<input type="checkbox" name="webmz_layout_conditions[' + index + '][object_ids][]" value="' + id + '" checked>' +
            '<span></span>' +
            '</label>'
        );
        $choice.find('span').text(label);
        $rule.find('.webmz-layout-rule__choices').append($choice);
    };

    var searchObjects = function ($rule, query) {
        var name = $rule.find('.webmz-layout-rule__name').val();

        if ('general' === name) {
            closeResults($rule);
            return;
        }

        $.getJSON(config.ajaxUrl, {
            action: 'webmz_search_layout_condition_objects',
            nonce: config.nonce,
            q: query,
            name: name
        }).done(function (response) {
            if (response && response.success && response.data && response.data.results) {
                renderResults($rule, response.data.results);
            }
        });
    };

    var buildRuleFromTemplate = function (type) {
        var html = $('#tmpl-webmz-layout-condition-rule').html()
            .replace(/__INDEX__/g, String(ruleIndex));

        ruleIndex += 1;
        return $(html);
    };

    $root.on('click', '.webmz-layout-conditions__add', function () {
        var $section = $(this).closest('.webmz-layout-conditions__section');
        var $rule = buildRuleFromTemplate('include');

        $section.find('.webmz-layout-conditions__rows').append($rule);
        reindexRules();
        refreshRuleState($rule);
    });

    $root.on('click', '.webmz-layout-rule__remove', function () {
        var $rows = $(this).closest('.webmz-layout-conditions__rows');

        if ($rows.find('.webmz-layout-rule').length <= 1) {
            var $rule = $(this).closest('.webmz-layout-rule');
            $rule.find('.webmz-layout-rule__name').val('general');
            $rule.find('.webmz-layout-rule__choices').empty();
            $rule.find('.webmz-layout-rule__search').val('');
            refreshRuleState($rule);
            return;
        }

        $(this).closest('.webmz-layout-rule').remove();
        reindexRules();
    });

    $root.on('change', '.webmz-layout-rule__name', function () {
        var $rule = $(this).closest('.webmz-layout-rule');
        $rule.find('.webmz-layout-rule__choices').empty();
        $rule.find('.webmz-layout-rule__search').val('');
        refreshRuleState($rule);

        if ('general' !== $(this).val()) {
            searchObjects($rule, '');
        }
    });

    $root.on('focus input', '.webmz-layout-rule__search', function () {
        var $rule = $(this).closest('.webmz-layout-rule');
        var query = $(this).val();

        if ('general' === $rule.find('.webmz-layout-rule__name').val()) {
            return;
        }

        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(function () {
            searchObjects($rule, query);
        }, 220);
    });

    $root.on('click', '.webmz-layout-rule__result:not(.is-empty)', function () {
        var $button = $(this);
        var $rule = $button.closest('.webmz-layout-rule');

        addChoice($rule, $button.data('id'), $button.data('label'));
        $rule.find('.webmz-layout-rule__search').val('');
        closeResults($rule);
    });

    $root.on('change', '.webmz-layout-rule__choice input', function () {
        if (!$(this).is(':checked')) {
            $(this).closest('.webmz-layout-rule__choice').remove();
        }
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('.webmz-layout-rule__picker').length) {
            $root.find('.webmz-layout-rule__results').removeClass('is-open');
        }
    });

    reindexRules();
    $root.find('.webmz-layout-rule').each(function () {
        refreshRuleState($(this));
    });
    refreshLockState();
}(jQuery));
