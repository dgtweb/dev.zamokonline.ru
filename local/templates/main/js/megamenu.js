$(function () {
	let megamenuContainer = $('.header-nav'),
		megamenuChildItems = megamenuContainer.find('.header-nav-item'),
		megamenuChildTarget = undefined,
		timerInitMegamenu = undefined,
		timerDestroyMegamenu = undefined;

	$(document).on('mouseenter', '.header-nav:not(.is-init) .header-nav-item', function(e) {
		let target = $(e.currentTarget);
		megamenuChildTarget = target;

		timerInitMegamenu = setTimeout(function() {
			megamenuContainer.addClass('is-init');
			megamenuChildTarget.addClass('is-hover');
		}, 500);
	});


	$(document).on('mouseenter', '.header-nav.is-init .header-nav-item', function(e) {
		let target = $(e.currentTarget);
		megamenuChildTarget = target;

		megamenuChildItems.removeClass('is-hover')
		megamenuChildTarget.addClass('is-hover');

		clearTimeout(timerDestroyMegamenu);
	});


	$(document).on('mouseleave', '.header-nav', function(e) {
		megamenuChildItems.removeClass('is-hover');


		timerDestroyMegamenu = setTimeout(function() {
			megamenuContainer.removeClass('is-init');
		}, 300);

		clearTimeout(timerInitMegamenu);
	});
});