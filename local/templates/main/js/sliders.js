$(function () {
    function initHeroSwiper() {
        $('.swiper-hero').each(function (_, container) {
            let jContainer = $(container);
            new Swiper(jContainer, {
                slidesPerView: 1,
                loop: true,
                loopAdditionalSlides: 1,

                navigation: {
                    nextEl: jContainer.find('.swiper-hero-button-next'),
                    prevEl: jContainer.find('.swiper-hero-button-prev'),
                },

                pagination: {
                    el: '.swiper-hero-pagination',
                    type: 'bullets',
                    clickable: true,
                },
            });
        });
    }

    function initBrandsSwiper() {
        $('.swiper-brands').each(function (_, container) {
            let jContainer = $(container);
            let prev = jContainer.find('.swiper-brands-button-prev');
            let next = jContainer.find('.swiper-brands-button-next');

            new Swiper(jContainer, {
                slidesPerView: 4,
                slidesPerGroup: 1,
                loop: true,
                loopAdditionalSlides: 1,
                watchSlidesProgress: true,
                watchSlidesVisibility: true,
                breakpointsInverse: true,
                breakpoints: {
                    1200: {
                        slidesPerView: 6,
                        watchSlidesProgress: true,
                        watchSlidesVisibility: true,
                    },
                },

                on: {
                    init: function () {
                        swiperHelpers.initRewindControls(
                            this,
                            prev,
                            next,
                            true
                        );
                    },
                },
            });
        });
    }

    function initRelatedSwiper() {
        $('.swiper-related-products').each(function (_, container) {
            let jContainer = $(container),
                slides = jContainer.find('.swiper-slide');

            new Swiper(jContainer, {
                slidesPerView: 2,
                slidesPerGroup: 1,
                loop: slides.length > 2 ? true : false,
                breakpointsInverse: true,
                breakpoints: {
                    576: {
                        slidesPerView: 3,
                        loop: slides.length > 3 ? true : false,
                    },

                    992: {
                        slidesPerView: 4,
                        loop: slides.length > 4 ? true : false,
                    },
                },

                navigation: {
                    nextEl: jContainer.find('.swiper-related-products-button-next'),
                    prevEl: jContainer.find('.swiper-related-products-button-prev'),
                },

                pagination: {
                    el: ".swiper-related-products-pagination",
                    type: "fraction",
                },
            });
        });
    }

    //  destroy slider
    function destroySlider(slider) {
        $(slider).each(function(index, element){
            if (typeof element.swiper != 'undefined' && element.swiper != null) {
                element.swiper.destroy();
            }
        })
    }

    function destroyBrandsSwiper() {
        return destroySlider('.swiper-brands');
    }

    


    initHeroSwiper();
    initRelatedSwiper();
    
    if(!windowSizeHelper.isPhone()) {
        initBrandsSwiper();
    }


    // window resize
    beforeWindowWidthResizeFunctions.push(function () {
        if(windowSizeHelper.isPhoneToTabletResize()) {
            initBrandsSwiper();
        }

        if(windowSizeHelper.isTabletToPhoneResize()) {
            destroyBrandsSwiper();
        }
    });
});