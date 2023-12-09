<?php

namespace Uisoft\App;

use Bitrix\Main\Page\Asset;

class ExDebug{
    public static function include($includeHere = false): void
    {
        if (defined('EX_DEBUG') && EX_DEBUG) {
            if ($includeHere) {
                echo '<link href="' . SITE_TEMPLATE_PATH . '/vendor/ex_debug/ex_debug.css" type="text/css" rel="stylesheet">';
                echo '<script type="text/javascript" src="' . SITE_TEMPLATE_PATH . '/vendor/ex_debug/ex_debug.js"></script>';

            } else {
                $asset = Asset::getInstance();
                $asset->addCss(SITE_TEMPLATE_PATH . "/vendor/ex_debug/ex_debug.css");
                $asset->addJs(SITE_TEMPLATE_PATH . "/vendor/ex_debug/ex_debug.js");
            }
        }
    }
}