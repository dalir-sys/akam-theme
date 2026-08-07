(function () {
    'use strict';

    function setupServiceBoxCounters(scope) {
        var counters = (scope || document).querySelectorAll('.webmz-service-box__counter[data-count-end]');

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

    function initServiceBox(scope) {
        setupServiceBoxCounters(scope);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initServiceBox(document);
        });
    } else {
        initServiceBox(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/webmz-service-box.default',
                function ($scope) {
                    initServiceBox($scope[0]);
                }
            );
        });
    }
})();
