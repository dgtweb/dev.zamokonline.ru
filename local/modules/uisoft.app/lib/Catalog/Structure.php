<?php

namespace Uisoft\App\Catalog;

use ALS\Helper\Sect;

class Structure
{
    public const IBLOCK_CODE = 'STRUCTURE';

    public static function getByUrl(string $url = ''): array
    {
        $result = [];
        if (empty($url)) {
            return $result;
        }
        
        \ALS\Helper\Dbg::showDebug($url, true, true);
        
        \ALS\Helper\Dbg::showDebug([
                                       'IBLOCK_CODE' => self::IBLOCK_CODE,
                                       'FILTER'      => [
                                           'UF_URL' => '%' . $url . '%'
                                       ],
                                       'SELECT'      => [
                                           'NAME:string>name'
                                       ]
                                   ], true, true);

        $item = Sect::getList(
            [
                'IBLOCK_CODE' => self::IBLOCK_CODE,
                'FILTER'      => [
                    'UF_URL' => '%' . $url . '%'
                ],
                'SELECT'      => [
                    'NAME:string>name'
                ]
            ]
        );

        if ($_SERVER["REMOTE_ADDR"] === "193.242.176.111") {
            \ALS\Helper\Dbg::showDebug($item, true, true);
        }

        return $result;
    }
}

