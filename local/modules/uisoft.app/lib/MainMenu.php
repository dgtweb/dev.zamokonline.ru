<?php

namespace Uisoft\App;

use ALS\Helper\CacheManager;

class MainMenu
{
    public const IBLOCK_CODE = 'MAIN_MENU';

    public static function get()
    {
        $elements = CacheManager::getIblockItemsFromCache(
            [
                'IBLOCK_CODE'  => self::IBLOCK_CODE,
                'FILTER'       => [
                    'ACTIVE' => 'Y',
                ],
                'SELECT'       => [
                    'ID:int>id',
                    'IBLOCK_SECTION_ID:int>sectionId',
                    'CODE:string>code',
                    'NAME:string>name',
                    'PROPERTY_ICON:File>icon',
                    'PROPERTY_URL:string>url',
                    'PROPERTY_BLUE_LINK:bool>bl',
                    'SORT:int>sort',
                ],
                'ORDER'        => [
                    'SORT' => 'ASC'
                ],
                '__SKIP_CACHE' => true,
            ]
        );

        $sections = CacheManager::getIblockSectionsFromCache(
            [
                'IBLOCK_CODE'  => self::IBLOCK_CODE,
                'FILTER'       => [
                    'ACTIVE' => 'Y',
                ],
                'SELECT'       => [
                    'ID:int>id',
                    'IBLOCK_SECTION_ID:int>sectionId',
                    'NAME:string>name',
                    'UF_ICON_SVG:File>icon',
                    'UF_URL:string>url',
                    'UF_VIDEO:string>video',
                    'UF_VIDEO_DESCRIPTION:string>videoDescription',
                    'UF_VIDEO_PREVIEW:File>videoPreview',
                    'DEPTH_LEVEL:int>depthLevel'
                ],
                'ORDER'        => [
                    'LEFT_MARGIN' => 'ASC'
                ],
                'ASSOC'        => 'Y',
                '__SKIP_CACHE' => true,
            ]
        );

        foreach ($sections as $section) {
            if($section['sectionId']>0 && !empty($sections[$section['sectionId']])){
                $sections[$section['sectionId']]['isParent'] = true;
            }
        }

        if (!empty($elements)) {
            foreach ($elements as $element) {
                if ($element['sectionId'] > 0 && !empty($sections[$element['sectionId']])) {
                    $sections[$element['sectionId']]['items'][] = $element;
                }
            }
        }

        $result = array_values($sections);

        return $result ?? [];
    }
}