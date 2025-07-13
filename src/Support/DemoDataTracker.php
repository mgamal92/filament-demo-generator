<?php


namespace Mgamal92\FilamentDemoGenerator\Support;

use Illuminate\Support\Facades\Cache;

class DemoDataTracker
{
    protected static function getKey(string $model): string
    {
        return 'filament-demo-generator:' . $model;
    }

    public static function add(string $model, int $id): void
    {
        $key = self::getKey($model);

        $current = Cache::get($key, []);
        $current[] = $id;

        Cache::put($key, $current, now()->addHours(2));
    }

    public static function get(string $model): array
    {
        return Cache::get(self::getKey($model), []);
    }

    public static function has(string $model): bool
    {
        return !empty(self::get($model));
    }

    public static function clear(string $model): void
    {
        Cache::forget(self::getKey($model));
    }
}
