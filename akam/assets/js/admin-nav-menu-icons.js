(function ($) {
    'use strict';

    $(document).on('click', '.webmz-nav-menu-icon-upload', function (event) {
        event.preventDefault();

        var $button = $(this);
        var target = $button.data('target');
        var preview = $button.data('preview');
        var frame = wp.media({
            title: $button.data('title') || webmzNavMenuIcons.mediaTitle,
            button: { text: $button.data('button') || webmzNavMenuIcons.mediaButton },
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

    $(document).on('click', '.webmz-nav-menu-icon-remove', function (event) {
        event.preventDefault();

        var target = $(this).data('target');
        var preview = $(this).data('preview');

        $(target).val('0');
        $(preview).addClass('is-empty').html('<span>' + webmzNavMenuIcons.emptyLabel + '</span>');
    });

    $(document).on('change', '.edit-menu-item-webmz-mega-enabled', function () {
        $(this).closest('.menu-item-settings').find('.field-webmz-mega-content').toggleClass('is-hidden', !this.checked);
    });
}(jQuery));
