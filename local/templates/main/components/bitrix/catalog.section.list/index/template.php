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

if (empty($arResult['SECTIONS'])) {
    return;
}
?>

<section class="section-categories section-gray">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title title-h1 text-center">Категории товаров</h2>
        </div>

        <div class="categories-grid row">

            <?
            foreach ($arResult['SECTIONS'] as $item): ?>
                <?
                $url = $item['UF_URL'] ?? $item['SECTION_PAGE_URL'];
                ?>
                <div class="category-item col-6 col-md-4">
                    <a href="<?= $url ?>" class="card-category">
                        <figure class="card-category-thumbnail">
                            <img src="<?= $item['PICTURE']['SRC'] ?>" alt="<?= $item['UF_TITLE_INDEX'] ?>" class="card-category-media">
                            <figcaption class="card-category-title"><?= $item['UF_TITLE_INDEX'] ?></figcaption>
                        </figure>
                    </a>
                </div>
            <?
            endforeach ?>
        </div>
    </div>
</section>
