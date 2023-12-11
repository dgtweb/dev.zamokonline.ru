<?php

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogProductsViewedComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

const HIDE_PAGE_SECTION_TEMPLATE = true;
const DEBUG = false;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Каталог");
?>
<section class="<?
$APPLICATION->ShowProperty('sectionClass', 'page'); ?>">
    <div class="container">
        <? $APPLICATION->IncludeComponent(
            "uisoft:catalog",
            ".default",
            array(
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "22",
                "AJAX_MODE" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "Y",
                "AJAX_OPTION_ADDITIONAL" => "",
                "PRICE_CODE" => array(
                    0 => "BASE",
                ),
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
                "USE_FILTER" => "Y",
                "COMPONENT_TEMPLATE" => ".default",
                "FILTER_NAME" => "arrFilter",
                "SEF_MODE" => "Y",
                "SEF_FOLDER" => "/catalog/",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => "",
                "IBLOCK_TYPE_STRUCTURE" => "structure",
                "IBLOCK_ID_STRUCTURE" => "11",
                "INCLUDE_SUBSECTIONS" => "Y",
                "SEF_URL_TEMPLATES" => array(
                    "sections" => "",
                    "section" => "#SECTION_CODE_PATH#/",
                    "element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#.html",
                    "compare" => "/",
                    "smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
                )
            ),
            false
        ); ?>

    </div>
</section>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
