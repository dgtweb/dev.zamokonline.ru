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

<div class="catalog-grid row">
    <?
    if (!empty($arResult["ITEMS"])): ?>
        <?
        foreach ($arResult["ITEMS"] as $arElement): ?>
            <?
            $arPrice = current($arElement["ITEM_PRICES"]);
            $price =  $arPrice['PRINT_RATIO_PRICE'];
            if ($arPrice['BASE_PRICE'] > $arPrice['PRICE']) {
                $priceOld = $arPrice['PRINT_RATIO_BASE_PRICE'];
            }

            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'],
                CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'],
                CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"),
                array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="catalog-item col-6 col-sm-4 col-xl-12 d-flex" id="<?= $this->GetEditAreaId($arElement['ID']); ?>">
                <div class="card-product card-product-horizontal d-flex flex-column">
                    <div class="row align-items-center flex-grow-1">
                        <div class="card-product-thumbnail-container col-12 col-xl-auto">
                            <figure class="card-product-thumbnail">
                                <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>" class="card-product-thumbnail-link">
                                    <img src="<?= $arElement['IMAGES']['preview']['src'] ?>" alt="<?= $arElement['IMAGES']['preview']['alt'] ?>"
                                         title="<?= $arElement['IMAGES']['preview']['title'] ?>"
                                         class="card-product-media">


                                <button type="button" class="card-product-quickview">
                                    <svg width="21" height="21">
                                        <use xlink:href="/images/icons/sprite.svg#icon-search"></use>
                                    </svg>
                                </button>
                                </a>
                            </figure>
                        </div>

                        <div class="card-product-typography-container col-12 col-xl-auto">
                            <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>" class="card-product-title"><?= $arElement["NAME"] ?></a>

                            <div class="card-product-brand">Производитель: <?= $arElement[ "PROPERTIES"]['CML2_MANUFACTURER']['VALUE'] ?></div>
                            <?
                            /*
                                                <div class="card-product-rating-block d-flex align-items-center">
                                                    <div class="card-product-rating">
                                                        <i class="icon-star-solid" ></i>
                                                        <i class="icon-star-solid" ></i>
                                                        <i class="icon-star-solid" ></i>
                                                        <i class="icon-star-solid" ></i>
                                                        <i class="icon-star" ></i>
                                                    </div>

                                                    <div class="card-product-rating-count">1 отзыв</div>
                                                </div>*/ ?>
                        </div>

                        <div class="card-product-order-container col-12 col-xl">
                            <div class="card-product-order-block">
                                <div class="card-product-price-block d-flex flex-wrap align-items-baseline">
                                    <div class="card-product-price"><?= $price?></div>
                                    <?
                                    if ($priceOld): ?>
                                        <div class="card-product-old-price"><?=$priceOld?></div>
                                    <?
                                    endif; ?>
                                </div>

                                <?
                                /*
                                                        <div class="card-product-quantity-block d-none d-xl-block float-left">
                                                            <div class="card-product-quantity-control custom-control custom-number">
                                                                <input class="custom-control-input" type="text" value="1" maxlength="5">
                                                                <button type="button" class="custom-control-button custom-control-plus">
                                                                    <i class="icon-plus" ></i>
                                                                </button>
                                                                <button type="button" class="custom-control-button custom-control-minus">
                                                                    <i class="icon-minus" ></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                */ ?>
                                <? if ($arElement['CAN_BUY']): ?>
                                <button type="button" class="card-product-buy btn" data-toggle="modal" data-target="#modalAddToCart">
                                    <svg class="card-product-buy-media" width="18" height="18">
                                        <use xlink:href="/images/icons/sprite.svg#icon-shopping-cart"></use>
                                    </svg>
                                    В корзину
                                </button>
                                <?     endif; ?>
                            </div>
                        </div>
                    </div>
                    <?
                    /*
                                <div class="card-product-labels d-flex flex-column align-items-start">
                                    <span class="card-product-label">Дополнительный ключ</span>
                                </div>

                                <div class="card-product-actions d-flex flex-column align-items-end">
                                    <button type="button" class="card-product-action card-product-action-compare" >
                                        <svg width="20" height="20">
                                            <use xlink:href="/images/icons/sprite.svg#icon-balance"></use>
                                        </svg>
                                    </button>
                                </div>
                    */ ?>
                </div>
            </div>
        <?
        endforeach ?>
    <?
    else: ?>
        <div>Товары не найдены =(</div>
    <?
    endif ?>
</div>

<?= $arResult["NAV_STRING"] ?>
