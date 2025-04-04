<?php

namespace App\Core;

final class Config
{
    /** @var array<string, string> */
    private static array $config = [];

    public static function load(): void
    {
        if (file_exists(__DIR__ . '/../../.env')) {
            $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (str_starts_with($line, '#') || empty($line)) {
                    continue;
                }

                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
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

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }
}
