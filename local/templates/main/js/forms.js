$(function () {
    // input mask
    $('input[type="tel"]').inputmask('+7 (999) 999-99-99');

    // custom select
    $('.custom-select').select2({
        minimumResultsForSearch: Infinity,
    });

    $('.custom-select-link').select2({
        width: 'auto',
        minimumResultsForSearch: Infinity,
    });

    // custom number
    $(document).on('click', '.custom-control-plus', function () {
        var input = $(this).parent().find('input');

        var val = parseInt(input.val(), 10);
        if (isNaN(val)) {
            val = 1;
        }

        input.val(val + 1);
        input.change();

        return false;
    });

    $(document).on('click', '.custom-control-minus', function () {
        var input = $(this).parent().find('input');

        var val = parseInt(input.val(), 10);
        if (isNaN(val)) {
            val = 1;
        }

        var count = val - 1;

        count = count < 1 ? 1 : count;
        input.val(count);
        input.change();

        return false;
    });

    // custom option
    $(document).on('change', '.custom-option .custom-control-input', function(e) {
        let target = $(e.currentTarget),
            targetContainer = target.closest('.custom-option'),
            targetDetails = targetContainer.find('.custom-control-details'),
            optionsContainer = targetContainer.closest('.form-options-group'),
            optionsChildrenDetails = optionsContainer.find('.custom-control-details');

        optionsChildrenDetails.slideUp();
        targetDetails.slideDown();
    });

    // custom input file
    bsCustomFileInput.init();
});
