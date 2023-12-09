<?php

namespace Uisoft\App;

use CFile;

final class Images
{
    public static function createCatalogDetailImage(int $imageId, array $params = [], string $alt = ''): ?array
    {
        $result = null;

        if ($imageId <= 0) {
            return null;
        }

        $defaultWidth = 800;
        $defaultHeight = 800;


        if (empty($params)) {
            $image = CFile::ResizeImageGet(
                $imageId,
                ["width" => $defaultWidth, "height" => $defaultHeight],
                BX_RESIZE_IMAGE_PROPORTIONAL,
                true
            );

            $result['img'] = [
                'src' => $image['src'],
                'width' => $image['width'],
                'height' => $image['height'],
                'alt' => htmlspecialchars($alt)
            ];
        } else {
            foreach ($params as $key => $param) {
                if (empty($param['width']) && empty($param['height'])) {
                    continue;
                }
                $image = CFile::ResizeImageGet(
                    $imageId,
                    ["width" => $param['width'] ?? 10000, "height" => $param['height'] ?? 10000],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );
                $result[$key] = [
                    'src' => $image['src'],
                    'width' => $image['width'],
                    'height' => $image['height'],
                    'alt' => htmlspecialchars($alt)
                ];
            }
        }
        return $result;
    }

}
