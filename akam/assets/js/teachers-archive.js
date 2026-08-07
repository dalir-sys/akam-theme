(function () {
	'use strict';

	function parseConfig(root) {
		try {
			return JSON.parse(root.getAttribute('data-webmz-teachers-archive') || '{}');
		} catch (error) {
			return {};
		}
	}

	function setLoading(root, isLoading) {
		var status = root.querySelector('.webmz-teachers-archive__status');
		if (!status) {
			return;
		}

		if (isLoading) {
			status.hidden = false;
			status.textContent = window.webmzTeachersArchive && webmzTeachersArchive.loadingText ? webmzTeachersArchive.loadingText : 'در حال بارگذاری...';
			return;
		}

		status.hidden = true;
		status.textContent = '';
	}

	function request(root, state, page) {
		if (!window.webmzTeachersArchive || !webmzTeachersArchive.ajaxUrl) {
			return;
		}

		var grid = root.querySelector('.webmz-teachers-archive__grid');
		var pagination = root.querySelector('.webmz-teachers-archive__pagination-wrap');

		setLoading(root, true);

		var body = new URLSearchParams();
		body.append('action', 'webmz_teachers_archive_filter');
		body.append('nonce', webmzTeachersArchive.nonce);
		body.append('posts_per_page', String(state.postsPerPage || 12));
		body.append('page', String(page || 1));
		body.append('title_tag', state.titleTag || 'h3');
		body.append('button_text', state.buttonText || '');
		body.append('show_stats', state.showStats ? '1' : '0');

		fetch(webmzTeachersArchive.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: body.toString()
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (payload) {
				if (!payload || !payload.success || !payload.data) {
					throw new Error('invalid-response');
				}

				if (grid) {
					grid.innerHTML = payload.data.html || '';
				}

				if (pagination) {
					pagination.innerHTML = payload.data.pagination || '';
				}

				state.currentPage = payload.data.currentPage || page;
				state.maxPages = payload.data.maxPages || 1;
				root.setAttribute('data-webmz-teachers-archive', JSON.stringify(state));
			})
			.catch(function () {
				var status = root.querySelector('.webmz-teachers-archive__status');
				if (status) {
					status.hidden = false;
					status.textContent = window.webmzTeachersArchive && webmzTeachersArchive.errorText ? webmzTeachersArchive.errorText : 'خطایی رخ داد.';
				}
			})
			.finally(function () {
				setLoading(root, false);
			});
	}

	function bindArchive(root) {
		var state = parseConfig(root);
		if (!state || root.dataset.webmzTeachersArchiveBound === '1') {
			return;
		}

		root.dataset.webmzTeachersArchiveBound = '1';

		root.addEventListener('click', function (event) {
			var target = event.target.closest('[data-page]');
			if (!target || !root.contains(target)) {
				return;
			}

			event.preventDefault();
			var page = parseInt(target.getAttribute('data-page'), 10);
			if (!page) {
				return;
			}

			request(root, state, page);
		});

		root.addEventListener('change', function (event) {
			var select = event.target.closest('.webmz-teachers-archive__per-page-select');
			if (!select || !root.contains(select)) {
				return;
			}

			state.postsPerPage = parseInt(select.value, 10) || 12;
			root.setAttribute('data-webmz-teachers-archive', JSON.stringify(state));
			request(root, state, 1);
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-webmz-teachers-archive]').forEach(bindArchive);
	});
})();
