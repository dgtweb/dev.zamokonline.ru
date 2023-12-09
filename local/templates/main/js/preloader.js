BX.showWait = function () {
  var loader = '<div class="loader"><div class="cssload-clock"></div></div>';
  $('body').append(loader);
};

BX.closeWait = function () {
  $('body').find('.loader').remove();
};
