<?php


namespace ALS\Helper\Sale;


use ALS\Helper\TypeConvert;
use Bitrix\Main\Loader;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Internals\DiscountCouponTable;
use Exception;


class Coupon {
    /**
     * Устанавливает купон для корзины
     * @param string $coupon
     * @return bool
     */
    public static function add(string $coupon): bool {
        if (!self::includeModules()) { return false; }

        return DiscountCouponsManager::add($coupon);
    }


    /**
     * Удаляет купон из корзины
     * @param string $coupon
     * @return bool
     */
    public static function delete(string $coupon): bool {
        if (!self::includeModules()) { return false; }

        return DiscountCouponsManager::delete($coupon);
    }


    /**
     * Возвращает список примененных купонов
     * @param array $select
     * @return string[]
     */
    public static function getActiveList(array $select = []): array {
        if (!self::includeModules()) { return []; }

        $items = array_values(DiscountCouponsManager::get());

        if (empty($select)) {
            return $items;
        }

        $typeConverter = new TypeConvert($select ?: []);

        return $typeConverter->convertDataTypes($items);
    }


    /**
     * Функция возвращает все доступные купоны
     * @param array $params
     * @return array
     */
    public static function getList(array $params = []): array {
        if (!self::includeModules()) { return []; }

        $coupons = [];
        $discountIterator = DiscountCouponTable::getList($params);

        while ($coupon = $discountIterator->fetch()) {
            $coupons[] = $coupon;
        }

        if ($params && $params['select']) {
            $typeConverter = new TypeConvert($params['select'] ?: []);
            return $typeConverter->convertDataTypes($coupons);
        }

        return $coupons;
    }


    /**
     * Функция возвращает true, если есть хоть один доступный купон
     * @return bool
     */
    public static function isAvailable(): bool {
        return !empty(self::getList());
    }


    /**
     * Функция подключает модули для работы класса
     * @return bool — true, если всё подключено
     */
    private static function includeModules(): bool {
        try {
            Loader::includeModule('sale');
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

}
