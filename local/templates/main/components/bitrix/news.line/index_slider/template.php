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

<section class="section-hero">
    <div class="swiper-hero-cover swiper-cover">
        <div class="swiper-hero swiper-container">
            <div class="swiper-wrapper row flex-nowrap no-gutters">
                <? foreach ($arResult["ITEMS"] as $arItem): ?>
                    <div class="swiper-slide col-12">
                        <div class="hero-block d-flex flex-column justify-content-center ">
                            <? if (!empty($arItem['PREVIEW_PICTURE']['SRC'])): ?>
                                <figure class="hero-banner-figure">
                                    <picture>
                                        <source srcset="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>"
                                                media="(min-width:768px)">
                                        <img loading="lazy" src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt=""
                                             class="hero-banner-media">
                                    </picture>
                                </figure>
                            <? endif; ?>

                            <div class="container">
                                <div class="form-row">
                                    <div class="hero-typography-container col-9 col-sm-6 col-md-8 col-lg-6">
                                        <div class="hero-title"><?= $arItem['PROPERTY_TITLE_VALUE'] ?></div>
                                        <div class="hero-subtitle"><?= $arItem['PREVIEW_TEXT'] ?></div>
                                        <? if (!empty($arItem['PROPERTY_LINK_VALUE'])): ?>
                                            <a href="<?= $arItem['PROPERTY_LINK_VALUE'] ?>" class="hero-cta btn">Узнать
                                                подробнее</a>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
            <? if (count($arResult["ITEMS"]) > 1): ?>
                <button type="button"
                        class="swiper-hero-button-prev swiper-button-prev swiper-button-xl d-flex align-items-center justify-content-center">
                    <svg class="swiper-button-media" width="45" height="45">
                        <use xlink:href="/images/icons/sprite.svg#icon-angle-left-xl"></use>
                    </svg>
                </button>

                <button type="button"
                        class="swiper-hero-button-next swiper-button-next swiper-button-xl d-flex align-items-center justify-content-center">
                    <svg class="swiper-button-media" width="45" height="45">
                        <use xlink:href="/images/icons/sprite.svg#icon-angle-right-xl"></use>
                    </svg>
                </button>

                <div class="swiper-hero-pagination swiper-pagination d-none d-md-block"></div>
            <? endif; ?>
        </div>
    </div>
</section>