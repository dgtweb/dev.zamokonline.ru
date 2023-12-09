<?

use Uisoft\App\Images;
use Uisoft\App\Tools;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent  $component
 * @var array                    $arParams
 * @var array                    $arResult
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

// Обработка изображений
$imgParams = [
    'icon' => ['width' => 100, 'height' => 100],
    'mini' => ['width' => 800, 'height' => 800],
    'lg' => ['width' => 1600, 'height' => 1600],
];

if (!empty($arResult['DETAIL_PICTURE'])) {
    $arResult['pictures'][] = Images::createCatalogDetailImage($arResult['DETAIL_PICTURE']['ID'], $imgParams);
}

if (!empty($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $k => $imageId) {
        $arResult['pictures'][] = Images::createCatalogDetailImage($imageId, $imgParams,
            $arResult['PROPERTIES']['MORE_PHOTO']['DESCRIPTION'][$k]);
    }
}

if($_SERVER["REMOTE_ADDR"] === "91.197.114.84")
{
    //\ALS\Helper\Dbg::show($arResult, true, true);
}


// Обработка свойств товара
$arResult['productProperties'] = Tools::prepareProductProperties($arResult['PROPERTIES']);
$arResult['colsProductProperties'] = Tools::arraySplitToCols($arResult['productProperties']);
//\ALS\Helper\Dbg::show($arResult['colsProductProperties'], true, true);
