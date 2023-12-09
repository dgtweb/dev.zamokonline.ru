function setSelectPageCountItems(element) {
  $(element).on('click', function (e) {
    e.preventDefault();
    clearTimeout($.data(this, 'timer'));
    $('ul', this).stop(true, true).slideDown(200);
    $(element+' .arrow').addClass('upside');
  });

  $(element).hover(function () {
    // clearTimeout($.data(this, 'timer'));
    // $('ul', this).stop(true, true).slideDown(200);
  }, function () {

    $.data(this, 'timer', setTimeout($.proxy(function () {
      $('ul', this).stop(true, true).slideUp(200);
      $(element+' .arrow').removeClass('upside');
    }, this), 100));
  });
}


