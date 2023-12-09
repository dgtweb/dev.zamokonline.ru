<?php


namespace ALS\Helper\Sale;


use ALS\Helper\TypeConvert;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Order;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Services\Base\RestrictionManager;
use Bitrix\Sale\Shipment;


class Payment {
    /**
     * Возвращает разрешенный список оплат
     *
     * @param Order $order - настроенный объект заказа
     * @param array|null $select - поля для конвертера типов
     * @param int $restrictionMode - режим работы ограничений \Bitrix\Sale\Services\Base\RestrictionManager::MODE_...
     * @return array
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws NotImplementedException
     * @throws SystemException
     */
    public static function getAllowList(Order $order, array $select = null, $restrictionMode = RestrictionManager::MODE_CLIENT): array {
        Loader::includeModule('sale');

        // Получаем коллекцию оплат
        $paymentCollection = $order->getPaymentCollection();

        // Создаем объект оплаты на сумму заказа
        $payment = $paymentCollection->createItem();
        $payment->setField('SUM', $order->getPrice());

        // Получаем список платежных систем
        $items = array_values(\Bitrix\Sale\PaySystem\Manager::getListWithRestrictions($payment, $restrictionMode));

        if ($select === null || count($select) === 0) {
            return $items;
        }

        $typeConverter = new TypeConvert($select);

        // Типографируем список оплат
        return $typeConverter->convertDataTypes($items);
    }
}
