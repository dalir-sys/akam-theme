(function () {
	'use strict';

	if (typeof window.webmzStoreArchive === 'undefined') {
		return;
	}

	var config = window.webmzStoreArchive;
	var debounceTimer = null;
	var priceDebounceTimer = null;

	function parseConfig(root) {
		var raw = root.getAttribute('data-webmz-store-archive');
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
			'.webmz-store-archive__term[data-term-id="' + state.termId + '"][data-taxonomy="' + state.taxonomy + '"]'
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

		if (state.minPrice > state.minBound) {
			urlObj.searchParams.set('min_price', String(state.minPrice));
		}

		if (state.maxPrice < state.maxBound) {
			urlObj.searchParams.set('max_price', String(state.maxPrice));
		}

		if (state.orderby && state.orderby !== 'menu_order') {
			urlObj.searchParams.set('orderby', state.orderby);
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
			webmzStoreArchive: {
				search: state.search,
				taxonomy: state.taxonomy,
				termId: state.termId,
				page: page,
				minPrice: state.minPrice,
				maxPrice: state.maxPrice,
				orderby: state.orderby,
			},
		};

		if (useReplace) {
			window.history.replaceState(historyState, '', newUrl);
		} else {
			window.history.pushState(historyState, '', newUrl);
		}
	}

	function syncSearchInputs(root, value, sidebarInput, mainInput) {
		if (sidebarInput) {
			sidebarInput.value = value;
		}
		if (mainInput) {
			mainInput.value = value;
		}
	}

	function syncStateFromUrl(state, root, sidebarInput, mainInput, sortSelect) {
		var current = new URL(window.location.href);
		var page = getPageFromPath(current.pathname);
		var path = normalizePath(current.pathname);
		var search = current.searchParams.get('s') || '';

		state.search = search;
		state.termId = 0;
		state.currentPage = page;
		state.minPrice = parseInt(current.searchParams.get('min_price') || String(state.minBound), 10) || state.minBound;
		state.maxPrice = parseInt(current.searchParams.get('max_price') || String(state.maxBound), 10) || state.maxBound;
		state.orderby = current.searchParams.get('orderby') || 'menu_order';

		syncSearchInputs(root, search, sidebarInput, mainInput);

		if (sortSelect) {
			sortSelect.value = state.orderby;
		}

		updatePriceRangeUi(root, state);

		if (search) {
			clearTermStates(root);
			return page;
		}

		var basePath = normalizePath(new URL(state.baseUrl || window.location.href, window.location.origin).pathname);
		var matched = false;

		root.querySelectorAll('.webmz-store-archive__term[data-term-url]').forEach(function (button) {
			var termUrl = button.getAttribute('data-term-url');
			if (!termUrl) {
				return;
			}
			var termPath = normalizePath(new URL(termUrl, window.location.origin).pathname);
			if (termPath === path) {
				state.termId = parseInt(button.getAttribute('data-term-id') || '0', 10);
				state.taxonomy = 'product_cat';
				updateTermStates(root, state.termId);
				matched = true;
			}
		});

		if (!matched) {
			state.termId = 0;
			state.taxonomy = path === basePath ? 'product_cat' : state.taxonomy;
			clearTermStates(root);
		}

		return page;
	}

	function buildRequestBody(state, page) {
		var body = new URLSearchParams();
		body.append('action', 'webmz_store_archive_filter');
		body.append('nonce', config.nonce);
		body.append('posts_per_page', String(state.postsPerPage || 6));
		body.append('page', String(page || 1));
		body.append('search', state.search || '');
		body.append('taxonomy', state.taxonomy || 'product_cat');
		body.append('term_id', String(state.termId || 0));
		body.append('load_mode', state.loadMode || 'pagination');
		body.append('min_price', String(state.minPrice || 0));
		body.append('max_price', String(state.maxPrice || 0));
		body.append('orderby', state.orderby || 'menu_order');
		body.append('card_title_tag', state.cardTitleTag || 'h3');
		body.append('button_text', state.buttonText || '');
		body.append('variable_button_text', state.variableButtonText || '');
		body.append('unavailable_button_text', state.unavailableButtonText || '');
		return body;
	}

	function setStatus(root, message) {
		var status = root.querySelector('.webmz-store-archive__status');
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

	function updateTermStates(root, termId) {
		var wrap = root.querySelector('.webmz-store-archive__terms-wrap');
		if (!wrap) {
			return;
		}
		wrap.querySelectorAll('.webmz-store-archive__term').forEach(function (button) {
			var id = parseInt(button.getAttribute('data-term-id') || '0', 10);
			button.classList.toggle('is-active', id === termId);
		});
	}

	function clearTermStates(root) {
		root.querySelectorAll('.webmz-store-archive__term').forEach(function (button) {
			button.classList.remove('is-active');
		});
		var allButton = root.querySelector('.webmz-store-archive__term[data-term-id="0"]');
		if (allButton) {
			allButton.classList.add('is-active');
		}
	}

	function formatPriceLabel(amount) {
		return String(amount).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function getSortLabel(sortSelect, orderby) {
		if (!sortSelect || !orderby || orderby === 'menu_order') {
			return '';
		}
		var option = sortSelect.querySelector('option[value="' + orderby + '"]');
		return option ? option.textContent.trim() : '';
	}

	function getCategoryLabel(root, termId) {
		if (!termId) {
			return '';
		}
		var button = root.querySelector(
			'.webmz-store-archive__term[data-term-id="' + termId + '"][data-taxonomy="product_cat"]'
		);
		if (!button) {
			return '';
		}
		var name = button.querySelector('.webmz-store-archive__term-name');
		return name ? name.textContent.trim() : '';
	}

	function getPriceCurrency(root) {
		var el = root.querySelector('.webmz-store-archive__price-value-currency');
		return el ? el.textContent.trim() : '';
	}

	function formatActivePriceRange(state, root) {
		if (state.minPrice <= state.minBound && state.maxPrice >= state.maxBound) {
			return '';
		}
		var currency = getPriceCurrency(root);
		var min = formatPriceLabel(state.minPrice);
		var max = formatPriceLabel(state.maxPrice);
		return min + ' – ' + max + (currency ? ' ' + currency : '');
	}

	function appendActiveFilterChip(list, label, value) {
		var li = document.createElement('li');
		li.className = 'webmz-store-archive__active-filter';
		var labelEl = document.createElement('span');
		labelEl.className = 'webmz-store-archive__active-filter-label';
		labelEl.textContent = label + ':';
		var valueEl = document.createElement('span');
		valueEl.className = 'webmz-store-archive__active-filter-value';
		valueEl.textContent = value;
		li.appendChild(labelEl);
		li.appendChild(valueEl);
		list.appendChild(li);
	}

	function updateActiveFiltersUi(root, state, sortSelect, widgetConfig) {
		var box = root.querySelector('.webmz-store-archive__active-filters');
		var list = root.querySelector('.webmz-store-archive__active-filters-list');
		if (!box || !list) {
			return;
		}

		var cfg = widgetConfig || {};
		var chips = [];

		var sortText = getSortLabel(sortSelect, state.orderby);
		if (sortText) {
			chips.push({ label: cfg.chipSortLabel || 'مرتب‌سازی', value: sortText });
		}

		if (state.search && state.search.trim()) {
			chips.push({ label: cfg.chipSearchLabel || 'جستجو', value: state.search.trim() });
		}

		var priceText = formatActivePriceRange(state, root);
		if (priceText) {
			chips.push({ label: cfg.chipPriceLabel || 'قیمت', value: priceText });
		}

		var categoryText = getCategoryLabel(root, state.termId);
		if (categoryText) {
			chips.push({ label: cfg.chipCategoryLabel || 'دسته‌بندی', value: categoryText });
		}

		list.innerHTML = '';

		if (!chips.length) {
			box.hidden = true;
			return;
		}

		box.hidden = false;
		chips.forEach(function (chip) {
			appendActiveFilterChip(list, chip.label, chip.value);
		});
	}

	function resetAllFilters(root, state, sidebarInput, mainInput, sortSelect, widgetConfig, request) {
		state.search = '';
		state.termId = 0;
		state.taxonomy = 'product_cat';
		state.orderby = 'menu_order';
		state.currentPage = 1;
		state.minPrice = state.minBound;
		state.maxPrice = state.maxBound;

		syncSearchInputs(root, '', sidebarInput, mainInput);
		if (sortSelect) {
			sortSelect.value = 'menu_order';
		}
		updatePriceRangeUi(root, state);
		clearTermStates(root);
		updateActiveFiltersUi(root, state, sortSelect, widgetConfig);
		request(1, false);
	}

	function updatePriceRangeUi(root, state) {
		var rangeWrap = root.querySelector('.webmz-store-archive__price-range');
		if (!rangeWrap) {
			return;
		}

		var minInput = rangeWrap.querySelector('.webmz-store-archive__price-range-input--min');
		var maxInput = rangeWrap.querySelector('.webmz-store-archive__price-range-input--max');
		var fill = rangeWrap.querySelector('.webmz-store-archive__price-range-fill');
		var minLabel = root.querySelector('.webmz-store-archive__price-value--min .webmz-store-archive__price-value-amount');
		var maxLabel = root.querySelector('.webmz-store-archive__price-value--max .webmz-store-archive__price-value-amount');

		if (!minInput || !maxInput) {
			return;
		}

		var minBound = parseInt(rangeWrap.getAttribute('data-min-bound') || '0', 10);
		var maxBound = parseInt(rangeWrap.getAttribute('data-max-bound') || '0', 10);
		var minVal = Math.max(minBound, Math.min(state.minPrice, state.maxPrice));
		var maxVal = Math.min(maxBound, Math.max(state.minPrice, state.maxPrice));

		minInput.value = String(minVal);
		maxInput.value = String(maxVal);
		state.minPrice = minVal;
		state.maxPrice = maxVal;

		var range = maxBound - minBound;
		var left = range > 0 ? ((maxBound - maxVal) / range) * 100 : 0;
		var right = range > 0 ? ((minVal - minBound) / range) * 100 : 0;

		if (fill) {
			fill.style.left = left + '%';
			fill.style.right = right + '%';
		}

		if (minLabel) {
			minLabel.textContent = formatPriceLabel(minVal);
		}
		if (maxLabel) {
			maxLabel.textContent = formatPriceLabel(maxVal);
		}
	}

	function bindPagination(root, state, request) {
		var pagination = root.querySelector('.webmz-store-archive__pagination');
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

		var sentinel = root.querySelector('.webmz-store-archive__infinite-sentinel');
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

		var sentinel = root.querySelector('.webmz-store-archive__infinite-sentinel');

		if (!state.hasMore) {
			if (loader) {
				loader.hidden = true;
			}
			if (sentinel) {
				sentinel.hidden = true;
			}
			root.classList.add('webmz-store-archive--infinite-ended');
			if (state.infiniteObserver) {
				state.infiniteObserver.disconnect();
				state.infiniteObserver = null;
			}
			return;
		}

		root.classList.remove('webmz-store-archive--infinite-ended');
		if (sentinel) {
			sentinel.hidden = false;
		}
		if (loader) {
			loader.hidden = true;
		}
	}

	function initWidget(root) {
		if (root.classList.contains('webmz-store-archive--editor') || root.dataset.webmzStoreArchiveReady === '1') {
			return;
		}

		var widgetConfig = parseConfig(root);
		widgetConfig.chipSortLabel = widgetConfig.chipSortLabel || config.chipSortLabel || '';
		widgetConfig.chipSearchLabel = widgetConfig.chipSearchLabel || config.chipSearchLabel || '';
		widgetConfig.chipPriceLabel = widgetConfig.chipPriceLabel || config.chipPriceLabel || '';
		widgetConfig.chipCategoryLabel = widgetConfig.chipCategoryLabel || config.chipCategoryLabel || '';
		var grid = root.querySelector('.webmz-store-archive__grid');
		var sidebarInput = root.querySelector('.webmz-store-archive__search-input');
		var mainInput = root.querySelector('.webmz-store-archive__main-search-input');
		var sortSelect = root.querySelector('.webmz-store-archive__sort-select');
		var loader = root.querySelector('.webmz-store-archive__infinite-loader');
		var paginationWrap = root.querySelector('.webmz-store-archive__pagination');
		var rangeWrap = root.querySelector('.webmz-store-archive__price-range');

		if (!grid) {
			return;
		}

		root.dataset.webmzStoreArchiveReady = '1';

		var state = {
			postsPerPage: widgetConfig.postsPerPage || 6,
			loadMode: widgetConfig.loadMode || 'pagination',
			infiniteOffset: widgetConfig.infiniteOffset || 240,
			baseUrl: widgetConfig.baseUrl || window.location.href,
			taxonomy: widgetConfig.taxonomy || widgetConfig.activeTab || 'product_cat',
			cardTitleTag: widgetConfig.cardTitleTag || 'h3',
			buttonText: widgetConfig.buttonText || '',
			variableButtonText: widgetConfig.variableButtonText || '',
			unavailableButtonText: widgetConfig.unavailableButtonText || '',
			minBound: widgetConfig.minPrice || 0,
			maxBound: widgetConfig.maxPrice || 0,
			minPrice: widgetConfig.minPrice || 0,
			maxPrice: widgetConfig.maxPrice || 0,
			orderby: widgetConfig.orderby || 'menu_order',
			search: (mainInput && mainInput.value.trim()) || (sidebarInput && sidebarInput.value.trim()) || '',
			termId: widgetConfig.termId || 0,
			currentPage: widgetConfig.currentPage || 1,
			maxPages: widgetConfig.maxPages || 1,
			hasMore: (widgetConfig.currentPage || 1) < (widgetConfig.maxPages || 1),
			loading: false,
			urlSync: true,
			infiniteObserver: null,
		};

		if (rangeWrap) {
			state.minBound = parseInt(rangeWrap.getAttribute('data-min-bound') || String(state.minBound), 10);
			state.maxBound = parseInt(rangeWrap.getAttribute('data-max-bound') || String(state.maxBound), 10);
		}

		syncStateFromUrl(state, root, sidebarInput, mainInput, sortSelect);
		updateActiveFiltersUi(root, state, sortSelect, widgetConfig);
		updateInfiniteScrollUi(root, state, loader);

		var clearFiltersBtn = root.querySelector('.webmz-store-archive__active-filters-clear');
		if (clearFiltersBtn) {
			clearFiltersBtn.addEventListener('click', function () {
				resetAllFilters(root, state, sidebarInput, mainInput, sortSelect, widgetConfig, request);
			});
		}

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
						paginationWrap = root.querySelector('.webmz-store-archive__pagination');
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

					updateActiveFiltersUi(root, state, sortSelect, widgetConfig);
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

		function handleSearchInput(value) {
			state.search = value.trim();
			state.termId = 0;
			state.currentPage = 1;
			clearTermStates(root);
			syncSearchInputs(root, state.search, sidebarInput, mainInput);
			request(1, false, { replaceUrl: false });
		}

		if (sidebarInput) {
			sidebarInput.addEventListener('input', function () {
				window.clearTimeout(debounceTimer);
				debounceTimer = window.setTimeout(function () {
					handleSearchInput(sidebarInput.value);
				}, 320);
			});
		}

		if (mainInput) {
			mainInput.addEventListener('input', function () {
				window.clearTimeout(debounceTimer);
				debounceTimer = window.setTimeout(function () {
					handleSearchInput(mainInput.value);
				}, 320);
			});
		}

		if (sortSelect) {
			sortSelect.addEventListener('change', function () {
				state.orderby = sortSelect.value || 'menu_order';
				state.currentPage = 1;
				request(1, false);
			});
		}

		if (rangeWrap) {
			var minInput = rangeWrap.querySelector('.webmz-store-archive__price-range-input--min');
			var maxInput = rangeWrap.querySelector('.webmz-store-archive__price-range-input--max');

			function onPriceChange() {
				var minVal = parseInt(minInput.value, 10);
				var maxVal = parseInt(maxInput.value, 10);

				if (minVal > maxVal) {
					if (minInput === document.activeElement) {
						maxVal = minVal;
						maxInput.value = String(maxVal);
					} else {
						minVal = maxVal;
						minInput.value = String(minVal);
					}
				}

				state.minPrice = minVal;
				state.maxPrice = maxVal;
				updatePriceRangeUi(root, state);

				window.clearTimeout(priceDebounceTimer);
				priceDebounceTimer = window.setTimeout(function () {
					state.currentPage = 1;
					request(1, false, { replaceUrl: false });
				}, 280);
			}

			if (minInput) {
				minInput.addEventListener('input', onPriceChange);
			}
			if (maxInput) {
				maxInput.addEventListener('input', onPriceChange);
			}
		}

		root.querySelectorAll('.webmz-store-archive__term').forEach(function (button) {
			button.addEventListener('click', function () {
				var termId = parseInt(button.getAttribute('data-term-id') || '0', 10);
				state.taxonomy = 'product_cat';
				state.termId = termId;
				state.search = '';
				state.currentPage = 1;
				syncSearchInputs(root, '', sidebarInput, mainInput);
				updateTermStates(root, termId);
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
			var page = syncStateFromUrl(state, root, sidebarInput, mainInput, sortSelect);
			state.urlSync = false;
			request(page, false);
			state.urlSync = true;
		});
	}

	function boot() {
		document.querySelectorAll('[data-webmz-store-archive]').forEach(initWidget);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz-store-archive.default', function ($scope) {
			var root = $scope[0] ? $scope[0].querySelector('[data-webmz-store-archive]') : null;
			if (!root) {
				root = $scope[0];
			}
			if (root) {
				initWidget(root);
			}
		});
	}
})();
