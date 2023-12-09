<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if (empty($arResult) || count($arResult) <= 1) {
    return "";
}

$strReturn = '
<section class="section-breadcrumb">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <nav class="breadcrumb-navigation" aria-label="breadcrumb" itemprop="https://schema.org/breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                            <ol class="breadcrumb list-unstyled">';
$itemSize = count($arResult);
for ($index = 0; $index < $itemSize; $index++) {
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if ($arResult[$index]["LINK"] !== "" && $index !== $itemSize - 1) {
        $strReturn .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href = "' . $arResult[$index]["LINK"] . '" title="' . $title . '" itemprop="item">' . $title . '</a ></li >';
    } else {
        $strReturn .= '<li class="breadcrumb-item active" aria-current="page">' . $title . '</li>';
    }
}
$strReturn .= '
                            </ol>
                        </nav>
                    </div>

                    <div class="col-auto d-none d-xl-block">
                        <a href="#" class="breadcrumb-back-btn btn-link" onclick="javascript:history.back(); return false;">
                            <i class="icon-angle-left"></i>Вернуться назад</a>
                    </div>
                </div>
            </div>
        </section>
';
return $strReturn;
