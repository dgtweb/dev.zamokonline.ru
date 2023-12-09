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
$this->setFrameMode(true);

?>

<?
if (!empty($arResult["ITEMS"])): ?>


    <section class="section-brands section-home-brands">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title title-h2 text-center">Бренды</h2>
            </div>

            <div class="swiper-brands-cover swiper-cover">
                <div class="swiper-brands swiper-container">
                    <div class="swiper-wrapper row flex-md-nowrap">

                        <?
                        foreach ($arResult["ITEMS"] as $arItem): ?>
                            <div class="swiper-slide col-4 col-md-3 col-xl-2">
                                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="card-brand">
                                    <figure class="card-brand-figure">
                                        <div class="card-brand-thumbnail">
                                            <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="KALE" class="card-brand-media">
                                        </div>

                                        <figcaption class="card-brand-title"><?= $arItem['NAME'] ?></figcaption>
                                    </figure>
                                </a>
                            </div>
                        <?
                        endforeach; ?>

                    </div>

                    <button type="button"
                            class="swiper-brands-button-prev swiper-button-prev d-none d-md-flex align-items-center justify-content-center">
                        <svg class="swiper-button-media" width="16" height="16">
                            <use xlink:href="/images/icons/sprite.svg#icon-angle-left"></use>
                        </svg>
                    </button>

                    <button type="button"
                            class="swiper-brands-button-next swiper-button-next d-none d-md-flex align-items-center justify-content-center">
                        <svg class="swiper-button-media" width="16" height="16">
                            <use xlink:href="/images/icons/sprite.svg#icon-angle-right"></use>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="section-cta text-center">
                <a href="/brands/" class="section-cta">Показать еще <i class="icon-angle-right"></i></a>
            </div>
        </div>
    </section>
<?
endif; ?>
