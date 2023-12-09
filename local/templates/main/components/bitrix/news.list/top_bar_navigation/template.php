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
    <div class="header-statusbar-wrapper d-none d-xl-block">
        <div class="container">
            <nav class="header-statusbar-navigation">
                <ul class="header-statusbar-nav list-unstyled d-flex align-items-center justify-content-between">
                    <? foreach ($arResult["ITEMS"] as $arItem): ?>
                        <?
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
                        <li class="nav-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                            <a href="<? echo $arItem["CODE"]; ?>" class="nav-link d-flex align-items-center no-gutters">
                                <div class="nav-thumbnail col-auto">
                                    <? echo $arItem["PREVIEW_TEXT"]; ?>
                                </div>

                                <div class="nav-value col"><? echo $arItem["NAME"] ?></div>
                            </a>
                        </li>

                    <? endforeach; ?>
                </ul>
            </nav>
        </div>
    </div>
<? endif; ?>
