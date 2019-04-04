<?php
declare(strict_types=1);

namespace Src;

class Environment
{
    public const PROD = 'prod';
    public const DEV = 'dev';

    public static function isDev(): bool
    {
        return self::getMode() === self::DEV;
    }

    private static function getMode(): string
    {
        return getenv('MODE') ?: self::PROD;
    }
}
