(function ($) {
    'use strict';

    function widgetConfig() {
        return window.webmzTadrisWidgets || {};
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });
    }

    function showSweet(options) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire(options);
        }

        if (options && (options.text || options.title)) {
            window.alert(options.text || options.title);
        }

        return Promise.resolve();
    }

    function showAjaxLoading(title, text) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({
                title: title || 'در حال انجام عملیات...',
                text: text || 'لطفاً چند لحظه صبر کنید.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'webmz-swal-popup webmz-swal-popup--loading'
                },
                didOpen: function () {
                    if (window.Swal && typeof window.Swal.showLoading === 'function') {
                        window.Swal.showLoading();
                    }
                }
            });
        }

        return Promise.resolve();
    }

    function showAlreadyInCartAlert(response) {
        var cfg = widgetConfig();

        closeSweet();

        return showSweet({
            icon: 'info',
            title: cfg.cartAlreadyInCartTitle || 'محصول در سبد خرید موجود است',
            text: (response && response.message) || cfg.cartAlreadyInCartMessage || 'شما قبلاً این محصول را به سبد خریدتان اضافه کرده‌اید.',
            confirmButtonText: cfg.cartAlreadyInCartButton || 'ثبت سفارش',
            showCancelButton: true,
            cancelButtonText: cfg.closeText || 'بستن',
            customClass: {
                popup: 'webmz-swal-popup',
                confirmButton: 'webmz-swal-confirm',
                cancelButton: 'webmz-swal-cancel'
            }
        }).then(function (result) {
            if (result && result.isConfirmed) {
                window.location.href = (response && response.checkout_url) || cfg.checkoutUrl;
            }
        });
    }

    window.webmzShowAlreadyInCartAlert = showAlreadyInCartAlert;

    function closeSweet() {
        if (window.Swal && typeof window.Swal.close === 'function') {
            window.Swal.close();
        }
    }

    function shouldOpenMiniCartAfterAdd(button) {
        return button && (button.closest('.tadris-product-type-1') || button.closest('.tadris-product-type-2'));
    }

    function openMiniCartAfterAdd() {
        if (typeof window.webmzOpenHeaderCart === 'function') {
            window.webmzOpenHeaderCart();
        }
    }

    function setAjaxButtonLoading(button, isLoading) {
        if (!button) {
            return;
        }

        button.classList.toggle('is-loading', !!isLoading);
        button.disabled = !!isLoading;

        if (isLoading) {
            button.setAttribute('aria-busy', 'true');
        } else {
            button.removeAttribute('aria-busy');
        }
    }

    function updateFavoriteButton(button, isSaved) {
        var cfg = widgetConfig();
        var label = button.querySelector('.webmz-favorite-label');

        button.classList.toggle('is-saved', !!isSaved);
        button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');

        if (label) {
            label.textContent = isSaved
                ? (button.getAttribute('data-remove-label') || cfg.removeSavedText || 'حذف از ذخیره')
                : (button.getAttribute('data-save-label') || cfg.saveText || 'ذخیره');
        }
    }

    function promptLoginForFavorite(cfg) {
        var openOtp = function () {
            if (typeof window.webmzOtpOpen === 'function') {
                window.webmzOtpOpen();
                return;
            }
            document.dispatchEvent(new CustomEvent('webmz:login-required'));
        };

        return showSweet({
            icon: 'info',
            title: cfg.favoriteLoginTitle || 'برای ذخیره باید وارد شوید',
            text: cfg.favoriteLoginText || 'برای ذخیره این محتوا ابتدا وارد حساب کاربری شوید یا ثبت‌نام کنید.',
            confirmButtonText: cfg.favoriteLoginButton || 'ورود / عضویت',
            showCancelButton: true,
            cancelButtonText: cfg.closeText || 'بستن',
            customClass: {
                popup: 'webmz-swal-popup',
                confirmButton: 'webmz-swal-confirm',
                cancelButton: 'webmz-swal-cancel'
            }
        }).then(function (result) {
            if (result && result.isConfirmed) {
                if (window.Swal && typeof window.Swal.close === 'function') {
                    window.Swal.close();
                }
                setTimeout(openOtp, 80);
            }
        });
    }

    function promptLoginForDownload(cfg) {
        var redirect = (window.location.href || '').split('#')[0];
        var openOtp = function () {
            if (typeof window.webmzOtpOpen === 'function') {
                window.webmzOtpOpen(redirect);
                return;
            }
            document.dispatchEvent(new CustomEvent('webmz:login-required', { detail: { redirect: redirect } }));
        };

        return showSweet({
            icon: 'info',
            title: cfg.downloadLoginTitle || 'برای دانلود باید وارد شوید',
            text: cfg.downloadLoginText || 'جهت دانلود باید لاگین کنید.',
            confirmButtonText: cfg.downloadLoginButton || 'ورود / عضویت',
            showCancelButton: true,
            cancelButtonText: cfg.closeText || 'بستن',
            customClass: {
                popup: 'webmz-swal-popup',
                confirmButton: 'webmz-swal-confirm',
                cancelButton: 'webmz-swal-cancel'
            }
        }).then(function (result) {
            if (result && result.isConfirmed) {
                if (window.Swal && typeof window.Swal.close === 'function') {
                    window.Swal.close();
                }
                setTimeout(openOtp, 80);
            }
        });
    }

    function toggleFavorite(button) {
        var cfg = widgetConfig();

        if (!cfg.ajaxUrl || !cfg.nonce || button.disabled) {
            return;
        }

        var isCurrentlySaved = button.classList.contains('is-saved') || button.getAttribute('aria-pressed') === 'true';

        setAjaxButtonLoading(button, true);

        showAjaxLoading(
            isCurrentlySaved
                ? (cfg.favoriteRemovingTitle || 'در حال حذف از ذخیره‌ها...')
                : (cfg.favoriteLoadingTitle || 'در حال ذخیره...'),
            cfg.loadingText || 'لطفاً چند لحظه صبر کنید.'
        );

        $.ajax({
            url: cfg.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'webmz_tadris_toggle_favorite',
                nonce: cfg.nonce,
                post_id: button.getAttribute('data-webmz-favorite-post')
            }
        }).done(function (response) {
            if (response && response.success) {
                var isSaved = !!response.data.active;

                updateFavoriteButton(button, isSaved);

                showSweet({
                    icon: isSaved ? 'success' : 'info',
                    title: isSaved
                        ? (cfg.favoriteSavedTitle || cfg.savedTitle || 'ذخیره شد')
                        : (cfg.favoriteRemovedTitle || 'حذف شد'),
                    text: response.data.message || '',
                    confirmButtonText: isSaved
                        ? (cfg.viewSavedText || 'مشاهده ذخیره‌ها')
                        : (cfg.closeText || 'بستن'),
                    showCancelButton: isSaved,
                    cancelButtonText: cfg.closeText || 'بستن',
                    customClass: {
                        popup: 'webmz-swal-popup',
                        confirmButton: 'webmz-swal-confirm',
                        cancelButton: 'webmz-swal-cancel'
                    }
                }).then(function (result) {
                    if (isSaved && result && result.isConfirmed && cfg.savedVideosUrl) {
                        window.location.href = cfg.savedVideosUrl;
                    }
                });

                return;
            }

            if (response && response.data && response.data.message && (response.data.message === cfg.loginMessage || /وارد/.test(response.data.message))) {
                promptLoginForFavorite(cfg);
                return;
            }

            showSweet({
                icon: 'error',
                title: cfg.savedTitle || 'ذخیره ویدیو',
                text: response && response.data ? response.data.message : cfg.loginMessage,
                confirmButtonText: cfg.closeText || 'بستن',
                customClass: {
                    popup: 'webmz-swal-popup',
                    confirmButton: 'webmz-swal-confirm'
                }
            });
        }).fail(function (xhr) {
            var response = xhr.responseJSON;
            var message = response && response.data && response.data.message
                ? response.data.message
                : (cfg.loginMessage || 'ابتدا وارد سایت شوید.');

            if (xhr.status === 401 || /وارد/.test(message)) {
                promptLoginForFavorite(cfg);
                return;
            }

            showSweet({
                icon: 'error',
                title: cfg.savedTitle || 'ذخیره ویدیو',
                text: message,
                confirmButtonText: cfg.closeText || 'بستن',
                customClass: {
                    popup: 'webmz-swal-popup',
                    confirmButton: 'webmz-swal-confirm'
                }
            });
        }).always(function () {
            setAjaxButtonLoading(button, false);
        });
    }

    function setupCounters(scope) {
        var counters = scope.querySelectorAll
            ? scope.querySelectorAll('.tadris-counter-number[data-count-end]')
            : [];

        counters.forEach(function (counter) {
            if (counter.getAttribute('data-count-ready') === 'yes') {
                return;
            }

            counter.setAttribute('data-count-ready', 'yes');

            var animate = function () {
                if (counter.getAttribute('data-count-done') === 'yes') {
                    return;
                }

                counter.setAttribute('data-count-done', 'yes');

                var start = parseInt(counter.getAttribute('data-count-start') || '0', 10);
                var end = parseInt(counter.getAttribute('data-count-end') || '0', 10);
                var duration = parseInt(counter.getAttribute('data-count-duration') || '1400', 10);
                var prefix = counter.getAttribute('data-count-prefix') || '';
                var suffix = counter.getAttribute('data-count-suffix') || '';
                var begin = window.performance.now();

                var frame = function (now) {
                    var progress = Math.min(1, (now - begin) / duration);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    var value = Math.round(start + ((end - start) * eased));

                    counter.textContent = prefix + value.toLocaleString('fa-IR') + suffix;

                    if (progress < 1) {
                        window.requestAnimationFrame(frame);
                    }
                };

                window.requestAnimationFrame(frame);
            };

            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            animate();
                            observer.disconnect();
                        }
                    });
                }, { threshold: 0.25 });

                observer.observe(counter);
            } else {
                animate();
            }
        });
    }

    function getPlyrBaseOptions(player) {
        var options = {};
        if (window.webmzTadrisWidgets && window.webmzTadrisWidgets.plyrIconUrl) {
            options.iconUrl = window.webmzTadrisWidgets.plyrIconUrl;
            options.loadSprite = true;
        }
        return options;
    }

    function setupPlayers(scope) {
        if (typeof window.Plyr === 'undefined') {
            return;
        }

        var players = scope.querySelectorAll
            ? scope.querySelectorAll('.tadris-player-tag, .tadris-sound-tag')
            : [];

        players.forEach(function (player) {
            if (player.closest('.webmz-pp') || player.closest('.webmz-ytp')) {
                return;
            }

            if (player.getAttribute('data-tadris-player-ready') === 'yes') {
                return;
            }

            player.setAttribute('data-tadris-player-ready', 'yes');

            /**
             * Podcast loop audio player controls.
             * Only applies to podcast widget cards.
             */
            if (player.classList.contains('tadris-sound-tag') && player.closest('.tadris-podcast-type-1')) {
                new window.Plyr(player, Object.assign(getPlyrBaseOptions(player), {
                    controls: [
                        'play',
                        'progress',
                        'current-time',
                        'duration'
                    ]
                }));

                return;
            }

            /**
             * Other video/audio players keep default Plyr controls.
             */
            new window.Plyr(player, getPlyrBaseOptions(player));
        });
    }

    function setupProductSliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = scope.querySelectorAll
            ? scope.querySelectorAll('.tadris-products-swiper')
            : [];

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                return;
            }

            var desktop = parseInt(slider.getAttribute('data-slides-desktop') || '3', 10);

            new window.Swiper(slider, {
                slidesPerView: 1.2,
                spaceBetween: 16,
                watchOverflow: true,
                pagination: {
                    el: slider.querySelector('.tadris-products-pagination'),
                    clickable: true
                },
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 18
                    },
                    992: {
                        slidesPerView: desktop,
                        spaceBetween: 24
                    }
                }
            });
        });
    }


    function setupPodcastSliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = scope.querySelectorAll
            ? scope.querySelectorAll('[data-tadris-podcast-swiper]')
            : [];

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-tadris-podcast-swiper') || '{}');
            } catch (error) {
                config = {};
            }

            var options = {
                slidesPerView: config.slidesMobile || 1,
                spaceBetween: config.spaceBetween || 24,
                watchOverflow: true,
                loop: !!config.loop,
                breakpoints: {
                    576: {
                        slidesPerView: config.slidesTablet || 2,
                        spaceBetween: config.spaceBetween || 24
                    },
                    992: {
                        slidesPerView: config.slidesDesktop || 2,
                        spaceBetween: config.spaceBetween || 24
                    }
                }
            };

            if (config.autoplay) {
                options.autoplay = {
                    delay: config.autoplayDelay || 3500,
                    disableOnInteraction: false
                };
            }

            if (config.navigation) {
                options.navigation = {
                    nextEl: slider.querySelector('.tadris-podcast-swiper-button-next'),
                    prevEl: slider.querySelector('.tadris-podcast-swiper-button-prev')
                };
            }

            if (config.pagination) {
                options.pagination = {
                    el: slider.querySelector('.tadris-podcast-swiper-pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }


    function setupProductLoop2Sliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = scope.querySelectorAll
            ? scope.querySelectorAll('[data-tadris-product-loop-2-swiper]')
            : [];

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-tadris-product-loop-2-swiper') || '{}');
            } catch (error) {
                config = {};
            }

            var options = {
                slidesPerView: config.slidesMobile || 1,
                spaceBetween: config.spaceBetween || 24,
                watchOverflow: true,
                loop: !!config.loop,
                breakpoints: {
                    576: {
                        slidesPerView: config.slidesTablet || 2,
                        spaceBetween: config.spaceBetween || 24
                    },
                    992: {
                        slidesPerView: config.slidesDesktop || 3,
                        spaceBetween: config.spaceBetween || 24
                    }
                }
            };

            if (config.autoplay) {
                options.autoplay = {
                    delay: config.autoplayDelay || 3500,
                    disableOnInteraction: false
                };
            }

            if (config.navigation) {
                options.navigation = {
                    nextEl: slider.querySelector('.tadris-products-loop-2-swiper-button-next'),
                    prevEl: slider.querySelector('.tadris-products-loop-2-swiper-button-prev')
                };
            }

            if (config.pagination) {
                options.pagination = {
                    el: slider.querySelector('.tadris-products-loop-2-swiper-pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }


    function setupProductLoop3Sliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = scope.querySelectorAll
            ? scope.querySelectorAll('[data-tadris-product-loop-3-swiper]')
            : [];

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-tadris-product-loop-3-swiper') || '{}');
            } catch (error) {
                config = {};
            }

            var options = {
                slidesPerView: config.slidesMobile || 1,
                spaceBetween: config.spaceBetween || 20,
                watchOverflow: true,
                loop: !!config.loop,
                breakpoints: {
                    576: {
                        slidesPerView: config.slidesTablet || 2,
                        spaceBetween: config.spaceBetween || 20
                    },
                    992: {
                        slidesPerView: config.slidesDesktop || 4,
                        spaceBetween: config.spaceBetween || 20
                    }
                }
            };

            if (config.autoplay) {
                options.autoplay = {
                    delay: config.autoplayDelay || 3500,
                    disableOnInteraction: false
                };
            }

            if (config.navigation) {
                options.navigation = {
                    nextEl: slider.querySelector('.tadris-products-loop-3-swiper-button-next'),
                    prevEl: slider.querySelector('.tadris-products-loop-3-swiper-button-prev')
                };
            }

            if (config.pagination) {
                options.pagination = {
                    el: slider.querySelector('.tadris-products-loop-3-swiper-pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }


    function setupBlogLoop2Sliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = scope.querySelectorAll
            ? scope.querySelectorAll('[data-tadris-blog-loop-2-swiper]')
            : [];

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-tadris-blog-loop-2-swiper') || '{}');
            } catch (error) {
                config = {};
            }

            var options = {
                slidesPerView: config.slidesMobile || 1,
                spaceBetween: config.spaceBetween || 24,
                watchOverflow: true,
                loop: !!config.loop,
                breakpoints: {
                    576: {
                        slidesPerView: config.slidesTablet || 2,
                        spaceBetween: config.spaceBetween || 24
                    },
                    992: {
                        slidesPerView: config.slidesDesktop || 3,
                        spaceBetween: config.spaceBetween || 24
                    }
                }
            };

            if (config.autoplay) {
                options.autoplay = {
                    delay: config.autoplayDelay || 3500,
                    disableOnInteraction: false
                };
            }

            if (config.navigation) {
                options.navigation = {
                    nextEl: slider.querySelector('.tadris-blog-loop-2-swiper-button-next'),
                    prevEl: slider.querySelector('.tadris-blog-loop-2-swiper-button-prev')
                };
            }

            if (config.pagination) {
                options.pagination = {
                    el: slider.querySelector('.tadris-blog-loop-2-swiper-pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }


    function setupBlogSliders(scope) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var sliders = scope.querySelectorAll
            ? scope.querySelectorAll('[data-tadris-blog-swiper]')
            : [];

        sliders.forEach(function (slider) {
            if (slider.swiper) {
                return;
            }

            var config = {};

            try {
                config = JSON.parse(slider.getAttribute('data-tadris-blog-swiper') || '{}');
            } catch (error) {
                config = {};
            }

            var options = {
                slidesPerView: config.slidesMobile || 1,
                spaceBetween: config.spaceBetween || 24,
                watchOverflow: true,
                loop: !!config.loop,
                breakpoints: {
                    576: {
                        slidesPerView: config.slidesTablet || 2,
                        spaceBetween: config.spaceBetween || 24
                    },
                    992: {
                        slidesPerView: config.slidesDesktop || 3,
                        spaceBetween: config.spaceBetween || 24
                    }
                }
            };

            if (config.autoplay) {
                options.autoplay = {
                    delay: config.autoplayDelay || 3500,
                    disableOnInteraction: false
                };
            }

            if (config.navigation) {
                options.navigation = {
                    nextEl: slider.querySelector('.tadris-blog-swiper-button-next'),
                    prevEl: slider.querySelector('.tadris-blog-swiper-button-prev')
                };
            }

            if (config.pagination) {
                options.pagination = {
                    el: slider.querySelector('.tadris-blog-swiper-pagination'),
                    clickable: true
                };
            }

            new window.Swiper(slider, options);
        });
    }

    function addSimpleCourseToCart(button) {
        var cfg = widgetConfig();

        if (!cfg.addToCartUrl || button.classList.contains('is-loading')) {
            return;
        }

        setAjaxButtonLoading(button, true);

        showAjaxLoading(
            cfg.cartLoadingTitle || 'در حال افزودن به سبد خرید...',
            cfg.loadingText || 'لطفاً چند لحظه صبر کنید.'
        );

        $.ajax({
            url: cfg.addToCartUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                product_id: button.getAttribute('data-product-id'),
                quantity: button.getAttribute('data-quantity') || 1
            }
        }).done(function (response) {
            if (response && response.error && response.already_in_cart) {
                showAlreadyInCartAlert(response);
                return;
            }

            if (response && response.error && response.product_url) {
                window.location.href = response.product_url;
                return;
            }

            if (response && response.fragments) {
                var immediateOpen = shouldOpenMiniCartAfterAdd(button);

                $(document.body).trigger(
                    'added_to_cart',
                    [response.fragments, response.cart_hash || '', $(button), { immediateOpen: immediateOpen }]
                );

                if (immediateOpen) {
                    closeSweet();
                    openMiniCartAfterAdd();
                    return;
                }

                showSweet({
                    icon: 'success',
                    title: cfg.cartAddedTitle || 'محصول به سبد خرید اضافه شد',
                    text: cfg.cartAddedMessage || 'به سبد خرید اضافه شد.',
                    confirmButtonText: cfg.checkoutButtonText || 'تسویه حساب',
                    showCancelButton: true,
                    cancelButtonText: cfg.closeText || 'بستن',
                    customClass: {
                        popup: 'webmz-swal-popup',
                        confirmButton: 'webmz-swal-confirm',
                        cancelButton: 'webmz-swal-cancel'
                    }
                }).then(function (result) {
                    if (result && result.isConfirmed && cfg.checkoutUrl) {
                        window.location.href = cfg.checkoutUrl;
                    }
                });
            }
        }).fail(function () {
            showSweet({
                icon: 'error',
                title: cfg.errorMessage || 'خطا',
                text: cfg.errorMessage || 'خطایی رخ داد.',
                confirmButtonText: cfg.closeText || 'بستن',
                customClass: {
                    popup: 'webmz-swal-popup',
                    confirmButton: 'webmz-swal-confirm'
                }
            });
        }).always(function () {
            setAjaxButtonLoading(button, false);
        });
    }

    function shareHtml(data) {
        var title = encodeURIComponent(data.title || document.title);
        var url = encodeURIComponent(data.url || window.location.href);
        var raw = escapeHtml(data.url || window.location.href);

        return '<div class="webmz-swal-share">' +
            '<a href="https://t.me/share/url?url=' + url + '&text=' + title + '" target="_blank" rel="noopener">Telegram</a>' +
            '<a href="https://wa.me/?text=' + title + '%20' + url + '" target="_blank" rel="noopener">WhatsApp</a>' +
            '<a href="https://twitter.com/intent/tweet?url=' + url + '&text=' + title + '" target="_blank" rel="noopener">X</a>' +
            '<a href="https://www.facebook.com/sharer/sharer.php?u=' + url + '" target="_blank" rel="noopener">Facebook</a>' +
            '<a href="mailto:?subject=' + title + '&body=' + url + '">Email</a>' +
            '<button type="button" data-webmz-copy-link="' + raw + '">' + escapeHtml(widgetConfig().copyText || 'کپی لینک') + '</button>' +
            '</div>';
    }

    function openShareModal(button) {
        var cfg = widgetConfig();

        var data = {
            url: button.getAttribute('data-share-url') || window.location.href,
            title: button.getAttribute('data-share-title') || document.title
        };

        showSweet({
            icon: 'info',
            title: cfg.shareTitle || 'اشتراک‌گذاری',
            html: shareHtml(data),
            showCloseButton: true,
            confirmButtonText: cfg.closeText || 'بستن',
            customClass: {
                popup: 'webmz-swal-popup webmz-swal-popup--share',
                confirmButton: 'webmz-swal-confirm'
            }
        });
    }

    function copyLink(url) {
        var cfg = widgetConfig();

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () {
                showSweet({
                    icon: 'success',
                    title: cfg.copiedMessage || 'لینک کپی شد.',
                    confirmButtonText: cfg.closeText || 'بستن',
                    timer: 1800,
                    customClass: {
                        popup: 'webmz-swal-popup',
                        confirmButton: 'webmz-swal-confirm'
                    }
                });
            });
        } else {
            window.prompt('Copy link:', url);
        }
    }


    function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    function scrollToTop(duration) {
        var start = window.pageYOffset || document.documentElement.scrollTop || 0;
        var startTime = null;
        duration = Math.max(0, parseInt(duration || 650, 10));

        if (!duration || start <= 0) {
            window.scrollTo(0, 0);
            return;
        }

        function step(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            var progress = Math.min((timestamp - startTime) / duration, 1);
            var value = start * (1 - easeInOutCubic(progress));
            window.scrollTo(0, value);

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        }

        window.requestAnimationFrame(step);
    }

    function updateBackToTopVisibility(scope) {
        var buttons = (scope || document).querySelectorAll ? (scope || document).querySelectorAll('[data-webmz-back-to-top].webmz-back-to-top--reveal, [data-webmz-back-to-top].webmz-back-to-top-2--reveal') : [];
        var scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

        buttons.forEach(function (button) {
            var threshold = parseInt(button.getAttribute('data-threshold') || '300', 10);
            button.classList.toggle('is-visible', scrollY >= threshold);
        });
    }

    function setupBackToTop(scope) {
        updateBackToTopVisibility(scope || document);
    }

    function initScope(scope) {
        setupCounters(scope);
        setupPlayers(scope);
        setupProductSliders(scope);
        setupProductLoop2Sliders(scope);
        setupProductLoop3Sliders(scope);
        setupBlogLoop2Sliders(scope);
        setupPodcastSliders(scope);
        setupBlogSliders(scope);
        setupBackToTop(scope);
    }

    window.webmzTadrisInitScope = initScope;

    document.addEventListener('click', function (event) {
        var favorite = event.target.closest('[data-webmz-favorite-post]');

        if (favorite) {
            event.preventDefault();
            toggleFavorite(favorite);
            return;
        }

        var downloadGuard = event.target.closest('[data-webmz-download-guard]');

        if (downloadGuard) {
            event.preventDefault();
            promptLoginForDownload(widgetConfig());
            return;
        }

        var shareToggle = event.target.closest('[data-webmz-share-toggle]');

        if (shareToggle) {
            event.preventDefault();
            openShareModal(shareToggle);
            return;
        }

        var copy = event.target.closest('[data-webmz-copy-link]');

        if (copy) {
            event.preventDefault();
            copyLink(copy.getAttribute('data-webmz-copy-link'));
            return;
        }

        var backToTop = event.target.closest('[data-webmz-back-to-top]');

        if (backToTop) {
            event.preventDefault();
            scrollToTop(backToTop.getAttribute('data-duration'));
            return;
        }

        var add = event.target.closest('[data-webmz-course-add]');

        if (add) {
            event.preventDefault();
            addSimpleCourseToCart(add);
        }
    });


    window.addEventListener('scroll', function () {
        updateBackToTopVisibility(document);
    }, { passive: true });

    $(function () {
        initScope(document);
    });

    $(window).on('elementor/frontend/init', function () {
        [
            'webmz-tadris-large-video.default',
            'webmz-tadris-product-loop.default',
            'webmz-tadris-product-loop-2.default',
            'webmz-tadris-product-loop-3.default',
            'webmz-tadris-tabbed-product-loop.default',
            'webmz-tadris-counter.default',
            'webmz-tadris-podcast-load.default',
            'webmz-tadris-blog-loop.default',
            'webmz-tadris-blog-loop-2.default',
            'webmz-tadris-blog-tile-loop.default',
            'webmz-tadris-back-to-top.default',
            'webmz-back-to-top-2.default',
            'webmz-post-download-box.default',
            'webmz-spw-product-media.default',
            'webmz-special-offer-slider.default'
        ].forEach(function (name) {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/' + name,
                function ($scope) {
                    initScope($scope[0]);
                }
            );
        });
    });

}(jQuery));