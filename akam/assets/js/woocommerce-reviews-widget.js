(function () {
    'use strict';

    function qs(root, selector) {
        return root ? root.querySelector(selector) : null;
    }

    function qsa(root, selector) {
        return root ? Array.prototype.slice.call(root.querySelectorAll(selector)) : [];
    }

    function setMessage(widget, text, type) {
        var message = qs(widget, '[data-webmz-wc-reviews-message]');
        if (!message) {
            return;
        }
        message.textContent = text || '';
        message.classList.remove('is-success', 'is-error');
        if (type) {
            message.classList.add('is-' + type);
        }
    }

    function updateCount(widget, count) {
        var counter = qs(widget, '[data-webmz-wc-reviews-count]');
        if (counter && typeof count !== 'undefined') {
            counter.textContent = count;
        }
    }

    function updateAverage(widget, average) {
        var averageWrap = qs(widget, '[data-webmz-wc-average-rating]');
        if (!averageWrap || typeof average === 'undefined') {
            return;
        }

        var strong = qs(averageWrap, 'strong');
        if (strong) {
            strong.textContent = Number(average).toFixed(1);
        }

        var stars = qsa(averageWrap, '.webmz-wc-review-star');
        stars.forEach(function (star, index) {
            star.classList.toggle('is-filled', average >= index + 1);
        });
    }

    function serializeForm(form) {
        var data = new FormData(form);
        var payload = {};
        data.forEach(function (value, key) {
            payload[key] = value;
        });
        return payload;
    }

    function postAjax(action, payload) {
        var formData = new FormData();
        var config = window.webmzWcReviewsWidget || {};

        formData.append('action', action);
        formData.append('nonce', config.nonce || '');

        Object.keys(payload || {}).forEach(function (key) {
            formData.append(key, payload[key]);
        });

        return fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        }).then(function (response) {
            return response.json();
        });
    }

    function setRating(widget, value) {
        var ratingInput = qs(widget, '[data-webmz-wc-review-rating]');
        var ratingField = qs(widget, '[data-webmz-wc-rating-field]');
        var rating = parseInt(value, 10) || 0;

        if (ratingInput) {
            ratingInput.value = String(rating);
        }

        if (!ratingField) {
            return;
        }

        qsa(ratingField, '.webmz-wc-review-rating-star').forEach(function (button) {
            var starValue = parseInt(button.getAttribute('data-rating-value'), 10) || 0;
            button.classList.toggle('is-active', rating >= starValue);
        });
    }

    function toggleRatingField(widget, show) {
        var ratingField = qs(widget, '[data-webmz-wc-rating-field]');
        if (!ratingField) {
            return;
        }
        ratingField.hidden = !show;
        if (!show) {
            setRating(widget, 0);
        }
    }

    function setRatingHover(ratingField, value) {
        var hoverValue = parseInt(value, 10) || 0;

        qsa(ratingField, '.webmz-wc-review-rating-star').forEach(function (button) {
            var starValue = parseInt(button.getAttribute('data-rating-value'), 10) || 0;
            button.classList.toggle('is-hover', hoverValue > 0 && starValue <= hoverValue);
        });
    }

    function clearRatingHover(ratingField) {
        qsa(ratingField, '.webmz-wc-review-rating-star').forEach(function (button) {
            button.classList.remove('is-hover');
        });
    }

    function initRatingHover(widget) {
        var ratingField = qs(widget, '[data-webmz-wc-rating-field]');
        var ratingInput = ratingField ? qs(ratingField, '.webmz-wc-review-rating-input') : null;

        if (!ratingField || !ratingInput) {
            return;
        }

        ratingInput.addEventListener('mouseover', function (event) {
            var star = event.target.closest('.webmz-wc-review-rating-star');
            if (!star || !ratingInput.contains(star)) {
                return;
            }
            setRatingHover(ratingField, star.getAttribute('data-rating-value'));
        });

        ratingInput.addEventListener('mouseleave', function () {
            clearRatingHover(ratingField);
        });
    }

    function initWidget(widget) {
        var productId = widget.getAttribute('data-product-id');
        var list = qs(widget, '[data-webmz-wc-reviews-list]');
        var form = qs(widget, '[data-webmz-wc-reviews-form]');
        var parentInput = qs(widget, '[data-webmz-wc-review-parent]');
        var replying = qs(widget, '[data-webmz-wc-replying]');
        var submitButton = form ? qs(form, '.webmz-comments-submit') : null;
        var ratingsEnabled = widget.getAttribute('data-ratings-enabled') === '1';
        var ratingRequired = widget.getAttribute('data-rating-required') === '1';
        var config = window.webmzWcReviewsWidget || {};

        if (!productId) {
            return;
        }

        initRatingHover(widget);

        widget.addEventListener('click', function (event) {
            var replyButton = event.target.closest('.webmz-comment-reply-button');
            var cancelReply = event.target.closest('[data-webmz-wc-cancel-reply]');
            var voteButton = event.target.closest('.webmz-comment-vote-button');
            var ratingButton = event.target.closest('.webmz-wc-review-rating-star');

            if (ratingButton) {
                event.preventDefault();
                setRating(widget, ratingButton.getAttribute('data-rating-value'));
                return;
            }

            if (replyButton && form && parentInput) {
                event.preventDefault();
                parentInput.value = replyButton.getAttribute('data-comment-id') || '0';
                toggleRatingField(widget, false);

                if (replying) {
                    var span = qs(replying, 'span');
                    if (span) {
                        span.textContent = (config.replyingTo || 'در حال پاسخ به') + ' ' + (replyButton.getAttribute('data-author') || '');
                    }
                    replying.hidden = false;
                }

                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                var textarea = qs(form, 'textarea[name="content"]');
                if (textarea) {
                    setTimeout(function () {
                        textarea.focus();
                    }, 350);
                }
                return;
            }

            if (cancelReply && parentInput) {
                event.preventDefault();
                parentInput.value = '0';
                toggleRatingField(widget, ratingsEnabled);
                if (replying) {
                    replying.hidden = true;
                }
                return;
            }

            if (voteButton) {
                event.preventDefault();

                if (voteButton.classList.contains('is-loading')) {
                    return;
                }

                voteButton.classList.add('is-loading');

                postAjax('webmz_wc_reviews_widget_vote', {
                    comment_id: voteButton.getAttribute('data-comment-id'),
                    vote_type: voteButton.getAttribute('data-vote-type')
                }).then(function (result) {
                    if (!result || !result.success) {
                        return;
                    }

                    var data = result.data || {};
                    var actions = voteButton.closest('.webmz-comment-chat-actions');
                    qsa(actions, '.webmz-comment-vote-button').forEach(function (button) {
                        var type = button.getAttribute('data-vote-type');
                        var strong = qs(button, 'strong');
                        button.classList.toggle('is-active', data.user_vote === type);
                        if (strong && typeof data[type] !== 'undefined') {
                            strong.textContent = data[type];
                        }
                    });
                }).finally(function () {
                    voteButton.classList.remove('is-loading');
                });
            }
        });

        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (submitButton && submitButton.classList.contains('is-loading')) {
                    return;
                }

                var payload = serializeForm(form);
                payload.product_id = productId;

                var isReply = parseInt(payload.parent, 10) > 0;
                if (!isReply && ratingsEnabled && ratingRequired && (!payload.rating || parseInt(payload.rating, 10) < 1)) {
                    setMessage(widget, config.ratingRequired || 'لطفاً امتیاز خود را انتخاب کنید.', 'error');
                    return;
                }

                if (submitButton) {
                    submitButton.classList.add('is-loading');
                    submitButton.disabled = true;
                }

                setMessage(widget, config.sending || 'در حال ارسال...', '');

                postAjax('webmz_wc_reviews_widget_submit', payload).then(function (result) {
                    var data = result && result.data ? result.data : {};

                    if (!result || !result.success) {
                        setMessage(widget, data.message || (config.error || 'خطایی رخ داد.'), 'error');
                        return;
                    }

                    if (list && data.html) {
                        list.innerHTML = data.html;
                    }
                    updateCount(widget, data.count);
                    updateAverage(widget, data.average_rating);
                    form.reset();
                    setRating(widget, 0);
                    toggleRatingField(widget, ratingsEnabled);
                    if (parentInput) {
                        parentInput.value = '0';
                    }
                    if (replying) {
                        replying.hidden = true;
                    }
                    setMessage(widget, data.message || (config.sent || 'نظر ثبت شد.'), 'success');
                }).catch(function () {
                    setMessage(widget, config.error || 'خطایی رخ داد.', 'error');
                }).finally(function () {
                    if (submitButton) {
                        submitButton.classList.remove('is-loading');
                        submitButton.disabled = false;
                    }
                });
            });
        }
    }

    function initAll(root) {
        qsa(root || document, '[data-webmz-wc-reviews-widget]').forEach(initWidget);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);
    });

    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz_wc_reviews_widget.default', function ($scope) {
            var scope = $scope && $scope[0] ? $scope[0] : document;
            initAll(scope);
        });
    }
}());
