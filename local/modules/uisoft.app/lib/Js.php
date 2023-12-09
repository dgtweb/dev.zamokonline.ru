<?php

namespace Uisoft\App;

use Bitrix\Main\Page\Asset;

class Js
{
    /**
     * Подключает js скрипты
     */
    public static function include(): void
    {
        $asset = Asset::getInstance();

        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/jquery-3.6.0/jquery-3.6.0.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/popper/popper.min.js");

        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/bootstrap-4.6.0/js/dist/util.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/bootstrap-4.6.0/js/dist/tab.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/bootstrap-4.6.0/js/dist/dropdown.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/bootstrap-4.6.0/js/dist/collapse.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/bootstrap-4.6.0/js/dist/modal.js");

        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/swiper/js/swiper.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/select2/js/select2.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/inputmask/jquery.inputmask.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/bs-custom-file-input/bs-custom-file-input.min.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/fancybox/jquery.fancybox.min.js");

        $asset->addJs(SITE_TEMPLATE_PATH . "/js/nf_pp.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/initial.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/window_size_helper.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/swiper_helpers.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/offcanvas.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/search.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/megamenu.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/sliders.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/forms.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/filter.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/nested_modals.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/preloader.js");
       // $asset->addJs(SITE_TEMPLATE_PATH . "/js/main.js");
        $asset->addJs(SITE_TEMPLATE_PATH . "/js/core.js");

        ExDebug::include();
    }
}
