<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

?>

<? if (!empty($arResult['ITEMS'])): ?>
    <div class="header-nav-wrapper d-none d-xl-block">
    <div class="container">
    <nav aria-label="navigation">
    <ul class="header-nav d-flex justify-content-between list-unstyled">
    <?
    $isPrevParent = false;
    $isPrevVideo = [];
    ?>
    <? foreach ($arResult['ITEMS'] as $k => $mainItem): ?>
        <? if ($mainItem['depthLevel'] === 1): ?>

            <? if ($k > 0): ?>
                <? if ($isPrevParent): ?>
                    </div></div>
                    <?if(!empty($isPrevVideo)):?>
                        <div class="megamenu-aside-container col-xl-4">
                            <div class="megamenu-presentation-block">
                                <a data-fancybox href="<?=$isPrevVideo['url']?>" class="card-video card-video-with-overlay" >
                                    <figure class="card-video-figure">
                                        <div class="card-video-thumbnail">
                                            <img src="<?=$isPrevVideo['preview']?>" class="card-video-media" alt="<?=$isPrevVideo['url']?>">
                                        </div>

                                        <figcaption class="card-video-title" ><?=$isPrevVideo['description']?></figcaption>
                                    </figure>
                                </a>
                            </div>
                        </div>
                    <?endif;?>
                    </div></div>
                <? endif; ?>
                </li>
            <? endif; ?>
            <li class="header-nav-item <?= ($mainItem['isParent']) ? 'has-dropdown' : '' ?>">
            <a href="<?= $mainItem['url'] ?>" class="header-nav-link d-flex no-gutters">
                <div class="header-nav-thumbnail col-auto">
                    <img src="<?= $mainItem['icon']['src'] ?>" alt="<?= $mainItem['name'] ?>"
                         class="header-nav-media">
                </div>
                <div class="header-nav-content align-self-center col"><?= $mainItem['name'] ?></div>
            </a>
            <? if ($mainItem['isParent']): ?>
                <div class="megamenu-wrapper">
                <div class="row">
                <div class="megamenu-content-container col-xl-8">
                <div class="row">

                <?
                    $isPrevParent = true;
                    if(!empty($mainItem['video'])){
                        $isPrevVideo = [
                                'url' => $mainItem['video'],
                            'description' => $mainItem['videoDescription'],
                            'preview' => $mainItem['videoPreview']['src']
                        ];
                    }
                    ?>
            <? else: ?>
                <?
                    $isPrevParent = false;
                $isPrevVideo = [];
                ?>
            <? endif; ?>
        <? else: ?>
            <div class="col-lg-4">
                <div class="megamenu-group">
                    <div class="megamenu-heading"><?= $mainItem['name'] ?></div>
                    <? if (!empty($mainItem['items'])): ?>
                        <ul class="megamenu-nav megamenu-nav-with-media list-unstyled">
                            <? foreach ($mainItem['items'] as $item): ?>
                                <li class="megamenu-nav-item">
                                    <a href="<?= $item['url'] ?>"
                                       class="megamenu-nav-link<?= ($item['bl']) ? ' megamenu-nav-link-more' : '' ?> d-flex no-gutters">

                                        <div class="megamenu-nav-thumbnail col-auto">
                                            <? if (!empty($item['icon'])): ?>
                                                <img src="<?= $item['icon']['src'] ?>"
                                                     alt="<?= $item['name'] ?>" class="megamenu-nav-media">
                                            <? endif; ?>
                                        </div>

                                        <div class="megamenu-nav-content align-self-center col">
                                            <?= $item['name'] ?>
                                        </div>
                                    </a>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    <? endif; ?>
                </div>
            </div>
        <? endif; ?>

    <? endforeach; ?>
    </li>
<? endif; ?>
</ul>
</nav>
</div>
</div>
