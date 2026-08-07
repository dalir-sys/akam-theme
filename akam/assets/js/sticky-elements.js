(function () {
    'use strict';

    function isElementorEditMode() {
        var body = document.body;

        if (body) {
            if (
                body.classList.contains('elementor-editor-active') ||
                body.classList.contains('elementor-editor-preview') ||
                body.classList.contains('elementor-edit-mode')
            ) {
                return true;
            }
        }

        if (window.elementorFrontend && typeof window.elementorFrontend.isEditMode === 'function') {
            return window.elementorFrontend.isEditMode();
        }

        return false;
    }

    var stickyItems = [];
    var stickyContainerItems = [];
    var lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    var ticking = false;
    var STICKY_HEADER_SCROLL_OFFSET = 200;

    function getScrollY() {
        return window.pageYOffset || document.documentElement.scrollTop || 0;
    }

    function getAdminBarOffset() {
        var bar = document.getElementById('wpadminbar');
        return bar ? bar.offsetHeight : 0;
    }

    function syncAdminBarOffsetVar() {
        document.documentElement.style.setProperty('--webmz-admin-bar-offset', getAdminBarOffset() + 'px');
    }

    function buildItem(element) {
        if (!element || element.getAttribute('data-webmz-sticky-ready') === '1') {
            return null;
        }

        element.setAttribute('data-webmz-sticky-ready', '1');

        var placeholder = document.createElement('div');
        placeholder.className = 'webmz-sticky-placeholder';
        element.parentNode.insertBefore(placeholder, element);

        return {
            element: element,
            placeholder: placeholder,
            hideEnabled: element.getAttribute('data-webmz-sticky-hide') === '1',
            hideDirection: element.getAttribute('data-webmz-sticky-hide-direction') === 'up' ? 'up' : 'down',
            startY: 0,
            stickyTop: null,
            documentTop: 0,
            rect: null,
            fixed: false
        };
    }

    function clearFixedStyles(item) {
        item.element.classList.remove('is-webmz-sticky', 'is-webmz-sticky-hidden', 'is-webmz-sticky-visible');
        item.element.style.width = '';
        item.element.style.left = '';
        item.element.style.right = '';
        item.element.style.top = '';
        item.element.style.removeProperty('--webmz-sticky-top');
        item.placeholder.classList.remove('is-active');
        item.fixed = false;
    }

    function measureItem(item) {
        var wasFixed = item.fixed;
        var currentY = getScrollY();

        if (wasFixed) {
            clearFixedStyles(item);
        }

        var rect = item.element.getBoundingClientRect();
        var adminOffset = getAdminBarOffset();
        var documentTop = Math.max(0, rect.top + currentY);
        var maxVisibleTop = Math.max(adminOffset, window.innerHeight - rect.height);
        var stickyTop = Math.max(adminOffset, Math.min(documentTop, maxVisibleTop));

        item.rect = rect;
        item.documentTop = documentTop;
        item.stickyTop = stickyTop;
        item.startY = Math.max(0, documentTop - stickyTop);
        item.placeholder.style.height = rect.height + 'px';

        if (wasFixed) {
            updateSticky(true);
        }
    }

    function measureAll() {
        if (isElementorEditMode()) {
            return;
        }

        syncAdminBarOffsetVar();
        stickyItems.forEach(measureItem);
        stickyContainerItems.forEach(measureContainerItem);
        updateSticky(true);
        updateStickyContainers();
    }

    function setFixed(item) {
        if (!item.fixed) {
            item.placeholder.classList.add('is-active');
            item.element.classList.add('is-webmz-sticky', 'is-webmz-sticky-visible');
            item.element.classList.remove('is-webmz-sticky-hidden');
            item.fixed = true;
        }

        var rect = item.placeholder.getBoundingClientRect();
        var stickyTop = typeof item.stickyTop === 'number' ? item.stickyTop : getAdminBarOffset();

        item.element.style.width = rect.width + 'px';
        item.element.style.left = rect.left + 'px';
        item.element.style.right = 'auto';
        item.element.style.setProperty('--webmz-sticky-top', stickyTop + 'px');
        item.element.style.setProperty('top', stickyTop + 'px', 'important');
    }

    function unsetFixed(item) {
        if (!item.fixed) {
            return;
        }

        clearFixedStyles(item);
    }

    function setVisibility(item, visible) {
        if (!item.hideEnabled || !item.fixed) {
            item.element.classList.add('is-webmz-sticky-visible');
            item.element.classList.remove('is-webmz-sticky-hidden');
            return;
        }

        if (visible) {
            item.element.classList.add('is-webmz-sticky-visible');
            item.element.classList.remove('is-webmz-sticky-hidden');
        } else {
            item.element.classList.add('is-webmz-sticky-hidden');
            item.element.classList.remove('is-webmz-sticky-visible');
        }
    }

    function updateSticky(forceVisible) {
        var currentY = getScrollY();
        var scrollingDown = currentY > lastScrollY;
        var scrollingUp = currentY < lastScrollY;

        stickyItems.forEach(function (item) {
            var activationY = Math.max(item.startY, STICKY_HEADER_SCROLL_OFFSET);

            if (currentY <= activationY || currentY <= 2) {
                unsetFixed(item);
                setVisibility(item, true);
                return;
            }

            setFixed(item);

            if (!item.hideEnabled || forceVisible) {
                setVisibility(item, true);
                return;
            }

            if ('down' === item.hideDirection) {
                setVisibility(item, !scrollingDown || scrollingUp);
            } else {
                setVisibility(item, !scrollingUp || scrollingDown);
            }
        });
    }

    function isRowColumnElement(node) {
        if (!node || !node.parentElement) {
            return false;
        }

        return (
            node.matches('.e-con.e-child, .elementor-column, .elementor-inner-column') &&
            (
                node.parentElement.classList.contains('e-con-inner') ||
                node.parentElement.classList.contains('elementor-row') ||
                node.parentElement.classList.contains('elementor-container')
            )
        );
    }

    function findColumnBoundary(element) {
        var node = element;

        while (node && node !== document.body) {
            if (isRowColumnElement(node)) {
                return node;
            }

            node = node.parentElement;
        }

        return element.closest('.e-con.e-child, .elementor-column, .elementor-inner-column') || element.parentElement || element;
    }

    function findRowBoundary(element) {
        var column = findColumnBoundary(element);

        if (column && column.parentElement) {
            if (column.parentElement.classList.contains('e-con-inner')) {
                var row = column.parentElement.parentElement;

                if (row) {
                    return row;
                }
            }

            if (column.parentElement.classList.contains('elementor-row')) {
                var section = column.parentElement.parentElement;

                if (section) {
                    return section;
                }
            }
        }

        return findSectionBoundary(element);
    }

    function findSectionBoundary(element) {
        var section = element.closest('.elementor-top-section, .elementor-section, .e-parent');

        return section || element.parentElement || element;
    }

    function findContainerBoundary(element, stayInColumn) {
        return stayInColumn ? findRowBoundary(element) : findSectionBoundary(element);
    }

    function getContentBottom(boundary, scrollY) {
        if (!boundary) {
            return scrollY;
        }

        var contentBottom = null;
        var inner = boundary.querySelector(':scope > .e-con-inner, :scope > .elementor-container');
        var contentRoot = inner || boundary;
        var children = contentRoot.children;
        var i;

        for (i = 0; i < children.length; i++) {
            var childRect = children[i].getBoundingClientRect();

            if (!childRect.height && !childRect.width) {
                continue;
            }

            var childBottom = childRect.bottom + scrollY;

            if (contentBottom === null || childBottom > contentBottom) {
                contentBottom = childBottom;
            }
        }

        if (contentBottom === null) {
            var rect = boundary.getBoundingClientRect();
            contentBottom = rect.top + scrollY + boundary.offsetHeight;
        }

        var styles = window.getComputedStyle(boundary);
        contentBottom += parseFloat(styles.paddingBottom) || 0;

        return contentBottom;
    }

    function ensureBoundaryPosition(boundary, item) {
        if (!boundary || item.boundaryPositionSet) {
            return;
        }

        var position = window.getComputedStyle(boundary).position;

        if ('static' === position) {
            boundary.classList.add('webmz-sticky-boundary');
            item.boundaryWasStatic = true;
        }

        item.boundaryPositionSet = true;
    }

    function markStickyColumnElement(element) {
        if (!isRowColumnElement(element)) {
            return;
        }

        element.classList.add('webmz-sticky-boundary-column');
        ensureBoundaryPosition(element, { boundaryPositionSet: false, boundaryWasStatic: false });
    }

    function releaseStickyOverflowAncestors(element) {
        var node = element ? element.parentElement : null;

        while (node && node !== document.body) {
            if (node.matches('.e-con, .elementor-section, .elementor-column, .elementor-inner-column')) {
                var overflow = window.getComputedStyle(node).overflow;
                var overflowY = window.getComputedStyle(node).overflowY;

                if (
                    (overflow && 'visible' !== overflow && 'clip' !== overflow) ||
                    (overflowY && 'visible' !== overflowY && 'clip' !== overflowY)
                ) {
                    node.classList.add('webmz-sticky-overflow-visible');
                }
            }

            node = node.parentElement;
        }
    }

    function prepareCssStickyContainer(element) {
        if (element.getAttribute('data-webmz-sticky-container-css-ready') === '1') {
            return;
        }

        element.setAttribute('data-webmz-sticky-container-css-ready', '1');

        var offset = parseFloat(element.getAttribute('data-webmz-sticky-container-offset') || '0') || 0;
        var column = findColumnBoundary(element);

        element.style.setProperty('--webmz-sticky-container-offset', offset + 'px');
        element.classList.add('webmz-sticky-container--stay-in-column');

        if (isRowColumnElement(element)) {
            markStickyColumnElement(element);
        }

        releaseStickyOverflowAncestors(element);
        syncAdminBarOffsetVar();
    }

    function isCssOnlyStickyContainer(element) {
        return (
            element.classList.contains('webmz-sticky-container--css-only') ||
            element.classList.contains('webmz-blog-archive__sidebar') ||
            element.classList.contains('webmz-store-archive__sidebar')
        );
    }

    function getStickyElementHeight(element, placeholder) {
        var measured = placeholder.offsetHeight || element.offsetHeight;

        if (element && isRowColumnElement(element)) {
            var inner = element.querySelector(':scope > .e-con-inner, :scope > .elementor-widget-wrap');

            if (inner && inner.offsetHeight) {
                measured = Math.min(measured || inner.offsetHeight, inner.offsetHeight);
            }
        }

        return measured;
    }

    function buildContainerItem(element) {
        if (!element || element.getAttribute('data-webmz-sticky-container-ready') === '1') {
            return null;
        }

        var stayInColumn = element.getAttribute('data-webmz-sticky-container-stay') === '1';

        if (stayInColumn || isCssOnlyStickyContainer(element)) {
            if (stayInColumn) {
                prepareCssStickyContainer(element);
            }

            return null;
        }

        element.setAttribute('data-webmz-sticky-container-ready', '1');

        var boundary = findContainerBoundary(element, false);
        var placeholder = document.createElement('div');
        placeholder.className = 'webmz-sticky-placeholder webmz-sticky-container-placeholder';
        element.parentNode.insertBefore(placeholder, element);

        var item = {
            element: element,
            placeholder: placeholder,
            boundary: boundary,
            stayInColumn: false,
            offset: parseFloat(element.getAttribute('data-webmz-sticky-container-offset') || '0') || 0,
            startY: 0,
            endY: 0,
            stickyTop: 0,
            mode: 'static',
            boundaryPositionSet: false,
            boundaryWasStatic: false
        };

        ensureBoundaryPosition(boundary, item);

        return item;
    }

    function clearContainerStyles(item) {
        item.element.classList.remove(
            'is-webmz-sticky-container-active',
            'is-webmz-sticky-container-fixed',
            'is-webmz-sticky-container-bottom'
        );
        item.element.style.position = '';
        item.element.style.top = '';
        item.element.style.left = '';
        item.element.style.right = '';
        item.element.style.width = '';
        item.element.style.zIndex = '';
        item.element.style.removeProperty('--webmz-sticky-container-top');
        item.placeholder.classList.remove('is-active');
        item.mode = 'static';
    }

    function getContainerScrollLimits(item, currentY) {
        var stickyTop = item.offset + getAdminBarOffset();
        var elementHeight = getStickyElementHeight(item.element, item.placeholder);
        var placeholderRect = item.placeholder.getBoundingClientRect();
        var documentTop = placeholderRect.top + currentY;
        var boundaryBottom = getContentBottom(item.boundary, currentY);
        var startY = Math.max(0, documentTop - stickyTop);
        var endY = Math.max(startY, boundaryBottom - elementHeight - stickyTop);

        return {
            stickyTop: stickyTop,
            elementHeight: elementHeight,
            startY: startY,
            endY: endY
        };
    }

    function measureContainerItem(item) {
        var wasActive = 'static' !== item.mode;
        var currentY = getScrollY();

        if (wasActive) {
            clearContainerStyles(item);
        }

        ensureBoundaryPosition(item.boundary, item);

        var limits = getContainerScrollLimits(item, currentY);

        item.stickyTop = limits.stickyTop;
        item.startY = limits.startY;
        item.endY = limits.endY;
        item.placeholder.style.height = limits.elementHeight + 'px';
    }

    function setContainerFixed(item, limits) {
        var rect = item.placeholder.getBoundingClientRect();

        item.placeholder.classList.add('is-active');
        item.element.classList.add('is-webmz-sticky-container-active', 'is-webmz-sticky-container-fixed');
        item.element.classList.remove('is-webmz-sticky-container-bottom');
        item.element.style.position = 'fixed';
        item.element.style.width = rect.width + 'px';
        item.element.style.left = rect.left + 'px';
        item.element.style.right = 'auto';
        item.element.style.setProperty('--webmz-sticky-container-top', limits.stickyTop + 'px');
        item.element.style.setProperty('top', limits.stickyTop + 'px', 'important');
        item.element.style.zIndex = '99';
        item.mode = 'fixed';
    }

    function setContainerBottom(item, limits, currentY) {
        var placeholderRect = item.placeholder.getBoundingClientRect();
        var boundaryBottom = getContentBottom(item.boundary, currentY);
        var top = Math.max(limits.stickyTop, boundaryBottom - currentY - limits.elementHeight);

        item.placeholder.classList.add('is-active');
        item.element.classList.add('is-webmz-sticky-container-active', 'is-webmz-sticky-container-bottom');
        item.element.classList.remove('is-webmz-sticky-container-fixed');
        item.element.style.position = 'fixed';
        item.element.style.setProperty('top', top + 'px', 'important');
        item.element.style.left = placeholderRect.left + 'px';
        item.element.style.right = 'auto';
        item.element.style.width = placeholderRect.width + 'px';
        item.element.style.zIndex = '99';
        item.mode = 'bottom';
    }

    function updateStickyContainers() {
        var currentY = getScrollY();

        stickyContainerItems.forEach(function (item) {
            var limits = getContainerScrollLimits(item, currentY);

            item.stickyTop = limits.stickyTop;
            item.startY = limits.startY;
            item.endY = limits.endY;
            item.placeholder.style.height = limits.elementHeight + 'px';

            if (limits.endY <= limits.startY) {
                clearContainerStyles(item);
                return;
            }

            if (currentY <= limits.startY) {
                clearContainerStyles(item);
                return;
            }

            if (currentY >= limits.endY) {
                setContainerBottom(item, limits, currentY);
                return;
            }

            setContainerFixed(item, limits);
        });
    }

    function requestUpdate() {
        if (isElementorEditMode()) {
            return;
        }

        if (ticking) {
            return;
        }

        ticking = true;
        window.requestAnimationFrame(function () {
            updateSticky(false);
            updateStickyContainers();
            lastScrollY = getScrollY();
            ticking = false;
        });
    }

    function registerStickyElement(element) {
        if (!element || isElementorEditMode()) {
            return false;
        }

        var added = false;

        if (
            element.getAttribute('data-webmz-sticky-header') === '1' &&
            element.getAttribute('data-webmz-sticky-ready') !== '1'
        ) {
            var headerItem = buildItem(element);

            if (headerItem) {
                stickyItems.push(headerItem);
                measureItem(headerItem);
                added = true;
            }
        }

        if (element.getAttribute('data-webmz-sticky-container') === '1') {
            var containerItem = buildContainerItem(element);

            if (containerItem) {
                stickyContainerItems.push(containerItem);
                measureContainerItem(containerItem);
                added = true;
            } else if (element.getAttribute('data-webmz-sticky-container-stay') === '1') {
                prepareCssStickyContainer(element);
                added = true;
            }
        }

        return added;
    }

    function initStickyElements(context) {
        if (isElementorEditMode()) {
            return;
        }

        context = context || document;
        var added = false;

        syncAdminBarOffsetVar();

        context.querySelectorAll('[data-webmz-sticky-header="1"], [data-webmz-sticky-container="1"]').forEach(function (element) {
            if (registerStickyElement(element)) {
                added = true;
            }
        });

        if (added) {
            updateSticky(true);
            updateStickyContainers();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (isElementorEditMode()) {
            return;
        }

        initStickyElements(document);
    });

    window.addEventListener('load', function () {
        if (isElementorEditMode()) {
            return;
        }

        window.requestAnimationFrame(measureAll);
    });

    window.addEventListener('scroll', requestUpdate, { passive: true });

    window.addEventListener('resize', function () {
        if (isElementorEditMode()) {
            return;
        }

        window.requestAnimationFrame(measureAll);
    });

    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            if (isElementorEditMode() || !$scope || !$scope[0]) {
                return;
            }

            registerStickyElement($scope[0]);
        });
    }
})();
