<?php


namespace ALS\Helper\Sale;


use ALS\Helper\Arr;
use ALS\Helper\TypeConvert;
use Bitrix\Main\InvalidOperationException;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\BasketBase;
use Bitrix\Sale\Discount;
use Bitrix\Sale\Discount\Context\Fuser;
use Exception;
use ALS\Helper\Dbg;


class Cart
{
    /**
     * Возвращает объект корзины текущего пользователя
     *
     * @param int         $fUserId
     * @param string|null $siteId
     *
     * @return BasketBase
     */
    public static function getCartByFUser(int $fUserId, string $siteId = SITE_ID): ?BasketBase
    {
        try {
            Loader::includeModule('sale');

            // Получаем корзину пользователя
            return Basket::loadItemsForFUser($fUserId, $siteId);
        } catch (Exception $exception) {
            return null;
        }
    }


    /**
     * Возвращает объект пустой корзины
     *
     * @param string|false|mixed|null $siteId
     *
     * @return BasketBase|null
     */
    public static function getEmptyCart(string $siteId = SITE_ID): ?BasketBase
    {
        try {
            Loader::includeModule('sale');

            return Basket::create($siteId);
        } catch (Exception $exception) {
            return null;
        }
    }


    /**
     * Проверяет корзину пользователя на пустоту
     *
     * @param int    $fUserId
     * @param string $siteId
     *
     * @return bool
     */
    public static function isEmpty(int $fUserId, string $siteId = SITE_ID): bool
    {
        $cart = self::getCartByFUser($fUserId, $siteId);

        return !($cart !== null) || $cart->isEmpty();
    }


    /**
     * Возвращает список товаров текущей или переданной корзины пользователя
     *
     * @param array{
     *     CART: \Bitrix\Sale\BasketBase,
     *     SITE_ID: string,
     *     FUSER_ID: string,
     *     SELECT: string[],
     *     USE_DISCOUNT: bool
     * } $params
     *
     * @return array
     */
    public static function getList(array $params): array
    {
        try {
            if (!empty($params['CART'])) {
                $cart = $params['CART'];
            } else {
                // Идентификатор сайта по умолчанию
                $params['SITE_ID'] = $params['SITE_ID'] ?? SITE_ID;

                // Идентификатор покупателя
                $params['FUSER_ID'] = $params['FUSER_ID'] ?? Buyer::getId();

                // Получаем корзину пользователя
                $cart = self::getCartByFUser($params['FUSER_ID'], $params['SITE_ID']);
            }

            // Используем скидки
            $params['USE_DISCOUNT'] = $params['USE_DISCOUNT'] ?? true;

            // Если корзина пустая возвращаем пустой список
            if ($cart === null || $cart->isEmpty()) {
                return [];
            }

            // Если используем скидки, использование идентификатора элемента корзины обязательно
            if ($params['USE_DISCOUNT'] === true) {
                $params['SELECT'][] = 'ID:int>id';
            }

            $typeConverter = new TypeConvert($params['SELECT'] ?: []);
            $selectFields = $typeConverter->getSelect();

            $result = [];
            foreach ($cart->getBasketItems() as $key => $cartItem) {
                $item = [];

                foreach ($selectFields as $field) {
                    $fieldValue = $cartItem->getField($field);

                    if ($field === 'ID' && $fieldValue === null) {
                        $fieldValue = $cartItem->getField('PRODUCT_ID');
                    }

                    $item[$field] = $fieldValue;
                }

                // Свойства товаров в корзине
                $basketPropertyCollection = $cartItem->getPropertyCollection();
                foreach ($basketPropertyCollection->getPropertyValues() as $itemProperty) {
                    $item['PROPS'][$itemProperty['CODE']] = [
                        'NAME' => $itemProperty['NAME'],
                        'VALUE' => $itemProperty['VALUE'],
                    ];
                }

                // Свойства товаров в корзине
//                $basketPropertyCollection = $cartItem->getPropertyCollection();
//                foreach($basketPropertyCollection->getPropertyValues() as $itemProperty){
//                    $item['PROPS'][$itemProperty['CODE']] = [
//                        'NAME' => $itemProperty['NAME'],
//                        'VALUE' => $itemProperty['VALUE'],
//                    ];
//                }

                $result[] = $item;
            }

            if ($params['USE_DISCOUNT'] === true) {
                self::modifyItemsAddDiscountPrice($cart, $result);
            }

            // Приведем массив к нужным типам данных
            if ($typeConverter->getTypes()) {
                $result = $typeConverter->convertDataTypes($result);
            }

            return $result;
        } catch (Exception $exception) {
            return [];
        }
    }


    /**
     * Функция возвращает общую цену корзины с учетом скидок
     *
     * @param array<int, array{price: int, oldPrice: int, quantity: int}> $items список товаров корзины
     *
     * @return array
     */
    public static function getSummary(array $items): array
    {
        $price = 0;
        $priceDiscount = 0;

        foreach ($items as $item) {
            // Цена со скидкой состоит из поля элементов price
            $priceDiscount += ($item['price'] * $item['quantity']);

            // Полная цена состоит из поля элементов oldPrice, если отсутствует значит скидки нету на товар и берется значение поля price
            $price += ($item['oldPrice'] ?? $item['price']) * $item['quantity'];
        }

        return [
            'price' => $price,
            'priceDiscount' => $priceDiscount,
        ];
    }

    /**
     * Модифицирует список товаров корзины, добавляя информацию о цене после применения скидок.
     * OLD_PRICE - старая цена
     * PRICE - текущая цена
     *
     * @param BasketBase $cart
     * @param array      $items
     *
     * @return void
     * @throws InvalidOperationException
     */
    private static function modifyItemsAddDiscountPrice(BasketBase $cart, array &$items): void
    {
        // Получаем объект со скидками из корзины пользователя
        $discounts = \Bitrix\Sale\Discount::buildFromBasket($cart, new Fuser($cart->getFUserId(true)));

        if ($discounts === null) {
            return;
        }

        $discounts->calculate();

        // Результат после применения скидок
        $discountResult = $discounts->getApplyResult(true);
        $tmpItems = array_column($items, null, 'ID');

        foreach ($discountResult['PRICES']['BASKET'] as $cartItemId => $discountCartItem) {
            if ($tmpItems[$cartItemId]['PRICE'] !== $discountCartItem['PRICE']) {
                $tmpItems[$cartItemId]['OLD_PRICE'] = $tmpItems[$cartItemId]['PRICE'];
                $tmpItems[$cartItemId]['PRICE'] = $discountCartItem['PRICE'];
            }
        }
        $items = array_values($tmpItems);
    }
}
