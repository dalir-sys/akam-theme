(function ($) {
    'use strict';

    function bindOrbitPause($orbit) {
        if (!$orbit.length || $orbit.data('webmzAhsBound')) {
            return;
        }

        $orbit.data('webmzAhsBound', true);

        $orbit.on('mouseenter focusin', '.webmz-ahs__icon', function () {
            $orbit.addClass('is-paused');
        });

        $orbit.on('mouseleave focusout', '.webmz-ahs__icon', function () {
            $orbit.removeClass('is-paused');
        });
    }

    function initWidget($scope) {
        var $widget = $scope.find('.webmz-ahs').first();

        if (!$widget.length) {
            return;
        }

        bindOrbitPause($widget.find('.webmz-ahs__orbit'));
    }

    function boot() {
        $('.elementor-widget-webmz-animated-hero-section .webmz-ahs').each(function () {
            initWidget($(this).closest('.elementor-widget-webmz-animated-hero-section'));
        });
    }

    $(window).on('elementor/frontend/init', function () {
        if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/webmz-animated-hero-section.default',
            initWidget
        );
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(jQuery);
