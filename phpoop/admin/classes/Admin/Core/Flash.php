<?php
declare(strict_types=1);

namespace Admin\Core;

final class Flash
{
    private const KEY = '_flash';

    public static function set(string $key, mixed $value): void
    {
        if (!isset($_SESSION[self::KEY]) || !is_array($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = [];
        }

        $_SESSION[self::KEY][$key] = $value;
    }

    public static function get(string $key): mixed
    {
        if (!isset($_SESSION[self::KEY]) || !is_array($_SESSION[self::KEY])) {
            return null;
        }

        if (!array_key_exists($key, $_SESSION[self::KEY])) {
            return null;
        }

        $value = $_SESSION[self::KEY][$key];
        unset($_SESSION[self::KEY][$key]);

        if (empty($_SESSION[self::KEY])) {
            unset($_SESSION[self::KEY]);
        }

        return $value;
    }
}
