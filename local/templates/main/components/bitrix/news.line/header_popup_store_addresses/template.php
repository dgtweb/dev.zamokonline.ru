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
        <? foreach ($arResult["ITEMS"] as $arItem): ?>

            <div class="header-shops-dropdown-group">
                <h5 class="header-shops-dropdown-title"><?=$arItem['PROPERTY_TYPE_VALUE']?>:</h5>

                <div class="header-contact-group">
                    <ul class="header-contact-list list-unstyled">
                        <li class="header-contact-item">
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media header-contact-metro-media"
                                         width="22" height="22">
                                        <use xlink:href="/images/icons/sprite.svg#icon-metro"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col">Метро
                                    <?=$arItem['PROPERTY_METRO_VALUE']?>
                                </div>
                            </div>
                        </li>

                        <li class="header-contact-item">
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media" width="20" height="20">
                                        <use xlink:href="/images/icons/sprite.svg#icon-location-solid"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col">
                                    <?=$arItem['PREVIEW_TEXT']?>
                                </div>
                            </div>
                        </li>

                        <li class="header-contact-item">
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media" width="18" height="18">
                                        <use xlink:href="/images/icons/sprite.svg#icon-clock-solid"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col">
                                    <?=$arItem['PROPERTY_OPENING_HOURS_VALUE']?>
                                </div>
                            </div>
                        </li>

                        <li class="header-contact-item">
                            <div class="header-contact-block d-flex no-gutters">
                                <div class="header-contact-thumbnail col-auto">
                                    <svg class="header-contact-media" width="18" height="18">
                                        <use xlink:href="/images/icons/sprite.svg#icon-phone-solid"></use>
                                    </svg>
                                </div>

                                <div class="header-contact-content align-self-center col">
                                    <?
                                    $phoneData = preg_replace('/[^0-9]/', '', $arItem['PROPERTY_PHONE_VALUE']);
                                    ?>
                                    <a href="tel:<?=$phoneData?>"><?=$arItem['PROPERTY_PHONE_VALUE']?></a></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        <? endforeach; ?>