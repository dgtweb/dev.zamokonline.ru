<?php

namespace ALS\Helper;

use ALSTypograf;
use CIBlock;
use CModule;
use COption;
use CPHPCache;

class Help
{

    /**
     * Функция конвертирует
     * @param string $str
     * @return string
     */
    public static function convertStringToLink(string $str = ''): string
    {
        $patternProtocol = '/[a-z]{2,6}:\/\//';

        if (preg_match($patternProtocol, $str)) {
            $result = $str;
        } else {
            $result = 'https://' . $str . '/';
        }

        return $result;
    }


    /**
     * Функция возвращает нужную словоформу числительного по количеству
     * @param int $n Количество
     * @param array $vars Массив словоформ (1, 2, 5)
     * @return string Результат
     */
    public static function getEnding($n, $vars)
    {
        if (!(int)$n) {
            return false;
        }

        $n = (int)$n;

        $plural = $n % 10 === 1 && $n % 100 !== 11
            ? $vars[0]
            : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)
                ? $vars[1]
                : $vars[2]);

        return $plural;
    }


    /**
     * Метод возвращает символьный код инфоблока по его ID
     * @param $iblockId
     * @param bool $useCache
     * @return string|null
     */
    public static function getIblockCode($iblockId, bool $useCache = false): ?string
    {
        CModule::IncludeModule('iblock');
        $result = null;

        $obCache = $useCache ? new CPHPCache : null;

        if ($obCache && $obCache->InitCache(3600, $iblockId, '/als.helper/help/getIblockCode/')) {
            $vars = $obCache->GetVars();
            $result = $vars['RESULT'];
        } else {
            $res = CIBlock::GetList([], ['ID' => $iblockId]);

            while ($item = $res->Fetch()) {
                if ($item['CODE']) {
                    $result = $item['CODE'];
                }
            }

            if ($obCache && $obCache->StartDataCache()) {
                $obCache->EndDataCache([
                                           'RESULT' => $result,
                                       ]);
            }
        }

        return $result;
    }


    /**
     * Функция возвращает ID инфоблока по его коду
     * @param string $code - Символьный код инфоблока
     * @param bool $useCache - Использовать кеш или нет
     * @return int - ID инфоблока
     */
    public static function getIblockIdByCode(string $code, bool $useCache = false): ?int
    {
        CModule::IncludeModule('iblock');
        $result = null;

        $obCache = $useCache ? new CPHPCache : null;

        if ($obCache && $obCache->InitCache(3600, $code, '/als.helper/help/getIblockIdByCode/')) {
            $vars = $obCache->GetVars();
            $result = $vars['RESULT'];
        } else {
            $res = CIBlock::GetList([], ['CODE' => $code]);

            while ($item = $res->Fetch()) {
                if ($item['ID']) {
                    $result = (int)$item['ID'];
                }
            }
        }

        if ($obCache && $obCache->StartDataCache()) {
            $obCache->EndDataCache(
                [
                    'RESULT' => $result,
                ]
            );
        }

        return $result;
    }


    /**
     * Функция возвращает массив всех инфоблоков на сайте
     * @param array $order
     * @param array $filter
     * @param bool $includeCount
     * @return array
     */
    public static function getIblockList(array $order = [], array $filter = [], bool $includeCount = false): array
    {
        CModule::IncludeModule('iblock');

        $res = CIBlock::GetList($order, $filter, $includeCount);
        $items = [];

        while ($item = $res->Fetch()) {
            $items[] = $item;
        }

        return $items;
    }


    /**
     * Функция возвращает параметр модуля
     * @param string $option Код параметра
     * @return string
     */
    public static function getOpt($option)
    {
        return COption::GetOptionString('als.helper', $option);
    }


    /**
     * Функция переводит дату из формата сайта в человеческий вид
     * @param string $date Дата в формате текущего сайта
     * @param string $format Формат в который необходимо её пробразовать
     * @return string Дата в отформатированном виде
     */
    public static function formatDateHuman(string $date, string $format): string
    {
        $result = FormatDateFromDB($date, $format);
        $result = preg_replace('/^0/', '', $result);
        $result = str_replace(' ', '&nbsp;', $result);

        if (LANGUAGE_ID === 'ru' && false !== strpos($format, 'MMMM')) {
            $result = mb_strtolower($result, 'UTF-8');
        }

        return $result;
    }


    /**
     * Функция превращает дату в формате сайта в человеческий вид.
     * Возвращает время, если дата соответствует сегодняшней, возвращает только время, дату, месяц, если дата текущего года
     *
     * @param string $date Дата в формате сайта
     * @return string
     */
    public static function formatDateHumanSmart(string $date): string
    {
        // Исходные параметры: парсинг даты и таймштампы
        $dateParsed = ParseDateTime($date);
        $timeStamp = MakeTimeStamp($date);

        $dateDayNum = floor($timeStamp / 86400);
        $nowDayNum = floor(time() / 86400);


        // Определение формата даты
        $format = 'DD MMMM YYYY в HH:MI';

        if ($dateDayNum === $nowDayNum) {
            $format = 'Сегодня в HH:MI';
        } elseif ($dateDayNum === $nowDayNum - 1) {
            $format = 'Вчера в HH:MI';
        } elseif ($dateParsed['YYYY'] === date('Y')) {
            $format = 'DD MMMM в HH:MI';
        }

        // Формирование результата
        return self::formatDateHuman($date, $format);
    }


    /**
     * Функция возвращает число в формате суммы
     * @param int $number Число для вывода суммы
     * @param int $decimal Число знаков после запятой, например, не более двух
     * @param string $groupSeparator Разделитель разрядов
     * @param string $fractionSeparator Разделитель дробных значений
     * @return string
     */
    public static function formatPrice($number, $decimal = 2, $groupSeparator = '&#8201;', $fractionSeparator = ',')
    {
        if (LANGUAGE_ID === 'en') {
            $fractionSeparator = '.';
            $groupSeparator = ',';
        }

        $roundNumber = round($number, $decimal);
        $numberParts = explode('.', $roundNumber);
        $decimalDefine = $numberParts[1] ? strlen($numberParts[1]) : 0;
        $separatorTmp = ($number >= 10000) ? '#' : false;

        $num = number_format($number, $decimalDefine, $fractionSeparator, $separatorTmp);
        $num = str_replace('#', $groupSeparator, $num);

        if (!$num) {
            $num = 0;
        }

        return $num;
    }


    /**
     * Вставляет ссылку mailto или tel для почты или телефона
     * @param $text
     * @param string $glue
     * @return mixed
     */
    public static function insertContactLink($text, $glue = ', ')
    {
        if (filter_var($text, FILTER_VALIDATE_EMAIL)) {
            return '<a href="mailto:' . $text . '">' . $text . '</a>';
        }

        if (preg_match('/^[0-9()+,\-\s]*$/', $text)) {
            $result = [];

            foreach (explode(',', $text) as $tel) {
                $tel = trim($tel);

                if ($tel) {
                    $result[] = '<a href="tel:' . preg_replace('/[^0-9+]/', '', $tel) . '">' . $tel . '</a>';
                }
            }

            return implode($glue, $result);
        }

        return $text;
    }

    /**
     * Функция минифицирует html-код
     * @param string $content html-код
     * @return string
     */
    public static function minifyHtml(string $content): string
    {
        // http://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
        $search = [
            '/\>[^\S ]+/s',  // strip whitespaces after tabs, except space
            '/[^\S ]+\</s',  // strip whitespaces before tabs, except space
            // '/(\s)+/s'       // shorten multiple whitespace sequences
        ];

        $replace = [
            '>',
            '<',
            // '\\1'
        ];

        return preg_replace($search, $replace, $content);
    }

    /**
     * Конвертирует строку из camelCase в UPPER_CASE с подчеркивание в качестве разделителя слов
     *
     * @param string $string Исходная строка в camelCase
     *
     * @return string
     */
    public static function convertFromCamelCaseToUpperCase(string $string): string
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * Конвертирует строку из UPPER_CASE в camelCase
     *
     * @param string $string Исходная строка в UPPER_CASE
     *
     * @return string
     */
    public static function convertFromUpperCaseToCamelCase(string $string): string
    {
        $string = str_replace("_", " ", $string);
        $string = strtolower($string);

        $firstSpace = strpos($string, " ");

        if ($firstSpace) {
            $firstWord = substr($string, 0, $firstSpace);
            $secondWords = substr($string, $firstSpace);
            $string = $firstWord . ucwords($secondWords);
        }

        return str_replace(" ", "", $string);
    }

}
