<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Bitrix vars
 *
 * @global CMain $APPLICATION
 */

CJSCore::Init(array("fx"));

// Определение главной страницы
$curDir = $APPLICATION->GetCurDir();
$isMainPage = ($curDir === "/") /*|| ERROR_404 === 'Y'*/
;
if ($isMainPage) {
    define("IS_MAIN_PAGE", true);
}

global $USER;
?>

    <!doctype html>
    <html lang="ru">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <?php
        // Подключение стилей
        \Uisoft\App\Css::include();

        //Подключение Js
        \Uisoft\App\Js::include();
        ?>

        <?php $APPLICATION->ShowHead(); ?>
        <title><?php $APPLICATION->ShowTitle() ?></title>

    </head>
<body class="page-home d-flex flex-column">
    <div class="admin-panel d-none">
        <?php $APPLICATION->ShowPanel(); ?>
    </div>

    <header class="header-wrapper">

        <?php $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "top_bar_navigation",
            array(
                "ACTIVE_DATE_FORMAT"              => "d.m.Y",
                "ADD_SECTIONS_CHAIN"              => "N",
                "AJAX_MODE"                       => "N",
                "AJAX_OPTION_ADDITIONAL"          => "",
                "AJAX_OPTION_HISTORY"             => "N",
                "AJAX_OPTION_JUMP"                => "N",
                "AJAX_OPTION_STYLE"               => "Y",
                "CACHE_FILTER"                    => "N",
                "CACHE_GROUPS"                    => "Y",
                "CACHE_TIME"                      => "36000000",
                "CACHE_TYPE"                      => "A",
                "CHECK_DATES"                     => "Y",
                "DETAIL_URL"                      => "",
                "DISPLAY_BOTTOM_PAGER"            => "N",
                "DISPLAY_DATE"                    => "N",
                "DISPLAY_NAME"                    => "N",
                "DISPLAY_PICTURE"                 => "N",
                "DISPLAY_PREVIEW_TEXT"            => "N",
                "DISPLAY_TOP_PAGER"               => "N",
                "FIELD_CODE"                      => array(
                    0 => "CODE",
                    1 => "PREVIEW_TEXT",
                    2 => "",
                ),
                "FILTER_NAME"                     => "",
                "HIDE_LINK_WHEN_NO_DETAIL"        => "N",
                "IBLOCK_ID"                       => "4",
                "IBLOCK_TYPE"                     => "menu",
                "INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
                "INCLUDE_SUBSECTIONS"             => "N",
                "MESSAGE_404"                     => "",
                "NEWS_COUNT"                      => "20",
                "PAGER_BASE_LINK_ENABLE"          => "N",
                "PAGER_DESC_NUMBERING"            => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL"                  => "N",
                "PAGER_SHOW_ALWAYS"               => "N",
                "PAGER_TEMPLATE"                  => ".default",
                "PAGER_TITLE"                     => "Новости",
                "PARENT_SECTION"                  => "",
                "PARENT_SECTION_CODE"             => "",
                "PREVIEW_TRUNCATE_LEN"            => "",
                "PROPERTY_CODE"                   => array(
                    0 => "",
                    1 => "",
                ),
                "SET_BROWSER_TITLE"               => "N",
                "SET_LAST_MODIFIED"               => "N",
                "SET_META_DESCRIPTION"            => "N",
                "SET_META_KEYWORDS"               => "N",
                "SET_STATUS_404"                  => "N",
                "SET_TITLE"                       => "N",
                "SHOW_404"                        => "N",
                "SORT_BY1"                        => "SORT",
                "SORT_BY2"                        => "NAME",
                "SORT_ORDER1"                     => "ASC",
                "SORT_ORDER2"                     => "ASC",
                "STRICT_SECTION_CHECK"            => "N",
                "COMPONENT_TEMPLATE"              => "top_bar_navigation"
            ),
            false
        ); ?>

        <div class="header-main-wrapper">
            <div class="header-main-container container">
                <div class="form-row align-items-center">
                    <div class="header-toggle-container col-auto d-xl-none">
                        <button type="button" class="header-toggle d-flex flex-column align-items-center">
                            <div class="header-toggle-thumbnail">
                                <svg class="header-toggle-media" width="18" height="18">
                                    <use xlink:href="/images/icons/sprite.svg#icon-menu"></use>
                                </svg>
                            </div>

                            <div class="header-toggle-value">Меню</div>
                        </button>
                    </div>

                    <div class="header-logo-container col-auto col-xl-2">
                        <? if (!$isMainPage): ?>
                            <a href="/" class="header-logo-block">
                                <div class="header-logo-thumbnail">
                                    <img src="/images/logo.svg" alt="Ваш Замок" class="header-logo-image">
                                </div>
                            </a>
                        <? else: ?>
                            <span class="header-logo-block">
                        <div class="header-logo-thumbnail">
                            <img src="/images/logo.svg" alt="Ваш Замок" class="header-logo-image">
                        </div>
                        </span>
                        <? endif; ?>
                    </div>


                    <? $APPLICATION->IncludeComponent(
                        "bitrix:news.line",
                        "header_store_addresses",
                        array(
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "CACHE_GROUPS"       => "Y",
                            "CACHE_TIME"         => "300",
                            "CACHE_TYPE"         => "A",
                            "COMPONENT_TEMPLATE" => "header_store_addresses",
                            "DETAIL_URL"         => "",
                            "FIELD_CODE"         => array(
                                0 => "NAME",
                                1 => "PREVIEW_TEXT",
                                2 => "PROPERTY_TYPE",
                                3 => "PROPERTY_METRO",
                                4 => "PROPERTY_PHONE",
                                5 => "PROPERTY_OPENING_HOURS",
                                6 => "PROPERTY_WHATSAPP",
                            ),
                            "IBLOCKS"            => array(0 => "5",),
                            "IBLOCK_TYPE"        => "services",
                            "NEWS_COUNT"         => "20",
                            "SORT_BY1"           => "SORT",
                            "SORT_BY2"           => "",
                            "SORT_ORDER1"        => "ASC",
                            "SORT_ORDER2"        => ""
                        )
                    ); ?>

                    <div class="header-account-container col-auto col-xl-5 ml-auto">
                        <div class="header-account-block">
                            <ul class="header-account-list d-flex flex-wrap justify-content-center list-unstyled">
                                <li class="header-account-item order-0">
                                    <button type="button" class="header-account-link header-search-toggle">
                                        <div class="header-account-thumbnail d-flex align-items-center">
                                            <svg class="header-account-media" width="27" height="27">
                                                <use xlink:href="/images/icons/sprite.svg#icon-search"></use>
                                            </svg>
                                        </div>

                                        <div class="header-account-value">Поиск</div>
                                    </button>
                                </li>

                                <li class="header-account-item has-dropdown d-none d-xl-block order-1">
                                    <a href="contacts.html" class="header-account-link" data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <div class="header-account-thumbnail d-flex align-items-center">
                                            <svg class="header-account-media" width="35" height="35">
                                                <use xlink:href="/images/icons/sprite.svg#icon-shop"></use>
                                            </svg>
                                        </div>

                                        <div class="header-account-value">Магазины</div>
                                    </a>

                                    <div class="header-shops-dropdown header-account-dropdown">
                                        <? $APPLICATION->IncludeComponent(
                                            "bitrix:news.line",
                                            "header_popup_store_addresses",
                                            array(
                                                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                                "CACHE_GROUPS"       => "Y",
                                                "CACHE_TIME"         => "300",
                                                "CACHE_TYPE"         => "A",
                                                "COMPONENT_TEMPLATE" => "header_store_addresses",
                                                "DETAIL_URL"         => "",
                                                "FIELD_CODE"         => array(
                                                    0 => "NAME",
                                                    1 => "PREVIEW_TEXT",
                                                    2 => "PROPERTY_TYPE",
                                                    3 => "PROPERTY_METRO",
                                                    4 => "PROPERTY_PHONE",
                                                    5 => "PROPERTY_OPENING_HOURS",
                                                    6 => "PROPERTY_WHATSAPP",
                                                ),
                                                "IBLOCKS"            => array(0 => "5",),
                                                "IBLOCK_TYPE"        => "services",
                                                "NEWS_COUNT"         => "20",
                                                "SORT_BY1"           => "SORT",
                                                "SORT_BY2"           => "",
                                                "SORT_ORDER1"        => "ASC",
                                                "SORT_ORDER2"        => ""
                                            )
                                        ); ?>
                                    </div>
                                </li>

                                <? /*
                            <li class="header-account-item order-3 order-xl-2">
                                <a href="#link" class="header-account-link">
                                    <div class="header-account-thumbnail d-flex align-items-center">
                                        <svg class="header-account-media" width="35" height="35">
                                            <use xlink:href="/images/icons/sprite.svg#icon-compare"></use>
                                        </svg>

                                        <span class="header-account-badge">2</span>
                                    </div>

                                    <div class="header-account-value">Сравнение</div>
                                </a>
                            </li>*/ ?>

                                <li class="header-account-item has-dropdown order-2 order-xl-3">
                                    <a href="cart.html" class="header-account-link cart">
                                        <div class="header-account-thumbnail d-flex align-items-center">
                                            <svg class="header-account-media" width="30" height="30">
                                                <use xlink:href="/images/icons/sprite.svg#icon-shopping-cart"></use>
                                            </svg>

                                            <span class="header-account-badge">2</span>
                                        </div>

                                        <div class="header-account-value">Корзина</div>
                                    </a>

                                    <div class="header-cart-dropdown header-account-dropdown">
                                        <ul class="header-cart-list list-unstyled">
                                            <li class="header-cart-item">
                                                <div class="card-header-order form-row">
                                                    <div class="card-header-order-thumbnail-container col-auto">
                                                        <a href="product.html" class="card-header-order-thumbnail">
                                                            <img src="/verstka/uploads/products/product-item-1.jpg"
                                                                 alt="Замок врезной MUL-T-LOCK DEAD"
                                                                 class="card-header-order-media">
                                                        </a>
                                                    </div>

                                                    <div class="card-header-order-content-container col">
                                                        <div class="card-header-order-title">
                                                            <a href="product.html">Замок врезной MUL-T-LOCK DEAD</a>
                                                        </div>

                                                        <div class="card-header-order-price text-nowrap">12 954
                                                            <span><span
                                                                        class="rur">руб</span>/шт</span></div>

                                                        <button type="button" class="card-header-order-delete">
                                                            <svg width="18" height="20">
                                                                <use xlink:href="/images/icons/sprite.svg#icon-trash"></use>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="header-cart-item">
                                                <div class="card-header-order form-row">
                                                    <div class="card-header-order-thumbnail-container col-auto">
                                                        <a href="product.html" class="card-header-order-thumbnail">
                                                            <img src="/verstka/uploads/products/product-item-2.jpg"
                                                                 alt="Цилиндр Adria 2018 60 (30х30)"
                                                                 class="card-header-order-media">
                                                        </a>
                                                    </div>

                                                    <div class="card-header-order-content-container col">
                                                        <div class="card-header-order-title">
                                                            <a href="product.html">Цилиндр Adria 2018 60 (30х30)</a>
                                                        </div>

                                                        <div class="card-header-order-price text-nowrap">12 954
                                                            <span><span
                                                                        class="rur">руб</span>/шт</span></div>

                                                        <button type="button" class="card-header-order-delete">
                                                            <svg width="18" height="20">
                                                                <use xlink:href="/images/icons/sprite.svg#icon-trash"></use>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="header-cart-footer">
                                            <div class="form-row align-items-center">
                                                <div class="col">
                                                    <div class="header-cart-total-block">
                                                        <div class="header-cart-total-title text-muted">Итого:</div>
                                                        <div class="header-cart-total">5900 руб.</div>
                                                    </div>
                                                </div>

                                                <div class="col-auto">
                                                    <a href="cart.html" class="btn">
                                                        <svg class="btn-media" width="18" height="18">
                                                            <use xlink:href="/images/icons/sprite.svg#icon-shopping-cart"></use>
                                                        </svg>
                                                        В корзину
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-search-wrapper" aria-expanded="false">
                <div class="header-search-container container">
                    <div class="row">
                        <div class="col-xl-5 offset-xl-2">
                            <div class="header-search-block">
                                <form class="header-search-form d-flex">
                                    <input type="search" class="header-search-input" placeholder="Поиск по каталогу"
                                           required>
                                    <button type="submit"
                                            class="header-search-button d-flex align-items-center justify-content-center">
                                        <svg class="header-search-button-icon" width="22" height="22">
                                            <use xlink:href="/images/icons/sprite.svg#icon-search"></use>
                                        </svg>
                                    </button>
                                </form>

                                <div class="header-search-suggestion-block">
                                    <ul class="header-search-suggestion-list list-unstyled">
                                        <li class="header-search-suggestion-item">
                                            <a href="catalog.html" class="header-search-suggestion-link"><b>замок</b>
                                                навесной</a>
                                        </li>
                                        <li class="header-search-suggestion-item">
                                            <a href="catalog.html" class="header-search-suggestion-link"><b>замок</b>
                                                межкомнатный</a>
                                        </li>
                                        <li class="header-search-suggestion-item">
                                            <a href="catalog.html" class="header-search-suggestion-link"><b>замок</b>
                                                врезной</a>
                                        </li>
                                        <li class="header-search-suggestion-item">
                                            <a href="catalog.html" class="header-search-suggestion-link"><b>замок</b>
                                                накладной</a>
                                        </li>
                                        <li class="header-search-suggestion-item">
                                            <a href="catalog.html" class="header-search-suggestion-link"><b>замок</b>
                                                висячий</a>
                                        </li>
                                        <li class="header-search-suggestion-item">
                                            <a href="catalog.html" class="header-search-suggestion-link"><b>замок</b>
                                                кодовый</a>
                                        </li>

                                        <li class="header-search-suggestion-item header-search-suggestion-category-item">
                                            <div class="header-search-suggestion-title">Перейти в категорию</div>

                                            <a href="catalog.html"
                                               class="header-search-suggestion-category d-flex align-items-center no-gutters">
                                                <div class="header-search-suggestion-category-thumbnail col-auto">
                                                    <img src="/images/header/catalog/icon-lock.svg" alt="Замки"
                                                         class="header-search-suggestion-category-media">
                                                </div>

                                                <div class="header-search-suggestion-category-content col">Замки</div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-search-bg bg"></div>
            </div>
        </div>

        <?$APPLICATION->IncludeComponent(
            "uisoft:main.menu",
            "",
            Array(
            )
        );?>

    </header>
    <!-- Offcanvas -->
    <div class="offcanvas-wrapper">
        <div class="offcanvas-content">

            <div class="header-logo-block header-logo-block-mobile-menu">
                <div class="header-logo-thumbnail">
                    <img src="/images/logo.svg" alt="Ваш Замок" class="header-logo-image">
                </div>
            </div>


            <div class="offcanvas-nav-block">
                <? /*<div class="offcanvas-nav-title">Каталог</div>*/ ?>

                <ul class="offcanvas-nav-list list-unstyled">
                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-lock.svg" alt="Замки" class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Замки</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-cylinder.svg" alt="Цилиндры"
                                     class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Цилиндры</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-handle.svg" alt="Ручки"
                                     class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Ручки</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-loop.svg" alt="Петли" class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Петли</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-lock.svg" alt="Замки" class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Замки</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-door-furniture.svg" alt="Дверная фурнитура"
                                     class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Дверная фурнитура</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-system-master.svg" alt="Мастер системы"
                                     class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Мастер системы</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-production-of-keys.svg" alt="Изготовление ключей"
                                     class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Изготовление ключей</div>
                        </a>
                    </li>

                    <li class="offcanvas-nav-item">
                        <a href="#link" class="offcanvas-nav-link d-flex no-gutters">
                            <div class="offcanvas-nav-thumbnail col-auto">
                                <img src="/images/header/catalog/icon-closer.svg" alt="Доводчики"
                                     class="offcanvas-nav-media">
                            </div>

                            <div class="offcanvas-nav-content align-self-center col">Доводчики</div>
                        </a>
                    </li>
                </ul>
            </div>

            <? $APPLICATION->IncludeComponent(
                "bitrix:news.line",
                "mobile_store_addresses",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "CACHE_GROUPS"       => "Y",
                    "CACHE_TIME"         => "300",
                    "CACHE_TYPE"         => "A",
                    "COMPONENT_TEMPLATE" => "header_store_addresses",
                    "DETAIL_URL"         => "",
                    "FIELD_CODE"         => array(
                        0 => "NAME",
                        1 => "PREVIEW_TEXT",
                        2 => "PROPERTY_TYPE",
                        3 => "PROPERTY_METRO",
                        4 => "PROPERTY_PHONE",
                        5 => "PROPERTY_OPENING_HOURS",
                        6 => "PROPERTY_WHATSAPP",
                    ),
                    "IBLOCKS"            => array(0 => "5",),
                    "IBLOCK_TYPE"        => "services",
                    "NEWS_COUNT"         => "20",
                    "SORT_BY1"           => "SORT",
                    "SORT_BY2"           => "",
                    "SORT_ORDER1"        => "ASC",
                    "SORT_ORDER2"        => ""
                )
            ); ?>




            <?php $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "top_bar_navigation_mobile",
                array(
                    "ACTIVE_DATE_FORMAT"              => "d.m.Y",
                    "ADD_SECTIONS_CHAIN"              => "N",
                    "AJAX_MODE"                       => "N",
                    "AJAX_OPTION_ADDITIONAL"          => "",
                    "AJAX_OPTION_HISTORY"             => "N",
                    "AJAX_OPTION_JUMP"                => "N",
                    "AJAX_OPTION_STYLE"               => "Y",
                    "CACHE_FILTER"                    => "N",
                    "CACHE_GROUPS"                    => "Y",
                    "CACHE_TIME"                      => "36000000",
                    "CACHE_TYPE"                      => "A",
                    "CHECK_DATES"                     => "Y",
                    "DETAIL_URL"                      => "",
                    "DISPLAY_BOTTOM_PAGER"            => "N",
                    "DISPLAY_DATE"                    => "N",
                    "DISPLAY_NAME"                    => "N",
                    "DISPLAY_PICTURE"                 => "N",
                    "DISPLAY_PREVIEW_TEXT"            => "N",
                    "DISPLAY_TOP_PAGER"               => "N",
                    "FIELD_CODE"                      => array(
                        0 => "CODE",
                        1 => "PREVIEW_TEXT",
                        2 => "",
                    ),
                    "FILTER_NAME"                     => "",
                    "HIDE_LINK_WHEN_NO_DETAIL"        => "N",
                    "IBLOCK_ID"                       => "4",
                    "IBLOCK_TYPE"                     => "menu",
                    "INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
                    "INCLUDE_SUBSECTIONS"             => "N",
                    "MESSAGE_404"                     => "",
                    "NEWS_COUNT"                      => "20",
                    "PAGER_BASE_LINK_ENABLE"          => "N",
                    "PAGER_DESC_NUMBERING"            => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL"                  => "N",
                    "PAGER_SHOW_ALWAYS"               => "N",
                    "PAGER_TEMPLATE"                  => ".default",
                    "PAGER_TITLE"                     => "Новости",
                    "PARENT_SECTION"                  => "",
                    "PARENT_SECTION_CODE"             => "",
                    "PREVIEW_TRUNCATE_LEN"            => "",
                    "PROPERTY_CODE"                   => array(
                        0 => "",
                        1 => "",
                    ),
                    "SET_BROWSER_TITLE"               => "N",
                    "SET_LAST_MODIFIED"               => "N",
                    "SET_META_DESCRIPTION"            => "N",
                    "SET_META_KEYWORDS"               => "N",
                    "SET_STATUS_404"                  => "N",
                    "SET_TITLE"                       => "N",
                    "SHOW_404"                        => "N",
                    "SORT_BY1"                        => "SORT",
                    "SORT_BY2"                        => "NAME",
                    "SORT_ORDER1"                     => "ASC",
                    "SORT_ORDER2"                     => "ASC",
                    "STRICT_SECTION_CHECK"            => "N",
                    "COMPONENT_TEMPLATE"              => "top_bar_navigation"
                ),
                false
            ); ?>

        </div>

        <div class="offcanvas-bg bg"></div>
    </div>

<main class="main-wrapper flex-grow-1">

<? if ($isMainPage): ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:news.line",
        "index_slider",
        array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "CACHE_GROUPS"       => "Y",
            "CACHE_TIME"         => "300",
            "CACHE_TYPE"         => "A",
            "COMPONENT_TEMPLATE" => "header_store_addresses",
            "DETAIL_URL"         => "",
            "FIELD_CODE"         => array(
                0 => "NAME",
                1 => "PREVIEW_TEXT",
                2 => "PROPERTY_TITLE",
                3 => "PROPERTY_LINK",
                4 => "PREVIEW_PICTURE",
            ),
            "IBLOCKS"            => array(0 => "6",),
            "IBLOCK_TYPE"        => "sliders",
            "NEWS_COUNT"         => "20",
            "SORT_BY1"           => "SORT",
            "SORT_BY2"           => "",
            "SORT_ORDER1"        => "ASC",
            "SORT_ORDER2"        => ""
        )
    ); ?>
<? else: ?>

    <? $APPLICATION->IncludeComponent(
	"bitrix:breadcrumb",
	"breadcrumb",
	array(
		"COMPONENT_TEMPLATE" => "breadcrumb",
		"START_FROM" => "0",
		"PATH" => "",
		"SITE_ID" => "s1"
	),
	false
); ?>
<? endif; ?>

<?php \Uisoft\App\IncludeHtmlTemplate::topPageSection(); ?>
