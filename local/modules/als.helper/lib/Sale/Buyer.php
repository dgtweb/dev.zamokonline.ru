<?php


namespace ALS\Helper\Sale;


use Bitrix\Main\Loader;
use Bitrix\Sale\Fuser;
use Exception;


class Buyer {
    /**
     * Возвращает идентификатор покупателя
     *
     * @return int|null
     */
    public static function getId(): ?int {
        try {
            Loader::includeModule('sale');
        } catch (Exception $exception) {
            return null;
        }

        return Fuser::getId();
    }

}
