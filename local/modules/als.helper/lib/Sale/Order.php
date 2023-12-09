<?php


namespace ALS\Helper\Sale;


use ALS\Helper\User;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Shipment;
use Exception;
use ALS\Helper\Dbg;


class Order {
    /**
     * Создает заказ с текущей корзиной
     *
     * @param array{
     *   SITE_ID: int,
     *   USER_ID: int,
     *   FUSER_ID: int,
     *   PERSON_TYPE_ID: int,
     *   DELIVERY_ID: int,
     *   PAYMENT_ID: int,
     *   CURRENCY: int,
     * } $params
     * @return \Bitrix\Sale\Order
     */
    public static function createWithCurrentCart(array $params = []): ?\Bitrix\Sale\Order {
        $siteId = $params['SITE_ID'] === LANGUAGE_ID ? 'ru' : ($params['SITE_ID'] ?? SITE_ID);
        $userId = $params['USER_ID'] ?? User::getAuthorizedId();
        $fUserId = $params['FUSER_ID'] ?? Buyer::getId();
        $currency = $params['CURRENCY'] ?? null;

        try {
            Loader::includeModule('sale');

            // Получаем корзину пользователя
            $basket = Basket::loadItemsForFUser($fUserId, $siteId);

            // Создаем заказ
            $order = \Bitrix\Sale\Order::create($siteId, $userId, $currency);

            // Устанавливаем корзину в заказ
            $order->setBasket($basket);

            // Устанавливаем тип покупателя
            if (!empty($params['PERSON_TYPE_ID'])) {
                $order->setPersonTypeId($params['PERSON_TYPE_ID']);
            }

            // Устанавливаем доставку
            if (!empty($params['DELIVERY_ID'])) {
                $is = self::setDelivery($order, $params['DELIVERY_ID']);

                if (!$is) {
                    return null;
                }
            }

            // Устанавливаем оплату
            if (!empty($params['PAYMENT_ID'])) {
                $is = self::setPayment($order, $params['PAYMENT_ID']);

                if (!$is) {
                    return null;
                }
            }

            return $order;
        } catch (Exception $exception) {
            // TODO: логировать $exception
            return null;
        }
    }


    /**
     * Устанавливает службу доставки в переданном объекте заказа
     *
     * @param \Bitrix\Sale\Order $order
     * @param int $deliveryId
     * @return bool
     */
    public static function setDelivery(\Bitrix\Sale\Order $order, int $deliveryId): bool {
        try {
            // Устанавливаем доставку
            // Получаем список отгрузок
            $shipmentCollection = $order->getShipmentCollection();

            /** @var Shipment создаем объект отгрузки */
            $shipment = $shipmentCollection->createItem(Manager::getObjectById($deliveryId));

            // Получаем коллекцию товаров отгрузки
            $shipmentItemCollection = $shipment->getShipmentItemCollection();

            foreach ($order->getBasket() as $item) {
                $shipmentItem = $shipmentItemCollection->createItem($item);
                $shipmentItem->setQuantity($item->getQuantity());
            }

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }


    /**
     * Устанавливает платежную систему с суммой оплаты в переданном объекте заказа
     *
     * @param \Bitrix\Sale\Order $order
     * @param int $paymentId
     * @return bool
     */
    public static function setPayment(\Bitrix\Sale\Order $order, int $paymentId): bool {
        try {
            $paymentCollection = $order->getPaymentCollection();

            // Создаем объект оплаты
            $payment = $paymentCollection->createItem(\Bitrix\Sale\PaySystem\Manager::getObjectById($paymentId));
            $payment->setField('SUM', $order->getPrice());

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }


    /**
     * Функция задает свойство заказа
     * @param \Bitrix\Sale\Order $order
     * @param string $field
     * @param $value
     * @return bool
     */
    public static function setValue(\Bitrix\Sale\Order $order, string $field, $value): bool {
        try {
            $order->setField($field, $value ?? '');
            return true;

        } catch (Exception $exception) {
            return false;
        }
    }

}
