(function () {
    'use strict';

    var initFloatingContact = function () {
        var widget = document.querySelector('.webmz-floating-contact');

        if (!widget || widget.getAttribute('data-webmz-floating-ready')) {
            return;
        }

        widget.setAttribute('data-webmz-floating-ready', '1');

        var toggle = widget.querySelector('.webmz-floating-contact__toggle');
        var closeTargets = widget.querySelectorAll('[data-webmz-floating-close]');

        if (!toggle) {
            return;
        }

        var closeTimer = null;

        var setOpen = function (isOpen) {
            if (closeTimer) {
                window.clearTimeout(closeTimer);
                closeTimer = null;
            }

            if (isOpen) {
                widget.classList.remove('is-closing');
                widget.classList.add('is-open');
            } else if (widget.classList.contains('is-open')) {
                widget.classList.add('is-closing');
                widget.classList.remove('is-open');
                closeTimer = window.setTimeout(function () {
                    widget.classList.remove('is-closing');
                    closeTimer = null;
                }, 260);
            }

            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        toggle.addEventListener('click', function () {
            setOpen(!widget.classList.contains('is-open'));
        });

        closeTargets.forEach(function (target) {
            target.addEventListener('click', function () {
                setOpen(false);
            });
        });

        document.addEventListener('keydown', function (event) {
            if ('Escape' === event.key) {
                setOpen(false);
            }
        });

        document.addEventListener('click', function (event) {
            if (!widget.classList.contains('is-open') || widget.contains(event.target)) {
                return;
            }

            setOpen(false);
        });
    };

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', initFloatingContact);
    } else {
        initFloatingContact();
    }
}());
