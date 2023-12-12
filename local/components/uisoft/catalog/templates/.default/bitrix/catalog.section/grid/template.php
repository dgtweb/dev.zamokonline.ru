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

//\ALS\Helper\Dbg::show($arResult, true, true);

?>


<div class="catalog-grid row">
    <?
    if (!empty($arResult["ITEMS"])): ?>
        <?
        foreach ($arResult["ITEMS"] as $arElement):
            $arPrice = current($arElement["ITEM_PRICES"]);
            $price = $arPrice['PRINT_RATIO_PRICE'];
            if ($arPrice['BASE_PRICE'] > $arPrice['PRICE']) {
                $priceOld = $arPrice['PRINT_RATIO_BASE_PRICE'];
            }
            ?>

            <?
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'],
                CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'],
                CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"),
                array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="catalog-item col-6 col-sm-4 d-flex" id="<?= $this->GetEditAreaId($arElement['ID']); ?>">
                <div class="card-product d-flex flex-column">
                    <figure class="card-product-thumbnail">
                        <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>" class="card-product-thumbnail-link">
                            <img src="<?= $arElement['IMAGES']['preview']['src'] ?>"
                                 alt="<?= $arElement['IMAGES']['preview']['alt'] ?>"
                                 title="<?= $arElement['IMAGES']['preview']['title'] ?>"
                                 class="card-product-media">


                            <button type="button" class="card-product-quickview">
                                <svg width="21" height="21">
                                    <use xlink:href="/images/icons/sprite.svg#icon-search"></use>
                                </svg>
                            </button>
                    </figure>
                    </a>

                    <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>"
                       class="card-product-title"><?= $arElement["NAME"] ?></a>

                    <div class="card-product-brand">Производитель: <?= $arElement[ "PROPERTIES"]['CML2_MANUFACTURER']['VALUE'] ?></div>
                    <?
                    /*
                                    <div class="card-product-rating-block d-flex align-items-center">
                                        <div class="card-product-rating">
                                            <i class="icon-star-solid"></i>
                                            <i class="icon-star-solid"></i>
                                            <i class="icon-star-solid"></i>
                                            <i class="icon-star-solid"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <div class="card-product-rating-count">1 отзыв</div>
                                    </div> */ ?>

                    <div class="card-product-order-block">
                        <div class="card-product-price-block d-flex flex-wrap align-items-baseline">

                            <div class="card-product-price"><?= $price ?></div>
                            <? if ($priceOld): ?>
                                <div class="card-product-old-price"><?= $priceOld ?></div>
                            <? endif; ?>

                            <?
                            /*<div class="card-product-old-price">2 095 <span><span class="rur">руб</span></span></div>*/ ?>
                        </div>

                        <?
                        /*<div class="card-product-quantity-block d-none d-xl-block float-left">
                                                <div class="card-product-quantity-control custom-control custom-number">
                                                    <input class="custom-control-input" type="text" value="1" maxlength="5">
                                                    <button type="button" class="custom-control-button custom-control-plus">
                                                        <svg class="custom-control-media" width="9" height="9">
                                                            <use xlink:href="/images/icons/sprite.svg#icon-plus"></use>
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="custom-control-button custom-control-minus">
                                                        <svg class="custom-control-media" width="9" height="9">
                                                            <use xlink:href="/images/icons/sprite.svg#icon-minus"></use>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>*/ ?>
                        <? if ($arElement['CAN_BUY']): ?>
                            <button type="button" class="card-product-buy btn" data-toggle="modal"
                                    data-target="#modalAddToCart">
                                <svg class="card-product-buy-media" width="18" height="18">
                                    <use xlink:href="/images/icons/sprite.svg#icon-shopping-cart"></use>
                                </svg>
                                В корзину
                            </button>
                        <? endif; ?>
                    </div>

                    <?
                    /*
                                    <div class="card-product-labels d-flex flex-column align-items-start">
                                        <span class="card-product-label">Дополнительный ключ</span>
                                    </div>

                                    <div class="card-product-actions d-flex flex-column align-items-end">
                                        <button type="button" class="card-product-action card-product-action-compare">
                                            <svg width="20" height="20">
                                                <use xlink:href="/images/icons/sprite.svg#icon-balance"></use>
                                            </svg>
                                        </button>
                                    </div>
                    */ ?>
                </div>
            </div>
        <?
        endforeach; ?>
    <?
    else: ?>
        <div>Товары не найдены =(</div>
    <?
    endif ?>

    <?
    /* Блок с рекламой
           <div class="catalog-item catalog-service-item col-6 col-sm-4 d-flex">
               <div class="card-catalog-service d-flex flex-column">
                   <div class="card-catalog-service-thumbnail">
                       <img src="uploads/catalog/service-find-by-photo.jpg" alt="" class="card-catalog-service-media">
                   </div>

                   <div class="card-catalog-service-content d-flex flex-column flex-grow-1">
                       <div class="card-catalog-service-title">Мы поможем вам подобрать замок по фото!</div>
                       <div class="card-catalog-service-description">Просто загрузите фото вашего замка на сайт</div>
                       <button type="button" class="card-catalog-service-cta btn btn-block mt-auto" data-toggle="modal"
                               data-target="#modalPhotoUploadRoot">Загрузить фото <span class="d-none d-md-inline">замка</span>
                       </button>
                   </div>
               </div>
           </div>
   */ ?>
</div>

<?= $arResult["NAV_STRING"] ?>
<?php
/*
<div class="catalog-section">
<table class="data-table" cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead>
	<tr>
		<td><?=GetMessage("CATALOG_TITLE")?></td>
		<?if(count($arResult["ITEMS"]) > 0):
			foreach($arResult["ITEMS"][0]["DISPLAY_PROPERTIES"] as $arProperty):?>
				<td><?=$arProperty["NAME"]?></td>
			<?endforeach;
		endif;?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
			<td><?=$arPrice["TITLE"]?></td>
		<?endforeach?>
		<?if(count($arResult["PRICES"]) > 0):?>
			<td>&nbsp;</td>
		<?endif?>
	</tr>
	</thead>
	<?foreach($arResult["ITEMS"] as $arElement):?>
	<?
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
	?>
	<tr id="<?=$this->GetEditAreaId($arElement['ID']);?>">
		<td>
			<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
			<?if(count($arElement["SECTION"]["PATH"])>0):?>
				<br />
				<?foreach($arElement["SECTION"]["PATH"] as $arPath):?>
					/ <a href="<?=$arPath["SECTION_PAGE_URL"]?>"><?=$arPath["NAME"]?></a>
				<?endforeach?>
			<?endif?>
		</td>
		<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
		<td>
			<?if(is_array($arProperty["DISPLAY_VALUE"]))
				echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
			elseif($arProperty["DISPLAY_VALUE"] === false)
				echo "&nbsp;";
			else
				echo $arProperty["DISPLAY_VALUE"];?>
		</td>
		<?endforeach?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
		<td>
			<?if($arPrice = $arElement["PRICES"][$code]):?>
				<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
					<s><?=$arPrice["PRINT_VALUE"]?></s><br /><span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
				<?else:?>
					<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
				<?endif?>
			<?else:?>
				&nbsp;
			<?endif;?>
		</td>
		<?endforeach;?>
		<?if(count($arResult["PRICES"]) > 0):?>
		<td>
			<?if($arElement["CAN_BUY"]):?>
				<noindex>
				<a href="<?echo $arElement["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
				&nbsp;<a href="<?echo $arElement["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
				</noindex>
			<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
				<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
							"NOTIFY_ID" => $arElement['ID'],
							"NOTIFY_URL" => htmlspecialcharsback($arElement["SUBSCRIBE_URL"]),
							"NOTIFY_USE_CAPTHA" => "N"
							),
							$component
						);?>
			<?endif?>&nbsp;
		</td>
		<?endif;?>
	</tr>
	<?endforeach;?>
</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>
</div>
*/ ?>
