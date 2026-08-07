(function () {
    'use strict';

    function initTabs(scope) {
        var roots = (scope || document).querySelectorAll('[data-webmz-contact-tabs]');

        roots.forEach(function (root) {
            var tabs = root.querySelectorAll('[data-webmz-tab]');
            var panels = root.querySelectorAll('[data-webmz-panel]');

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var index = tab.getAttribute('data-webmz-tab');

                    tabs.forEach(function (item) {
                        var active = item === tab;
                        item.classList.toggle('is-active', active);
                        item.setAttribute('aria-selected', active ? 'true' : 'false');
                    });

                    panels.forEach(function (panel) {
                        panel.classList.toggle('is-active', panel.getAttribute('data-webmz-panel') === index);
                    });
                });
            });
        });
    }

    function initGallery(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = (scope || document).querySelectorAll('[data-webmz-gallery-swiper]');

        sliders.forEach(function (slider) {
            if (slider.dataset.webmzGalleryReady === 'yes') {
                return;
            }

            var config = {};
            try {
                config = JSON.parse(slider.getAttribute('data-webmz-gallery-swiper') || '{}');
            } catch (error) {
                config = {};
            }

            slider.dataset.webmzGalleryReady = 'yes';

            new window.Swiper(slider, {
                slidesPerView: 1.15,
                centeredSlides: true,
                loop: config.loop !== false,
                spaceBetween: 22,
                autoplay: config.autoplay ? { delay: 3500, disableOnInteraction: false } : false,
                breakpoints: {
                    768: {
                        slidesPerView: Math.max(1, Number(config.slidesPerView || 3) - 1),
                        spaceBetween: 24
                    },
                    1024: {
                        slidesPerView: Number(config.slidesPerView || 3),
                        spaceBetween: 32
                    }
                },
                navigation: {
                    nextEl: slider.querySelector('.webmz-gallery-swiper__button--next'),
                    prevEl: slider.querySelector('.webmz-gallery-swiper__button--prev')
                }
            });
        });
    }

    function setMessage(form, type, text) {
        var message = form.querySelector('.webmz-ajax-form__message');

        if (!message) {
            return;
        }

        message.classList.remove('is-success', 'is-error');
        if (type) {
            message.classList.add('is-' + type);
        }
        message.textContent = text || '';
    }

    function initForms(scope) {
        var forms = (scope || document).querySelectorAll('[data-webmz-ajax-form]');
        var config = window.webmzContactWidgets || {};

        forms.forEach(function (form) {
            if (form.dataset.webmzAjaxReady === 'yes') {
                return;
            }

            form.dataset.webmzAjaxReady = 'yes';

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                var button = form.querySelector('[type="submit"]');
                var data = new FormData(form);

                if (!config.ajaxUrl) {
                    setMessage(form, 'error', config.errorText || 'خطایی رخ داد. دوباره تلاش کنید.');
                    return;
                }

                if (button) {
                    button.disabled = true;
                    button.classList.add('is-loading');
                }

                setMessage(form, '', config.sendingText || 'در حال ارسال...');

                window.fetch(config.ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data
                }).then(function (response) {
                    return response.json();
                }).then(function (response) {
                    if (response && response.success) {
                        form.reset();
                        setMessage(form, 'success', (response.data && response.data.message) || config.successText || 'درخواست شما ارسال شد.');
                        return;
                    }

                    setMessage(form, 'error', (response && response.data && response.data.message) || config.errorText || 'خطایی رخ داد. دوباره تلاش کنید.');
                }).catch(function () {
                    setMessage(form, 'error', config.errorText || 'خطایی رخ داد. دوباره تلاش کنید.');
                }).finally(function () {
                    if (button) {
                        button.disabled = false;
                        button.classList.remove('is-loading');
                    }
                });
            });
        });
    }

    function initScope(scope) {
        initTabs(scope);
        initGallery(scope);
        initForms(scope);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initScope(document);
        });
    } else {
        initScope(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
                return;
            }

            [
                'webmz-image-gallery-swiper.default',
                'webmz-contact-tabs.default',
                'webmz-ajax-contact-form.default'
            ].forEach(function (hook) {
                window.elementorFrontend.hooks.addAction('frontend/element_ready/' + hook, function ($scope) {
                    initScope($scope[0]);
                });
            });
        });
    }
})();
