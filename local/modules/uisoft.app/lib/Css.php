<?php

namespace Uisoft\App;

use Bitrix\Main\Page\Asset;

class Css
{

    /**
     * Подключает css стили
     */
    public static function include(): void
    {
        $asset = Asset::getInstance();

        // $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/bootstrap/css/bootstrap.min.css');
        $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/bootstrap-4.6.0/dist/css/bootstrap.min.css');
        $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/swiper/css/swiper.min.css');
        $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/select2/css/select2.min.css');
        $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/fancybox/jquery.fancybox.min.css');
        $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/jquery-ui-1.12.1.custom/jquery-ui.css');
        $asset->addCss(SITE_TEMPLATE_PATH . '/vendor/jquery-ui-1.12.1.custom/jquery-ui.structure.css');


        $asset->addCss(SITE_TEMPLATE_PATH . "/css/nf_pp.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/_fonts.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/_core.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/_typography.css");
        //$asset->addCss(SITE_TEMPLATE_PATH . "/css/_preloader.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/preloaders.css");


        /* components */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_badge.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_breadcrumb.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_pagination.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_forms.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/buttons.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_select2.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_nav.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_swiper.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_dropdown.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_offcanvas.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_megamenu.css");


        /* cards */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/cards/_card.banner.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/cards/_card.brand.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/cards/_card.product.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/cards/_card.video.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/cards/_card.review.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/cards/_card.order.css");

        /* header */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.statusbar.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.toggle.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.logo.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.contact.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.account.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.nav.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.search.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.shops.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/header/_header.cart.css");

        /* sections */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.hero.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.categories.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.products.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.services.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.brands.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.video.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.steps.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.find-by-photo.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/sections/_section.contact.css");


        /* page home */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-home/_home.brands.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-home/_home.video.css");


        /* page catalog */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.filter.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.banner.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.controls.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.grid.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.service.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-catalog/_catalog.tags.css");


        /* page product */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.gallery.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.actions.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.code.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.trade.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.colors.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.price.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.summary.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.specifications.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.scheme.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.description.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.reviews.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-product/_product.online.css");


        /* page cart */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-cart/_cart.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-cart/_cart.order.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-cart/_cart.promocode.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-cart/_cart.total.css");


        /* page checkout */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-checkout/_checkout.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-checkout/_checkout.data.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-checkout/_checkout.summary.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/pages/page-checkout/_checkout.actions.css");


        /* footer */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/footer/_footer.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/footer/_footer.contact.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/footer/_footer.nav.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/layout/footer/_footer.copyright.css");


        /* modals */
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_modal.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_modal.photo.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_modal.cart.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_modal.order.css");
        $asset->addCss(SITE_TEMPLATE_PATH . "/css/components/_modal.review.css");
    }
}
