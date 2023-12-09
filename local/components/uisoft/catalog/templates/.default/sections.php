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

$sectionParams = ["COMPONENT_TEMPLATE" => "main-track",
    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
    "SECTION_ID" => "",
    "SECTION_CODE" => "",
    "COUNT_ELEMENTS" => "N",
    "TOP_DEPTH" => "1",
    "SECTION_FIELDS" => array(
        0 => "",
        1 => "",
    ),
    "SECTION_USER_FIELDS" => array(
        0 => "",
        1 => "",
    ),
    "SECTION_URL" => "",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "36000000",
    "CACHE_GROUPS" => "Y",
    "ADD_SECTIONS_CHAIN" => "N",
    "FILTER_NAME" => ""
];
?>

<? $APPLICATION->IncludeComponent(
    'bitrix:catalog.section.list' ,
    '',
    $sectionParams,
    $component
);
?>
