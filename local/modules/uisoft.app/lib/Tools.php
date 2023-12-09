<?php

namespace Uisoft\App;


use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;

final class Tools
{
    /**
     * Возвращает массив URL для выбора типа списка товаров в разделе
     * Параметр сохраняется в Cookie
     *
     * @return array
     */
    public static function getCatalogSectionView(): array
    {
        $result = [];
        $urlParamView = 'viewTypeSection';

        $request = Application::getInstance()->getContext()->getRequest();
        $uri = new Uri($request->getRequestUri());
        $uri->deleteParams([$urlParamView]);

        $types = ['grid', 'list'];
        $view = Cookie::get('viewTypeSection') ?? 'grid';

        foreach ($types as $type) {
            $result[$type] = [
                'type' => $type,
                'url' => $uri->addParams([$urlParamView => $type])->getUri(),
                'set' => $type === $view
            ];
        }
        return $result;
    }

    /** Кол-во товаров на странице
     *
     * @return array
     */
    public static function getCatalogItemsCount(): array
    {
        $result = [];
        $urlParamCount = 'itemsCountSection';

        $request = Application::getInstance()->getContext()->getRequest();
        $uri = new Uri($request->getRequestUri());
        $uri->deleteParams([$urlParamCount]);

        $counts = [1, 6, 12, 24, 48];
        $c = Cookie::get('itemsCountSection') ?? 12;


        foreach ($counts as $count) {
            if ($count === (int)$c) {
                $result['select'] = $count;
            }
            $result['items'][$count] = [
                'count' => $count,
                'url' => $uri->addParams([$urlParamCount => $count])->getUri(),
                'set' => $count === (int)$c
            ];
        }
        return $result;
    }


    /**  Сортировка товаров
     *
     * @return array
     */
    public static function getCatalogOrder(): array
    {
        $result = [];
        $urlParam = 'orderSection';

        $request = Application::getInstance()->getContext()->getRequest();
        $uri = new Uri($request->getRequestUri());
        $uri->deleteParams([$urlParam]);

        $orders = [
            'popular' => [
                'name' => 'Популярные',
                'field' => '',
                'order' => 'asc'
            ],
            'priceAsc' => [
                'name' => 'Сначала дешёвые',
                'field' => 'CATALOG_PRICE_1',
                'order' => 'asc'
            ],
            'priceDesc' => [
                'name' => 'Сначала дорогие',
                'field' => 'CATALOG_PRICE_1',
                'order' => 'desc'
            ],
        ];

        $curParam = Cookie::get($urlParam) ?? 'popular';


        foreach ($orders as $order => $params) {
            if ($order === $curParam) {
                $result['select'] = [
                    'name' => $params['name'],
                    'field' => $params['field'],
                    'order' => $params['order'],
                ];
            }
            $result['items'][$order] = [
                'name' => $params['name'],
                'code' => $order,
                'url' => $uri->addParams([$urlParam => $order])->getUri(),
                'set' => $order === $curParam,
            ];
        }

        return $result;
    }

    /**
     *  Возвращает первый элемент массива
     *
     * @param array $array
     *
     * @return mixed|null
     */
    public static function first(array $array)
    {
        return array_shift($array);
    }

    public static function setCatalogSetParamsRedirect($uriParam): void
    {
        $urlParamView = $uriParam;
        $request = Application::getInstance()->getContext()->getRequest();
        $uri = new Uri($request->getRequestUri());
        parse_str($uri->getQuery(), $arUrlParams);
        if (!empty($arUrlParams[$urlParamView])) {
            Cookie::set($urlParamView, $arUrlParams[$urlParamView]);
            LocalRedirect($uri->deleteParams([$urlParamView])->getUri());
        }
    }

    /**
     * @param $paramQueryClear
     *
     * @return void
     */
    public static function clearFilterRedirect($paramQueryClear): void
    {
        $request = Application::getInstance()->getContext()->getRequest();
        if (strpos($request->getRequestUri(), $paramQueryClear) !== false) {
            LocalRedirect(str_replace($paramQueryClear, '', $request->getRequestUri()));
        }
    }

    /**
     * Возвращает бренд для элемента каталога. Поиск производит по определенным свойствам
     *
     * @param $element
     *
     * @return array
     */
    public static function getBrandByElementItem($element): array
    {
        $props = ['BRAND', 'PROIZVODITEL', '_PROIZVODITEL'];

        foreach ($props as $prop) {
            if (!empty($element['PROPERTIES'][$prop]['VALUE'])) {
                return [
                    'NAME' => trim($element['PROPERTIES'][$prop]['VALUE'])
                ];
            }
        }
        return [];
    }

    public static function prepareProductProperties(array $properties): array
    {
        $result = [];
        foreach ($properties as $property) {
            if (empty($property['VALUE'])) {
                continue;
            }

            $excludeProps = [
                'MORE_PHOTO',
                'CML2_TRAITS',
                'CML2_BASE_UNIT',
                'CML2_TAXES',
                'FILES',
                'CML2_BAR_CODE' // штрихкод
            ];

            if (in_array($property['CODE'], $excludeProps)) {
                continue;
            }

            $result[$property['CODE']] = [
                'name' => $property['NAME'],
                'value' => $property['VALUE'],
                'sort' => $property['SORT']
            ];
        }

        return $result;
    }

    public static function arraySplitToCols(array $items, $countCols = 0, $vector = 'vertical'): array
    {
        if ($countCols <= 0) {
            $countCols = 2;
        }

        $result = [];

        $itemsCount = count($items);
        $itemsInColumn = ceil($itemsCount / $countCols);

        //\ALS\Helper\Dbg::show($itemsInColumn, true, true);
        $i = $j = 0;
        foreach ($items as $code => $item) {
            if ($vector === 'vertical') {
                if ($j >= $itemsInColumn) {
                    $i++;
                    $j = 0;
                }
                $result[$i][$code] = $item;
                $j++;
            } else {
                if ($i >= $countCols) {
                    $j++;
                    $i = 0;
                }
                $result[$i][$code] = $item;
                $i++;
            }
        }
        return $result;
    }
}
