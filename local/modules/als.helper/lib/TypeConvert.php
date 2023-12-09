<?php

namespace ALS\Helper;


use Bitrix\Main\Type\DateTime;

class TypeConvert {
    /**
     * Регулярка для разбора строки в select <br>
     * 0 - исходная строка <br>
     * 1 - поле в битриксе <br>
     * 3 - тип данных <br>
     * 4 - если есть `?`, то пустые значения не попадают в выдачу
     * 5 - название поля на выходе <br>
     */
    private const typePattern = '/^([\w\.]+)(:|)([\w\[\]]+|)(\?>|\?$|>|)(\w+|)$/';

    /** @var array Массив строк - чистый SELECT */
    private $select = [];

    /** @var array Массив типов данных для select */
    private $types = [];


    public function __construct(array $select = []) {
        foreach ($select as $field) {
            /**
             * 0 - исходная строка
             * 1 - поле в битриксе
             * 3 - тип данных
             * 4 - обязательное поле или нет
             * 5 - название поля на выходе
             */
            $fieldMatches = [];

            if (preg_match(self::typePattern, $field, $fieldMatches)) {
                $bxFieldName = $fieldMatches[1];
                $this->select[] = $bxFieldName;

                if (false !== strpos($bxFieldName, 'PROPERTY_')) {
                    // Если это не поле элемента, а его свойство:

                    $isLinkToAnother = (bool)strpos($bxFieldName, '.');
                    $isLinkToProperty = (bool)strpos($bxFieldName, '.PROPERTY_');

                    if (!$isLinkToAnother || $isLinkToProperty) {
                        // И это не свойство привязки или привязка к другому свойству, то добавим _VALUE
                        $bxFieldName = $fieldMatches[1] . '_VALUE';
                    }
                }

                $isRequired = strpos($fieldMatches[4], '?') === false;

                $this->types[] = [$bxFieldName, $fieldMatches[3], $isRequired, $fieldMatches[5]];
            }
        }
    }


    public function getSelect(): array {
        return $this->select ?: [];
    }


    public function getTypes(): array {
        return $this->types ?: [];
    }


    /**
     * Метод конвертирует результаты getList-а по заданным в SELECT типам
     *
     * Возможные типы данных
     *  <li> string | string[]
     *  <li> int | int[]
     *  <li> float | float[]
     *  <li> bool
     *  <li> DateHuman
     *  <li> DateTs
     *  <li> Date
     *  <li> DateISO8601
     *  <li> DescriptiveFloat | DescriptiveFloat[]
     *  <li> DescriptiveInt | DescriptiveInt[]
     *  <li> DescriptiveString | DescriptiveString[]
     *  <li> EnumBool // depend from `'GET_ENUM_CODE' => 'Y'`
     *  <li> EnumCode | EnumCode[] // depend from `'GET_ENUM_CODE' => 'Y'`
     *  <li> EnumId // depend from `'GET_ENUM_CODE' => 'Y'`
     *  <li> EnumId[] // depend from `'GET_ENUM_CODE' => 'Y'`
     *  <li> File | File[]
     *  <li> Json
     *  <li> Image | Image[]
     *  <li> ImageSrc | ImageSrc[]
     *  <li> Html | Html[]
     *  <li> DescriptiveHtml[]
     *  <li> Map
     *  <li> Table | Table[]
     *  <li> Tags
     *  <li> Skip — пропустить преобразования
     *  <li> X - Удалить поле из финальной выдачи
     *
     * @param array $items
     * @return array
     */
    public function convertDataTypes(array $items = []): array {
        $result = [];

        foreach ($items as $k => $item) {
            foreach ($this->types as $type) {
                $value = null;

                if (isset($item[$type[0]])) {
                    $value = $item[$type[0]];
                } elseif (false !== strpos($type[0], '.')) {
                    // Если это свойство привязки
                    $itemField = str_replace('.', '_', $type[0]);
                    $value = $item[$itemField];
                }

                $fieldType = $type[1] ?: 'string';
                $fieldRequired = $type[2];
                $fieldName = $type[3] ?: $type[0];

                $descriptionPropKey = str_replace('_VALUE', '_DESCRIPTION', $type[0]);

                if ($fieldType === 'string') {
                    $value = (string) (trim($value) ?: '');

                } elseif ($fieldType === 'string[]') {
                    $newValue = [];

                    foreach ($value as $string) {
                        $newValue[] = (string) ($string ?: '');
                    }

                    $value = $newValue;

                } elseif ($fieldType === 'int') {
                    $value = is_string($value) || !empty($value) ? (int) $value : null;

                } elseif ($fieldType === 'int[]') {
                    $newValue = [];

                    foreach ($value as $number) {
                        $newValue[] = (int) $number;
                    }

                    $value = $newValue;

                } elseif ($fieldType === 'float') {
                    $value = (float)str_replace(',', '.', $value);

                } elseif ($fieldType === 'float[]') {
                    $newValue = [];

                    foreach ($value as $number) {
                        if (is_string($number)) {
                            $number = str_replace(',', '.', $number);
                        }

                        $newValue[] = (float)$number;
                    }

                    $value = $newValue;

                } elseif ($fieldType === 'bool') {
                    $value = $value === 'N' ? false : (bool)$value;

                } elseif ($fieldType === 'DateHuman') {
                    if ($value) {
                        $yearNow = date('Y');
                        $newYear = preg_replace('/(.+)(\d{4})$/', '$2', $value);
                        $dateFormat = ($yearNow === $newYear) ? 'DD MMMM' : 'DD MMMM YYYY';
                        $value = Help::formatDateHuman($value, $dateFormat);
                    }

                } elseif ($fieldType === 'DateTs') {
                    $value = MakeTimeStamp($value, FORMAT_DATETIME);
//                } elseif ($fieldType === 'Date') {
//                    $obDate = DateTime::createFromTimestamp(MakeTimeStamp($value, FORMAT_DATETIME));
//                    $value = $obDate->format("d.m.Y");
                } elseif ($fieldType === 'DateISO8601') {
                    if ($value && is_string($value)) {

                        $obDate = new DateTime($value);
                        $value = $obDate->format("Y-m-d");
                    }
                } elseif ($fieldType === 'DateISO8601[]') {
                    $newValue = [];

                    foreach ($value as $date) {
                        if ($date && is_string($date)) {
                            $obDate = new DateTime($date);
                            $newValue[] = $obDate->format("Y-m-d");
                        }
                    }
                    $value = $newValue;
                } elseif ($fieldType === 'EnumBool') {
                    $fieldNameEnum = str_replace('_VALUE', '_XML_ID', $type[0]);
                    $value = ($item[$fieldNameEnum] === 'Y');

                } elseif ($fieldType === 'EnumCode') {
                    $fieldNameEnum = str_replace('_VALUE', '_XML_ID', $type[0]);
                    $value = $item[$fieldNameEnum];

                } elseif ($fieldType === 'EnumCode[]') {
                    $fieldCode = str_replace(['PROPERTY_', '_VALUE'], '', $type[0]);

                    $propEnumValues = El::getPropEnumDict([
                        'IBLOCK_ID'     => (int)$item['IBLOCK_ID'],
                        'PROPERTY_CODE' => $fieldCode,
                        'ASSOC'         => 'Y',
                        'SELECT'        => [
                            'ID:int>id',
                            'XML_ID>xmlId',
                        ],
                    ]);

                    $newValue = [];
                    foreach (array_keys($value) as $id) {
                        $newValue[] = $propEnumValues[(int)$id]['xmlId'];
                    }

                    $value = $newValue;

                } elseif ($fieldType === 'EnumId') {
                    $fieldNameEnum = str_replace('_VALUE', '_ENUM_ID', $type[0]);
                    $value = (int)$item[$fieldNameEnum];

                } elseif ($fieldType === 'EnumId[]') {
                    $value = array_keys($value);

                } elseif ($fieldType === 'DescriptiveFloat') {
                    $value = [
                        'value'       => (float)$value,
                        'description' => $item[$descriptionPropKey],
                    ];

                } elseif ($fieldType === 'DescriptiveFloat[]') {
                    $dataFormatted = [];
                    foreach ($value as $valueKey => $valueData) {
                        $dataFormatted[] = [
                            'value'       => (float)$valueData,
                            'description' => $item[$descriptionPropKey][$valueKey],
                        ];
                    }
                    $value = $dataFormatted;

                } elseif ($fieldType === 'DescriptiveInt') {
                    $value = [
                        'value'       => (int)$value,
                        'description' => $item[$descriptionPropKey],
                    ];

                } elseif ($fieldType === 'DescriptiveInt[]') {
                    $dataFormatted = [];
                    foreach ($value as $valueKey => $valueData) {
                        $dataFormatted[] = [
                            'value'       => (int)$valueData,
                            'description' => $item[$descriptionPropKey][$valueKey],
                        ];
                    }
                    $value = $dataFormatted;

                } elseif ($fieldType === 'DescriptiveString') {

                    $value = [
                        'value'       => $value,
                        'description' => $item[$descriptionPropKey],
                    ];

                } elseif ($fieldType === 'DescriptiveString[]') {

                    //Dbg::show($value, true, true);
                    $dataFormatted = [];
                    foreach ($value as $valueKey => $valueData) {
                        $dataFormatted[] = [
                            'value'       => $valueData,
                            'description' => $item[$descriptionPropKey][$valueKey],
                        ];
                    }
                    $value = $dataFormatted;

                } elseif ($fieldType === 'File') {
                    $value = is_numeric($value)
                        ? File::getDataTiny((int)$value)
                        : null;

                } elseif ($fieldType === 'File[]') {
                    $valueNew = [];

                    foreach ($value as $valueKey => $fileId) {
                        $fileData = is_numeric($fileId)
                            ? File::getDataTiny((int)$fileId)
                            : [];
                        $fileData['description'] = $item[$descriptionPropKey][$valueKey];

                        $valueNew[] = $fileData;
                    }

                    $value = $valueNew;

                } elseif ($fieldType === 'Json') {
                    $value = json_decode(trim($value), true);

                } elseif ($fieldType === 'Image') {
                    $value = File::getImageDataById($value);

                } elseif ($fieldType === 'Image[]' && is_array($value)) {
                    $valueNew = [];

                    foreach ($value as $imageId) {
                        $valueNew[] = File::getImageDataById($imageId);
                    }

                    $value = $valueNew;

                } elseif ($fieldType === 'ImageSrc') {
                    $value = File::getImageDataById($value)['src'];

                } elseif ($fieldType === 'ImageSrc[]' && is_array($value)) {
                    $valueNew = [];

                    foreach ($value as $imageId) {
                        $valueNew[] = File::getImageDataById($imageId)['src'];
                    }

                    $value = $valueNew;

                } elseif ($fieldType === 'Html') {
                    $value = $value['TYPE'] === 'HTML' ? $value['TEXT'] : TxtToHTML($value['TEXT']);

                } elseif ($fieldType === 'Html[]') {
                    $dataFormatted = [];
                    foreach ($value as $valueKey => $valueData) {
                        $dataFormatted[] = $valueData['TYPE'] === 'HTML'
                            ? $valueData['TEXT']
                            : TxtToHTML($valueData['TEXT']);
                    }
                    $value = $dataFormatted;

                } elseif ($fieldType === 'DescriptiveHtml[]') {
                    $dataFormatted = [];
                    foreach ($value as $valueKey => $valueData) {
                        $dataFormatted[] = [
                            'value'       => $valueData['TYPE'] === 'HTML'
                                ? $valueData['TEXT']
                                : TxtToHTML($valueData['TEXT']),
                            'description' => $item[$descriptionPropKey][$valueKey],
                        ];
                    }
                    $value = $dataFormatted;

                } elseif ($fieldType === 'Map') {
                    $coordinates = explode(',', $value);
                    $value = $value ? [(float)$coordinates[0], (float)$coordinates[1]] : null;

                } elseif ($fieldType === 'Table' && $value['TYPE'] === 'HTML' && $value['TEXT']) {
                    $value = Html::getDataFromTable($value['TEXT']);

                } elseif ($fieldType === 'Table[]' && count($value)) {
                    $valueFormatted = [];
                    foreach ($value as $bitrixText) {
                        $valueFormatted[] = Html::getDataFromTable($bitrixText['TEXT']);
                    }

                    $value = $valueFormatted;

                } elseif ($fieldType === 'Tags') {
                    $tagsInArray = explode(',', $value);

                    $value = [];

                    if ($value) {
                        foreach ($tagsInArray as $tag) {
                            $value[] = trim($tag);
                        }
                    }

                } elseif ($fieldType === 'DescriptiveTable[]') {
                    $dataFormatted = [];
                    foreach ($value as $valueKey => $valueData) {
                        $dataFormatted[] = [
                            'value'       => Html::getDataFromTable($valueData['TEXT']),
                            'description' => $item[$descriptionPropKey][$valueKey],
                        ];
                    }

                    $value = $dataFormatted;

                }

                if ($fieldType !== 'X') {
                    if ($fieldRequired || (!$fieldRequired && !empty($value))) {
                        $result[$k][$fieldName] = $value;
                    }
                }
            }
        }


        return $result;
    }

}
