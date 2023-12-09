<?php

declare(strict_types=1);

namespace ALS\Helper;

use DomainException;

class SessionMode
{
    public const KEY = '_sessionMode';

    public const DISALLOW = 0;
    public const READONLY = 1;

    public static function setMode(int $mode): void
    {
        if (self::DISALLOW === $mode) {
            self::setModeDisallow();
        } elseif (self::READONLY === $mode) {
            self::setModeReadOnly();
        } else {
            throw new DomainException('Not know mode. Allow: 0, 1');
        }
    }

    private static function setModeReadOnly(): void
    {
        define('BX_SECURITY_SESSION_READONLY', true);
    }

    private static function setModeDisallow(): void
    {
        define('BX_SECURITY_SESSION_VIRTUAL', true);
    }
}
