<?php

namespace ALS\Helper;

use Bitrix\Iblock\InheritedProperty\SectionValues;
use CIBlockSection;
use CModule;


class Sect
{

    // =========================================================================
    // === CRUD ================================================================
    // =========================================================================

    /**
     * Функция для добавления раздела инфоблока
     *
     * @param array $params Поля и свойства нового раздела
     *
     * @return int|string ID нового раздела или код ошибки в строке
     */
    public static function add($params)
    {
        CModule::IncludeModule('iblock');

        $fields = $params;

        if ($fields['IBLOCK_CODE']) {
            $fields['IBLOCK_ID'] = Help::getIblockIdByCode($fields['IBLOCK_CODE']);
            unset($fields['IBLOCK_CODE']);
        }

        $sect = new CIBlockSection;
        if ($sectionId = $sect->Add($fields)) {
            return $sectionId;
        }

        return 'Error: ' . $sect->LAST_ERROR;
    }


    /**
     * Функция возвращает массив с данными о разделах инфоблока
     *
     * @param array $params Параметры выборки
     *
     * @return array
     */
    public static function getList($params)
    {
        // Подключение модуля для работы с инфоблоками
        CModule::IncludeModule('iblock');
        $typeConverter = new TypeConvert($params['SELECT']);


        // Определение ID инфоблока
        if ($params['IBLOCK_ID']) {
            $iblockID = (int)$params['IBLOCK_ID'];
        } elseif (is_numeric($params['TYPE'])) {
            $iblockID = (int)$params['TYPE'];
        } elseif (!empty($params['IBLOCK_CODE'])) {
            $iblockID = Help::getIblockIdByCode($params['IBLOCK_CODE'], true);
        } else {
            $iblockID = Help::getOpt('IBLOCK_' . $params['TYPE'] . '_ID');
        }


        // Определение направления сортировки
        $order = $params['ORDER'] ?: ['SORT' => 'ASC'];


        // Определение фильтра
        $filter = $params['FILTER'] ?: [];

        if ($iblockID) {
            $filter['IBLOCK_ID'] = $iblockID;
        }


        // Определение постраничной навигации
        $nav = $params['NAV'] ?: false;


        // Определение полей выборки
        $select = array_merge($typeConverter->getSelect(), ['ID', 'IBLOCK_ID']);

        // Выборка результата из базы
        $rsSection = CIBlockSection::GetList(
            $order,
            $filter,
            $params['bIncCnt'],
            $select,
            $nav
        );

        $result = [];
        if ($params['GET_NEXT'] === 'Y') {
            while ($section = $rsSection->GetNext()) {
                $result[$section['ID']] = $section;
            }
        } else {
            while ($section = $rsSection->Fetch()) {
                $result[$section['ID']] = $section;
            }
        }

        if ($params['GET_ENUM_CODE'] === 'Y') {
            $ufListParams = [
                'select' => ['ID', 'FIELD_NAME'],
                'filter' => [
                    'ENTITY_ID'    => self::getEntityId($iblockID),
                    'FIELD_NAME'   => $select,
                    'USER_TYPE_ID' => 'enumeration',
                ],
            ];

            $ufList = [];
            foreach (UserField::getList($ufListParams) as $uf) {
                $ufList[] = $uf['FIELD_NAME'];
            }

            $ufParams = [
                'IBLOCK_ID' => $iblockID,
            ];

            $enumCodes = [];
            foreach (UserField::getEnumList($ufParams) as $uf) {
                $enumCodes[$uf['ID']] = $uf['XML_ID'];
            }

            foreach ($result as $j => $item) {
                foreach ($ufList as $enum) {
                    $result[$j][$enum] = $enumCodes[$item[$enum]];
                }
            }
        }

        // Приведем массив к нужным типам данных
        $result = $typeConverter->convertDataTypes($result);

        return $params['ASSOC'] === 'Y'
            ? $result
            : array_values($result);
    }


    /**
     * Метод обновляет параметры раздела
     *
     * @param int $id ID раздела
     * @param array $params Поля раздела
     *
     * @return bool
     */
    public static function update($id, $params)
    {
        $result = null;

        if (is_numeric($id) && is_array($params)) {
            CModule::IncludeModule('iblock');

            $section = new CIBlockSection;
            $resultUpdate = $section->Update($id, $params);

            if ($resultUpdate === true) {
                $result = $resultUpdate;
            } else {
                $result = $section->LAST_ERROR;
            }
        } else {
            $result = 'Error in ID or fields';
        }

        return $result;
    }


    // =========================================================================
    // === ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ ==============================================
    // =========================================================================

    /**
     * Функция деактивирует элемент с указанным кодом
     *
     * @param int $id ID элемента
     *
     * @return bool
     */
    public static function deactivate($id)
    {
        return self::update(
            $id,
            ['ACTIVE' => 'N']
        );
    }


    /**
     * Возвращает идентификатор сущности
     *
     * @param string $code
     *
     * @return string|null
     * @throws LoaderException
     */
    public static function getEntityId($code): ?string
    {
        $iblockId = is_numeric($code) ? (int)$code : Help::getIblockIdByCode($code);
        return $iblockId ? 'IBLOCK_' . $iblockId . '_SECTION' : null;
    }


    /**
     * Функция возвращает значение поля раздела инфоблока
     *
     * @param int $id ID раздела
     * @param string $field Символьный код поля
     *
     * @return string
     */
    public static function getField($id, $field)
    {
        if (!is_numeric($id)) {
            return null;
        }

        $sectionList = self::getList(
            [
                'FILTER' => ['ID' => $id],
                'SELECT' => [$field],
            ]
        );

        $section = end($sectionList);

        return $section[$field];
    }


    /**
     * Функция возвращает ID раздела
     *
     * @param string $ib Тип инфоблока из опций модуля
     * @param string $code Код раздела
     * @param array $params Массив доп.параметров <br>
     *                       <li> Если нужно создать ненайденный раздел, то передать 'FORCE_CREATE' => 'Y'
     *
     * @return int ID раздела
     */
    public static function getIdBySectionCode($ib, $code, array $params = [])
    {
        $sectionId = null;

        $section = self::getList(
            [
                'TYPE'   => $ib,
                'FILTER' => ['CODE' => $code],
            ]
        );

        if (count($section) === 0) {
            if ($params['FORCE_CREATE'] === 'Y') {
                $name = $params['NAME'] ?: $code;

                $sectionId = self::add([
                                           'TYPE' => $ib,
                                           'CODE' => $code,
                                           'NAME' => $name,
                                       ]);
            }
        } elseif (count($section) === 1) {
            $sectionId = end($section)['ID'];
        }

        return $sectionId;
    }


    /**
     * Функция возвращает ID раздела по символьному коду
     *
     * @param string $iblockCode Символьный код инфоблока
     * @param string $sectionCode Код раздела
     *
     * @return ?int
     */
    public static function getIdByCode(int $iblockId, string $sectionCode): ?int
    {
        $result = null;

        if (!empty($iblockId) && !empty($sectionCode)) {
            $sectionQuery = [
                'IBLOCK_ID' => $iblockId,
                'FILTER'    => [
                    'CODE'   => $sectionCode,
                    'ACTIVE' => 'Y'
                ],
                'SELECT'    => [
                    'ID:int>id',
                ]
            ];

            $sectionList = self::getList($sectionQuery);

            $section = array_shift($sectionList);

            if ($section && $section['id']) {
                $result = (int)$section['id'];
            }
        }

        return $result;
    }


    /**
     * Функция возвращает ID раздела
     *
     * @param string $code Символьный код инфоблока
     * @param string $xml_id XML_ID раздела
     *
     * @return int
     */
    public static function getIdByXmlId($code, $xml_id)
    {
        $result = null;

        if ($code && $xml_id) {
            $sectionQuery = [
                'IBLOCK_CODE' => $code,
                'FILTER'      => ['XML_ID' => $xml_id],
            ];

            $sectionList = self::getList($sectionQuery);
            $section = array_shift($sectionList);

            if ($section && $section['ID']) {
                $result = (int)$section['ID'];
            }
        }

        return $result;
    }


    /**
     * Функция возвращает свойство раздела
     *
     * @param string $iblockCode Код инфоблока к которому относится раздел
     * @param int $id ID раздела
     * @param string $code Символьный код свойства
     *
     * @return mixed Значение свойства или false
     */
    public static function getProp($iblockCode, $id, $code)
    {
        $sections = self::getList([
                                      'IBLOCK_CODE' => $iblockCode,
                                      'FILTER'      => ['ID' => $id],
                                      'SELECT'      => [$code],
                                  ]);

        return (count($sections) === 1) ? end($sections)[$code] : null;
    }


    /**
     * Метод возвращает массив с описанием мета-тегов title, keywords и description
     *
     * @param int $iblockId - ID инфоблока
     * @param array $section - Массив элемента с ключем ['id']
     *
     * @return array
     */
    public static function getSeo(int $iblockId, array $section): array
    {
        $props = new SectionValues($iblockId, $section['id']);
        $values = $props->getValues();

        return [
            'title'       => $values['SECTION_META_TITLE'] ?: $values['SECTION_PAGE_TITLE'] ?: $section['name'] ?: $section['NAME'],
            'caption'     => $values['SECTION_PAGE_TITLE'] ?: $values['SECTION_META_TITLE'],
            'keywords'    => $values['SECTION_META_KEYWORDS'] ?: $values['SECTION_PAGE_KEYWORDS'],
            'description' => $values['SECTION_META_DESCRIPTION'] ?: $values['SECTION_PAGE_DESCRIPTION'],
        ];
    }

    /**
     * Возвращает навигационную цепочку для раздела
     *
     * @param            $iblockId
     * @param            $sectionId
     * @param array|null $select
     *
     * @return array
     */
    public static function getNavChain($iblockId, $sectionId, ?array $select = []): array
    {
        CModule::IncludeModule('iblock');

        if (empty($select)) {
            $select = [
                'ID:int>id',
                'NAME:string>name',
                'CODE:string>code',
                'IBLOCK_SECTION_ID:int>sectionId',
                'DEPTH_LEVEL:int>depthLevel'
            ];
        }

        $typeConverter = new TypeConvert($select);

        $iblockId = (int)$iblockId;
        $sectionId = (int)$sectionId;

        if (empty($iblockId) || empty($sectionId)) {
            return [];
        }

        // Определение полей выборки
        $select = $typeConverter->getSelect();


        $result = CIBlockSection::GetNavChain($iblockId, $sectionId, $select, true);
        return $typeConverter->convertDataTypes($result);
    }


    /**
     * Функция возвращает список разделов, отсортированный в порядке "полного развернутого дерева"
     *
     * @param array $params Массив с параметрами выборки. Используемые ключи <br>
     *                      <li> IBLOCK_CODE
     *                      <li> IBLOCK_ID
     *                      <li> FILTER
     *                      <li> SELECT
     *                      <li> GET_NEXT
     *
     * @return array Данные по разделам
     */
    public static function getTreeList($params)
    {
        CModule::IncludeModule('iblock');
        $typeConverter = new TypeConvert($params['SELECT'] ?: []);

        $iblockId = $params['IBLOCK_ID'] ?: Help::getIblockIdByCode($params['IBLOCK_CODE']);

        // Определение фильтра
        $filter = $params['FILTER'] ?: [];

        if ($iblockId) {
            $filter['IBLOCK_ID'] = $iblockId;
        }


        // Определение полей выборки
        $select = $typeConverter->getSelect();


        // Выборка результата из базы
        $rsSection = CIBlockSection::GetTreeList($filter, $select);

        $result = [];
        if ($params['GET_NEXT'] === 'Y') {
            while ($section = $rsSection->GetNext()) {
                if ($section['ID']) {
                    $key = $section['ID'];
                    $result[$key] = $section;
                } else {
                    $result[] = $section;
                }
            }
        } else {
            while ($section = $rsSection->Fetch()) {
                if ($section['ID']) {
                    $key = $section['ID'];
                    $result[$key] = $section;
                } else {
                    $result[] = $section;
                }
            }
        }

        if ($params['GET_ENUM_CODE'] === 'Y') {
            $ufListParams = [
                'select' => ['ID', 'FIELD_NAME'],
                'filter' => [
                    'ENTITY_ID'    => self::getEntityId($iblockId),
                    'FIELD_NAME'   => $select,
                    'USER_TYPE_ID' => 'enumeration',
                ],
            ];

            $ufList = [];
            foreach (UserField::getList($ufListParams) as $uf) {
                $ufList[] = $uf['FIELD_NAME'];
            }

            $ufParams = [
                'IBLOCK_ID' => $iblockId,
            ];

            $enumCodes = [];
            foreach (UserField::getEnumList($ufParams) as $uf) {
                $enumCodes[$uf['ID']] = $uf['XML_ID'];
            }

            foreach ($result as $j => $item) {
                foreach ($ufList as $enum) {
                    $result[$j][$enum] = $enumCodes[$item[$enum]];
                }
            }
        }

        if ($select) {
            $result = $typeConverter->convertDataTypes($result);
        }


        if ($params['AS_ARRAY'] === 'Y') {
            $result = array_values($result);
        }
        return $result;
    }


    public static function getSectionLang($iblock, string $lang = 'ru', $skipCache = false): array
    {
        $params = [
            'FILTER'       => [
                'ACTIVE' => 'Y',
                'CODE'   => $lang,
            ],
            'SELECT'       => [
                'ID:int>id',
                'CODE:string>code',
                'DEPTH_LEVEL:int>dl',
                'LEFT_MARGIN:int>lm',
                'RIGHT_MARGIN:int>rm',
            ],
            'AS_ARRAY'     => 'Y',
            '__SKIP_CACHE' => $skipCache,
        ];

        if (is_numeric($iblock)) {
            $params['IBLOCK_ID'] = (int)$iblock;
        } else {
            $params['IBLOCK_CODE'] = $iblock;
        }

        $sections = CacheManager::getIblockSectionsFromCache($params);

        if (is_array($sections)) {
            return $sections[0] ?: [];
        }

        return [];
    }

}
