(function () {
	'use strict';

	var config = window.webmzBlogCtaWidgets || {};

	function showSwal(message, type) {
		var icon = type === 'success' ? 'success' : 'error';
		var confirmText = config.confirmText || 'متوجه شدم';

		if (window.Swal && typeof window.Swal.fire === 'function') {
			return window.Swal.fire({
				text: message,
				icon: icon,
				confirmButtonText: confirmText,
				customClass: {
					popup: 'webmz-swal-popup',
					confirmButton: 'webmz-swal-confirm',
				},
			});
		}

		window.alert(message);
		return Promise.resolve();
	}

	function initNewsletterForm(form) {
		if (!form || form.dataset.webmzNewsletterInit === '1') {
			return;
		}

		form.dataset.webmzNewsletterInit = '1';

		var wrapper = form.closest('.webmz-newsletter');
		var submitBtn = form.querySelector('.webmz-newsletter__submit');
		var successMessage = wrapper ? wrapper.getAttribute('data-success-message') : '';
		var errorText = config.errorText || 'خطایی رخ داد. دوباره تلاش کنید.';

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			if (!config.ajaxUrl) {
				return;
			}

			var emailInput = form.querySelector('[name="email"]');
			var email = emailInput ? emailInput.value.trim() : '';

			if (!email) {
				showSwal('لطفاً ایمیل خود را وارد کنید.', 'error');
				return;
			}

			setLoading(submitBtn, true);

			var formData = new FormData(form);

			fetch(config.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (data) {
					setLoading(submitBtn, false);

					if (data && data.success) {
						var msg = (data.data && data.data.message) || successMessage;
						showSwal(msg, 'success');
						form.reset();
						return;
					}

					var errMsg = (data && data.data && data.data.message) || errorText;
					showSwal(errMsg, 'error');
				})
				.catch(function () {
					setLoading(submitBtn, false);
					showSwal(errorText, 'error');
				});
		});
	}

	function setLoading(button, isLoading) {
		if (!button) {
			return;
		}

		button.disabled = isLoading;
		button.classList.toggle('is-loading', isLoading);

		if (isLoading) {
			button.setAttribute('aria-busy', 'true');
			return;
		}

		button.removeAttribute('aria-busy');
	}

	function initScope(scope) {
		var root = scope || document;
		var forms = root.querySelectorAll('.webmz-newsletter__form');

		forms.forEach(initNewsletterForm);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initScope(document);
		});
	} else {
		initScope(document);
	}

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		var newsletterWidgets = [
			'webmz-newsletter-subscribe',
			'webmz-zhaket-footer-newsletter',
		];

		newsletterWidgets.forEach(function (widgetName) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/' + widgetName + '.default',
				function ($scope) {
					initScope($scope[0]);
				}
			);
		});
	}
})();
