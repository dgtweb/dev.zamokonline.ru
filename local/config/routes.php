<?php

declare(strict_types=1);

use Uisoft\App\Controller\Catalog;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Главная коллекция маршрутов
$routes = new RouteCollection();

$routes->add(
    'catalog-cart-add',
    (new Route(
        '/cart/{productId}/add',
        [
            '_controller' => [Catalog\Cart::class, 'add'],
        ]
    ))->setMethods('GET')
);

$routes->add(
    'catalog-cart-add-count',
    (new Route(
        '/cart/{productId}/{count}/add',
        [
            '_controller' => [Catalog\Cart::class, 'add'],
        ]
    ))->setMethods('GET')
);

$routes->add(
    'catalog-cart-del',
    (new Route(
        '/cart/{productId}/del',
        [
            '_controller' => [Catalog\Cart::class, 'del'],
        ]
    ))
        ->setMethods('GET')
        ->setRequirements([
      //      'code' => '^[a-zA-Z0-9_-]+$',
            'productId' => '^\d+$',
        ])
);


//$routes->add(
//    'catalog-product-set',
//    (new Route(
//        '/catalog/product/set',
//        [
//            '_controller' => [Catalog\Element::class, 'set'],
//        ]
//    ))->setMethods('POST')
//);
//
//// Активация/деактивация товара
//$routes->add(
//    'catalog-product-active-set',
//    (new Route(
//        '/catalog/product/{productId}/active/{flag}',
//        [
//            '_controller' => [Catalog\Element::class, 'setActive'],
//        ]
//    ))->setMethods('GET')
//);

// Возвращаем собранный объект списка маршрутов
return $routes;
