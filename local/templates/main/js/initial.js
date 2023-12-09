(function ($, window) {
    window.beforeWindowWidthResizeFunctions = [];
    $(document).ready(function () {
        windowSizeHelper.setWindowWidth();

        window.addEventListener('resize', function () {
            let beforeResizeFunctionsResults = [];

            beforeWindowWidthResizeFunctions.forEach(function (callback) {
                let callbackResult = callback();
                if (typeof callbackResult != 'undefined') {
                    beforeResizeFunctionsResults.push(callbackResult);
                }
            });

            if (beforeResizeFunctionsResults.length > 0) {
                $.when.apply(this, beforeResizeFunctionsResults).then(function () {
                    windowSizeHelper.setWindowWidth();
                });
            } else {
                windowSizeHelper.setWindowWidth();
            }
        });
    });
})(jQuery, window);
