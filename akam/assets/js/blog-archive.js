(function () {
	'use strict';

	if (typeof window.webmzBlogArchive === 'undefined') {
		return;
	}

	var config = window.webmzBlogArchive;
	var debounceTimer = null;

	function parseConfig(root) {
		var raw = root.getAttribute('data-webmz-blog-archive');
		if (!raw) {
			return {};
		}
		try {
			return JSON.parse(raw);
		} catch (error) {
			return {};
		}
	}

	function normalizePath(path) {
		return path.replace(/\/page\/\d+\/?$/, '').replace(/\/+$/, '') || '/';
	}

	function getPageFromPath(pathname) {
		var match = pathname.match(/\/page\/(\d+)\/?$/);
		return match ? parseInt(match[1], 10) || 1 : 1;
	}

	function applyPageToPath(pathname, page) {
		var clean = pathname.replace(/\/page\/\d+\/?$/, '');
		if (!clean.endsWith('/')) {
			clean += '/';
		}
		if (page <= 1) {
			return clean;
		}
		return clean + 'page/' + page + '/';
	}

	function getActiveTermButton(root, state) {
		if (!state.termId) {
			return null;
		}
		return root.querySelector(
			'.webmz-blog-archive__term[data-term-id="' + state.termId + '"][data-taxonomy="' + state.taxonomy + '"]'
		);
	}

	function buildFilterUrl(state, page, root) {
		var urlObj;

		if (state.search) {
			urlObj = new URL(state.baseUrl || window.location.href, window.location.origin);
			urlObj.search = '';
			urlObj.searchParams.set('s', state.search);
		} else if (state.termId > 0) {
			var termButton = getActiveTermButton(root, state);
			var termUrl = termButton ? termButton.getAttribute('data-term-url') : '';
			urlObj = new URL(termUrl || state.baseUrl || window.location.href, window.location.origin);
			urlObj.search = '';
		} else {
			urlObj = new URL(state.baseUrl || window.location.href, window.location.origin);
			urlObj.search = '';
		}

		urlObj.pathname = applyPageToPath(urlObj.pathname, page);
		return urlObj.pathname + urlObj.search;
	}

	function updateBrowserUrl(state, page, root, useReplace) {
		if (!window.history || typeof window.history.pushState !== 'function') {
			return;
		}

		var newUrl = buildFilterUrl(state, page, root);
		var currentUrl = window.location.pathname + window.location.search;

		if (newUrl === currentUrl) {
			return;
		}

		var historyState = {
			webmzBlogArchive: {
				search: state.search,
				taxonomy: state.taxonomy,
				termId: state.termId,
				page: page,
			},
		};

		if (useReplace) {
			window.history.replaceState(historyState, '', newUrl);
		} else {
			window.history.pushState(historyState, '', newUrl);
		}
	}

	function activateTabUi(root, taxonomy) {
		root.querySelectorAll('.webmz-blog-archive__tab').forEach(function (tab) {
			var isActive = tab.getAttribute('data-tab') === taxonomy;
			tab.classList.toggle('is-active', isActive);
			tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
		});

		root.querySelectorAll('.webmz-blog-archive__tab-panel').forEach(function (panel) {
			var isActive = panel.getAttribute('data-tab-panel') === taxonomy;
			panel.classList.toggle('is-active', isActive);
			panel.hidden = !isActive;
		});
	}

	function syncStateFromUrl(state, root, searchInput) {
		var current = new URL(window.location.href);
		var page = getPageFromPath(current.pathname);
		var path = normalizePath(current.pathname);
		var search = current.searchParams.get('s') || '';

		state.search = search;
		state.termId = 0;
		state.currentPage = page;

		if (searchInput) {
			searchInput.value = search;
		}

		if (search) {
			clearTermStates(root);
			return page;
		}

		var basePath = normalizePath(new URL(state.baseUrl || window.location.href, window.location.origin).pathname);
		var matched = false;

		root.querySelectorAll('.webmz-blog-archive__term[data-term-url]').forEach(function (button) {
			var termUrl = button.getAttribute('data-term-url');
			if (!termUrl) {
				return;
			}
			var termPath = normalizePath(new URL(termUrl, window.location.origin).pathname);
			if (termPath === path) {
				state.termId = parseInt(button.getAttribute('data-term-id') || '0', 10);
				state.taxonomy = button.getAttribute('data-taxonomy') || 'category';
				activateTabUi(root, state.taxonomy);
				updateTermStates(root, state.termId, state.taxonomy);
				matched = true;
			}
		});

		if (!matched) {
			state.termId = 0;
			state.taxonomy = path === basePath ? 'category' : state.taxonomy;
			clearTermStates(root);
		}

		return page;
	}

	function buildRequestBody(state, page) {
		var body = new URLSearchParams();
		body.append('action', 'webmz_blog_archive_filter');
		body.append('nonce', config.nonce);
		body.append('posts_per_page', String(state.postsPerPage || 6));
		body.append('page', String(page || 1));
		body.append('search', state.search || '');
		body.append('taxonomy', state.taxonomy || 'category');
		body.append('term_id', String(state.termId || 0));
		body.append('load_mode', state.loadMode || 'pagination');
		body.append('card_title_tag', state.cardTitleTag || 'h3');
		body.append('read_more_text', state.readMoreText || '');
		return body;
	}

	function setStatus(root, message) {
		var status = root.querySelector('.webmz-blog-archive__status');
		if (!status) {
			return;
		}
		if (!message) {
			status.hidden = true;
			status.textContent = '';
			return;
		}
		status.hidden = false;
		status.textContent = message;
	}

	function getActivePanel(root, taxonomy) {
		return root.querySelector('[data-tab-panel="' + taxonomy + '"]');
	}

	function updateTermStates(root, termId, taxonomy) {
		var panel = getActivePanel(root, taxonomy);
		if (!panel) {
			return;
		}
		panel.querySelectorAll('.webmz-blog-archive__term').forEach(function (button) {
			var id = parseInt(button.getAttribute('data-term-id') || '0', 10);
			button.classList.toggle('is-active', id === termId);
		});
	}

	function clearTermStates(root) {
		root.querySelectorAll('.webmz-blog-archive__term').forEach(function (button) {
			button.classList.remove('is-active');
		});
		root.querySelectorAll('.webmz-blog-archive__term[data-term-id="0"]').forEach(function (button) {
			var panel = button.closest('[data-tab-panel]');
			if (panel && panel.classList.contains('is-active')) {
				button.classList.add('is-active');
			}
		});
	}

	function switchTab(root, state, tabTaxonomy, request, searchInput) {
		state.taxonomy = tabTaxonomy;
		state.termId = 0;
		state.search = '';
		state.currentPage = 1;

		if (searchInput) {
			searchInput.value = '';
		}

		root.querySelectorAll('.webmz-blog-archive__tab').forEach(function (tab) {
			var isActive = tab.getAttribute('data-tab') === tabTaxonomy;
			tab.classList.toggle('is-active', isActive);
			tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
		});

		root.querySelectorAll('.webmz-blog-archive__tab-panel').forEach(function (panel) {
			var isActive = panel.getAttribute('data-tab-panel') === tabTaxonomy;
			panel.classList.toggle('is-active', isActive);
			panel.hidden = !isActive;
		});

		clearTermStates(root);
		request(1, false);
	}

	function bindPagination(root, state, request) {
		var pagination = root.querySelector('.webmz-blog-archive__pagination');
		if (!pagination) {
			return;
		}

		pagination.addEventListener('click', function (event) {
			var link = event.target.closest('a.page-numbers');
			if (!link || link.classList.contains('current')) {
				return;
			}
			event.preventDefault();

			var page = 1;
			if (link.classList.contains('next')) {
				page = state.currentPage + 1;
			} else if (link.classList.contains('prev')) {
				page = Math.max(1, state.currentPage - 1);
			} else {
				page = parseInt(link.textContent, 10) || 1;
			}

			request(page, false);
		});
	}

	function initInfiniteScroll(root, state, request) {
		if (state.loadMode !== 'infinite_scroll') {
			return null;
		}

		var sentinel = root.querySelector('.webmz-blog-archive__infinite-sentinel');
		if (!sentinel || typeof IntersectionObserver === 'undefined') {
			return null;
		}

		if (state.infiniteObserver) {
			state.infiniteObserver.disconnect();
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting || state.loading || !state.hasMore) {
						return;
					}
					request(state.currentPage + 1, true);
				});
			},
			{
				root: null,
				rootMargin: '0px 0px ' + String(state.infiniteOffset || 240) + 'px 0px',
				threshold: 0,
			}
		);

		observer.observe(sentinel);
		state.infiniteObserver = observer;
		return observer;
	}

	function updateInfiniteScrollUi(root, state, loader) {
		if (state.loadMode !== 'infinite_scroll') {
			return;
		}

		var sentinel = root.querySelector('.webmz-blog-archive__infinite-sentinel');

		if (!state.hasMore) {
			if (loader) {
				loader.hidden = true;
			}
			if (sentinel) {
				sentinel.hidden = true;
			}
			root.classList.add('webmz-blog-archive--infinite-ended');
			if (state.infiniteObserver) {
				state.infiniteObserver.disconnect();
				state.infiniteObserver = null;
			}
			return;
		}

		root.classList.remove('webmz-blog-archive--infinite-ended');
		if (sentinel) {
			sentinel.hidden = false;
		}
		if (loader) {
			loader.hidden = true;
		}
	}

	function initWidget(root) {
		if (root.classList.contains('webmz-blog-archive--editor') || root.dataset.webmzBlogArchiveReady === '1') {
			return;
		}

		var widgetConfig = parseConfig(root);
		var grid = root.querySelector('.webmz-blog-archive__grid');
		var searchInput = root.querySelector('.webmz-blog-archive__search-input');
		var loader = root.querySelector('.webmz-blog-archive__infinite-loader');
		var paginationWrap = root.querySelector('.webmz-blog-archive__pagination');

		if (!grid) {
			return;
		}

		root.dataset.webmzBlogArchiveReady = '1';

		var state = {
			postsPerPage: widgetConfig.postsPerPage || 6,
			loadMode: widgetConfig.loadMode || 'pagination',
			infiniteOffset: widgetConfig.infiniteOffset || 240,
			baseUrl: widgetConfig.baseUrl || window.location.href,
			taxonomy: widgetConfig.taxonomy || widgetConfig.activeTab || 'category',
			cardTitleTag: widgetConfig.cardTitleTag || 'h3',
			readMoreText: widgetConfig.readMoreText || '',
			search: searchInput ? searchInput.value.trim() : '',
			termId: widgetConfig.termId || 0,
			currentPage: widgetConfig.currentPage || 1,
			maxPages: widgetConfig.maxPages || 1,
			hasMore: (widgetConfig.currentPage || 1) < (widgetConfig.maxPages || 1),
			loading: false,
			urlSync: true,
			infiniteObserver: null,
		};

		updateInfiniteScrollUi(root, state, loader);

		function toggleLoader(show) {
			if (!loader || !state.hasMore) {
				return;
			}
			loader.hidden = !show;
		}

		function request(page, append, urlOptions) {
			urlOptions = urlOptions || {};
			if (state.loading) {
				return;
			}

			state.loading = true;
			root.classList.add('is-loading');
			if (append && state.loadMode === 'infinite_scroll') {
				toggleLoader(true);
			} else {
				setStatus(root, config.loadingText || '');
			}

			fetch(config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				},
				body: buildRequestBody(state, page).toString(),
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (payload) {
					if (!payload || !payload.success || !payload.data) {
						throw new Error('invalid_response');
					}

					var data = payload.data;
					if (append) {
						var temp = document.createElement('div');
						temp.innerHTML = data.html;
						while (temp.firstChild) {
							grid.appendChild(temp.firstChild);
						}
					} else {
						grid.innerHTML = data.html;
					}

					state.currentPage = data.currentPage || page;
					state.maxPages = data.maxPages || 1;
					state.hasMore = !!data.hasMore;

					if (state.loadMode === 'pagination') {
						if (paginationWrap) {
							paginationWrap.outerHTML = data.pagination || '';
						} else if (data.pagination) {
							grid.insertAdjacentHTML('afterend', data.pagination);
						}
						paginationWrap = root.querySelector('.webmz-blog-archive__pagination');
						bindPagination(root, state, request);
					}

					if (data.foundPosts === 0) {
						setStatus(root, config.emptyText || '');
					} else {
						setStatus(root, '');
					}

					if (state.urlSync && !append) {
						updateBrowserUrl(state, state.currentPage, root, !!urlOptions.replaceUrl);
					}

					updateInfiniteScrollUi(root, state, loader);

					if (state.loadMode === 'infinite_scroll' && state.hasMore && !state.infiniteObserver) {
						initInfiniteScroll(root, state, request);
					}
				})
				.catch(function () {
					setStatus(root, config.errorText || '');
				})
				.finally(function () {
					state.loading = false;
					root.classList.remove('is-loading');
					if (loader && state.hasMore) {
						loader.hidden = true;
					}
					updateInfiniteScrollUi(root, state, loader);
				});
		}

		if (searchInput) {
			searchInput.addEventListener('input', function () {
				window.clearTimeout(debounceTimer);
				debounceTimer = window.setTimeout(function () {
					state.search = searchInput.value.trim();
					state.termId = 0;
					state.currentPage = 1;
					clearTermStates(root);
					request(1, false, { replaceUrl: false });
				}, 320);
			});
		}

		root.querySelectorAll('.webmz-blog-archive__tab').forEach(function (tab) {
			tab.addEventListener('click', function () {
				var tabTaxonomy = tab.getAttribute('data-tab') || 'category';
				if (tabTaxonomy === state.taxonomy) {
					return;
				}
				switchTab(root, state, tabTaxonomy, request, searchInput);
			});
		});

		root.querySelectorAll('.webmz-blog-archive__term').forEach(function (button) {
			button.addEventListener('click', function () {
				var termId = parseInt(button.getAttribute('data-term-id') || '0', 10);
				var taxonomy = button.getAttribute('data-taxonomy') || state.taxonomy;
				state.taxonomy = taxonomy;
				state.termId = termId;
				state.search = '';
				state.currentPage = 1;
				if (searchInput) {
					searchInput.value = '';
				}
				updateTermStates(root, termId, taxonomy);
				request(1, false);
			});
		});

		bindPagination(root, state, request);
		if (state.loadMode === 'infinite_scroll' && state.hasMore) {
			initInfiniteScroll(root, state, request);
		}

		window.addEventListener('popstate', function () {
			if (state.loading) {
				return;
			}
			var page = syncStateFromUrl(state, root, searchInput);
			state.urlSync = false;
			request(page, false);
			state.urlSync = true;
		});
	}

	function boot() {
		document.querySelectorAll('[data-webmz-blog-archive]').forEach(initWidget);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-blog-archive.default', function ($scope) {
			var root = $scope[0] ? $scope[0].querySelector('[data-webmz-blog-archive]') : null;
			if (!root) {
				root = $scope[0];
			}
			if (root) {
				initWidget(root);
			}
		});
	}
})();
