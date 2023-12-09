<?php

declare(strict_types=1);

namespace Uisoft\App\Controller\Catalog;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\NotSupportedException;
use Bitrix\Main\ObjectNotFoundException;
use Uisoft\App\Catalog;

use Uisoft\App\Controller\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class Cart
{
    /**
     * @throws ObjectNotFoundException
     * @throws NotSupportedException
     * @throws NotImplementedException
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentException
     */
    public static function add($productId, $count = 1): array
    {
        $productId = (int)$productId;
        $count = $params['count'] ?? 1;

        if ($productId <= 0) {
            throw new BadRequestHttpException('ID товара не указан');
        }

        $result = Catalog\Cart::add(['productId' => $productId, 'count' => $count]);

        if (!$result['success']) {
            throw new BadRequestHttpException($result['error']);
        }

        return [
            'cart' => $result['cart'],
            'template' => [
                'html' => [
                    '.header-account-badge' => count($result['cart']),
                    '.product-trade-block .product-buy.btn' => 'В корзине'
                ],
                'exec' => [
                    '$("#button-add-to-cart").hide();',
                    '$("#button-in-cart").fadeIn();'
                ],
            ]
        ];
    }

    public static function get()
    {
        //return Catalog\Cart::get();
    }

    public static function del($productId): ?array
    {
        $productId = (int)$productId;
        if ($productId <= 0) {
            throw new BadRequestHttpException('ID товара не указан');
        }

        
        $result = Catalog\Cart::delete($productId);
        \ALS\Helper\Dbg::show($result, true, true);

        if (!$result['success']) {
            throw new BadRequestHttpException($result['error']);
        }
        
        //return Catalog\Cart::remove(Helper::getRequestData($request));
        return [];
    }

    public static function clear()
    {
        //return Catalog\Cart::clear();
    }
}
