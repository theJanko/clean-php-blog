<?php

namespace App\Core;

class Config
{
    private static $config = [];

    public static function load(): void
    {
        if (file_exists(__DIR__ . '/../../.env')) {
            $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (strpos($line, '#') === 0 || empty($line)) {
                    continue;
                }

                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    $value = trim($value, '"\'');

                    $value = self::expandVariables($value);

                    self::$config[$key] = $value;
                }
            }
        }
    }

    private static function expandVariables(string $value): string
    {
        return preg_replace_callback('/\$([A-Za-z0-9_]+)|\${([A-Za-z0-9_]+)}/', function ($matches) {
            $varName = $matches[1] ?? $matches[2];
            return self::$config[$varName] ?? '';
        }, $value);
    }

    public static function get(string $key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }
}
