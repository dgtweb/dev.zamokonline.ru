$(function () {
    var headerContainer = $('.header-wrapper'),
        headerContactContainer = headerContainer.find('.header-contact-container'),
        headerSearchToggle = headerContainer.find('.header-search-toggle'),
        headerSearchContainer = headerContainer.find('.header-search-wrapper'),
        headerSearchInput = headerSearchContainer.find('.header-search-input'),
        headerSearchSuggestionsContainer = headerSearchContainer.find('.header-search-suggestion-block'),
        headerBg = headerSearchContainer.find('.header-search-bg');

    function showHeaderSearch() {
        headerSearchContainer.addClass('show');
        headerSearchToggle.addClass('hide');
        headerContactContainer.addClass('hide');
        headerBg.addClass('show');
        headerSearchInput.focus();
    }

    function hideHeaderSearch() {
        headerSearchContainer.removeClass('show');
        headerSearchToggle.removeClass('hide');
        headerContactContainer.removeClass('hide');
        headerBg.removeClass('show');

        hideHeaderSearchSuggestion();
    }

    function showHeaderSearchSuggestion() {
        headerSearchSuggestionsContainer.addClass('show');
    }

    function hideHeaderSearchSuggestion() {
        headerSearchSuggestionsContainer.removeClass('show');
    }
    
    // show header search form
    $(headerSearchToggle).on('click', function(e) {
        let target = $(e.currentTarget);

        if(target.hasClass('hide')) {
            return hideHeaderSearch();
        }

        showHeaderSearch();
    });

    // hide header search form
    $(headerBg).on('click', function() {
        hideHeaderSearch();
    });

    // show header search suggestions
    $(headerSearchInput).on('input', function (e) {
        if(headerSearchInput[0].value.length == 0) {
            hideHeaderSearchSuggestion()
        }
        
        if(headerSearchInput[0].value.length >= 1) {
            showHeaderSearchSuggestion()
        }
    });
});