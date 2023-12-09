<?php

/**
 * @global CMain                       $APPLICATION
 * @var array                          $arParams
 * @var array                          $arResult
 * @var CatalogProductsViewedComponent $component
 * @var CBitrixComponentTemplate       $this
 * @var string                         $templateName
 * @var string                         $componentPath
 * @var string                         $templateFolder
 */

const HIDE_PAGE_SECTION_TEMPLATE = true;
const DEBUG = false;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Каталог");
?>
    <section class="<?
    $APPLICATION->ShowProperty('sectionClass', 'page'); ?>">
        <div class="container">
            <div class="display-header">
                <h1 class="display-title"><?
                    $APPLICATION->ShowTitle(); ?></h1>
            </div>

            <?
            $APPLICATION->IncludeComponent("uisoft:catalog", "", array(
                "IBLOCK_TYPE" => "catalog",    // Тип инфоблока
                "IBLOCK_ID" => "7",    // Инфоблок
                "AJAX_MODE" => "Y",    // Включить режим AJAX
                "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
                "AJAX_OPTION_STYLE" => "Y",    // Включить подгрузку стилей
                "AJAX_OPTION_HISTORY" => "Y",    // Включить эмуляцию навигации браузера
                "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
                "USE_FILTER" => "Y",    // Показывать фильтр
                "COMPONENT_TEMPLATE" => ".default",
                "FILTER_NAME" => "arrFilter",    // Фильтр
                "SEF_MODE" => "Y",    // Включить поддержку ЧПУ
                "SEF_FOLDER" => "/catalog/",    // Каталог ЧПУ (относительно корня сайта)
                "SET_STATUS_404" => "N",    // Устанавливать статус 404
                "SHOW_404" => "N",    // Показ специальной страницы
                "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
                "IBLOCK_TYPE_STRUCTURE" => "structure",    // Тип инфоблока структуры разделов
                "IBLOCK_ID_STRUCTURE" => "11",    // Инфоблок структуры разделов
                "SEF_URL_TEMPLATES" => array(
                    "sections" => "",
                    "section" => "#SECTION_CODE_PATH#/",
                    "element" => "#SECTION_CODE_PATH#/#ELEMENT_ID#/",
                    "compare" => "",
                    "smart_filter" => "#SECTION_CODE_PATH#/f/#SMART_FILTER_PATH#/",
                )
            ),
                false
            ); ?>

        </div>
    </section>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
