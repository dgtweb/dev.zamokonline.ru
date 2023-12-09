<?php

declare(strict_types=1);

namespace Uisoft\App\Catalog;

use ALS\Helper\CacheManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Product
{
    private const IBLOCK_CODE = 'CATALOG';

    /**
     *  Возвращает список товаров каталога
     *
     * @param array $customParams
     * @param bool  $isNoCache
     *
     * @return array
     */
    public static function get(array $customParams = [], bool $isNoCache = false): array
    {
        // TODO: убрать после теста
        $isNoCache = true;

        $defaultParams = [
            'IBLOCK_CODE' => self::IBLOCK_CODE,
            'FILTER' => ['ACTIVE' => 'Y'],
            'SELECT' => [
                'ID:int>id',
                'ACTIVE:bool>active',
                'IBLOCK_ID:int>iblockId',
                'CODE:string>code',
                'SORT:int>sort',
                'NAME:string>name',
                'DETAIL_PICTURE:Image>mainImage',
                'AVAILABLE:string>available',
                'QUANTITY:int>quantity'
            ],
            '__SKIP_CACHE' => $isNoCache,
        ];

        if ($customParams['__IGNORE_ACTIVE']) {
            unset($defaultParams['FILTER']['ACTIVE'], $customParams['__IGNORE_ACTIVE']);
        }

        // Если необходимо заменить поля для выборки новыми.
        // Используется в получении списка товаров
        if ($customParams['__REPLACE_SELECT']) {
            unset($defaultParams['SELECT'], $customParams['__REPLACE_SELECT']);
        }

        $params = array_merge_recursive($defaultParams, $customParams ?: []);
        $elements = CacheManager::getIblockItemsFromCache($params);
        return array_values($elements);
    }

    /**
     * Товар по id
     *
     * @param int $productId
     *
     * @return array
     */
    public static function getById(int $productId): ?array
    {
        return self::get([
            'FILTER' => [
                'ID' => $productId,
            ]
        ]);
    }

    /**
     * Общее кол-во товаров по фильтру
     *
     * @param array $params
     *
     * @return int
     */
    public static function getCountByFilter(array $params): int
    {
        $params['GROUP'] = [];
        $result = CacheManager::getIblockItemsFromCache($params);

        return (int)$result['cnt'];
    }
}
