(function ($) {
	'use strict';

	$(document).on('click', '[data-webmz-pll-media-select]', function (event) {
		event.preventDefault();

		if (!window.wp || !window.wp.media) {
			return;
		}

		var field = $(this).data('webmz-pll-media-select');
		var $input = $('#webmz_product_loop_thumb');
		var $preview = $('[data-webmz-pll-media-preview="' + field + '"]');
		var $remove = $('[data-webmz-pll-media-remove="' + field + '"]');

		var frame = window.wp.media({
			title: 'انتخاب تصویر',
			button: { text: 'استفاده' },
			library: { type: 'image' },
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

			$input.val(attachment.id || '');
			$preview.removeClass('is-empty').html('<img src="' + url + '" alt="">');
			$remove.prop('hidden', false);
		});

		frame.open();
	});

	$(document).on('click', '[data-webmz-pll-media-remove]', function (event) {
		event.preventDefault();

		var field = $(this).data('webmz-pll-media-remove');
		var $input = $('#webmz_product_loop_thumb');
		var $preview = $('[data-webmz-pll-media-preview="' + field + '"]');

		$input.val('');
		$preview.addClass('is-empty').html('<span class="description">تصویری انتخاب نشده</span>');
		$(this).prop('hidden', true);
	});
})(jQuery);
