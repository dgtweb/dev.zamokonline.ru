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

//\ALS\Helper\Dbg::show($arResult, true, true);
?>
<div class="header-contact-container col-xl-5 d-none d-xl-block">
    <div class="form-row">
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
            <div class="col-xl-6 header-address-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <div class="header-contact-group">
                    <ul class="header-contact-list list-unstyled">
                        <li class="header-contact-item" >
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media" width="14" height="14">
                                        <use xlink:href="/images/icons/sprite.svg#icon-phone-solid"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col" >

                                    <?
                                    $phoneData = preg_replace('/[^0-9]/', '', $arItem['PROPERTY_PHONE_VALUE']);
                                    ?>
                                    <a href="tel:<?= $phoneData ?>"><nobr><?=$arItem['PROPERTY_PHONE_VALUE']?></nobr></a>
                                    <? if (!empty($arItem['PROPERTY_WHATSAPP_VALUE'])): ?>
                                        &nbsp;<a href="https://wa.me/<?= $arItem['PROPERTY_WHATSAPP_VALUE'] ?>"
                                           class="header-contact-whatsapp-link"
                                           target="_blank">
                                            <svg class="header-contact-whatsapp-media" width="16" height="16">
                                                <use xlink:href="/images/icons/sprite.svg#icon-whatsapp"></use>
                                            </svg>
                                        </a>
                                    <? endif; ?>
                                </div>
                            </div>
                        </li>
                        <li class="header-contact-item">
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media" width="16" height="16">
                                        <use xlink:href="/images/icons/sprite.svg#icon-location-solid"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col">метро <?=$arItem['PROPERTY_METRO_VALUE']?>
                                </div>
                            </div>
                        </li>
                        <li class="header-contact-item">
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media" width="14" height="14">
                                        <use xlink:href="/images/icons/sprite.svg#icon-clock-solid"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col">
                                   <nobr> <?=$arItem['PROPERTY_OPENING_HOURS_VALUE']?></nobr>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        <? endforeach; ?>
    </div>
</div>
