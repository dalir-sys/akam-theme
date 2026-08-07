(function () {
    'use strict';

    function syncTocLinks(root) {
        var contentSelector = root.getAttribute('data-toc-content') || '.webmz-dynamic-content';
        var content = document.querySelector(contentSelector);

        if (!content) {
            return;
        }

        var tags = (root.getAttribute('data-toc-tags') || '')
            .split(',')
            .map(function (tag) {
                return tag.trim();
            })
            .filter(Boolean);

        if (!tags.length) {
            return;
        }

        var headings = content.querySelectorAll(tags.join(','));
        var links = root.querySelectorAll('[data-webmz-toc-link]');

        links.forEach(function (link, index) {
            var heading = headings[index];

            if (!heading) {
                return;
            }

            var id = heading.id;

            if (!id) {
                id = 'webmz-toc-' + index;
                heading.id = id;
            }

            link.setAttribute('href', '#' + id);
        });
    }

    function scrollToTarget(root, link) {
        var href = link.getAttribute('href');

        if (!href || href.charAt(0) !== '#') {
            return;
        }

        var target = document.querySelector(href);

        if (!target) {
            return;
        }

        var offset = parseInt(root.getAttribute('data-toc-offset'), 10);

        if (isNaN(offset)) {
            offset = 80;
        }

        var top = target.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
            top: top,
            behavior: 'smooth'
        });

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', href);
        }
    }

    function initToc(root) {
        syncTocLinks(root);

        var toggle = root.querySelector('[data-webmz-toc-toggle]');

        if (toggle) {
            toggle.addEventListener('click', function () {
                var expanded = toggle.getAttribute('aria-expanded') === 'true';

                toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                root.classList.toggle('is-collapsed', expanded);
            });
        }

        root.addEventListener('click', function (event) {
            var link = event.target.closest('[data-webmz-toc-link]');

            if (!link) {
                return;
            }

            event.preventDefault();
            scrollToTarget(root, link);
        });
    }

    function boot() {
        document.querySelectorAll('[data-webmz-post-toc]').forEach(initToc);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
}());
