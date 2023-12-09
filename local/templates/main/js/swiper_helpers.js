(function () {
    /**
     * Toggle controls visibility according to swiper.
     *
     * @param swiper
     * @param prev
     * @param next
     */
    function toggleControlsVisibility(swiper, prev, next) {
        if (swiper.isBeginning && swiper.isEnd) {
            prev.addClass('swiper-button-disabled');
            next.addClass('swiper-button-disabled');
        } else {
            prev.removeClass('swiper-button-disabled');
            next.removeClass('swiper-button-disabled');
        }
    }

    /**
     * Init rewind controls (next and prev) to the swiper.
     * Rewind means, that swiper will rewind to the start or to the end on end or beginning.
     *
     * @param swiper
     * @param prev
     * @param next
     * @param check_window_resize
     */
    function initRewindControls(swiper, prev, next, check_window_resize) {
        var prevHandler, nextHandler, resizeEventListener;

        if (typeof check_window_resize == 'undefined') {
            check_window_resize = true;
        }

        prevHandler = function () {
            if (swiper.isBeginning) {
                swiper.slideTo(swiper.slides.length - 1);
            } else {
                swiper.slidePrev();
            }
        };
        prev.on('click', prevHandler);

        nextHandler = function () {
            if (swiper.isEnd) {
                swiper.slideTo(0);
            } else {
                swiper.slideNext();
            }
        };
        next.on('click', nextHandler);

        toggleControlsVisibility(swiper, prev, next);
        resizeEventListener = function () {
            toggleControlsVisibility(swiper, prev, next);
        };

        if (check_window_resize) {
            $(window).on('resize', resizeEventListener);
        }

        swiper.on('observerUpdate', resizeEventListener);
        swiper.on('beforeDestroy', function () {
            prev.off('click', prevHandler);
            next.off('click', nextHandler);
            if (check_window_resize) {
                $(window).off('resize', resizeEventListener);
            }
        });
    }


    window.swiperHelpers = {
        initRewindControls: initRewindControls
    };
})();