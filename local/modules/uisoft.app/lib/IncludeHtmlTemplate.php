<?php

namespace Uisoft\App;

use ALS\Helper\Help;

class IncludeHtmlTemplate
{
    private const DEFAULT_CODE = 'defaultSection';

    /**
     * Покажет верхнюю часть дефолтного шаблона или шаблона из константы, если она определена
     */
    public static function topPageSection(): void
    {
        $hidePageSection = false;
        if (defined('HIDE_PAGE_SECTION_TEMPLATE') && HIDE_PAGE_SECTION_TEMPLATE === true) {
            $hidePageSection = true;
        }

        if ($hidePageSection !== true) {
            if (defined('PAGE_SECTION_TEMPLATE')) {
                self::top(PAGE_SECTION_TEMPLATE);
            } else {
                self::top();
            }
        }
    }

    /**
     * Покажет нижнюю часть дефолтного шаблона или шаблона из константы, если она определена
     */
    public static function bottomPageSection(): void
    {
        $hidePageSection = false;
        if (defined('HIDE_PAGE_SECTION_TEMPLATE') && HIDE_PAGE_SECTION_TEMPLATE === true) {
            $hidePageSection = true;
        }

        if ($hidePageSection !== true) {
            if (defined('PAGE_SECTION_TEMPLATE')) {
                self::bottom(PAGE_SECTION_TEMPLATE);
            } else {
                self::bottom();
            }
        }
    }

    /**
     * @param string $code
     * @param false $toString
     * @return string|null
     */
    public static function top(string $code = '', bool $toString = false): ?string
    {
        if ($code === '') {
            $code = self::DEFAULT_CODE;
        }

        $arTemplate = self::getContent($code);

        if ($toString === false && !empty($arTemplate[0])) {
            echo $arTemplate[0];
            return null;
        }

        return $arTemplate[0] ?? null;
    }

    /**
     * @param string $code
     * @param false $toString
     * @return string|null
     */
    public static function bottom(string $code = '', bool $toString = false): ?string
    {
        if ($code === '') {
            $code = self::DEFAULT_CODE;
        }

        $arTemplate = self::getContent($code);

        if ($toString === false && !empty($arTemplate[1])) {
            echo $arTemplate[1];
            return null;
        }

        return $arTemplate[1] ?? null;
    }

    /**
     * @param string $text
     * @param string $code
     * @param bool $toString
     * @return string|null
     */
    public static function insert(string $text, string $code = '', bool $toString = false): ?string
    {
        if ($code === '') {
            $code = self::DEFAULT_CODE;
        }

        $arTemplate = self::getContent($code);

        if ($toString === false && !empty($arTemplate[1])) {
            echo $arTemplate[0] . $text . $arTemplate[1];
            return null;
        }

        return ($arTemplate[0] ?? '') . $text . ($arTemplate[1] ?? '');
    }

    /**
     * @param string $code
     * @return array
     */
    private static function getContent(string $code): array
    {
        $result = [];
        $code = ToLower(Help::convertFromCamelCaseToUpperCase($code));
        $templateFile = $_SERVER['DOCUMENT_ROOT'] . '/include/templates/' . $code . '.inc.html';
        if (is_file($templateFile)) {
            $templateString = file_get_contents($templateFile);
            $result = explode('#WORK_AREA#', $templateString);
        }
        return $result;
    }
}
