<?php

namespace Uisoft\App;

use Bitrix\Main\Context;

class Cookie
{
    private const cookiePrefix = 'VZ_';

    public static function set($cookieName = '', $value = null): bool
    {
        if (empty($cookieName)) {
            return false;
        }

        Context::getCurrent()->getResponse()->addCookie(
            new \Bitrix\Main\Web\Cookie(self::cookiePrefix . $cookieName, $value)
        );
        return true;
    }

    public static function get($cookieName): ?string
    {
        if (empty($cookieName)) {
            return '';
        }
        return \Bitrix\Main\Context::getCurrent()->getRequest()->getCookie(self::cookiePrefix . $cookieName);

    }
}