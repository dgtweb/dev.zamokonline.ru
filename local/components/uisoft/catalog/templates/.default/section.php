<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use Bitrix\Main\Loader;
use Uisoft\App\Bitrix\ComponentEngine;
use Uisoft\App\Tools;

Tools::setCatalogSetParamsRedirect('viewTypeSection');
Tools::setCatalogSetParamsRedirect('itemsCountSection');
Tools::setCatalogSetParamsRedirect('orderSection');

// Вид списка товаров grid/list
$listTypeUrls = Tools::getCatalogSectionView();

// Кол-во товаров на странице
$countsUrls = Tools::getCatalogItemsCount();

// Сортировка товаров
$orderUrls = Tools::getCatalogOrder();

?>

<div class="display-header"><h1 class="display-title"><? $APPLICATION->ShowTitle(); ?></h1></div>
<?
////Необходимо сохранить тип списка в cookie если он был изменен
//$isListSection = true;

$this->setFrameMode(true);

$isFilter = $arParams['USE_FILTER'] === 'Y';

if ($isFilter) {
    $arFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
    );

    if ((int)$arResult["VARIABLES"]["SECTION_ID"] > 0) {
        $arFilter["ID"] = (int)$arResult["VARIABLES"]["SECTION_ID"];
    } elseif ($arResult["VARIABLES"]["SECTION_CODE"] !== '') {
        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
    }

    $obCache = new CPHPCache();
    if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog")) {
        $arCurSection = $obCache->GetVars();
    } elseif ($obCache->StartDataCache()) {
        $arCurSection = array();
        if (Loader::includeModule("iblock")) {
            $dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID", 'NAME'));

            if (defined("BX_COMP_MANAGED_CACHE")) {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                if ($arCurSection = $dbRes->Fetch()) {
                    $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);
                }

                $CACHE_MANAGER->EndTagCache();
            } elseif (!$arCurSection = $dbRes->Fetch()) {
                $arCurSection = array();
            }
        }
        $obCache->EndDataCache($arCurSection);
    }
    if (!isset($arCurSection)) {
        $arCurSection = array();
    }
}

// Т. к. для получения фильтра из реального раздела мы раздел структуры на него, нам необходимо передать в
// SEF_RULE подставленный путь к разделу из структуры
$sefRule = '';
if (!empty($arResult['STRUCTURE_SECTION']['sectionStructurePath'])) {
    $sefRule = ComponentEngine::prepareSefRule($arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
        $arResult['STRUCTURE_SECTION']['sectionStructurePath']);
}

// Запомним ID раздела
$GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $arResult["VARIABLES"]["SECTION_ID"];

?>

<div class="row">
    <div class="catalog-filter-container col-12 col-xl-3">
        <div class="filter-wrapper">
            <div class="filter-inner">
                <div class="filter-header text-center d-md-none">
                    <div class="filter-title">Фильтры</div>
                    <button type="button" class="filter-close" aria-label="Close">
                        <i aria-hidden="true" class="icon-angle-left"></i>
                    </button>
                </div>

                <form action="" class="filter-form">
                    <div class="filter-options-block">

                        <?
                        $smartFilterParams = [
                            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                            'CACHE_TIME' => $arParams['CACHE_TIME'],
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CONVERT_CURRENCY' => 'N',
                            'DISPLAY_ELEMENT_COUNT' => 'Y',
                            'FILTER_NAME' => 'arrFilter',
                            // Если раздел находится в инфоблоке 'Структура каталога'
                            'STRUCTURE_SECTION' => $arResult['STRUCTURE_SECTION'],
                            // Не отображать недоступные товары
                            'HIDE_NOT_AVAILABLE' => 'N',
                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                            // Имя массива с переменными для построения ссылок в постраничной навигации
                            'PAGER_PARAMS_NAME' => 'arrPager',
                            // Позиция для отображения всплывающего блока с информацией о фильтрации
                            'POPUP_POSITION' => 'right',
                            // Имя входящего массива для дополнительной фильтрации элементов
                            'PREFILTER_NAME' => 'smartPreFilter',
                            // Тип цены
                            'PRICE_CODE' => ['BASE'],
                            // Сохранять установки фильтра в сессии пользователя
                            'SAVE_IN_SESSION' => 'N',
                            // Код раздела
                            'SECTION_CODE' => '',
                            // Блок ЧПУ умного фильтра
                            'SMART_FILTER_PATH' => $arResult['VARIABLES']['SMART_FILTER_PATH'],
                            // Правило для обработки
                           "SEF_RULE" =>  $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                            // Путь из символьных кодов раздела
                            'SECTION_CODE_PATH' => $arResult['VARIABLES']['SECTION_CODE_PATH'],
                            // Описание
                            'SECTION_DESCRIPTION' => '-',
                            // ID раздела инфоблока
                            'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
                            // Заголовок
                            'SECTION_TITLE' => '-',
                            // Включить поддержку ЧПУ
                            'SEF_MODE' => 'Y',
                            // Цветовая тема
                            'TEMPLATE_THEME' => '',
                            // Включить поддержку Яндекс Островов
                            'XML_EXPORT' => 'N',
                            // мгновенная фильтрация при включенном ajax
                            'INSTANT_RELOAD' => 'Y'
                        ];


                        $APPLICATION->IncludeComponent(
                            "bitrix:catalog.smart.filter",
                            "",
                            $smartFilterParams,
                            $component,
                            ["HIDE_ICONS" => "Y"]
                        );

                  //      Tools::clearFilterRedirect('filter/clear/');
                        ?>
                    </div>
                </form>
            </div>
            <div class="filter-bg bg"></div>
        </div>
    </div>
    <div class="catalog-content-container col-12 col-xl-9 d-flex flex-column">
        <?
        /* <div class="catalog-banner-block order-9 order-xl-0">
                       <div class="card-banner">
                           <div class="card-banner-inner">
                               <div class="form-row">
                                   <div class="card-banner-typography-container col-6 col-xl-7 align-self-center">
                                       <div class="card-banner-title">Замена и установка цилиндра бесплатно!</div>
                                       <div class="card-banner-description d-none d-md-block">В пределах МКАД (г.Москва). При стоимости цилиндра от 10 000 руб.</div>
                                   </div>

                                   <div class="card-banner-media-container col-6 col-xl-5 d-flex">
                                       <figure class="card-banner-thumbnail">
                                           <img src="uploads/banners/banner-item-1.jpg" alt="Замена и установка цилиндра бесплатно!" class="card-banner-media">
                                       </figure>
                                   </div>
                               </div>
                           </div>

                           <div class="card-banner-description text-center d-md-none">В пределах МКАД (г.Москва). При стоимости цилиндра от 10 000 руб.</div>
                       </div>
                   </div>*/ ?>

        <div class="catalog-controls-block catalog-mobile-controls d-xl-none">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <button type="button" class="catalog-filter-toggle filter-toggle btn">Фильтры</button>
                </div>

                <?
                // todo: тут закончил. надо сделать сортировку для моб. версии
                ?>
                <div class="col-auto">
                    <button type="button" class="btn btn-secondary btn-outline dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Сортировка
                    </button>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                        <?
                        foreach ($orderUrls['items'] as $itemUrl): ?>
                            <a href="<?= $itemUrl['url'] ?>" class="dropdown-item <?= ($itemUrl['set']) ? 'select' : '' ?>">
                                <?= $itemUrl['name'] ?></a>
                        <?
                        endforeach; ?>
                    </div>
                </div>


            </div>
        </div>

        <div class="catalog-controls-block catalog-desktop-controls d-none d-xl-block">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="catalog-sort-block d-flex align-items-center">
                        <div class="uisoft-select-order catalog-order">
                            <?= $orderUrls['select']['name'] ?><span class="arrow"></span>
                            <ul class="catalog-order-select">

                                <?
                                foreach ($orderUrls['items'] as $itemUrl): ?>
                                    <li><a href="<?= $itemUrl['url'] ?>" <?= ($itemUrl['set']) ? 'class="select"'
                                            : '' ?>><?= $itemUrl['name'] ?></a></li>
                                <?
                                endforeach; ?>
                            </ul>
                        </div>
                        <?
                        /*<span class="catalog-control-label">Сортировать:</span>
                                                <button type="button" class="catalog-sort-button">
                                                    <svg width="12" height="12">
                                                        <use xlink:href="/images/icons/sprite.svg#icon-descending"></use>
                                                    </svg>
                                                    По цене
                                                </button>*/ ?>
                    </div>
                </div>

                <script type="text/javascript">
                  BX.ready(function () {
                    setSelectPageCountItems('.uisoft-select-order');
                  });
                </script>


                <div class="col-auto d-none d-md-block">
                    <div class="catalog-view-block d-flex align-items-center">

                        <span class="catalog-control-label">Показать по:</span>
                        <div class="uisoft-select-view-count catalog-view-count"><?= $countsUrls['select'] ?><span class="arrow"></span>
                            <ul class="catalog-view-count-select">
                                <?
                                foreach ($countsUrls['items'] as $itemUrl): ?>
                                    <li><a href="<?= $itemUrl['url'] ?>" <?= ($itemUrl['set']) ? 'class="select"'
                                            : '' ?>><?= $itemUrl['count'] ?></a></li>
                                <?
                                endforeach; ?>
                            </ul>
                        </div>

                        <script type="text/javascript">
                          BX.ready(function () {
                            setSelectPageCountItems('.uisoft-select-view-count');
                          });
                        </script>

                        <?
                        /*
                                                                        <label for="catalog-view-select" class="catalog-control-label">Показать по:</label>
                                                                        <select name="" id="catalog-view-select" class="custom-select custom-select-link" width="auto">
                                                                            <option value="12">12</option>
                                                                            <option value="24">24</option>
                                                                            <option value="48">48</option>
                                                                        </select>*/ ?>
                    </div>
                </div>

                <div class="col-auto ml-auto d-none d-md-block">
                    <div class="catalog-layout-block d-flex align-items-center">
                        <span class="catalog-control-label">Вид:</span>

                        <ul class="catalog-layout-list d-inline-flex list-unstyled">
                            <li class="catalog-layout-item">
                                <a href="<?= $listTypeUrls['grid']['url'] ?>"
                                   class="catalog-layout-link <?= ($listTypeUrls['grid']['set']) ? 'active' : '' ?>">
                                    <svg width="17" height="17">
                                        <use xlink:href="/images/icons/sprite.svg#icon-layout-grid"></use>
                                    </svg>
                                </a>
                            </li>

                            <li class="catalog-layout-item">
                                <a href="<?= $listTypeUrls['list']['url'] ?>"
                                   class="catalog-layout-link <?= ($listTypeUrls['list']['set']) ? 'active' : '' ?>">
                                    <svg width="18" height="17">
                                        <use xlink:href="/images/icons/sprite.svg#icon-layout-list"></use>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <?

        // Изменяем ЧПУ для элементов. Необходимо чтоб каталог был из структуры
        $sefRuleElement = '';
        if (!empty($arResult['STRUCTURE_SECTION']['sectionStructurePath'])) {
            $sefRuleElement = ComponentEngine::prepareSefRule($arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                $arResult['STRUCTURE_SECTION']['sectionStructurePath']);
        }

        $sectionParams = [
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
            "SECTION_CODE" => "",
            "FILTER_NAME" => "arrFilter",
            "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
            "SHOW_ALL_WO_SECTION" => "N",

            // Сортировка
            "ELEMENT_SORT_FIELD" => $orderUrls['select']['field'],
            "ELEMENT_SORT_ORDER" => $orderUrls['select']['order'],
//            "ELEMENT_SORT_FIELD2" => 'NAME',
//            "ELEMENT_SORT_ORDER2" => 'ASC',

            "PAGE_ELEMENT_COUNT" => (int)$countsUrls['select'],

            "PROPERTY_CODE" => ($arParams["LIST_PROPERTY_CODE"] ?? []),
            "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],

            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_FILTER" => $arParams["CACHE_FILTER"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
            "PRICE_CODE" => $arParams["~PRICE_CODE"],
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

            "SET_BROWSER_TITLE" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_LAST_MODIFIED" => "N",
            "ADD_SECTIONS_CHAIN" => "Y",

            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
            "ADD_PROPERTIES_TO_BASKET" => ($arParams["ADD_PROPERTIES_TO_BASKET"] ?? ''),
            "PARTIAL_PRODUCT_PROPERTIES" => ($arParams["PARTIAL_PRODUCT_PROPERTIES"] ?? ''),
            "PRODUCT_PROPERTIES" => ($arParams["PRODUCT_PROPERTIES"] ?? []),

            "OFFERS_CART_PROPERTIES" => ($arParams["OFFERS_CART_PROPERTIES"] ?? []),
            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => ($arParams["LIST_OFFERS_PROPERTY_CODE"] ?? []),
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => ($arParams["LIST_OFFERS_LIMIT"] ?? 0),


            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL" => $sefRuleElement,
            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
            'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

            'LABEL_PROP' => $arParams['LABEL_PROP'],
            'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
            'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
            'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
            'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
            'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
            'PRODUCT_ROW_VARIANTS' => "[{'VARIANT':'3','BIG_DATA':true}]",
            'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
            'ENLARGE_PROP' => $arParams['LIST_ENLARGE_PROP'] ?? '',
            'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
            'SLIDER_INTERVAL' => $arParams['LIST_SLIDER_INTERVAL'] ?? '',
            'SLIDER_PROGRESS' => $arParams['LIST_SLIDER_PROGRESS'] ?? '',

            "DISPLAY_TOP_PAGER" => 'N',
            "DISPLAY_BOTTOM_PAGER" => 'Y',
            "HIDE_SECTION_DESCRIPTION" => "Y",

            "RCM_TYPE" => $arParams['BIG_DATA_RCM_TYPE'] ?? '',
            "SHOW_FROM_SECTION" => 'Y',

            'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
            'OFFER_TREE_PROPS' => ($arParams['OFFER_TREE_PROPS'] ?? []),
            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
            'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'MESS_SHOW_MAX_QUANTITY' => ($arParams['~MESS_SHOW_MAX_QUANTITY'] ?? ''),
            'RELATIVE_QUANTITY_FACTOR' => ($arParams['RELATIVE_QUANTITY_FACTOR'] ?? ''),
            'MESS_RELATIVE_QUANTITY_MANY' => ($arParams['~MESS_RELATIVE_QUANTITY_MANY'] ?? ''),
            'MESS_RELATIVE_QUANTITY_FEW' => ($arParams['~MESS_RELATIVE_QUANTITY_FEW'] ?? ''),
            'MESS_BTN_BUY' => ($arParams['~MESS_BTN_BUY'] ?? ''),
            'MESS_BTN_ADD_TO_BASKET' => ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? ''),
            'MESS_BTN_SUBSCRIBE' => ($arParams['~MESS_BTN_SUBSCRIBE'] ?? ''),
            'MESS_BTN_DETAIL' => ($arParams['~MESS_BTN_DETAIL'] ?? ''),
            'MESS_NOT_AVAILABLE' => ($arParams['~MESS_NOT_AVAILABLE'] ?? ''),
            'MESS_BTN_COMPARE' => ($arParams['~MESS_BTN_COMPARE'] ?? ''),

            'USE_ENHANCED_ECOMMERCE' => ($arParams['USE_ENHANCED_ECOMMERCE'] ?? ''),
            'DATA_LAYER_NAME' => ($arParams['DATA_LAYER_NAME'] ?? ''),
            'BRAND_PROPERTY' => ($arParams['BRAND_PROPERTY'] ?? ''),

            'TEMPLATE_THEME' => ($arParams['TEMPLATE_THEME'] ?? ''),
            'ADD_TO_BASKET_ACTION' => 'ADD',
            'SHOW_CLOSE_POPUP' => $arParams['COMMON_SHOW_CLOSE_POPUP'] ?? '',
            'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
            'COMPARE_NAME' => $arParams['COMPARE_NAME'],
            'USE_COMPARE_LIST' => 'Y',
            'BACKGROUND_IMAGE' => '',
            'DISABLE_INIT_JS_IN_COMPONENT' => ($arParams['DISABLE_INIT_JS_IN_COMPONENT'] ?? '')
        ];

         //\ALS\Helper\Dbg::show($sectionParams, true, true);

        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            $listTypeUrls['grid']['set'] ? 'grid' : 'list',
            $sectionParams,
            $component
        );
        ?>
    </div>
</div>
