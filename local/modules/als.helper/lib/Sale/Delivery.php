<?php


namespace ALS\Helper\Sale;


use ALS\Helper\TypeConvert;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Order;
use Bitrix\Sale\Services\Base\RestrictionManager;
use Bitrix\Sale\Shipment;
use ALS\Helper\Dbg;


class Delivery
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getById(int $id): array
    {
        // Загружаем модуль магазина
        Loader::includeModule('sale');

        $result = [];

        $select = [
            'ID:int>id',
        ];

        if (!empty($id)) {
            $result = \Bitrix\Sale\Delivery\Services\Table::getList(
                [
                    'filter' => ['ACTIVE' => 'Y', 'ID' => $id],
                ]
            );

            $result = $result->fetchAll();
            $typeConverter = new TypeConvert($select);
            $result = $typeConverter->convertDataTypes($result);
        }

        return $result;
    }

    /**
     * Возвращает список разрешенных доставок для заказа
     *
     * @param Order $order           - настроенный объект заказа
     * @param array $select          - поля для конвертера типов
     * @param int   $restrictionMode - режим работы ограничений \Bitrix\Sale\Services\Base\RestrictionManager::MODE_...
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws LoaderException
     * @throws ObjectNotFoundException
     */
    public static function getAllowList(Order $order, array $select = [], $restrictionMode = RestrictionManager::MODE_CLIENT): array
    {
        // Загружаем модуль магазина
        Loader::includeModule('sale');

        // Получаем список отгрузок
        $shipmentCollection = $order->getShipmentCollection();

        // Создаем объект отгрузки
        $shipment = $shipmentCollection->createItem();

        // Получаем коллекцию товаров отгрузки
        /** @var Shipment Отгрузка */
        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        foreach ($order->getBasket() as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        // Получаем список способов доставки
        $items = array_values(Manager::getRestrictedList($shipment, $restrictionMode));

        if (!$select) {
            return $items;
        }

        $typeConverter = new TypeConvert($select);

        return $typeConverter->convertDataTypes($items);
    }

}
