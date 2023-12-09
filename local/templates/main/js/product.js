$(function () {
    // product gallery
    $('.product-gallery-wrapper').each(function (_, container) {
        let mainSwiper = null, thumbsSwiper = null;

        let sliderContainer,
            slides, prev, next,
            thumbsSwiperContainer, mainSwiperContainer;

        sliderContainer = $(container);
        next = sliderContainer.find('.swiper-product-thumbnails-button-next');
        prev = sliderContainer.find('.swiper-product-thumbnails-button-prev');


        // Swiper containers
        mainSwiperContainer = sliderContainer.find('.swiper-product-gallery');
        thumbsSwiperContainer = sliderContainer.find('.swiper-product-thumbnails');
        slides = mainSwiperContainer.find('.swiper-slide');

        let getThumbnails = function () {
            return thumbsSwiperContainer.find('.swiper-slide');
        };

        let updateActiveSlideView = function () {
            let thumbnails = getThumbnails();
            if (thumbnails.length !== 0) {
                thumbnails.removeClass('swiper-slide-thumb-active');
                thumbnails.eq(mainSwiper.realIndex).addClass('swiper-slide-thumb-active');
            }
        };

        if (thumbsSwiperContainer.length === 1) {
            // Init thumbs swiper
        }
        thumbsSwiperContainer.on('click', '.swiper-slide', function (e) {
            let index = getThumbnails().index(e.currentTarget);
            mainSwiper.slideTo(index + 1);
            e.preventDefault();
        });

        thumbsSwiper = new Swiper(thumbsSwiperContainer, {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 10,
            slidesOffsetBefore: 35,
            slidesOffsetAfter: 0,
            breakpointsInverse: true,
            breakpoints: {
                // when window width is >= 768px
                768: {
                    direction: 'vertical',
                    slidesPerView: 3,
                    spaceBetween: 10,
                    slidesOffsetBefore: 0,
                    slidesOffsetAfter: 0,
                },
                // when window width is >= 1200px
                1200: {
                    direction: 'vertical',
                    slidesPerView: 4,
                    spaceBetween: 10,
                    slidesOffsetBefore: 0,
                    slidesOffsetAfter: 0,
                },
            }
        });

        // Init main swiper
        mainSwiper = new Swiper(mainSwiperContainer, {
            slidesPerView: 1,
            loop: slides.length > 1,
            touchRatio: slides.length > 1 ? 1 : 0,

            on: {
                init: function () {
                    $(prev).on('click', function() {
                        mainSwiper.slidePrev();
                    });

                    $(next).on('click', function() {
                        mainSwiper.slideNext();
                    });
                },
            },
        });

        mainSwiper.on('slideChange', function() {
            console.log('slide change');
            if (thumbsSwiper) {
                thumbsSwiper.slideTo(this.realIndex);
            }
            updateActiveSlideView();
        });

        updateActiveSlideView();

        // product fancybox
        $().fancybox({
            selector : '.swiper-product-gallery .swiper-slide:not(.swiper-slide-duplicate) .product-gallery',
            backFocus : false,
            loop: true,
            keyboard: true,
            buttons: [
                "close"
            ],
            
            // change active swiper slide after change active fancybox slide 
            afterShow : function( instance, current ) {
                mainSwiper.slideToLoop(current.index);
            }
        });

        // remove dublication swiper slides in fancybox gallery
        $(document).on('click', '.swiper-product-gallery .swiper-slide-duplicate', function(e) {
            var $slides = $(this)
                .parent()
                .children('.swiper-slide:not(.swiper-slide-duplicate)');

            $slides
                .eq( ( $(this).attr("data-swiper-slide-index") || 0) % $slides.length )
                .find('a.product-gallery')
                .trigger("click.fb-start", { $trigger: $(this) });

            return false;
        });
    });

    // product specifications
    $(document).on('click', '.product-specification-toggle', function(e) {
        let specificationTab = $('.product-nav-tabs #product-specifications-tab');
        $(specificationTab).tab('show');

        $('html, body').animate({
            scrollTop: $(specificationTab).offset().top - parseInt($('body').css('padding-top'))
        }, 500);
    });

    // product reviews
    $('.swiper-product-reviews').each(function (_, container) {
        let jContainer = $(container),
            slides = jContainer.find('.swiper-slide');

        new Swiper(jContainer, {
            observer: true,
            observeParents: true,
            slidesPerView: 1,
            breakpointsInverse: true,
            breakpoints: {
                576: {
                    slidesPerView: 2,
                },
            },

            navigation: {
                nextEl: jContainer.find('.swiper-product-reviews-button-next'),
                prevEl: jContainer.find('.swiper-product-reviews-button-prev'),
            },

            pagination: {
                el: ".swiper-product-reviews-pagination",
                type: "fraction",
            },
        });
    });
});
