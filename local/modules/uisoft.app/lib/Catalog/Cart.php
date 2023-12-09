<?php

declare(strict_types=1);

namespace Uisoft\App\Catalog;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\NotSupportedException;
use Bitrix\Main\ObjectNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Uisoft\App\Tools;
use ALS\Helper\Sale;

class Cart
{
    private const MODULE_ID = 'catalog';

    /**
     * @throws ObjectNotFoundException
     * @throws NotSupportedException
     * @throws NotImplementedException
     * @throws ArgumentNullException
     * @throws ArgumentTypeException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentException
     */
    public static function add($params): ?array
    {
        $result = [
            'success' => true
        ];

        $productId = (int)$params['productId'];
        $count = $params['count'] ?? 1;

        if ($productId <= 0) {
            return [
                'success' => false,
                'error' => 'ID товара не указан'
            ];
        }

        $product = Tools::first(Product::get([
            'FILTER' => [
                'ID' => $productId,
            ],
            'SELECT' => [
                'ID:int>id',
                'AVAILABLE:string>available',
                'QUANTITY:int>quantity'
            ],
            '__REPLACE_SELECT' => true
        ]));

        if (empty($product)) {
            return [
                'success' => false,
                'error' => 'Товар не найден'
            ];
        }

        if ($product['available'] !== 'Y') {
            return [
                'success' => false,
                'error' => 'Товар не доступен'
            ];
        }

        // если пытаются положить больше чем есть в наличии
        $count = ($product['quantity'] < $count) ? $product['quantity'] : $count;

        // Кладем в корзину
        $basket = Sale\Cart::getCartByFUser(Sale\Buyer::getId());

        // Если не удалось получить объект корзины – ошибка
        if ($basket === null) {
            return [
                'success' => false,
                'error' => 'Не удалось получить корзину пользователя'
            ];
        }

        $item = Tools::first($basket->getExistsItems(self::MODULE_ID, $productId));

        if (!empty($item)) {
            // Прибавляем к текущему количеству или оставляем максимально возможное
            $count = ($item->getQuantity() + $count) > $product['quantity'] ? $product['quantity'] : $item->getQuantity() + $count;
            $item->setField('QUANTITY', $count);
        } else {
            $item = $basket->createItem(self::MODULE_ID, $productId);

            $item->setFields(
                [
                    'QUANTITY' => $count,
                    'CURRENCY' => CurrencyManager::getBaseCurrency(),
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                ]
            );
        }

        $resultSave = $basket->save();

        if (!$resultSave->isSuccess()) {
            throw new BadRequestHttpException(implode(';', $resultSave->getErrorMessages()));
        }

        if ($item->getId() === 0) {
            return [
                'success' => false,
                'error' => 'Ошибка добавления товара в корзину'
            ];
        }

        $result['cart'] = self::get();
        return $result;
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectNotFoundException
     * @throws ArgumentException
     * @throws NotImplementedException
     */
    public static function delete(int $productId = 0)
    {
        if ($productId <= 0) {
            return [
                'success' => false,
                'error' => 'Товар не указан'
            ];
        }
        

        $basket = Sale\Cart::getCartByFUser(Sale\Buyer::getId());

        
        \ALS\Helper\Dbg::show($basket, true, true);
     //   $basketAr = $basket->getItemById($productId);
        $basket->getItemById($productId)->delete();
        $basket->save();

        return [
            'success' => true,
            'cart' => self::get()
        ];
    }

    public static function get(): array
    {
        $basket = Sale\Cart::getCartByFUser(Sale\Buyer::getId());

        return Sale\Cart::getList([
            'CART' => $basket,
            'SELECT' => [
                'ID:int>id',
                'NAME:string>name',
                'PRICE:float>price',
                'PRODUCT_ID:int>productId',
                'QUANTITY:int>quantity',
                'DETAIL_PICTURE:Image>image'
            ]
        ]);
    }
}
