<?php


namespace ALS\Helper;


use ALS\Helper\Exception\ArgumentException;
use ALS\Helper\HlBlock\HlBlock;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserFieldTable;
use CUserFieldEnum;

/**
 * Работа с пользовательскими полями
 */
class UserField
{
    /**
     * Возвращает список пользовательских полей
     *
     * @param array $params - стандартные поля ORM:
     * <li>select:
     * <li><li>FIELD_NAME - поле символьного кода
     * <li><li>ENTITY_ID - символьный идентификатор сущности
     * <li>filter
     * @return array
     * @throws SystemException
     */
    public static function getList(array $params = []): array
    {
        return UserFieldTable::getList($params)->fetchAll();
    }


    /**
     * Возвращает список значений пользовательских полей перечисляемого типа.
     * Для корректной выборки важно использование параметров: HL_CODE|IBLOCK_CODE и CODE
     *
     * @param array $params :
     * <li>HL_CODE - символьный код хайлоад-блока - для выборки перечислений определенного хайлоадблока
     * <li>IBLOCK_CODE - символьный код инфоблока - для выборки перечислений определенного инфоблока
     * <li>CODE - символьный код свойства, для выборки перечислений определенного поля
     * <li>QUERY - данные запроса:
     * <li><li>FILTER:
     * <li><li><li>ID - идентификатор значения списка;
     * <li><li><li>USER_FIELD_ID - идентификатор пользовательского поля;
     * <li><li><li>VALUE - значение для отображения;
     * <li><li><li>DEF - флаг умолчания;
     * <li><li><li>SORT - сортировка;
     * <li><li><li>XML_ID - код внешнего источника.
     * <li><li><li>!USER_FIELD_NAME - символьный код пользовательского поля, при использовании происходит объединение с таблицей b_user_field,
     * если на проекте используются свойства с одинаковыми символьными кодами, вернет значения всех полей с указанными кодом
     * <li><li>SORT
     * <li><li>SELECT
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     */
    public static function getEnumList(array $params = []): array
    {
        $result = [];

        $params['QUERY']['FILTER'] = $params['QUERY']['FILTER'] ?? [];

        // Выборка идентификатора пользовательского свойства для фильтрации
        if (!empty($params['CODE'])) {
            if (!empty($params['HL_CODE'])) {
                $entityId = HlBlock::getEntityId($params['HL_CODE']);

                if (empty($entityId)) {
                    throw new ArgumentException('Not correct parameter HL_CODE');
                }
            } else if (!empty($params['IBLOCK_CODE'])) {
                $entityId = Sect::getEntityId($params['IBLOCK_CODE']);

                if (empty($entityId)) {
                    throw new ArgumentException('Not correct parameter IBLOCK_CODE');
                }
            } else if (!empty($params['IBLOCK_ID'])) {
                $entityId = Sect::getEntityId($params['IBLOCK_ID']);

                if (empty($entityId)) {
                    throw new ArgumentException('Not correct parameter IBLOCK_ID');
                }
            }

            $filter = (!empty($entityId) ? ['ENTITY_ID' => $entityId] : []);

            $fields = self::getList([
                'filter' => $filter,
                'select' => ['ID', 'FIELD_NAME'],
                'cache'  => ['ttl' => 3600 * 24],
            ]);

            $fieldId = Arr::findInArr($fields, 'FIELD_NAME', $params['CODE'], 'ID');

            if (empty($fieldId)) {
                throw new ArgumentException('Not correct parameter CODE');
            }

            $params['QUERY']['FILTER']['USER_FIELD_ID'] = $fieldId;
        }

        $userField = (new CUserFieldEnum)->GetList($params['QUERY']['SORT'] ?? [], $params['QUERY']['FILTER']);
        while ($item = $userField->Fetch()) {
            $result[] = $item;
        }

        if (!empty($params['SELECT'])) {
            $result = (new TypeConvert($params['SELECT']))->convertDataTypes($result);
        }

        return $result;
    }
}
