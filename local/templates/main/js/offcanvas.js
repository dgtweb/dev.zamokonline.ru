$(function () {
    let offcanvasToggle = $('.header-toggle'),
        offcanvasContainer = $('.offcanvas-wrapper'),
        offcanvasBg = offcanvasContainer.find('.offcanvas-bg');

    function showOffcanvas() {
        offcanvasContainer.addClass('show');
        offcanvasBg.addClass('show');
    }

    function closeOffcanvas() {
        offcanvasContainer.removeClass('show');
        offcanvasBg.removeClass('show');
    }

    $(offcanvasToggle).on('click', showOffcanvas);
    $(offcanvasBg).on('click', closeOffcanvas);
});