<?

use Uisoft\App\Tools;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain         $APPLICATION
 * @var CBitrixComponent $component
 * @var array            $arParams
 * @var array            $arResult
 * @var array            $sectionParams
 */

//Make all properties present in order
//to prevent html table corruption
foreach ($arResult["ITEMS"] as $key => $arElement) {
    $title = !empty($arElement['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])
        ? $arElement['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
        : $arElement['NAME'];

    $alt = !empty($arElement['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'])
        ? $arElement['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']
        : $arElement['NAME'];

    $arRes = array();
    foreach ($arParams["PROPERTY_CODE"] as $pid) {
        $arRes[$pid] = CIBlockFormatProperties::GetDisplayValue($arElement, $arElement["PROPERTIES"][$pid], "catalog_out");
    }
    $arResult["ITEMS"][$key]["DISPLAY_PROPERTIES"] = $arRes;
    $arResult["ITEMS"][$key]['BRAND'] = Tools::getBrandByElementItem($arElement);

    foreach ($arResult["PRICES"] as $code => $arPrice) {
        if ($arPrice = $arElement["PRICES"][$code]) {
            if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]) {
                $arResult["ITEMS"][$key]["DISPLAY_PRICES"] = [
                    'price' => str_replace('&#8381;', '<span><span class="rur">руб</span></span>', $arPrice["PRINT_DISCOUNT_VALUE"]),
                    'oldPrice' => str_replace('&#8381;', '<span><span class="rur">руб</span></span>', $arPrice["PRINT_VALUE"]),
                ];
            } else {
                $arResult["ITEMS"][$key]["DISPLAY_PRICES"] = [
                    'price' => str_replace('&#8381;', '<span><span class="rur">руб</span></span>', $arPrice["PRINT_VALUE"]),
                ];
            }
        }
    }

    // Картинки
    if (!empty($arElement['DETAIL_PICTURE'])) {
        $arResult["ITEMS"][$key]['IMAGES']['preview'] = CFile::ResizeImageGet(
            $arElement['DETAIL_PICTURE']['ID'],
            ['width' => 300, 'height' => 300],
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true);

        $arResult["ITEMS"][$key]['IMAGES']['preview']['alt'] = $alt;
        $arResult["ITEMS"][$key]['IMAGES']['preview']['title'] = $title;
    } else {
        $arResult["ITEMS"][$key]['IMAGES']['preview'] = [
            'src' => '/images/no_image_300_300.png',
            'width' => 300,
            'height' => 300
        ];
    }
}


