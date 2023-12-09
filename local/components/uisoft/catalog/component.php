<?php

use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */

// Если мы используем фильтр, имя фильтра берется из $arParams["FILTER_NAME"]
if (isset($arParams["USE_FILTER"]) && $arParams["USE_FILTER"] === "Y") {
    $arParams["FILTER_NAME"] = trim($arParams["FILTER_NAME"]);
    if ($arParams["FILTER_NAME"] === '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
        $arParams["FILTER_NAME"] = "arrFilter";
    }
} else {
    $arParams["FILTER_NAME"] = "";
}

$arParams['ACTION_VARIABLE'] = (isset($arParams['ACTION_VARIABLE']) ? trim($arParams['ACTION_VARIABLE']) : 'action');
if ($arParams["ACTION_VARIABLE"] === '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"])) {
    $arParams["ACTION_VARIABLE"] = "action";
}

// Дефолтные предустановки для ЧПУ
$smartBase = $arParams["SEF_URL_TEMPLATES"]["section"] ?: "#SECTION_ID#/";

$arDefaultUrlTemplates404 = [
    'sections' => '',
    'section' => '#SECTION_ID#/',
    'element' => '#SECTION_ID#/#ELEMENT_ID#/',
    'compare' => 'compare.php?action=COMPARE',
    'smart_filter' => $smartBase . 'filter/#SMART_FILTER_PATH#/apply/'
];

$arDefaultVariableAliases404 = [];
$arDefaultVariableAliases = [];

$arComponentVariables = [
    "SECTION_ID",
    "SECTION_CODE",
    "ELEMENT_ID",
    "ELEMENT_CODE",
    "action",
];

$arVariables = [];

// Мы всегда используем режим ЧПУ
if ($arParams["SEF_MODE"] !== "Y") {
    throw new Exception('Components always work in SEF_MODE mode');
}

//$engine = new CComponentEngine($this);

// Переназначим ComponentEngine (внесены дополнительные правки для работы с инфоблоком структуры одновременно)
$engine = new \Uisoft\App\Bitrix\ComponentEngine($this);

if (!Loader::includeModule('iblock')) throw new \Exception('Module iblock error');


$engine->addGreedyPart("#SECTION_CODE_PATH#");
$engine->addGreedyPart("#SMART_FILTER_PATH#");
$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));

$arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
$arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

// Получаем урл запроса
$requestURL = Bitrix\Main\Context::getCurrent()->getRequest()->getRequestedPage();

// Функция вернет раздел структуры если он найден иначе пустой массив
$structureSection = $engine->prepareUrl($requestURL, $arVariables, $arUrlTemplates);

//if (empty($structureSection)) {
//    // Раздел в структуре не найден 404 ошибка
//    Tools::process404("", true, true, true, $arParams["FILE_404"]);
//}


// Получим страницу компонента
$componentPage = $engine->guessComponentPath(
    $arParams["SEF_FOLDER"],
    $arUrlTemplates,
    $arVariables
);


if ($componentPage === "smart_filter") {
    $componentPage = "section";
}

if (!$componentPage && isset($_REQUEST["q"])) {
    $componentPage = "search";
}

$b404 = false;
if (!$componentPage) {
    $componentPage = "sections";
    $b404 = true;
}


if ($componentPage === "section") {
    if (isset($arVariables["SECTION_ID"])) {
        $b404 |= ((int)$arVariables["SECTION_ID"] . "" !== $arVariables["SECTION_ID"]);
    } else {
        $b404 |= !isset($arVariables["SECTION_CODE"]);
    }
}

if ($b404 && CModule::IncludeModule('iblock')) {
    $folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
    if ($folder404 !== "/") {
        $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
    }
    if (mb_substr($folder404, -1) === "/") {
        $folder404 .= "index.php";
    }

    if ($folder404 !== $APPLICATION->GetCurPage(true)) {
        Tools::process404(
            ""
            ,
            ($arParams["SET_STATUS_404"] === "Y")
            ,
            ($arParams["SET_STATUS_404"] === "Y")
            ,
            ($arParams["SHOW_404"] === "Y")
            ,
            $arParams["FILE_404"]
        );
    }
}

$engine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

$arResult = array(
    "FOLDER" => $arParams["SEF_FOLDER"],
    "URL_TEMPLATES" => $arUrlTemplates,
    "VARIABLES" => $arVariables,
    "ALIASES" => $arVariableAliases,
    'STRUCTURE_SECTION' => $structureSection
);


$this->IncludeComponentTemplate($componentPage);

// Классы для секций страниц каталога
switch ($componentPage) {
    case 'sections':
        $APPLICATION->SetPageProperty('sectionClass', 'section-catalog-index');
        break;

    case 'section':
        $APPLICATION->SetPageProperty('sectionClass', 'section-catalog');
        break;

    case 'element':
        $APPLICATION->SetPageProperty('sectionClass', 'section-product');
        break;
}
