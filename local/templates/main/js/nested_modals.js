$(function () {
    $(document).on('click', '.modal [data-toggle="nested-modal"]', function (e) {
        let target = $(e.currentTarget);
        let nestedModalContainer = target.attr('data-target');
        let modalParentContainer = target.closest('.modal');

        $(modalParentContainer).modal('hide');

        setTimeout(function() {
            $(nestedModalContainer).modal('show');
        }, 400);
    });
});