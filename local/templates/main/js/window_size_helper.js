(function ($, window) {
    var mobileWidth = 1023,
        phoneWidth = 767;

    var windowWidth = window.innerWidth;

    function setWindowWidth() {
        if (windowWidth != window.innerWidth) {
            windowWidth = window.innerWidth;
        }
    }

    function isMobile() {
        return (window.innerWidth <= mobileWidth);
    }

    function isPhone() {
        return (window.innerWidth <= phoneWidth);
    }

    function isTablet() {
        return isMobile() && !isPhone();
    }

    function isHorizontalResize() {
        return windowWidth != window.innerWidth;
    }

    function isMobileToDesktopResize() {
        return isHorizontalResize() && windowWidth <= mobileWidth && window.innerWidth > mobileWidth;
    }

    function isDesktopToMobileResize() {
        return isHorizontalResize() && windowWidth > mobileWidth && window.innerWidth <= mobileWidth;
    }

    function isTabletToPhoneResize() {
        return isHorizontalResize() && windowWidth > phoneWidth && window.innerWidth <= phoneWidth;
    }

    function isPhoneToTabletResize() {
        return isHorizontalResize() && windowWidth <= phoneWidth && window.innerWidth > phoneWidth;
    }

    function isDesktopMobileSwitch() {
        return isDesktopToMobileResize() || isMobileToDesktopResize();
    }

    function isTabletPhoneSwitch() {
        return isTabletToPhoneResize() || isPhoneToTabletResize();
    }

    window.windowSizeHelper = {
        setWindowWidth: setWindowWidth,
        isMobile: isMobile,
        isPhone: isPhone,
        isTablet: isTablet,
        isHorizontalResize: isHorizontalResize,
        isMobileToDesktopResize: isMobileToDesktopResize,
        isDesktopToMobileResize: isDesktopToMobileResize,
        isTabletToPhoneResize: isTabletToPhoneResize,
        isPhoneToTabletResize: isPhoneToTabletResize,
        isDesktopMobileSwitch: isDesktopMobileSwitch,
        isTabletPhoneSwitch: isTabletPhoneSwitch
    };
})(jQuery, window);