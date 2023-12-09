<?php

declare(strict_types=1);

namespace Uisoft\App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class Helper
{
    /**
     * @param Request $request
     *
     * @return mixed
     * @throws \JsonException
     */
    public static function getRequestData(Request $request): array
    {
        $contentRequest = $request->getContent();

        if (empty($contentRequest)) {
            throw new BadRequestHttpException('The requestData is empty');
        }

        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (empty($requestData)) {
            throw new BadRequestHttpException('The requestData is empty');
        }

        return $requestData;
    }
}
