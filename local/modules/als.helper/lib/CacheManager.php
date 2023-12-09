<?php

namespace ALS\Helper;

use Bitrix\Main\Data\Cache;
use CPHPCache;

class CacheManager
{
    private const DIR = '/als.project';


    /**
     * Метод возвращает список торговых каталогов с использованием CacheManager проекта
     *
     * @param $params
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getCatalogsFromCache($params): array
    {
        $obCache = new CPHPCache;
        $catalogCacheDir = $params['TYPE'] ?: 'all';
        $cachePath = '/catalogs_v1/' . $catalogCacheDir . '/';

        if ($obCache->InitCache(3600, $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $items = Catalog::getList($params);

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }
        return $items;
    }


    /**
     * Метод возвращает список инфоблоков с использованием CacheManager проекта
     *
     * @param $params - Параметры выборки инфоблоков
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIblocksFromCache($params): array
    {
        $obCache = new CPHPCache;
        $cachePath = '/iblocks/';

        if ($obCache->InitCache(3600, serialize($params), $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $items = Iblock::getList($params);
            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }

        return $items;
    }


    /**
     * Метод возвращает список элементов инфоблока с использованием CacheManager проекта
     *
     * @param array    $params - Параметры выборки элементов
     *                         $params[__SKIP_CACHE] - Прямая выборка без кеша
     * @param callable $callback
     *
     * @return array
     */
    public static function getIblockItemsFromCache($params, $callback = null): array
    {
        if ($params['__SKIP_CACHE']) {
            $items = El::getList($params);

            if (is_callable($callback)) {
                $callback($items);
            }

            return $items;
        }

        $obCache = new CPHPCache;
        $iblockCacheDir = $params['IBLOCK_CODE'] ?: (string)$params['IBLOCK_ID'];
        $cachePath = '/' . $iblockCacheDir . '/items/';

        if ($obCache->InitCache(3600, serialize($params), $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $items = El::getList($params);

            if (is_callable($callback)) {
                $callback($items);
            }

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }

        return $items;
    }


    /**
     * Метод возвращает список разделов инфоблока с использованием CacheManager проекта
     *
     * @param               $params - Параметры выборки разделов
     * @param callable|null $callback
     *
     * @return array
     */
    public static function getIblockSectionsFromCache($params, callable $callback = null): array
    {
        if ($params['__SKIP_CACHE']) {
            $items = Sect::getList($params);

            if (is_callable($callback)) {
                $callback($items);
            }
            return $items;
        }

        $obCache = new CPHPCache;

        $iblockCacheDir = $params['IBLOCK_CODE'] ?: (string)$params['IBLOCK_ID'];
        $cachePath = '/' . $iblockCacheDir . '/sections/';

        if ($obCache->InitCache(3600, serialize($params), $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $items = Sect::getList($params);

            if (is_callable($callback)) {
                $callback($items);
            }

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }

        return $items;
    }


    /**
     * Метод возвращает значения свойства список инфоблока. Кеш-обертка над El::getPropEnumDict()
     *
     * @param array    $params - Параметры выборки записей
     *                         $params[__SKIP_CACHE] - Прямая выборка без кеша
     * @param callable $callback
     *
     * @return array
     */
    public static function getIblockPropEnumDict($params, $callback = null): array
    {
        if ($params['__SKIP_CACHE']) {
            $items = El::getPropEnumDict($params);

            if (is_callable($callback)) {
                $callback($items);
            }

            return $items;
        }

        $obCache = new CPHPCache;
        $cachePath = '/' . $params['IBLOCK_CODE'] . '/iblock-prop-enum-dict/';

        if ($obCache->InitCache(3600, serialize($params), $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $items = El::getPropEnumDict($params);

            if (is_callable($callback)) {
                $callback($items);
            }

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }

        return $items;
    }


    /**
     * Метод очищает кеш по ключу
     *
     * @param int|string $key
     */
    public static function clear($key = ''): void
    {
        $dirs = [
            '/' . $key . '/items/',
            '/' . $key . '/sections/',
            '/' . $key . '/navChains/',
        ];

        foreach ($dirs as $dir) {
            if ($dir) {
                $cache = Cache::createInstance();
                $cache->cleanDir($dir);
            }
        }
    }

    /**
     *  Список единиц измерения
     *
     * @return array
     */
    public static function getMeasuresFromCache(): array
    {
        $obCache = new CPHPCache;
        $cachePath = '/catalogs_v1/measures/';

        if ($obCache->InitCache(3600, $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $items = Measures::getList();
            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }
        return $items;
    }


    /**
     * Возвращает цепочку навигации для раздела
     * $params['IBLOCK_ID'] обязателен
     *
     * @param      $params
     * @param null $callback
     *
     * @return array|mixed
     *
     */
    public static function getNavChainFromCache($params, $callback = null)
    {
        $params['IBLOCK_ID'] = (int)$params['IBLOCK_ID'];
        if ($params['IBLOCK_ID'] <= 0) {
            return [];
        }

        if ($params['__SKIP_CACHE']) {
            $sectionId = $params['SECTION_ID'] ?: Sect::getIdByCode($params['IBLOCK_ID'], $params['SECTION_CODE']);
            $items = Sect::getNavChain($params['IBLOCK_ID'], $sectionId, $params['SELECT']);

            if (is_callable($callback)) {
                $callback($items);
            }
            return $items;
        }

        $obCache = new CPHPCache;
        $iblockCacheDir = $params['IBLOCK_CODE'] ?: (string)$params['IBLOCK_ID'];
        $cachePath = '/' . $iblockCacheDir . '/navChains/';

        if ($obCache->InitCache(3600, serialize($params), $cachePath)) {
            $vars = $obCache->GetVars();
            $items = $vars['RESULT'];
        } else {
            $sectionId = $params['SECTION_ID'] ?: Sect::getIdByCode($params['IBLOCK_ID'], $params['SECTION_CODE']);
            $items = Sect::getNavChain($params['IBLOCK_ID'], $sectionId, $params['SELECT']);

            if (is_callable($callback)) {
                $callback($items);
            }

            if ($obCache->StartDataCache()) {
                $obCache->EndDataCache(['RESULT' => $items]);
            }
        }
        return $items;
    }

    /**
     * По событию метод сбрасывает кеши соответствующего инфоблока
     *
     * @param array $event
     */
    public static function processingEvent(array $event): void
    {
        $iblockId = (int)$event['IBLOCK_ID'];
        $iblockCode = Help::getIblockCode($iblockId);

        self::clear($iblockId);
        self::clear($iblockCode);
    }
    
}
