<?php

namespace ALS\Helper;

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Iblock\SectionElementTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CIBlock;
use CIBlockElement;
use CIBlockProperty;
use CIBlockPropertyEnum;
use CModule;


class El
{

    // =========================================================================
    // ================================= CRUD ==================================
    // =========================================================================

    /**
     * Функция для добавления элемента инфоблока
     *
     * @param array $params Поля и свойства нового элемента
     *
     * @return int|string ID нового элемента или код ошибки в строке
     */
    public static function add($params)
    {
        CModule::IncludeModule('iblock');

        $fields = $params;

        // Определение ID инфоблока
        $iblockId = null;

        if (!empty($fields['TYPE'])) {
            $iblockId = Help::getOpt('IBLOCK_' . $fields['TYPE'] . '_ID');
        } elseif (!empty($fields['IBLOCK_ID'])) {
            $iblockId = $fields['IBLOCK_ID'];
        } elseif (!empty($fields['IBLOCK_CODE'])) {
            $iblockId = Help::getIblockIdByCode($fields['IBLOCK_CODE']);
        }

        $fields['IBLOCK_ID'] = $iblockId;
        unset($fields['TYPE']);


        $el = new CIBlockElement;
        if ($ELEMENT_ID = $el->Add($fields)) {
            return $ELEMENT_ID;
        }

        return 'Error: ' . $el->LAST_ERROR;
    }


    /**
     * Функция возвращает массив с данными об элементах инфоблока
     *
     * @param array $params Параметры выборки
     *
     * @return array
     */
    public static function getList($params): array
    {
        CModule::IncludeModule('iblock');

        // Определение ID инфоблока
        $iblockId = false;
        if (!empty($params['IBLOCK_ID'])) {
            $iblockId = (int)$params['IBLOCK_ID'];
        } elseif (!empty($params['TYPE'])) {
            $iblockId = Help::getOpt('IBLOCK_' . $params['TYPE'] . '_ID');
        } elseif (!empty($params['IBLOCK_CODE'])) {
            $iblockId = Help::getIblockIdByCode($params['IBLOCK_CODE']);
        }

        // Определение направления сортировки
        $order = ['SORT' => 'ASC'];
        if (!empty($params['ORDER'])) {
            $order = $params['ORDER'];
        }

        // Определение фильтра
        $filter = [];
        if (!empty($params['FILTER'])) {
            $filter = $params['FILTER'];
        }
        $filter['SHOW_HISTORY'] = 'Y';

        if ($iblockId) {
            $filter['IBLOCK_ID'] = $iblockId;
        }

        // Определение группировки
        $group = $params['GROUP'] ?: false;
        if ($params['GROUP'] === []) {
            $group = [];
        }

        // Определение постраничной навигации
        $nav = $params['NAV'] ?: false;

        // Определение полей выборки
        $typeConverter = new TypeConvert($params['SELECT'] ?: []);
        $select = array_merge($typeConverter->getSelect(), ['ID', 'IBLOCK_ID']);

        // Выборка результата из базы
        $rsElement = CIBlockElement::GetList(
            $order,
            $filter,
            $group,
            $nav,
            $select
        );

        $result = [];

        if (is_string($rsElement)) {
            return [
                'cnt' => (int)$rsElement
            ];
        }

        if ($params['GET_NEXT'] === 'Y') {
            while ($element = $rsElement->GetNext(true, false)) {
                if ($element['ID']) {
                    $key = $element['ID'];
                    $result[$key] = $element;
                } else {
                    $result[] = $element;
                }
            }
        } else {
            while ($element = $rsElement->Fetch()) {
                if ($element['ID']) {
                    $key = $element['ID'];

                    if ($result[$key]) {
                        $result[$key] = Arr::getMergeExt(
                            $result[$key],
                            $element
                        );
                    } else {
                        $result[$key] = $element;
                    }
                } else {
                    $result[] = $element;
                }
            }
        }


        foreach ($result as $key => $element) {
            if (!is_array($element)) {
                continue;
            }

            foreach ($element as $keyField => $valField) {
                $matches = [];

                if (preg_match('/^PROPERTY_(\w+)_VALUE$/', $keyField, $matches)) {
                    if (is_string($valField)) {
                        $result[$key]['PROP'][$matches[1]] = trim($valField);
                    } else {
                        $result[$key]['PROP'][$matches[1]] = $valField;
                    }
                }
            }
        }


        if ($params['GET_ENUM_CODE'] === 'Y') {
            // Если необходима выборка кодов свойств типа «список»
            $enumXmlID = [];

            foreach ($result as $key => $element) {
                if (!is_array($element)) {
                    continue;
                }

                foreach ($element as $keyField => $valField) {
                    $matches = [];

                    if (preg_match('/^PROPERTY_(\w+)_ENUM_ID$/', $keyField, $matches)) {
                        // Если поле относится к свойству «список»

                        if ($enumXmlID[$valField]) {
                            $xmlID = $enumXmlID[$valField];
                        } else {
                            $propEnum = CIBlockPropertyEnum::GetByID($valField);
                            $xmlID = $propEnum['XML_ID'];
                            $enumXmlID[$valField] = $xmlID;
                        }

                        $result[$key]['PROPERTY_' . $matches[1] . '_XML_ID'] = $xmlID;
                    }
                }
            }
        }

        // Приведем массив к нужным типам данных
        if ($typeConverter->getTypes()) {
            $result = $typeConverter->convertDataTypes($result);
        }

        return $params['ASSOC'] === 'Y'
            ? $result
            : array_values($result);
    }


    /**
     * Функция обновляет поля и свойства элемента
     *
     * @param int   $id     ID изменяемой записи
     * @param array $params Массив полей [FIELDS] и свойств [PROPS/PROPERTY_VALUES]
     *
     * @return bool|string
     */
    public static function update($id, $params)
    {
        CModule::IncludeModule('iblock');


        // Поля элемента
        $element = $params['FIELDS'] ?: $params;

        $removeFields = ['PROPERTY_VALUES', 'PROPS', 'TYPE', 'IBLOCK_CODE'];

        foreach ($removeFields as $field) {
            if (isset($element[$field])) {
                unset($element[$field]);
            }
        }


        // Свойства элемента
        $props = [];
        if ($params['PROPERTY_VALUES']) {
            $props = $params['PROPERTY_VALUES'];
            unset($params['PROPERTY_VALUES']);
        } elseif ($params['PROPS']) {
            $props = $params['PROPS'];
            unset($params['PROPS']);
        }


        // Обновление в БД
        $el = new CIBlockElement;
        $res = $el->Update($id, $element);

        if ($res === true && is_array($props)) {
            // Определение ID инфоблока
            $iblockId = is_numeric($params['IBLOCK_ID'])
                ? $params['IBLOCK_ID']
                : Help::getIblockIdByCode($params['IBLOCK_CODE']);

            if ($iblockId) {
                CIBlockElement::SetPropertyValuesEx($id, $iblockId, $props);
            }

            return true;
        }

        return $el->LAST_ERROR;
    }


    /**
     * Функция удаляет элемент инфоблока
     *
     * @const object $DB Класс для работы с базой данной
     *
     * @param int $id ID удаляемого элемента
     *
     * @return boolean true, если удаление прошло успешно и false, если нет
     */
    public static function delete($id): bool
    {
        if ((int)$id) {
            CModule::IncludeModule('iblock');
            global $DB;

            $DB->StartTransaction();
            if (!CIBlockElement::Delete($id)) {
                $DB->Rollback();
            } else {
                $DB->Commit();

                return true;
            }
        }

        return false;
    }


    // =========================================================================
    // ======================== ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ =========================
    // =========================================================================

    /**
     * Функция делает массив ассоциативным по ключу 'id'
     *
     * @param array $items Ссылка на массив, который надо сделать ассоциативным по id
     * @param bool  $apply Если не задано или false, применяться функция не будет
     */
    public static function applyAssoc(array &$items, $apply = true): void
    {
        if (!$apply) {
            return;
        }

        $assocItems = [];

        foreach ($items as $item) {
            $assocItems[$item['id']] = $item;
        }

        $items = $assocItems;
    }


    /**
     * Функция деактивирует элемент с указанным кодом
     *
     * @param int $id ID элемента
     *
     * @return bool|string
     */
    public static function deactivate($id)
    {
        return self::update(
            $id,
            [
                'FIELDS' => [
                    'ACTIVE' => 'N',
                ],
            ]
        );
    }


    /**
     * Метод возвращает набор полей и свойств для элемента инфоблока
     *
     * @param array $item       - Элемент инфоблока
     * @param array $fieldCodes - Массив соответствий полей элемента к полям в битриксе
     * @param array $propCodes  - Массив соответствий полей элемента к свойствам в битриксе
     *
     * @return array - Массив с полями [FIELDS] и [PROPS]
     */
    public static function getBitrixData(array $item, array $fieldCodes, array $propCodes): array
    {
        CModule::IncludeModule('iblock');
        $iblockId = (int)CIBlockElement::GetIBlockByID($item['id']);

        $fields = [];
        $props = [];

        foreach ($item as $field => $value) {
            $bxFieldCode = $fieldCodes[$field];
            $bxPropCode = is_array($propCodes[$field]) ? $propCodes[$field][0] : $propCodes[$field];
            $type = is_array($propCodes[$field]) ? $propCodes[$field][1] : null;
            $propData = $bxPropCode ? self::getPropData($iblockId, $bxPropCode) : '';

            // Нужно ли пропустить сохранение этого свойства
            $skipProp = false;

            // Корректируем значение
            $valueType = 'any';
            $valueCorrected = $value;

            // Определяем тип свойства
            if (!$type && $bxPropCode) {
                if (is_array($value) && $propData['PROPERTY_TYPE'] === 'F') {
                    $type = 'File';
                }

                if ($propData['PROPERTY_TYPE'] === 'L' && is_bool($value) && $value === true) {
                    $type = 'EnumBool';
                }

                if ($propData['PROPERTY_TYPE'] === 'L' && is_string($value)) {
                    $type = 'EnumCode';
                }
            }


            // Преобразуем файлы в формат пригодный для битрикса
            if ($type === 'File') {
                if ($item['id'] > 0 && $item[$field]) {
                    $oldValues = array_map('intval', self::getProp($iblockId, $item['id'], $bxPropCode));
                    $newValues = array_column($item[$field], 'id');

                    if ($oldValues === $newValues) {
                        $skipProp = true;
                    }
                }

                if ($propData['MULTIPLE'] === 'Y') {
                    $valueType = 'files';
                    $valueCorrected = [];

                    if (count($value) === 0) {
                        $valueCorrected = [['VALUE' => ['del' => 'Y']]];
                    } else {
                        foreach ($value as $val) {
                            $filePath = $_SERVER['DOCUMENT_ROOT'] . $val['src'];
                            $valueCorrected[] = [
                                'VALUE'       => \CFile::MakeFileArray($filePath),
                                'DESCRIPTION' => $val['name'],
                            ];
                        }
                    }
                } else {
                    $valueType = 'file';
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . $valueCorrected['src'];
                    $valueCorrected = \CFile::MakeFileArray($filePath);
                }
            }

            // Преобразуем картинки в массив с информацией о файле для сохранения
            if (is_array($value) && $value['id'] && $value['src']) {
                $valueType = 'file';
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $valueCorrected['src'];
                $valueCorrected = \CFile::MakeFileArray($filePath);
            }

            // Если тип поля EnumBool
            if ($type === 'EnumBool') {
                $enumDict = CacheManager::getIblockPropEnumDict(
                    [
                        'IBLOCK_CODE'   => (int)$propData['IBLOCK_ID'],
                        'FILTER'        => ['XML_ID' => 'Y'],
                        'PROPERTY_CODE' => $bxPropCode,
                        'SELECT'        => [
                            'ID:int>id',
                            'XML_ID>code',
                            'VALUE>name',
                        ],
                    ]
                );

                $valueCorrected = $enumDict && $enumDict[0] ? $enumDict[0]['id'] : null;
            }

            // Обработка EnumCode
            if ($type === 'EnumCode') {
                $enumDict = CacheManager::getIblockPropEnumDict(
                    [
                        'IBLOCK_CODE'   => (int)$propData['IBLOCK_ID'],
                        'FILTER'        => ['XML_ID' => $value],
                        'PROPERTY_CODE' => $bxPropCode,
                        'SELECT'        => ['ID:int>id'],
                    ]
                );

                $valueCorrected = $enumDict && $enumDict[0] ? $enumDict[0]['id'] : null;
            }

            if ($type === 'Table') {
                if (is_array($value)) {
                    $table = '<table>';
                    foreach ($value as $row) {
                        $table .= '<tr>';
                        foreach ($row as $col) {
                            $table .= '<td>' . $col . '</td>';
                        }
                        $table .= '</tr>';
                    }
                    $table .= '</table>';

                    $valueCorrected = $table;
                } else {
                    $valueCorrected = null;
                }
            }

            // Кастомная логика сохранения картинок блока
            if ($bxFieldCode === 'PREVIEW_PICTURE' || $bxFieldCode === 'DETAIL_PICTURE') {
                // Если картинка не задана или не задан src, удалим её
                // https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/update.php
                if (!$value || !$value['src']) {
                    $valueCorrected = ['del' => 'Y'];
                }
            }

            // Обработка DescriptiveString
            if (is_array($value) && $value[0] && array_key_exists('value', $value[0]) && array_key_exists('description', $value[0])) {
                $valueCorrected = [];

                foreach ($value as $valueItem) {
                    $valueCorrected[] = [
                        'VALUE'       => $valueItem['value'],
                        'DESCRIPTION' => $valueItem['description'],
                    ];
                }
            }

            // Разделяем значения на поля и свойства
            if ($bxFieldCode) {
                if ($field === 'active') {
                    $fields[$bxFieldCode] = $valueCorrected ? 'Y' : 'N';
                } else {
                    if ($field === 'previewText') {
                        $fields['PREVIEW_TEXT_TYPE'] = 'html';
                        $fields['PREVIEW_TEXT'] = $valueCorrected;
                    } else {
                        if ($field === 'detailText') {
                            $fields['DETAIL_TEXT_TYPE'] = 'html';
                            $fields['DETAIL_TEXT'] = $valueCorrected;
                        } else {
                            $fields[$bxFieldCode] = $valueCorrected;
                        }
                    }
                }
            } else {
                if ($bxPropCode && !$skipProp) {
                    if (is_array($valueCorrected) && empty($valueCorrected)) {
                        // Пустой массив множественного свойства не сохранится
                        // //dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/setpropertyvaluesex.php
                        $valueCorrected = false;
                    }

                    $isValueFile = $valueType === 'file' || $valueType === 'files';

                    if (is_array($valueCorrected) && !$valueCorrected[0]['DESCRIPTION'] && !$isValueFile) {
                        // Если свойство множественное и не используется DESCRIPTION,
                        // то добавим его, иначе порядок значений в битриксе
                        // может не измениться
                        foreach ($valueCorrected as $valueField => $valueCorrectedItem) {
                            $valueCorrected[$valueField] = [
                                'VALUE'       => $valueCorrectedItem,
                                'DESCRIPTION' => mt_rand(0, 9999999),
                            ];
                        }
                    }

                    $propName = $bxPropCode ?: strtoupper($field);
                    $props[$propName] = $valueCorrected;
                }
            }
        }

        return [
            'FIELDS' => $fields,
            'PROPS'  => $props,
        ];
    }


    /**
     * Функция принимает свойства элемента, выбирает аналогичный из базы
     * и сравнивает их
     *
     * @param array $element Массив со свойствами и параметрами элемента
     *
     * @return array
     */
    public static function getDiff($element): ?array
    {
        if (!$element['TYPE']) {
            return null;
        }
        if (!(int)$element['_DIFF_ID']) {
            return null;
        }

        $diff = [];

        // Определим перечень сравниваемых полей и свойств
        $fieldsExclude = ['TYPE', '_DIFF_INFO', '_DIFF_ID'];
        $fields = [];

        foreach ($element as $field => $value) {
            if (!in_array($field, $fieldsExclude, false)) {
                $fields[] = $field;
            }
        }

        $props = [];
        if (is_array($element['PROPERTY_VALUES'])) {
            foreach ($element['PROPERTY_VALUES'] as $code => $value) {
                $props[] = $code;
            }
        }
        // ---------------------------------------------------------------------


        // Выборка элемента из базы
        $select = $fields;
        foreach ($props as $code) {
            $select[] = 'PROPERTY_' . $code;
        }

        $elementListInBase = self::getList(
            [
                'TYPE'   => $element['TYPE'],
                'FILTER' => ['ID' => $element['_DIFF_ID']],
                'SELECT' => $select,
            ]
        );

        $elementInBase = end($elementListInBase);
        // ---------------------------------------------------------------------


        // Сравнение элементов
        $element1 = $element;
        foreach ($fieldsExclude as $field) {
            unset($element1[$field]);
        }

        if (is_array($element1['PROPERTY_VALUES'])) {
            foreach ($element1['PROPERTY_VALUES'] as $field => $value) {
                $element1['PROPERTY_' . $field . '_VALUE'] = $value;
            }
        }
        unset($element1['PROPERTY_VALUES']);


        $element2 = $elementInBase;
        if (is_array($element2)) {
            unset($element2['PROP']);
            foreach ($element2 as $field => $value) {
                if (preg_match('/PROPERTY_\w+_ENUM_ID/', $field)) {
                    unset($element2[$field]);
                }

                if (preg_match('/PROPERTY_\w+_VALUE/', $field)) {
                    unset($element2[$field]);
                }
            }

            $diff = array_merge(
                array_diff($element1, $element2),
                array_diff($element2, $element1)
            );
        }
        // ---------------------------------------------------------------------


        return $diff;
    }


    /**
     * Функция возвращает поле элемента инфоблока
     *
     * @param int    $id
     * @param string $code
     *
     * @return bool
     */
    public static function getField($id, $code): ?bool
    {
        if ((int)$id) {
            return null;
        }
        if (!$code) {
            return null;
        }


        // Выборка элементов из базы
        $query = [
            'FILTER' => ['ID' => $id],
            'SELECT' => [$code],
        ];

        if ($code === 'DETAIL_PAGE_URL') {
            $query['GET_NEXT'] = 'Y';
        }

        $elements = self::getList($query);

        if ($elements) {
            foreach ($elements as $element) {
                return $element[$code];
            }
        }

        return false;
    }


    /**
     * Функция возвращает ID элемента
     *
     * @param string $ib     Тип инфоблока из опций модуля
     * @param string $code   Код элемента
     * @param array  $params Массив доп.параметров <br>
     *                       <li> Если нужно создать ненайденный элемент, то передать "FORCE_CREATE" => "Y"
     *                       <li> Если нужно создать с заранее заданными параметрами, то передать их можно в ключе "NEW_ELEMENT"
     *
     * @return int ID раздела
     */
    public static function getIdByElementCode($ib, $code, array $params = [])
    {
        // Выборка элемента по символьному коду
        $elements = self::getList(
            [
                'TYPE'   => $ib,
                'FILTER' => ['CODE' => $code],
            ]
        );


        // Проверка результата
        if (count($elements) > 1) {
            return null;
        }

        if (count($elements) === 1) {
            $element = end($elements);
            return $element['ID'];
        }

        if ($params['FORCE_CREATE'] === 'Y') {
            $elementNewFields = [
                'TYPE' => $ib,
                'NAME' => $code,
                'CODE' => $code,
            ];

            if (is_array($params['NEW_ELEMENT'])) {
                $elementNewFields = $params['NEW_ELEMENT'];
            }

            $elementNewId = self::add($elementNewFields);

            if ((int)$elementNewId) {
                return $elementNewId;
            }
        }

        return null;
    }


    /**
     * Функция возвращает ID элемента
     *
     * @param string $code   Внешний код инфоблока
     * @param string $xml_id XML_ID раздела
     *
     * @return array
     */
    public static function getIdByXmlId($code, $xml_id): ?array
    {
        $result = null;

        if ($code && $xml_id) {
            $elementQuery = [
                'IBLOCK_CODE' => $code,
                'FILTER'      => ['XML_ID' => $xml_id],
            ];

            $elementList = self::getList($elementQuery);
            $element = array_shift($elementList);

            if ($element && $element['ID']) {
                $result = $element['ID'];
            }
        }

        return $result;
    }


    public static function getLinkToElementInAdmin(int $iblockId, int $id, int $sectionId = null): string
    {
        return CIBlock::GetAdminElementEditLink(
            $iblockId,
            $id,
            ['find_section_section' => $sectionId ?: -1, 'WF' => 'Y', 'menu' => null]
        );
    }


    /**
     * Функция возвращает параметр элемента инфоблока
     *
     * @param string|int|boolean $type     Кодовое обозначение инфоблока
     *                                     из опций модуля или ID инфоблока или false
     * @param int                $id       ID элемента
     * @param string             $propCode Символьный код свойства
     *
     * @return array
     */
    public static function getProp($type, $id, $propCode): ?array
    {
        CModule::IncludeModule('iblock');


        // Выборка из базы
        // Определение ID инфоблока
        if (is_numeric($type)) {
            $iblockId = $type;
        } else {
            $iblockId = Help::getOpt('IBLOCK_' . $type . '_ID');
        }

        $resProperty = CIBlockElement::GetProperty(
            $iblockId,
            $id,
            [],
            ['CODE' => $propCode]
        );
        // ---------------------------------------------------------------------


        // Формирование результата
        $result = null;
        $resultItems = [];
        $props = [];

        while ($property = $resProperty->Fetch()) {
            $resultItems[] = $property['VALUE'];
            $props[] = $property;
        }

        if ($resultItems[0] && $props[0]['MULTIPLE'] !== 'Y') {
            $result = $resultItems[0];
        } elseif ($resultItems && $resultItems[0] !== null) {
            $result = $resultItems;
        }

        TrimArr($result);

        // ---------------------------------------------------------------------


        return $result;
    }


    /**
     * Функция возвращает информацию о свойстве инфоблока
     *
     * @param string|int $type     - ID инфоблока или его символьный код
     * @param string     $propCode Код свойства
     *
     * @return array
     */
    public static function getPropData($type, $propCode): array
    {
        CModule::IncludeModule('iblock');


        // Определение ID инфоблока
        if (is_numeric($type)) {
            $iblockId = $type;
        } else {
            $iblockId = Help::getOpt('IBLOCK_' . $type . '_ID');
        }


        // Выборка из базы
        $resProps = CIBlockProperty::GetList(
            [],
            [
                'IBLOCK_ID' => $iblockId,
                'CODE'      => $propCode,
            ]
        );

        $props = [];
        while ($prop = $resProps->GetNext()) {
            $props[] = $prop;
        }

        return $props[0] ?? [];
    }


    /**
     * Функция возвращает ID значения свойства списка
     *
     * @param int|string $type     ID инфоблока или его символьный код
     * @param string     $propCode Код свойства
     * @param string     $xmlParam XML_ID значение, ID которого нужно получить
     *
     * @return int ID свойства
     */
    public static function getPropEnumID($type, $propCode, $xmlParam): int
    {
        CModule::IncludeModule("iblock");

        if (is_numeric($type)) {
            $iblockId = $type;
        } else {
            $iblockId = Help::getIblockIdByCode($type);
        }

        $resProp = CIBlockPropertyEnum::GetList(
            [],
            [
                'IBLOCK_ID' => $iblockId,
                'CODE'      => $propCode,
                'XML_ID'    => $xmlParam,
            ]
        );
        $prop = $resProp->Fetch();

        return (int)$prop['ID'];
    }


    /**
     * @param $params
     *
     * @return array - Массив данных о свойстве списке инфоблока
     */
    public static function getPropEnumDict($params): array
    {
        $iblockId = $params['IBLOCK_ID'] ?: Help::getIblockIdByCode($params['IBLOCK_CODE']);
        $result = [];

        $order = $params['ORDER'] ?: [];
        $filter = array_merge(
            [
                'IBLOCK_ID' => $iblockId,
                'CODE'      => $params['PROPERTY_CODE'],
            ],
            ($params['FILTER'] ?: [])
        );

        $resultRows = CIBlockPropertyEnum::GetList($order, $filter);

        /**
         * Массив элементов выборки с такими полями:
         * [ID] => 7
         * [PROPERTY_ID] => 27
         * [VALUE] => Название значения свойства
         * [DEF] => N
         * [SORT] => 10
         * [XML_ID] => block-house
         * [TMP_ID] =>
         * [EXTERNAL_ID] => block-house
         * [PROPERTY_NAME] => Название свойства
         * [PROPERTY_CODE] => FORM
         * [PROPERTY_SORT] => 200
         */
        $elements = [];
        while ($row = $resultRows->Fetch()) {
            $elements[] = $row;
        }

        // Приведем массив к нужным типам данных
        $typeConverter = new TypeConvert($params['SELECT'] ?: []);
        if ($typeConverter->getTypes()) {
            $result = $typeConverter->convertDataTypes($elements);
        }

        // Если нужен ассоциативный массив, то соберем его
        if ($params['ASSOC'] === 'Y') {
            $assocResult = [];

            foreach ($result as $item) {
                $assocResult[$item['id']] = $item;
            }

            return $assocResult;
        }

        return $result;
    }


    /**
     * Функция возвращает ID значения свойства инфоблока
     *
     * @param int|string $type  Символьный код инфоблока или его ID
     * @param int        $id    ID элемента
     * @param string     $code  Символьный код свойства
     * @param mixed      $value Значение свойства
     *
     * @return array|bool
     */
    public static function getPropIdByValue($type, $id, $code, $value)
    {
        CModule::IncludeModule('iblock');
        $result = false;


        // Определим ID инфоблока
        if (is_numeric($type)) {
            $iblockId = $type;
        } else {
            $iblockId = Help::getIblockIdByCode($type);
        }


        // Определим ID значения свойства
        $resProp = CIBlockElement::GetProperty(
            $iblockId,
            $id,
            [],
            ['CODE' => $code]
        );

        while ($prop = $resProp->Fetch()) {
            if ($prop['VALUE'] === $value) {
                $result = $prop['PROPERTY_VALUE_ID'];
                break;
            }
        }


        return $result;
    }


    /**
     * Функция возвращает названия свойств инфоблока по символьным кодам
     *
     * @param string $iblock — ID или символьный код инфоблока
     * @param array  $props  — Массив строк — свойств инфоблока, названия которых нужны
     *
     * @return array — Ассоциативный массив [%СИМВОЛЬНЫЙ_КОД% => %НАЗВАНИЕ_СВОЙСТВА%]
     */
    public static function getPropNames(string $iblock, array $props): array
    {
        CModule::IncludeModule('iblock');

        // Определим ID инфоблока
        if (is_numeric($iblock)) {
            $iblockId = $iblock;
        } else {
            $iblockId = Help::getIblockIdByCode($iblock);
        }


        // Выберем массив свойств инфоблока
        $propListRes = CIBlock::GetProperties($iblockId);
        $propList = [];
        while ($prop = $propListRes->GetNext()) {
            $propList[] = $prop;
        }


        // Соберем соответствия между свойствами и их названиями
        $result = [];
        foreach ($props as $propName) {
            foreach ($propList as $propData) {
                if ($propData['CODE'] === $propName) {
                    $result[$propName] = $propData['NAME'];
                }
            }
        }


        return $result;
    }


    /**
     * Функция устанавливает свойства элемента
     *
     * @param int|string $iblockType Тип инфоблока или ID
     * @param int        $id         ID элемента
     * @param array      $props      Массив пар "Название свойства"=>"Значение"
     */
    public static function setProp($iblockType, $id, $props): void
    {
        CModule::IncludeModule('iblock');


        // Определение ID инфоблока
        $iblockId = is_numeric($iblockType) ? $iblockType : Help::getIblockIdByCode($iblockType);


        CIBlockElement::SetPropertyValuesEx(
            $id,
            $iblockId,
            $props
        );
    }


    /**
     * Функция меняет сортировку значений свойств множественного свойства
     *
     * @param int|string $type      $iblockType Тип инфоблока или ID
     * @param int        $id        ID элемента
     * @param string     $propName  Символьный код свойства
     * @param array      $newSortId Новый порядок значений
     *
     * @return bool
     */
    public static function sortPropFiles($type, $id, $propName, $newSortId): bool
    {
        CModule::IncludeModule('iblock');
        $result = false;


        // Определение ID инфоблока
        if (is_numeric($type)) {
            $iblockId = $type;
        } else {
            $iblockId = Help::getOpt('IBLOCK_' . $type . '_ID');
        }


        // Выборка параметров свойств
        $resProp = CIBlockElement::GetProperty(
            $iblockId,
            $id,
            [],
            ['CODE' => $propName]
        );

        $propList = [];
        while ($prop = $resProp->Fetch()) {
            $propList[] = $prop;
        }


        // Формирование массива для схранения нового порядка
        $sortedProp = [];
        foreach ($newSortId as $sortValue) {
            foreach ($propList as $prop) {
                if ($prop['VALUE'] === $sortValue) {
                    $sortedProp[$prop['PROPERTY_VALUE_ID']]
                        = CIBlock::makeFilePropArray(['VALUE' => $sortValue]);
                }
            }
        }

        if (count($newSortId) === count($sortedProp)) {
            self::setProp($type, $id, [$propName => $sortedProp]);
            $result = true;
        }


        return $result;
    }


    /**
     * Метод возвращает массив с описанием мета-тегов title, keywords и description
     *
     * @param int   $iblockId - ID инфоблока
     * @param array $element  - Массив элемента с ключом ['id']
     *
     * @return array
     */
    public static function getSeo(int $iblockId, array $element): array
    {
        $props = new ElementValues($iblockId, $element['id']);
        $values = $props->getValues();

        return [
            'title'       =>
                $values['ELEMENT_META_TITLE']
                    ?: $element['name']
                    ?: $element['NAME'],
            'keywords'    =>
                $values['ELEMENT_META_KEYWORDS']
                    ?: $values['SECTION_META_KEYWORDS'],
            'description' =>
                $values['ELEMENT_META_DESCRIPTION']
                    ?: $values['SECTION_META_DESCRIPTION'],
        ];
    }


    /**
     * Функция обновляет значение
     *
     * @param int    $iblockId - ID инфоблока
     * @param int    $id       - ID элемента
     * @param string $code     - Символьный код свойства
     * @param string $value    - Новое значение
     */
    public static function updateProp($iblockId, $id, $code, $value): void
    {
        CModule::IncludeModule('iblock');

        CIBlockElement::SetPropertyValuesEx(
            $id,
            $iblockId,
            [$code => $value]
        );
    }


    /**
     * Функция увеличивает счетчик просмотров записи
     *
     * @param int $id — ID записи
     */
    public static function incShowCount(int $id): void
    {
        if (!$id) {
            return;
        }

        CModule::IncludeModule('iblock');
        CIBlockElement::CounterInc($id);
    }


    /**
     * Возвращает все привязки элементов к разделам
     *
     * @param int|null $iblockId
     * @param int      $cacheTime
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getBindingElementsSections(int $iblockId = null, int $cacheTime = 3600 * 24): array
    {
        $params = [
            'select' => [
                'IBLOCK_ELEMENT_ID',
                'IBLOCK_SECTION_ID',
                'IBLOCK_ID' => 'IBLOCK_SECTION.IBLOCK_ID',
            ],
        ];

        if ($iblockId !== null) {
            $params['filter']['IBLOCK_SECTION.IBLOCK_ID'] = $iblockId;
        }

        if ($cacheTime !== null) {
            $params['cache'] = ['ttl' => $cacheTime];
        }

        $items = SectionElementTable::getList($params)->fetchAll();

        return array_map(
            static function ($item) {
                return [
                    'elementId' => (int)$item['IBLOCK_ELEMENT_ID'],
                    'sectionId' => (int)$item['IBLOCK_SECTION_ID'],
                    'iblockId'  => (int)$item['IBLOCK_ID'],
                ];
            },
            $items
        );
    }

    /**
     * Возвращает список элементов с добавленным списком разделов
     *
     * @param array<int, array{id:int}> $elements
     * @param array<int, array{elementId:int,sectionId:int}> $bindingElementsSections
     * @param string $keyCode
     *
     * @return array
     */
    public static function modifyElementsAddedSections(
        array $elements,
        array $bindingElementsSections,
        string $keyCode = 'sectionId'
    ): array {
        return array_map(
            static function ($item) use ($bindingElementsSections, $keyCode) {
                foreach ($bindingElementsSections as $section) {
                    if ($item['id'] !== $section['elementId']) {
                        continue;
                    }

                    if (empty($item[$keyCode])) {
                        $item[$keyCode] = [];
                    }

                    $item[$keyCode][] = $section['sectionId'];
                }

                return $item;
            },
            $elements
        );
    }


    /**
     * Возвращает значения свойств
     *
     * @param int   $iblockId
     * @param int   $elementId
     * @param array $params
     *
     * @return array
     * @throws LoaderException
     */
    public static function getValuesPropertiesList(int $iblockId, int $elementId, array $params = []): array
    {
        Loader::includeModule('iblock');

        $result = [];

        $props = CIBlockElement::GetProperty(
            $iblockId,
            $elementId,
            $params['ORDER']['BY'] ?? 'SORT',
            $params['ORDER']['SORT'] ?? 'ASC',
            $params['FILTER'] ?? []
        );

        while ($prop = $props->Fetch()) {
            $result[] = $prop;
        }

        return $result;
    }


    /**
     * Возвращает уникальный символьный код
     *
     * @param int    $iblockId
     * @param string $code
     *
     * @return string
     */
    public static function getUniqueCode(int $iblockId, string $code): string
    {
        $counter = 1;

        while (true) {
            $codeResult = $counter === 1 ? $code : $code . '-' . $counter;

            $is = self::getList(
                [
                    'SELECT' => [
                        'ID',
                    ],
                    'FILTER' => [
                        'IBLOCK_ID' => $iblockId,
                        '=CODE'     => $codeResult,
                    ],
                    'NAV'    => [
                        'nTopCount' => 1,
                    ]
                ]
            );

            if (empty($is)) {
                return $codeResult;
            }

            $counter++;
        }
    }


    /**
     * Возвращает идентификатор инфоблока элемента
     *
     * @param int $id
     *
     * @return int|null
     * @throws LoaderException
     */
    public static function getIblockIdById(int $id): ?int
    {
        Loader::includeModule('iblock');

        $id = CIBlockElement::GetIBlockByID($id);

        return $id ?: null;
    }

}
