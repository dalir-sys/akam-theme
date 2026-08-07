(function () {
    'use strict';

    function clearTypingState(container) {
        if (container._tadrisTypingTimeout) {
            window.clearTimeout(container._tadrisTypingTimeout);
            container._tadrisTypingTimeout = null;
        }
    }

    function initHeadingAnimation(scope) {
        var containers = (scope || document).querySelectorAll('.tadris-heading-animation');

        containers.forEach(function (container) {
            if (container.getAttribute('data-typed-ready') === 'yes') {
                return;
            }

            container.setAttribute('data-typed-ready', 'yes');

            var typedEl = container.querySelector('.tadris-heading-animation__typed');

            if (!typedEl) {
                return;
            }

            var words = [];

            try {
                words = JSON.parse(container.getAttribute('data-words') || '[]');
            } catch (error) {
                words = [];
            }

            if (!words.length) {
                return;
            }

            var typeSpeed = parseInt(container.getAttribute('data-type-speed') || '80', 10);
            var deleteSpeed = parseInt(container.getAttribute('data-delete-speed') || '40', 10);
            var pause = parseInt(container.getAttribute('data-pause') || '2000', 10);
            var wordIndex = 0;
            var charIndex = 0;
            var isDeleting = false;

            var tick = function () {
                var currentWord = words[wordIndex] || '';

                if (isDeleting) {
                    charIndex = Math.max(0, charIndex - 1);
                    typedEl.textContent = currentWord.substring(0, charIndex);

                    if (charIndex === 0) {
                        isDeleting = false;
                        wordIndex = (wordIndex + 1) % words.length;
                        container._tadrisTypingTimeout = window.setTimeout(tick, typeSpeed);
                        return;
                    }

                    container._tadrisTypingTimeout = window.setTimeout(tick, deleteSpeed);
                    return;
                }

                charIndex = Math.min(currentWord.length, charIndex + 1);
                typedEl.textContent = currentWord.substring(0, charIndex);

                if (charIndex === currentWord.length) {
                    container._tadrisTypingTimeout = window.setTimeout(function () {
                        isDeleting = true;
                        tick();
                    }, pause);
                    return;
                }

                container._tadrisTypingTimeout = window.setTimeout(tick, typeSpeed);
            };

            clearTypingState(container);
            typedEl.textContent = '';
            tick();
        });
    }

    function resetHeadingAnimation(scope) {
        var containers = (scope || document).querySelectorAll('.tadris-heading-animation[data-typed-ready="yes"]');

        containers.forEach(function (container) {
            clearTypingState(container);
            container.removeAttribute('data-typed-ready');

            var typedEl = container.querySelector('.tadris-heading-animation__typed');

            if (typedEl) {
                typedEl.textContent = '';
            }
        });

        initHeadingAnimation(scope);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initHeadingAnimation(document);
        });
    } else {
        initHeadingAnimation(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/webmz-tadris-heading-animation.default',
                function ($scope) {
                    initHeadingAnimation($scope[0]);
                }
            );
        });

        window.jQuery(window).on('elementor/editor/after_edit', function () {
            resetHeadingAnimation(document);
        });
    }
})();
