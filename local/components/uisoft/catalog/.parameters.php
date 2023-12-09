<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Bitrix\Currency;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

const DEBUG = true;

/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('iblock')) {
    return;
}

$catalogIncluded = Loader::includeModule('catalog');

$usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$compatibleMode = !(isset($arCurrentValues['COMPATIBLE_MODE']) && $arCurrentValues['COMPATIBLE_MODE'] === 'N');

$arIBlockType = CIBlockParameters::GetIBlockTypes();

// Получим инфоблоки, которые являются каталогами торгрвых пердложений
$offersIblock = [];
if ($catalogIncluded) {
    $iterator = Catalog\CatalogIblockTable::getList(
        [
            'select' => ['IBLOCK_ID'],
            'filter' => ['!=PRODUCT_IBLOCK_ID' => 0]
        ]
    );
    while ($row = $iterator->fetch()) {
        $offersIblock[$row['IBLOCK_ID']] = true;
    }
    unset($row, $iterator);
}

// Инфоблоки
$arIBlock = [];
$iblockFilter = (!empty($arCurrentValues['IBLOCK_TYPE']) ?
    [
        'TYPE'   => $arCurrentValues['IBLOCK_TYPE'],
        'ACTIVE' => 'Y'
    ]
    : ['ACTIVE' => 'Y']);

$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $id = (int)$arr['ID'];

    if (isset($offersIblock[$id])) {
        continue;
    }

    $arIBlock[$id] = '[' . $id . '] ' . $arr['NAME'];
}
unset($rsIBlock, $iblockFilter);


// Инфоблоки для структуры разделов
$arIBlockStructure = [];
$iblockFilterStructure = (!empty($arCurrentValues['IBLOCK_TYPE_STRUCTURE']) ?
    [
        'TYPE'   => $arCurrentValues['IBLOCK_TYPE_STRUCTURE'],
        'ACTIVE' => 'Y'
    ]
    : ['ACTIVE' => 'Y']);


$rsIBlockStructure = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilterStructure);

while ($arr = $rsIBlockStructure->Fetch()) {
    $id = (int)$arr['ID'];

    if (isset($offersIblock[$id])) {
        continue;
    }

    $arIBlockStructure[$id] = '[' . $id . '] ' . $arr['NAME'];
}
unset($id, $arr, $rsIBlockStructure, $iblockFilter, $offersIblock);


$arComponentParameters = [
    "GROUPS"     => [
        "FILTER_SETTINGS" => [
            "NAME" => GetMessage("T_IBLOCK_DESC_FILTER_SETTINGS"),
        ],
    ],
    "PARAMETERS" => [
        // "USER_CONSENT" => array(), // Соглашение на обработку персональных данных
        "AJAX_MODE"   => array(),
        "SEF_MODE"    => array(
            "sections" => array(
                "NAME"      => GetMessage("SECTIONS_TOP_PAGE"),
                "DEFAULT"   => "",
                "VARIABLES" => array(),
            ),
            "section"  => array(
                "NAME"      => GetMessage("SECTION_PAGE"),
                "DEFAULT"   => "#SECTION_ID#/",
                "VARIABLES" => array(
                    "SECTION_ID",
                    "SECTION_CODE",
                    "SECTION_CODE_PATH",
                ),
            ),
            "element"  => array(
                "NAME"      => GetMessage("DETAIL_PAGE"),
                "DEFAULT"   => "#SECTION_ID#/#ELEMENT_ID#/",
                "VARIABLES" => array(
                    "ELEMENT_ID",
                    "ELEMENT_CODE",
                    "SECTION_ID",
                    "SECTION_CODE",
                    "SECTION_CODE_PATH",
                ),
            ),
            "compare"  => array(
                "NAME"      => GetMessage("COMPARE_PAGE"),
                "DEFAULT"   => "compare.php?action=#ACTION_CODE#",
                "VARIABLES" => array(
                    "action",
                ),
            ),
        ),
        "IBLOCK_TYPE" => array(
            "PARENT"  => "BASE",
            "NAME"    => GetMessage("IBLOCK_TYPE"),
            "TYPE"    => "LIST",
            "VALUES"  => $arIBlockType,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID"   => array(
            "PARENT"            => "BASE",
            "NAME"              => GetMessage("IBLOCK_IBLOCK"),
            "TYPE"              => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES"            => $arIBlock,
            "REFRESH"           => "Y",
        ),

        "IBLOCK_TYPE_STRUCTURE" => array(
            "PARENT"  => "BASE",
            "NAME"    => GetMessage("IBLOCK_TYPE_STRUCTURE"),
            "TYPE"    => "LIST",
            "VALUES"  => $arIBlockType,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID_STRUCTURE"   => array(
            "PARENT"            => "BASE",
            "NAME"              => GetMessage("IBLOCK_ID_STRUCTURE"),
            "TYPE"              => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES"            => $arIBlockStructure,
            "REFRESH"           => "Y",
        ),

        "USE_FILTER" => array(
            "PARENT"  => "FILTER_SETTINGS",
            "NAME"    => GetMessage("T_IBLOCK_DESC_USE_FILTER"),
            "TYPE"    => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ),
    ]
];

if ($arCurrentValues["USE_FILTER"] === "Y") {
    $arComponentParameters["PARAMETERS"]["FILTER_NAME"] = array(
        "PARENT"  => "FILTER_SETTINGS",
        "NAME"    => GetMessage("T_IBLOCK_FILTER"),
        "TYPE"    => "STRING",
        "DEFAULT" => "arrFilter",
    );
}

CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);

if ($arCurrentValues["SEF_MODE"] === "Y") {
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"] = array();
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["ELEMENT_ID"] = array(
        "NAME"     => GetMessage("CP_BC_VARIABLE_ALIASES_ELEMENT_ID"),
        "TEMPLATE" => "#ELEMENT_ID#",
    );
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["ELEMENT_CODE"] = array(
        "NAME"     => GetMessage("CP_BC_VARIABLE_ALIASES_ELEMENT_CODE"),
        "TEMPLATE" => "#ELEMENT_CODE#",
    );
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_ID"] = array(
        "NAME"     => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_ID"),
        "TEMPLATE" => "#SECTION_ID#",
    );
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_CODE"] = array(
        "NAME"     => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_CODE"),
        "TEMPLATE" => "#SECTION_CODE#",
    );
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SECTION_CODE_PATH"] = array(
        "NAME"     => GetMessage("CP_BC_VARIABLE_ALIASES_SECTION_CODE_PATH"),
        "TEMPLATE" => "#SECTION_CODE_PATH#",
    );
    $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]["SMART_FILTER_PATH"] = array(
        "NAME"     => GetMessage("CP_BC_VARIABLE_ALIASES_SMART_FILTER_PATH"),
        "TEMPLATE" => "#SMART_FILTER_PATH#",
    );

    $smartBase = ($arCurrentValues["SEF_URL_TEMPLATES"]["section"] ? $arCurrentValues["SEF_URL_TEMPLATES"]["section"] : "#SECTION_ID#/");
    $arComponentParameters["PARAMETERS"]["SEF_MODE"]["smart_filter"] = array(
        "NAME"      => GetMessage("CP_BC_SEF_MODE_SMART_FILTER"),
        "DEFAULT"   => $smartBase . "filter/#SMART_FILTER_PATH#/apply/",
        "VARIABLES" => array(
            "SECTION_ID",
            "SECTION_CODE",
            "SECTION_CODE_PATH",
            "SMART_FILTER_PATH",
        ),
    );
}