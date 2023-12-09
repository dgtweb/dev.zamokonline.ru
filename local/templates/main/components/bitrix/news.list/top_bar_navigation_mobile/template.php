<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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
$this->setFrameMode(true);


?>

<? if (!empty($arResult["ITEMS"])): ?>
    <div class="offcanvas-nav-block">
       <?/* <div class="offcanvas-nav-title">Информация</div>*/?>
        <ul class="offcanvas-nav-list list-unstyled">
            <? foreach ($arResult["ITEMS"] as $arItem): ?>
                <?
                if ($arItem['PROPERTIES']['HIDE_IN_MOBILE']['VALUE'] === 'Y') {
                    continue;
                }
                $this->AddEditAction(
                    $arItem['ID'],
                    $arItem['EDIT_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
                );
                $this->AddDeleteAction(
                    $arItem['ID'],
                    $arItem['DELETE_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                    array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
                );
                ?>

                <li class="offcanvas-nav-item">
                    <a href="<? echo $arItem["CODE"]; ?>" class="offcanvas-nav-link d-flex no-gutters">
                        <div class="offcanvas-nav-thumbnail col-auto">
                            <? echo $arItem["PREVIEW_TEXT"]; ?>
                        </div>

                        <div class="offcanvas-nav-content align-self-center col"><? echo $arItem["NAME"] ?></div>
                    </a>
                </li>

            <? endforeach; ?>
        </ul>
    </div>
<? endif; ?>
