(function () {
    'use strict';

    function qs(root, selector) {
        return root ? root.querySelector(selector) : null;
    }

    function qsa(root, selector) {
        return root ? Array.prototype.slice.call(root.querySelectorAll(selector)) : [];
    }

    function setMessage(widget, text, type) {
        var message = qs(widget, '[data-webmz-comments-message]');
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
        var counter = qs(widget, '[data-webmz-comments-count]');
        if (counter && typeof count !== 'undefined') {
            counter.textContent = count;
        }
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
        formData.append('action', action);
        formData.append('nonce', window.webmzCommentsWidget ? window.webmzCommentsWidget.nonce : '');

        Object.keys(payload || {}).forEach(function (key) {
            formData.append(key, payload[key]);
        });

        return fetch(window.webmzCommentsWidget.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        }).then(function (response) {
            return response.json();
        });
    }

    function initWidget(widget) {
        var postId = widget.getAttribute('data-post-id');
        var list = qs(widget, '[data-webmz-comments-list]');
        var form = qs(widget, '[data-webmz-comments-form]');
        var parentInput = qs(widget, '[data-webmz-comment-parent]');
        var replying = qs(widget, '[data-webmz-replying]');
        var submitButton = form ? qs(form, '.webmz-comments-submit') : null;

        if (!postId) {
            return;
        }

        widget.addEventListener('click', function (event) {
            var replyButton = event.target.closest('.webmz-comment-reply-button');
            var cancelReply = event.target.closest('[data-webmz-cancel-reply]');
            var voteButton = event.target.closest('.webmz-comment-vote-button');

            if (replyButton && form && parentInput) {
                event.preventDefault();
                parentInput.value = replyButton.getAttribute('data-comment-id') || '0';

                if (replying) {
                    var span = qs(replying, 'span');
                    if (span) {
                        span.textContent = (window.webmzCommentsWidget.replyingTo || 'در حال پاسخ به') + ' ' + (replyButton.getAttribute('data-author') || '');
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

                postAjax('webmz_comments_widget_vote', {
                    comment_id: voteButton.getAttribute('data-comment-id'),
                    vote_type: voteButton.getAttribute('data-vote-type')
                }).then(function (result) {
                    if (!result || !result.success) {
                        return;
                    }

                    var data = result.data || {};
                    var comment = voteButton.closest('.webmz-comment-chat-item');
                    if (!comment) {
                        return;
                    }

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
                payload.post_id = postId;

                if (submitButton) {
                    submitButton.classList.add('is-loading');
                    submitButton.disabled = true;
                }

                setMessage(widget, window.webmzCommentsWidget.sending || 'در حال ارسال...', '');

                postAjax('webmz_comments_widget_submit', payload).then(function (result) {
                    var data = result && result.data ? result.data : {};

                    if (!result || !result.success) {
                        setMessage(widget, data.message || (window.webmzCommentsWidget.error || 'خطایی رخ داد.'), 'error');
                        return;
                    }

                    if (list && data.html) {
                        list.innerHTML = data.html;
                    }
                    updateCount(widget, data.count);
                    form.reset();
                    if (parentInput) {
                        parentInput.value = '0';
                    }
                    if (replying) {
                        replying.hidden = true;
                    }
                    setMessage(widget, data.message || (window.webmzCommentsWidget.sent || 'دیدگاه ثبت شد.'), 'success');
                }).catch(function () {
                    setMessage(widget, window.webmzCommentsWidget.error || 'خطایی رخ داد.', 'error');
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
        qsa(root || document, '[data-webmz-comments-widget]').forEach(initWidget);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);
    });

    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/webmz_comments_widget.default', function ($scope) {
            var scope = $scope && $scope[0] ? $scope[0] : document;
            initAll(scope);
        });
    }
}());
