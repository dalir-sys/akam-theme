(function ($) {
	'use strict';

	function renderListRow(template, name, index) {
		return template
			.replace(/\{\{name\}\}/g, name)
			.replace(/\{\{index\}\}/g, String(index));
	}

	function nextIndex(wrap) {
		return wrap.find('.webmz-teacher-list-row').length;
	}

	$(document).on('click', '[data-webmz-teacher-add]', function () {
		var button = $(this);
		var wrap = $('#' + button.data('webmz-teacher-add'));
		var name = button.data('webmz-teacher-name');
		var template = $('#tmpl-webmz-teacher-list-row').html();

		if (!wrap.length || !template || !name) {
			return;
		}

		wrap.append(renderListRow(template, name, nextIndex(wrap)));
	});

	$(document).on('click', '.webmz-teacher-remove-row', function () {
		var row = $(this).closest('.webmz-teacher-list-row');
		var wrap = row.parent();

		if (wrap.find('.webmz-teacher-list-row').length <= 1) {
			row.find('input').val('');
			return;
		}

		row.remove();
	});

	$(document).on('click', '[data-webmz-teacher-media-select]', function (event) {
		event.preventDefault();

		if (!wp || !wp.media) {
			return;
		}

		var field = $(this).data('webmz-teacher-media-select');
		var $input = $('#webmz_teacher_' + field + (field === 'expertise' ? '_photo' : ''));
		var $preview = $('[data-webmz-teacher-media-preview="' + field + '"]');
		var $remove = $('[data-webmz-teacher-media-remove="' + field + '"]');

		var frame = wp.media({
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

	$(document).on('click', '[data-webmz-teacher-media-remove]', function (event) {
		event.preventDefault();

		var field = $(this).data('webmz-teacher-media-remove');
		var $input = $('#webmz_teacher_' + field + (field === 'expertise' ? '_photo' : ''));
		var $preview = $('[data-webmz-teacher-media-preview="' + field + '"]');

		$input.val('');
		$preview.addClass('is-empty').html('<span class="description">تصویری انتخاب نشده</span>');
		$(this).prop('hidden', true);
	});
})(jQuery);
