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
<ul class="offcanvas-shops-list list-unstyled">
    <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <li class="offcanvas-shop-item">
            <div class="offcanvas-contact-group">

                <ul class="offcanvas-contact-list list-unstyled">
                    <li class="offcanvas-contact-item">
                        <div class="offcanvas-contact-block d-flex no-gutters">
                            <div class="offcanvas-contact-thumbnail col-auto">
                                <svg class="offcanvas-contact-media offcanvas-contact-metro-media" width="16"
                                     height="16">
                                    <use xlink:href="/images/icons/sprite.svg#icon-metro"></use>
                                </svg>
                            </div>
                            <div class="offcanvas-contact-content align-self-center col"><?= $arItem['PROPERTY_TYPE_VALUE'] ?>
                                на метро&nbsp;<b><?= $arItem['PROPERTY_METRO_VALUE'] ?></b>
                            </div>
                        </div>
                    </li>

                    <li class="offcanvas-contact-item">
                        <div class="offcanvas-contact-block d-flex no-gutters">
                            <div class="offcanvas-contact-thumbnail col-auto">
                                <svg class="offcanvas-contact-media" width="14" height="14">
                                    <use xlink:href="/images/icons/sprite.svg#icon-phone"></use>
                                </svg>
                            </div>

                            <div class="offcanvas-contact-content align-self-center col">
                                <?
                                $phoneData = preg_replace('/[^0-9]/', '', $arItem['PROPERTY_PHONE_VALUE']);
                                ?>
                                <div class="offcanvas-contact-tel"><a href="tel:<?= $phoneData ?>">
                                        <nobr><?= $arItem['PROPERTY_PHONE_VALUE'] ?></nobr>
                                    </a></div>
                                <? if (!empty($arItem['PROPERTY_WHATSAPP_VALUE'])): ?>
                                    <div class="offcanvas-contact-icon-whatsapp"><a
                                                href="https://wa.me/<?= $arItem['PROPERTY_WHATSAPP_VALUE'] ?>"
                                                class="offcanvas-contact-whatsapp-link"
                                                target="_blank">
                                            <svg class="header-contact-whatsapp-media" width="18" height="18">
                                                <use xlink:href="/images/icons/sprite.svg#icon-whatsapp"></use>
                                            </svg>
                                        </a></div>
                                <? endif; ?>
                            </div>
                        </div>
                    </li>

                    <li class="offcanvas-contact-item">
                        <div class="offcanvas-contact-block d-flex no-gutters">
                            <div class="offcanvas-contact-thumbnail col-auto">
                                <svg class="offcanvas-contact-media" width="18" height="18">
                                    <use xlink:href="/images/icons/sprite.svg#icon-clock"></use>
                                </svg>
                            </div>

                            <div class="offcanvas-contact-content align-self-center col"><?= $arItem['PROPERTY_OPENING_HOURS_VALUE'] ?></div>
                        </div>
                    </li>

                    <li class="offcanvas-contact-item">
                        <div class="offcanvas-contact-block d-flex no-gutters">
                            <div class="offcanvas-contact-thumbnail col-auto">
                                <svg class="offcanvas-contact-media" width="20" height="20">
                                    <use xlink:href="/images/icons/sprite.svg#icon-location"></use>
                                </svg>
                            </div>

                            <div class="offcanvas-contact-content align-self-center col"><?= $arItem['PREVIEW_TEXT'] ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </li>
    <? endforeach; ?>
</ul>