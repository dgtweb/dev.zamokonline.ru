BX.showWait = function () {
    var loader = '<div class="uis-preloader"><div class="cssload-clock"></div></div>';
    $('body').append(loader);
};

BX.closeWait = function () {
    $('body').find('.uis-preloader').remove();
};