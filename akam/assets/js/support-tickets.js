(function ($) {
    'use strict';

    function frontendCfg() {
        return window.webmzSupportTickets || {};
    }

    function adminCfg() {
        return window.webmzSupportTicketsAdmin || {};
    }

    function attachmentCfg() {
        var cfg = frontendCfg();

        if (cfg.attachments && cfg.attachments.enabled) {
            return cfg.attachments;
        }

        var admin = adminCfg();

        return admin.attachments || {};
    }

    function getFileExtension(filename) {
        var parts = String(filename || '').toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function formatFileSize(bytes) {
        if (!bytes) {
            return '';
        }

        if (bytes < 1024) {
            return bytes + ' B';
        }

        if (bytes < 1024 * 1024) {
            return Math.round(bytes / 1024) + ' KB';
        }

        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function validateSelectedFile(file, cfg) {
        var labels = cfg.labels || {};
        var extensions = cfg.extensions || [];
        var ext = getFileExtension(file.name);

        if (extensions.indexOf(ext) === -1) {
            return labels.invalidType || 'فرمت فایل مجاز نیست.';
        }

        if (file.size > cfg.maxSize) {
            return labels.tooLarge || 'حجم فایل بیش از حد مجاز است.';
        }

        return '';
    }

    function getUploadContainer(uploadBox) {
        return uploadBox.closest('[data-webmz-ticket-admin-reply-wrap]')
            || uploadBox.closest('[data-webmz-ticket-form]');
    }

    function setMessage(container, message, type) {
        var box = container
            ? (container.querySelector('[data-webmz-ticket-form-message]') || container.querySelector('[data-webmz-ticket-admin-status]'))
            : null;

        if (!box) {
            return;
        }

        if (box.hasAttribute('data-webmz-ticket-admin-status')) {
            box.className = 'webmz-ticket-admin-reply__message is-' + (type || 'info');
        } else {
            box.className = 'webmz-ticket-form__message is-' + (type || 'info');
        }

        box.textContent = message || '';
    }

    function appendContainerUploads(container, formData) {
        if (!container || !formData) {
            return;
        }

        container.querySelectorAll('[data-webmz-ticket-upload]').forEach(function (uploadBox) {
            if (typeof uploadBox.webmzGetSelectedFiles !== 'function') {
                return;
            }

            uploadBox.webmzGetSelectedFiles().forEach(function (file) {
                formData.append('attachments[]', file, file.name);
            });
        });
    }

    function resetUploadContainer(container) {
        if (!container) {
            return;
        }

        container.dispatchEvent(new CustomEvent('webmz-ticket-reset-upload', {
            bubbles: false
        }));

        container.querySelectorAll('[data-webmz-ticket-upload]').forEach(function (uploadBox) {
            if (typeof uploadBox.webmzResetUpload === 'function') {
                uploadBox.webmzResetUpload();
            }
        });
    }

    function initTicketUpload(uploadBox) {
        if (!uploadBox || uploadBox.dataset.webmzUploadReady === '1') {
            return;
        }

        var cfg = attachmentCfg();
        if (!cfg.enabled) {
            return;
        }

        var dropzone = uploadBox.querySelector('[data-webmz-ticket-dropzone]');
        var fileInput = uploadBox.querySelector('[data-webmz-ticket-file-input]');
        var list = uploadBox.querySelector('[data-webmz-ticket-upload-list]');
        var container = getUploadContainer(uploadBox);
        var selectedFiles = [];

        if (!dropzone || !fileInput || !list || !container) {
            return;
        }

        uploadBox.dataset.webmzUploadReady = '1';

        function renderList() {
            var labels = cfg.labels || {};
            list.innerHTML = '';

            selectedFiles.forEach(function (file) {
                var item = document.createElement('li');
                item.className = 'webmz-ticket-upload__item';

                var name = document.createElement('span');
                name.className = 'webmz-ticket-upload__item-name';
                name.textContent = file.name + ' (' + formatFileSize(file.size) + ')';

                var remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'webmz-ticket-upload__item-remove';
                remove.textContent = labels.remove || 'حذف';
                remove.addEventListener('click', function () {
                    var fileIndex = selectedFiles.indexOf(file);

                    if (fileIndex > -1) {
                        selectedFiles.splice(fileIndex, 1);
                    }

                    renderList();
                });

                item.appendChild(name);
                item.appendChild(remove);
                list.appendChild(item);
            });
        }

        function addFiles(fileList) {
            var labels = cfg.labels || {};
            var files = Array.prototype.slice.call(fileList || []);

            files.forEach(function (file) {
                if (selectedFiles.length >= cfg.maxFiles) {
                    return;
                }

                var duplicate = selectedFiles.some(function (existing) {
                    return existing.name === file.name && existing.size === file.size && existing.lastModified === file.lastModified;
                });

                if (duplicate) {
                    return;
                }

                var error = validateSelectedFile(file, cfg);
                if (error) {
                    setMessage(container, error, 'error');
                    return;
                }

                selectedFiles.push(file);
            });

            if (selectedFiles.length > cfg.maxFiles) {
                selectedFiles = selectedFiles.slice(0, cfg.maxFiles);
                setMessage(container, labels.tooMany || 'تعداد فایل بیش از حد مجاز است.', 'error');
            }

            renderList();
        }

        function resetFiles() {
            selectedFiles = [];
            fileInput.value = '';
            renderList();
        }

        uploadBox.webmzResetUpload = resetFiles;
        uploadBox.webmzGetSelectedFiles = function () {
            return selectedFiles.slice();
        };

        dropzone.addEventListener('click', function () {
            fileInput.click();
        });

        dropzone.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                fileInput.click();
            }
        });

        fileInput.addEventListener('change', function () {
            addFiles(fileInput.files);
            fileInput.value = '';
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', function (event) {
            addFiles(event.dataTransfer ? event.dataTransfer.files : []);
        });

        container.addEventListener('webmz-ticket-reset-upload', resetFiles);
    }

    function initAllTicketUploads() {
        document.querySelectorAll('[data-webmz-ticket-upload]').forEach(initTicketUpload);
    }

    function scrollTicketMessagesToBottom(container) {
        var lists;

        if (container && container.matches && container.matches('[data-webmz-ticket-messages]')) {
            lists = [container];
        } else {
            lists = (container || document).querySelectorAll('[data-webmz-ticket-messages]');
        }

        lists.forEach(function (list) {
            list.scrollTop = list.scrollHeight;
        });
    }

    function initTicketMessagesScroll() {
        scrollTicketMessagesToBottom();
        requestAnimationFrame(scrollTicketMessagesToBottom);
    }

    function setLoading(button, isLoading, text) {
        if (!button) {
            return;
        }

        if (isLoading) {
            button.dataset.originalText = button.textContent;
            button.textContent = text || 'در حال ارسال...';
            button.disabled = true;
            button.classList.add('is-loading');
            button.setAttribute('aria-busy', 'true');
        } else {
            button.textContent = button.dataset.originalText || button.textContent;
            button.disabled = false;
            button.classList.remove('is-loading');
            button.removeAttribute('aria-busy');
        }
    }

    function submitFrontendForm(form) {
        var cfg = frontendCfg();
        var type = form.getAttribute('data-webmz-ticket-form');
        var button = form.querySelector('[type="submit"]');
        var formData = new FormData(form);

        appendContainerUploads(form, formData);

        formData.append('nonce', cfg.nonce || '');
        formData.append('action', type === 'reply' ? 'webmz_ticket_reply' : 'webmz_ticket_create');

        setLoading(button, true, cfg.sending || 'در حال ارسال...');
        setMessage(form, '', 'info');

        $.ajax({
            url: cfg.ajaxUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (response) {
            if (!response || !response.success) {
                setMessage(form, response && response.data ? response.data.message : (cfg.errorMessage || 'خطایی رخ داد.'), 'error');
                return;
            }

            setMessage(form, response.data.message || 'ارسال شد.', 'success');

            if (type === 'create' && response.data.ticket_url) {
                window.location.href = response.data.ticket_url;
                return;
            }

            if (type === 'create' || type === 'reply') {
                if (type === 'reply') {
                    var list = document.querySelector('[data-webmz-ticket-messages]');
                    var textarea = form.querySelector('textarea[name="message"]');

                    if (list && response.data.html) {
                        list.insertAdjacentHTML('beforeend', response.data.html);
                        scrollTicketMessagesToBottom(list);
                    }

                    if (textarea) {
                        textarea.value = '';
                    }
                }

                resetUploadContainer(form);
            }
        }).fail(function (xhr) {
            var response = xhr.responseJSON;
            setMessage(form, response && response.data ? response.data.message : (cfg.errorMessage || 'خطایی رخ داد.'), 'error');
        }).always(function () {
            setLoading(button, false);
        });
    }

    function submitAdminReply(button) {
        var cfg = adminCfg();
        var box = button.closest('[data-webmz-admin-ticket]');

        if (!box) {
            return;
        }

        var ticketId = box.getAttribute('data-webmz-admin-ticket');
        var textarea = box.querySelector('[data-webmz-ticket-admin-message]');
        var close = box.querySelector('[data-webmz-ticket-close-after-reply]');
        var status = box.querySelector('[data-webmz-ticket-admin-status]');
        var list = box.querySelector('[data-webmz-ticket-messages]');
        var replyWrap = box.querySelector('[data-webmz-ticket-admin-reply-wrap]');
        var message = textarea ? textarea.value : '';
        var formData = new FormData();

        formData.append('action', 'webmz_ticket_admin_reply');
        formData.append('nonce', cfg.nonce || '');
        formData.append('ticket_id', ticketId);
        formData.append('message', message);
        formData.append('close_ticket', close && close.checked ? '1' : '0');

        if (replyWrap) {
            appendContainerUploads(replyWrap, formData);
        }

        status.textContent = '';
        setLoading(button, true, cfg.sending || 'در حال ارسال...');

        $.ajax({
            url: cfg.ajaxUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (response) {
            if (!response || !response.success) {
                status.className = 'webmz-ticket-admin-reply__message is-error';
                status.textContent = response && response.data ? response.data.message : (cfg.errorMessage || 'خطایی رخ داد.');
                return;
            }

            status.className = 'webmz-ticket-admin-reply__message is-success';
            status.textContent = response.data.message || 'پاسخ ارسال شد.';

            if (list && response.data.html) {
                list.insertAdjacentHTML('beforeend', response.data.html);
                scrollTicketMessagesToBottom(list);
            }

            if (textarea) {
                textarea.value = '';
            }

            if (replyWrap) {
                resetUploadContainer(replyWrap);
            }
        }).fail(function (xhr) {
            var response = xhr.responseJSON;
            status.className = 'webmz-ticket-admin-reply__message is-error';
            status.textContent = response && response.data ? response.data.message : (cfg.errorMessage || 'خطایی رخ داد.');
        }).always(function () {
            setLoading(button, false);
        });
    }

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('[data-webmz-ticket-form]');

        if (!form) {
            return;
        }

        event.preventDefault();
        submitFrontendForm(form);
    });

    document.addEventListener('click', function (event) {
        var reply = event.target.closest('[data-webmz-ticket-admin-reply]');

        if (!reply) {
            return;
        }

        event.preventDefault();
        submitAdminReply(reply);
    });

    function bootSupportTickets() {
        initAllTicketUploads();
        initTicketMessagesScroll();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootSupportTickets);
    } else {
        bootSupportTickets();
    }

    $(bootSupportTickets);

    document.addEventListener('webmzAccountLoaded', bootSupportTickets);
}(jQuery));
