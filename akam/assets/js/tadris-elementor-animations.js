(function ($) {
    'use strict';

    var mouseEffects = [];
    var rafId = null;
    var documentMoveBound = false;
    var elementorHooksBound = false;
    var editorObserverBound = false;
    var editorRefreshTimer = null;

    function prefersReducedMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function isEditorPreview() {
        var body = document.body;

        if (
            body && (
                body.classList.contains('elementor-editor-preview') ||
                body.classList.contains('elementor-editor-active') ||
                body.classList.contains('elementor-edit-mode')
            )
        ) {
            return true;
        }

        try {
            return window.self !== window.top && !!window.elementorFrontend;
        } catch (error) {
            return false;
        }
    }

    function parseNumber(value, fallback) {
        var parsed = parseFloat(value);
        return isNaN(parsed) ? fallback : parsed;
    }

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function isSettingYes(settings, key) {
        return !!(settings && settings[key] === 'yes');
    }

    function getSliderSetting(settings, key, fallback) {
        if (!settings || !settings[key] || typeof settings[key].size === 'undefined') {
            return fallback;
        }

        return parseNumber(settings[key].size, fallback);
    }

    function getMouseScopeFromSettings(settings, key) {
        return settings && settings[key] === 'page' ? 'page' : 'element';
    }

    function parseElementSettings(element) {
        var raw = element.getAttribute('data-settings');

        if (!raw) {
            return null;
        }

        try {
            return JSON.parse(raw);
        } catch (error) {
            return null;
        }
    }

    function getMouseScope(element, attributeName, settings, settingsKey) {
        if (settings && settings[settingsKey]) {
            return getMouseScopeFromSettings(settings, settingsKey);
        }

        return element.getAttribute(attributeName) === 'page' ? 'page' : 'element';
    }

    function isEventOverElement(event, element) {
        var rect = element.getBoundingClientRect();

        return (
            event.clientX >= rect.left &&
            event.clientX <= rect.right &&
            event.clientY >= rect.top &&
            event.clientY <= rect.bottom
        );
    }

    function getViewportSize() {
        return {
            width: window.innerWidth || document.documentElement.clientWidth || 1,
            height: window.innerHeight || document.documentElement.clientHeight || 1
        };
    }

    function getPageMouseOffset(event, element) {
        var rect = element.getBoundingClientRect();
        var viewport = getViewportSize();
        var centerX = rect.left + rect.width / 2;
        var centerY = rect.top + rect.height / 2;
        var relX = clamp((event.clientX - centerX) / (viewport.width * 0.5), -1, 1);
        var relY = clamp((event.clientY - centerY) / (viewport.height * 0.5), -1, 1);

        return {
            relX: relX,
            relY: relY
        };
    }

    function getElementMouseOffset(event, element) {
        var rect = element.getBoundingClientRect();

        if (!rect.width || !rect.height) {
            return {
                relX: 0,
                relY: 0
            };
        }

        return {
            relX: clamp((event.clientX - (rect.left + rect.width / 2)) / (rect.width / 2), -1, 1),
            relY: clamp((event.clientY - (rect.top + rect.height / 2)) / (rect.height / 2), -1, 1)
        };
    }

    function querySelfAndDescendants(root, selector) {
        var items = [];

        if (!root || !root.querySelectorAll) {
            return items;
        }

        if (root.nodeType === 1 && root.matches(selector)) {
            items.push(root);
        }

        root.querySelectorAll(selector).forEach(function (element) {
            if (items.indexOf(element) === -1) {
                items.push(element);
            }
        });

        return items;
    }

    function elementHasAnimationSettings(element) {
        if (!element) {
            return false;
        }

        var settings = parseElementSettings(element);

        if (settings) {
            return !!(
                isSettingYes(settings, 'webmz_tadris_float_cloud_enabled') ||
                isSettingYes(settings, 'webmz_tadris_glow_beneath_enabled') ||
                isSettingYes(settings, 'webmz_tadris_mouse_tilt_enabled') ||
                isSettingYes(settings, 'webmz_tadris_mouse_follow_enabled') ||
                isSettingYes(settings, 'webmz_tadris_mouse_magnetic_enabled') ||
                isSettingYes(settings, 'webmz_tadris_mouse_spotlight_enabled')
            );
        }

        return element.matches(
            '.webmz-tadris-float-cloud, .webmz-tadris-glow-beneath, .webmz-tadris-mouse-tilt, .webmz-tadris-mouse-follow, .webmz-tadris-mouse-magnetic, .webmz-tadris-mouse-spotlight, [data-tilt]'
        );
    }

    function resetMouseAnimationState(element) {
        if (!element) {
            return;
        }

        mouseEffects = mouseEffects.filter(function (effect) {
            return effect.element !== element;
        });

        if (element.vanillaTilt && typeof element.vanillaTilt.destroy === 'function') {
            element.vanillaTilt.destroy();
        }

        element.removeAttribute('data-webmz-mouse-tilt-init');
        element.removeAttribute('data-webmz-mouse-follow-init');
        element.removeAttribute('data-webmz-mouse-magnetic-init');
        element.removeAttribute('data-webmz-mouse-spotlight-init');

        if (!parseElementSettings(element)) {
            element.style.transform = '';
            element.classList.remove('is-spotlight-active');
        }
    }

    function syncElementAnimationDom(element, settings) {
        if (!settings) {
            return;
        }

        element.classList.toggle('webmz-tadris-float-cloud', isSettingYes(settings, 'webmz_tadris_float_cloud_enabled'));
        element.classList.toggle('webmz-tadris-glow-beneath', isSettingYes(settings, 'webmz_tadris_glow_beneath_enabled'));

        if (isSettingYes(settings, 'webmz_tadris_glow_beneath_enabled')) {
            element.style.setProperty('--webmz-tadris-glow-color', settings.webmz_tadris_glow_beneath_color || '#F4A261');
        }

        var tiltEnabled = isSettingYes(settings, 'webmz_tadris_mouse_tilt_enabled');
        element.classList.toggle('webmz-tadris-mouse-tilt', tiltEnabled);

        if (tiltEnabled) {
            var tiltScope = getMouseScopeFromSettings(settings, 'webmz_tadris_mouse_tilt_scope');
            var tiltAxis = settings.webmz_tadris_mouse_tilt_axis || '';

            element.setAttribute('data-tilt', '');
            element.setAttribute('data-webmz-mouse-tilt-scope', tiltScope);
            element.setAttribute('data-tilt-max', String(getSliderSetting(settings, 'webmz_tadris_mouse_tilt_max', 15)));
            element.setAttribute('data-tilt-speed', String(getSliderSetting(settings, 'webmz_tadris_mouse_tilt_speed', 200)));
            element.setAttribute('data-tilt-perspective', String(getSliderSetting(settings, 'webmz_tadris_mouse_tilt_perspective', 1000)));
            element.setAttribute('data-tilt-scale', String(getSliderSetting(settings, 'webmz_tadris_mouse_tilt_scale', 1)));

            if (tiltScope === 'page') {
                element.setAttribute('data-tilt-full-page-listening', 'true');
            } else {
                element.removeAttribute('data-tilt-full-page-listening');
            }

            if (tiltAxis === 'x' || tiltAxis === 'y') {
                element.setAttribute('data-tilt-axis', tiltAxis);
            } else {
                element.removeAttribute('data-tilt-axis');
            }

            if (isSettingYes(settings, 'webmz_tadris_mouse_tilt_reverse')) {
                element.setAttribute('data-tilt-reverse', 'true');
            } else {
                element.removeAttribute('data-tilt-reverse');
            }

            if (isSettingYes(settings, 'webmz_tadris_mouse_tilt_glare')) {
                element.setAttribute('data-tilt-glare', 'true');
                element.setAttribute('data-tilt-max-glare', String(getSliderSetting(settings, 'webmz_tadris_mouse_tilt_max_glare', 0.5)));
            } else {
                element.removeAttribute('data-tilt-glare');
                element.removeAttribute('data-tilt-max-glare');
            }
        } else {
            element.removeAttribute('data-tilt');
            element.removeAttribute('data-tilt-full-page-listening');
            element.removeAttribute('data-tilt-axis');
            element.removeAttribute('data-tilt-reverse');
            element.removeAttribute('data-tilt-glare');
            element.removeAttribute('data-tilt-max-glare');
        }

        var followEnabled = isSettingYes(settings, 'webmz_tadris_mouse_follow_enabled');
        element.classList.toggle('webmz-tadris-mouse-follow', followEnabled);

        if (followEnabled) {
            var followAxis = settings.webmz_tadris_mouse_follow_axis || 'both';

            element.setAttribute('data-webmz-mouse-follow-intensity', String(getSliderSetting(settings, 'webmz_tadris_mouse_follow_intensity', 25)));
            element.setAttribute('data-webmz-mouse-follow-speed', String(getSliderSetting(settings, 'webmz_tadris_mouse_follow_speed', 0.22)));
            element.setAttribute('data-webmz-mouse-follow-axis', followAxis);
            element.setAttribute('data-webmz-mouse-follow-reverse', settings.webmz_tadris_mouse_follow_direction === 'reverse' ? '1' : '0');
            element.setAttribute('data-webmz-mouse-follow-scope', getMouseScopeFromSettings(settings, 'webmz_tadris_mouse_follow_scope'));
        } else {
            element.removeAttribute('data-webmz-mouse-follow-intensity');
            element.removeAttribute('data-webmz-mouse-follow-speed');
            element.removeAttribute('data-webmz-mouse-follow-axis');
            element.removeAttribute('data-webmz-mouse-follow-reverse');
            element.removeAttribute('data-webmz-mouse-follow-scope');
        }

        var magneticEnabled = isSettingYes(settings, 'webmz_tadris_mouse_magnetic_enabled');
        element.classList.toggle('webmz-tadris-mouse-magnetic', magneticEnabled);

        if (magneticEnabled) {
            element.setAttribute('data-webmz-mouse-magnetic-strength', String(getSliderSetting(settings, 'webmz_tadris_mouse_magnetic_strength', 20)));
            element.setAttribute('data-webmz-mouse-magnetic-radius', String(getSliderSetting(settings, 'webmz_tadris_mouse_magnetic_radius', 150)));
            element.setAttribute('data-webmz-mouse-magnetic-speed', String(getSliderSetting(settings, 'webmz_tadris_mouse_magnetic_speed', 0.25)));
            element.setAttribute('data-webmz-mouse-magnetic-scope', getMouseScopeFromSettings(settings, 'webmz_tadris_mouse_magnetic_scope'));
        } else {
            element.removeAttribute('data-webmz-mouse-magnetic-strength');
            element.removeAttribute('data-webmz-mouse-magnetic-radius');
            element.removeAttribute('data-webmz-mouse-magnetic-speed');
            element.removeAttribute('data-webmz-mouse-magnetic-scope');
        }

        var spotlightEnabled = isSettingYes(settings, 'webmz_tadris_mouse_spotlight_enabled');
        element.classList.toggle('webmz-tadris-mouse-spotlight', spotlightEnabled);

        if (spotlightEnabled) {
            element.setAttribute('data-webmz-mouse-spotlight-scope', getMouseScopeFromSettings(settings, 'webmz_tadris_mouse_spotlight_scope'));
            element.style.setProperty('--webmz-mouse-spotlight-color', settings.webmz_tadris_mouse_spotlight_color || 'rgba(255,255,255,0.35)');
            element.style.setProperty('--webmz-mouse-spotlight-size', String(getSliderSetting(settings, 'webmz_tadris_mouse_spotlight_size', 60)) + '%');
        } else {
            element.removeAttribute('data-webmz-mouse-spotlight-scope');
            element.classList.remove('is-spotlight-active');
        }
    }

    function registerEffect(effect) {
        if (!effect || !effect.element) {
            return;
        }

        mouseEffects.push(effect);
        bindDocumentMove();
        startAnimationLoop();
    }

    function bindDocumentMove() {
        if (documentMoveBound) {
            return;
        }

        documentMoveBound = true;
        document.addEventListener('mousemove', onDocumentMouseMove, { passive: true });
    }

    function onDocumentMouseMove(event) {
        mouseEffects.forEach(function (effect) {
            if (typeof effect.onMouseMove !== 'function') {
                return;
            }

            var inside = isEventOverElement(event, effect.element);
            var shouldRun = effect.scope === 'page' || inside;

            if (!shouldRun) {
                if (!effect.isInactive) {
                    effect.isInactive = true;

                    if (typeof effect.onMouseLeave === 'function') {
                        effect.onMouseLeave();
                    }
                }

                return;
            }

            effect.isInactive = false;
            effect.onMouseMove(event, inside);
        });
    }

    function startAnimationLoop() {
        if (rafId !== null) {
            return;
        }

        function frame() {
            var hasActive = false;

            mouseEffects.forEach(function (effect) {
                if (typeof effect.update === 'function' && effect.update()) {
                    hasActive = true;
                }
            });

            rafId = hasActive ? window.requestAnimationFrame(frame) : null;
        }

        rafId = window.requestAnimationFrame(frame);
    }

    function createTransformEffect(element, options) {
        var state = {
            element: element,
            scope: options.scope,
            currentX: 0,
            currentY: 0,
            targetX: 0,
            targetY: 0,
            speed: options.speed,
            active: false,
            onMouseMove: options.onMouseMove,
            onMouseLeave: options.onMouseLeave || function () {
                state.reset();
            },
            update: function () {
                var deltaX = this.targetX - this.currentX;
                var deltaY = this.targetY - this.currentY;

                if (!this.active && Math.abs(deltaX) < 0.05 && Math.abs(deltaY) < 0.05) {
                    return false;
                }

                this.currentX += deltaX * this.speed;
                this.currentY += deltaY * this.speed;

                if (!this.active && Math.abs(this.targetX - this.currentX) < 0.05 && Math.abs(this.targetY - this.currentY) < 0.05) {
                    this.currentX = this.targetX;
                    this.currentY = this.targetY;
                }

                this.element.style.transform = 'translate3d(' + this.currentX.toFixed(2) + 'px,' + this.currentY.toFixed(2) + 'px,0)';
                return this.active || Math.abs(this.targetX - this.currentX) > 0.05 || Math.abs(this.targetY - this.currentY) > 0.05;
            },
            reset: function () {
                this.active = false;
                this.targetX = 0;
                this.targetY = 0;
                startAnimationLoop();
            }
        };

        return state;
    }

    function initMouseTilt(root) {
        if (prefersReducedMotion() || typeof window.VanillaTilt === 'undefined') {
            return;
        }

        var elements = querySelfAndDescendants(root || document, '[data-tilt].webmz-tadris-mouse-tilt');

        elements.forEach(function (element) {
            if (!element || element.hasAttribute('data-webmz-mouse-tilt-init')) {
                return;
            }

            element.setAttribute('data-webmz-mouse-tilt-init', '1');

            var settings = parseElementSettings(element);
            var fullPage = getMouseScope(element, 'data-webmz-mouse-tilt-scope', settings, 'webmz_tadris_mouse_tilt_scope') === 'page';

            if (element.vanillaTilt && typeof element.vanillaTilt.destroy === 'function') {
                element.vanillaTilt.destroy();
            }

            window.VanillaTilt.init(element, {
                'full-page-listening': fullPage
            });
        });
    }

    function initMouseFollow(root) {
        var elements = querySelfAndDescendants(root || document, '.webmz-tadris-mouse-follow');

        elements.forEach(function (element) {
            if (!element || element.hasAttribute('data-webmz-mouse-follow-init')) {
                return;
            }

            element.setAttribute('data-webmz-mouse-follow-init', '1');
            element.style.transition = 'none';

            var settings = parseElementSettings(element);
            var intensity = settings
                ? getSliderSetting(settings, 'webmz_tadris_mouse_follow_intensity', 25)
                : parseNumber(element.getAttribute('data-webmz-mouse-follow-intensity'), 25);
            var speed = settings
                ? getSliderSetting(settings, 'webmz_tadris_mouse_follow_speed', 0.22)
                : parseNumber(element.getAttribute('data-webmz-mouse-follow-speed'), 0.22);
            var reverse = settings
                ? settings.webmz_tadris_mouse_follow_direction === 'reverse'
                : element.getAttribute('data-webmz-mouse-follow-reverse') === '1';
            var axis = settings
                ? (settings.webmz_tadris_mouse_follow_axis || 'both')
                : (element.getAttribute('data-webmz-mouse-follow-axis') || 'both');
            var mouseScope = getMouseScope(element, 'data-webmz-mouse-follow-scope', settings, 'webmz_tadris_mouse_follow_scope');
            var direction = reverse ? -1 : 1;
            var effect = createTransformEffect(element, {
                scope: mouseScope,
                speed: speed,
                onMouseMove: function (event) {
                    var offset = mouseScope === 'page'
                        ? getPageMouseOffset(event, element)
                        : getElementMouseOffset(event, element);

                    effect.active = true;
                    effect.targetX = (axis === 'y' ? 0 : offset.relX * intensity * direction);
                    effect.targetY = (axis === 'x' ? 0 : offset.relY * intensity * direction);
                    startAnimationLoop();
                }
            });

            registerEffect(effect);
        });
    }

    function initMouseMagnetic(root) {
        var elements = querySelfAndDescendants(root || document, '.webmz-tadris-mouse-magnetic');

        elements.forEach(function (element) {
            if (!element || element.hasAttribute('data-webmz-mouse-magnetic-init')) {
                return;
            }

            element.setAttribute('data-webmz-mouse-magnetic-init', '1');
            element.style.transition = 'none';

            var settings = parseElementSettings(element);
            var strength = settings
                ? getSliderSetting(settings, 'webmz_tadris_mouse_magnetic_strength', 20)
                : parseNumber(element.getAttribute('data-webmz-mouse-magnetic-strength'), 20);
            var radius = settings
                ? getSliderSetting(settings, 'webmz_tadris_mouse_magnetic_radius', 150)
                : parseNumber(element.getAttribute('data-webmz-mouse-magnetic-radius'), 150);
            var speed = settings
                ? getSliderSetting(settings, 'webmz_tadris_mouse_magnetic_speed', 0.25)
                : parseNumber(element.getAttribute('data-webmz-mouse-magnetic-speed'), 0.25);
            var mouseScope = getMouseScope(element, 'data-webmz-mouse-magnetic-scope', settings, 'webmz_tadris_mouse_magnetic_scope');
            var effect = createTransformEffect(element, {
                scope: mouseScope,
                speed: speed,
                onMouseMove: function (event) {
                    var rect = element.getBoundingClientRect();
                    var viewport = getViewportSize();
                    var centerX = rect.left + rect.width / 2;
                    var centerY = rect.top + rect.height / 2;
                    var distanceX = event.clientX - centerX;
                    var distanceY = event.clientY - centerY;
                    var distance = Math.sqrt(distanceX * distanceX + distanceY * distanceY);
                    var localRadius = radius + Math.max(rect.width, rect.height) / 2;
                    var maxDistance = mouseScope === 'page'
                        ? Math.max(localRadius, Math.sqrt(viewport.width * viewport.width + viewport.height * viewport.height) * 0.5)
                        : localRadius;
                    var pull = clamp(1 - distance / maxDistance, 0, 1);

                    if (mouseScope === 'element' && distance > maxDistance) {
                        effect.reset();
                        return;
                    }

                    var offsetX = distanceX === 0 && distanceY === 0 ? 0 : (distanceX / (distance || 1)) * strength * pull;
                    var offsetY = distanceY === 0 && distanceY === 0 ? 0 : (distanceY / (distance || 1)) * strength * pull;

                    effect.active = true;
                    effect.targetX = offsetX;
                    effect.targetY = offsetY;
                    startAnimationLoop();
                }
            });

            registerEffect(effect);
        });
    }

    function initMouseSpotlight(root) {
        var elements = querySelfAndDescendants(root || document, '.webmz-tadris-mouse-spotlight');

        elements.forEach(function (element) {
            if (!element || element.hasAttribute('data-webmz-mouse-spotlight-init')) {
                return;
            }

            element.setAttribute('data-webmz-mouse-spotlight-init', '1');

            var settings = parseElementSettings(element);
            var mouseScope = getMouseScope(element, 'data-webmz-mouse-spotlight-scope', settings, 'webmz_tadris_mouse_spotlight_scope');

            if (mouseScope === 'page') {
                element.classList.add('is-spotlight-active');
            }

            registerEffect({
                element: element,
                scope: mouseScope,
                onMouseMove: function (event) {
                    var rect = element.getBoundingClientRect();

                    if (!rect.width || !rect.height) {
                        return;
                    }

                    var x = ((event.clientX - rect.left) / rect.width) * 100;
                    var y = ((event.clientY - rect.top) / rect.height) * 100;

                    if (mouseScope === 'element') {
                        x = clamp(x, 0, 100);
                        y = clamp(y, 0, 100);
                    }

                    element.style.setProperty('--webmz-mouse-spotlight-x', x.toFixed(2) + '%');
                    element.style.setProperty('--webmz-mouse-spotlight-y', y.toFixed(2) + '%');
                    element.classList.add('is-spotlight-active');
                },
                onMouseLeave: function () {
                    if (mouseScope === 'element') {
                        element.classList.remove('is-spotlight-active');
                    }
                },
                update: function () {
                    return false;
                }
            });
        });
    }

    function initElementAnimations(element) {
        if (!element || prefersReducedMotion() || !elementHasAnimationSettings(element)) {
            return;
        }

        var settings = parseElementSettings(element);

        resetMouseAnimationState(element);
        syncElementAnimationDom(element, settings);
        initMouseTilt(element);
        initMouseFollow(element);
        initMouseMagnetic(element);
        initMouseSpotlight(element);
    }

    function initMouseAnimations(root) {
        if (prefersReducedMotion()) {
            return;
        }

        var elements = [];

        if (root && root.nodeType === 1) {
            if (elementHasAnimationSettings(root)) {
                elements.push(root);
            }

            root.querySelectorAll('.elementor-element').forEach(function (element) {
                if (elements.indexOf(element) === -1 && elementHasAnimationSettings(element)) {
                    elements.push(element);
                }
            });
        } else {
            document.querySelectorAll('.elementor-element').forEach(function (element) {
                if (elementHasAnimationSettings(element)) {
                    elements.push(element);
                }
            });
        }

        elements.forEach(initElementAnimations);
    }

    function scheduleEditorRefresh() {
        clearTimeout(editorRefreshTimer);
        editorRefreshTimer = window.setTimeout(function () {
            initMouseAnimations(document);
        }, 16);
    }

    function bindEditorObserver() {
        if (editorObserverBound || typeof MutationObserver === 'undefined' || !document.body) {
            return;
        }

        editorObserverBound = true;

        var observer = new MutationObserver(function (mutations) {
            var elementsToRefresh = [];

            mutations.forEach(function (mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-settings' && mutation.target && mutation.target.classList && mutation.target.classList.contains('elementor-element')) {
                    elementsToRefresh.push(mutation.target);
                }

                if (mutation.type === 'childList' && mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType !== 1) {
                            return;
                        }

                        if (node.classList.contains('elementor-element')) {
                            elementsToRefresh.push(node);
                        }

                        if (node.querySelectorAll) {
                            node.querySelectorAll('.elementor-element').forEach(function (element) {
                                elementsToRefresh.push(element);
                            });
                        }
                    });
                }
            });

            if (!elementsToRefresh.length) {
                return;
            }

            clearTimeout(editorRefreshTimer);
            editorRefreshTimer = window.setTimeout(function () {
                elementsToRefresh.forEach(function (element) {
                    initElementAnimations(element);
                });
            }, 16);
        });

        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['data-settings'],
            childList: true,
            subtree: true
        });
    }

    function handleElementReady($scope) {
        var node = $scope && $scope[0] ? $scope[0] : null;

        if (!node) {
            return;
        }

        initElementAnimations(node);
        node.querySelectorAll('.elementor-element').forEach(initElementAnimations);
    }

    function bindElementorHooks() {
        if (elementorHooksBound || !window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        elementorHooksBound = true;

        window.elementorFrontend.hooks.addAction('frontend/init', function () {
            initMouseAnimations(document);
            bindEditorObserver();
        });

        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', handleElementReady);
    }

    function boot() {
        initMouseAnimations(document);
        bindElementorHooks();
        bindEditorObserver();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    $(window).on('elementor/frontend/init', function () {
        bindElementorHooks();
        initMouseAnimations(document);
        bindEditorObserver();
    });
}(window.jQuery));
