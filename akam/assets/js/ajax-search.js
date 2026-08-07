(function () {
	'use strict';

	var storageKey = 'webmz_recent_searches';

	function characterLength(value) {
		return Array.from(value || '').length;
	}

	function getRecentSearches() {
		try {
			var stored = window.localStorage.getItem(storageKey);
			var terms = stored ? JSON.parse(stored) : [];

			return Array.isArray(terms) ? terms : [];
		} catch (error) {
			return [];
		}
	}

	function addRecentSearch(term) {
		var value = String(term || '').trim();

		if (characterLength(value) < 3) {
			return;
		}

		var terms = getRecentSearches().filter(function (item) {
			return item !== value;
		});

		terms.unshift(value);
		terms = terms.slice(0, 8);

		try {
			window.localStorage.setItem(storageKey, JSON.stringify(terms));
		} catch (error) {
			/* Storage may be unavailable in private browsing. */
		}
	}

	function getChipTerm(button) {
		if (!button) {
			return '';
		}

		return (
			button.getAttribute('data-webmz-term') ||
			button.textContent ||
			''
		).trim();
	}

	function isZhaketMode(root) {
		return root && root.classList.contains('webmz-ajax-search--zhaket');
	}

	function getZhaketIdle(root) {
		return root ? root.querySelector('[data-webmz-zhaket-idle]') : null;
	}

	function getZhaketResults(root) {
		return root ? root.querySelector('[data-webmz-zhaket-results]') : null;
	}

	function getZhaketBanner(root) {
		return root ? root.querySelector('[data-webmz-zhaket-banner]') : null;
	}

	function showZhaketIdle(root) {
		var idle = getZhaketIdle(root);
		var results = getZhaketResults(root);
		var banner = getZhaketBanner(root);

		root.classList.remove('is-searching');

		if (idle) {
			idle.hidden = false;
		}

		if (results) {
			results.hidden = true;
		}

		if (banner) {
			banner.hidden = false;
		}
	}

	function showZhaketResults(root) {
		var idle = getZhaketIdle(root);
		var results = getZhaketResults(root);
		var banner = getZhaketBanner(root);

		root.classList.add('is-searching');

		if (idle) {
			idle.hidden = true;
		}

		if (results) {
			results.hidden = false;
		}

		if (banner) {
			banner.hidden = true;
		}
	}

	function createTermButton(term) {
		var button = document.createElement('button');

		button.type = 'button';
		button.className = 'webmz-ajax-search__chip';
		button.setAttribute('data-webmz-term', term);
		button.textContent = term;

		return button;
	}

	function renderRecentSearches(root) {
		var holder = root.querySelector('[data-webmz-recent]');

		if (!holder) {
			return;
		}

		var terms = getRecentSearches();

		holder.innerHTML = '';

		if (!terms.length) {
			holder.innerHTML = '<span class="webmz-ajax-search__muted">جستجویی ثبت نشده است.</span>';
			return;
		}

		terms.forEach(function (term) {
			holder.appendChild(createTermButton(term));
		});
	}

	function destroySlider(slider) {
		if (slider && slider.webmzSwiper && typeof slider.webmzSwiper.destroy === 'function') {
			slider.webmzSwiper.destroy(true, true);
			slider.webmzSwiper = null;
		}
	}

	function initialiseSliders(root) {
		if (typeof window.Swiper === 'undefined') {
			return;
		}

		root.querySelectorAll('.webmz-ajax-search__slider').forEach(function (slider) {
			var block = slider.closest('.webmz-ajax-search__block');
			var desktopSlides = slider.classList.contains('webmz-ajax-search__slider--products') ? 4 : 3;

			destroySlider(slider);

			slider.webmzSwiper = new window.Swiper(slider, {
				slidesPerView: 1.2,
				spaceBetween: 12,
				watchOverflow: true,
				navigation: {
					nextEl: block.querySelector('.webmz-search-next'),
					prevEl: block.querySelector('.webmz-search-prev')
				},
				breakpoints: {
					480: {
						slidesPerView: 2,
						spaceBetween: 12
					},
					768: {
						slidesPerView: 3,
						spaceBetween: 14
					},
					1024: {
						slidesPerView: desktopSlides,
						spaceBetween: 16
					}
				}
			});
		});
	}

	function getEventElement(target) {
		if (!target) {
			return null;
		}

		return target.nodeType === 1 ? target : target.parentElement;
	}

	function applyChipSearch(root, input, panel, term, saveHistory) {
		var value = String(term || '').trim();
		var minChars = parseInt(root.dataset.minChars || '3', 10);

		if (characterLength(value) < minChars) {
			return;
		}

		input.value = value;
		syncEnterState(root, value);
		panel.hidden = false;
		requestSearch(root, value, saveHistory);
		input.focus();
	}

	function hidePanel(root) {
		var panel = root.querySelector('.webmz-ajax-search__panel');

		if (isZhaketMode(root)) {
			showZhaketIdle(root);

			if (panel) {
				panel.hidden = false;
			}

			return;
		}

		if (panel) {
			panel.hidden = true;
		}
	}

	function clearResults(root) {
		var products = root.querySelector('[data-webmz-results="products"]');
		var posts = root.querySelector('[data-webmz-results="posts"]');

		if (products) {
			products.innerHTML = '';
		}

		if (posts) {
			posts.innerHTML = '';
		}

		root.querySelectorAll('.webmz-ajax-search__slider').forEach(destroySlider);

		if (isZhaketMode(root)) {
			showZhaketIdle(root);
			return;
		}

		hidePanel(root);
	}

	function setLoading(root, active) {
		var loading = root.querySelector('.webmz-ajax-search__loading');

		if (loading) {
			loading.hidden = !active;
		}

		root.classList.toggle('is-loading', active);
	}

	function resetEnterState(root) {
		root.webmzEnterTerm = '';
		root.webmzEnterCount = 0;
	}

	function syncEnterState(root, term) {
		var value = String(term || '').trim();

		if (!value) {
			resetEnterState(root);
			return;
		}

		if (value !== root.webmzEnterTerm) {
			root.webmzEnterTerm = value;
			root.webmzEnterCount = 0;
		}
	}

	function getSearchPageUrl(term) {
		var base = (window.webmzAjaxSearch && webmzAjaxSearch.searchUrl) || '/';
		var separator = base.indexOf('?') === -1 ? '?' : '&';

		return base + separator + 's=' + encodeURIComponent(term);
	}

	function navigateToSearchPage(term, minChars) {
		var value = String(term || '').trim();
		var minimum = minChars || 3;

		if (characterLength(value) < minimum) {
			return;
		}

		window.location.href = getSearchPageUrl(value);
	}

	function requestSearch(root, term, saveHistory) {
		var value = String(term || '').trim();
		var minChars = parseInt(root.dataset.minChars || '3', 10);
		var panel = root.querySelector('.webmz-ajax-search__panel');

		if (characterLength(value) < minChars) {
			clearResults(root);
			return;
		}

		if (!window.webmzAjaxSearch || !webmzAjaxSearch.ajaxUrl) {
			return;
		}

		panel.hidden = false;

		if (isZhaketMode(root)) {
			showZhaketResults(root);
		}

		if (saveHistory) {
			addRecentSearch(value);
			renderRecentSearches(root);
		}

		if (root.webmzAbortController) {
			root.webmzAbortController.abort();
		}

		root.webmzAbortController = new AbortController();
		setLoading(root, true);

		var params = new URLSearchParams();
		params.append('action', 'webmz_live_search');
		params.append('nonce', webmzAjaxSearch.nonce);
		params.append('term', value);
		params.append('products_limit', root.dataset.productsLimit || '8');
		params.append('posts_limit', root.dataset.postsLimit || '8');

		window.fetch(webmzAjaxSearch.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: params.toString(),
			signal: root.webmzAbortController.signal
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (response) {
				if (!response || !response.success) {
					throw new Error('search-failed');
				}

				root.querySelector('[data-webmz-results="products"]').innerHTML = response.data.products_html;
				root.querySelector('[data-webmz-results="posts"]').innerHTML = response.data.posts_html;
				initialiseSliders(root);
			})
			.catch(function (error) {
				if (error && error.name === 'AbortError') {
					return;
				}

				root.querySelector('[data-webmz-results="products"]').innerHTML =
					'<div class="swiper-slide webmz-ajax-search__empty">خطا در دریافت نتایج.</div>';
				root.querySelector('[data-webmz-results="posts"]').innerHTML =
					'<div class="swiper-slide webmz-ajax-search__empty">دوباره تلاش کنید.</div>';
				initialiseSliders(root);
			})
			.finally(function () {
				setLoading(root, false);
			});
	}

	function initWidget(root) {
		if (!root || root.dataset.webmzReady === 'yes') {
			return;
		}

		root.dataset.webmzReady = 'yes';

		var form = root.querySelector('.webmz-ajax-search__form');
		var input = root.querySelector('.webmz-ajax-search__input');
		var panel = root.querySelector('.webmz-ajax-search__panel');
		var debounce;

		if (!form || !input || !panel) {
			return;
		}

		if (isZhaketMode(root)) {
			panel.hidden = false;
			showZhaketIdle(root);
		}

		renderRecentSearches(root);

		input.addEventListener('input', function () {
			var term = input.value.trim();

			syncEnterState(root, term);
			window.clearTimeout(debounce);

			if (characterLength(term) < 3) {
				clearResults(root);
				return;
			}

			debounce = window.setTimeout(function () {
				requestSearch(root, term, false);
			}, 350);
		});

		input.addEventListener('focus', function () {
			if (characterLength(input.value.trim()) >= 3) {
				panel.hidden = false;
			}
		});

		input.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				hidePanel(root);
			}
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var term = input.value.trim();
			var minChars = parseInt(root.dataset.minChars || '3', 10);

			if (characterLength(term) < minChars) {
				return;
			}

			syncEnterState(root, term);
			root.webmzEnterCount = (root.webmzEnterCount || 0) + 1;

			if (root.webmzEnterCount >= 2) {
				navigateToSearchPage(term, minChars);
				return;
			}

			requestSearch(root, term, true);
		});

		root.addEventListener('mousedown', function (event) {
			var target = getEventElement(event.target);

			if (target && target.closest('[data-webmz-term]')) {
				event.preventDefault();
			}
		});

		root.addEventListener('click', function (event) {
			var target = getEventElement(event.target);
			var termButton = target ? target.closest('[data-webmz-term]') : null;

			if (termButton) {
				event.preventDefault();
				event.stopPropagation();
				window.clearTimeout(debounce);
				applyChipSearch(root, input, panel, getChipTerm(termButton), true);
				return;
			}

			if (target && target.closest('.webmz-search-product, .webmz-search-post')) {
				addRecentSearch(input.value.trim());
			}
		});

		document.addEventListener('click', function (event) {
			var target = getEventElement(event.target);

			if (!target || root.contains(target)) {
				return;
			}

			if (root.classList.contains('webmz-ajax-search--popup') && target.closest('.webmz-search-popup__overlay')) {
				return;
			}

			hidePanel(root);
		});
	}

	function initScope(scope) {
		if (!scope || !scope.querySelectorAll) {
			return;
		}

		scope.querySelectorAll('.webmz-ajax-search').forEach(initWidget);

		if (scope.classList && scope.classList.contains('webmz-ajax-search')) {
			initWidget(scope);
		}
	}

	function observeWidgets() {
		if (!window.MutationObserver || !document.body) {
			return;
		}

		new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				mutation.addedNodes.forEach(function (node) {
					if (node.nodeType === 1) {
						initScope(node);
					}
				});
			});
		}).observe(document.body, {
			childList: true,
			subtree: true
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initScope(document);
		observeWidgets();
	});
}());
