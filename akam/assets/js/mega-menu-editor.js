(function () {
	'use strict';

	function getMainItemOptions() {
		var modelOptions = getMainItemOptionsFromModel();
		if (modelOptions.length) {
			return modelOptions;
		}

		var titles = Array.prototype.slice.call(document.querySelectorAll('.elementor-control-main_items .elementor-repeater-row-item-title'));
		var options = [];

		titles.forEach(function (title, index) {
			var label = title.textContent ? title.textContent.trim() : '';
			options.push({
				value: 'item-' + (index + 1),
				label: label || ('منوی اصلی ' + (index + 1))
			});
		});

		return options;
	}

	function getMainItemOptionsFromModel() {
		if (!window.elementor || !window.elementor.channels || !window.elementor.channels.editor) {
			return [];
		}

		var view = window.elementor.channels.editor.request('panel:get:editedElementView');
		var settings = view && view.model && view.model.get ? view.model.get('settings') : null;
		var mainItems = settings && settings.get ? settings.get('main_items') : null;

		if (!mainItems) {
			return [];
		}

		if (mainItems.toJSON) {
			mainItems = mainItems.toJSON();
		}

		if (!Array.isArray(mainItems)) {
			return [];
		}

		return mainItems.map(function (item, index) {
			return {
				value: 'item-' + (index + 1),
				label: item && item.title ? item.title : ('منوی اصلی ' + (index + 1))
			};
		});
	}

	function updateSelect(select, options) {
		if (!select || !options.length) {
			return;
		}

		var current = select.value || 'item-1';
		var html = options.map(function (option) {
			return '<option value="' + option.value + '">' + option.label + '</option>';
		}).join('');

		if (select.getAttribute('data-webmz-parent-options') === html) {
			return;
		}

		select.innerHTML = html;
		select.setAttribute('data-webmz-parent-options', html);

		if (options.some(function (option) { return option.value === current; })) {
			select.value = current;
		} else {
			select.value = options[0].value;
			select.dispatchEvent(new Event('change', { bubbles: true }));
		}
	}

	function refreshParentSelects() {
		var options = getMainItemOptions();

		if (!options.length) {
			return;
		}

		document.querySelectorAll('.elementor-control-parent_key select, select[data-setting="parent_key"]').forEach(function (select) {
			updateSelect(select, options);
		});
	}

	function scheduleRefresh() {
		window.clearTimeout(window.webmzMegaMenuEditorTimer);
		window.webmzMegaMenuEditorTimer = window.setTimeout(refreshParentSelects, 80);
	}

	document.addEventListener('click', scheduleRefresh, true);
	document.addEventListener('input', scheduleRefresh, true);
	document.addEventListener('change', scheduleRefresh, true);

	if (window.MutationObserver) {
		new MutationObserver(scheduleRefresh).observe(document.body, {
			childList: true,
			subtree: true,
			characterData: true
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', refreshParentSelects);
	} else {
		refreshParentSelects();
	}
}());
