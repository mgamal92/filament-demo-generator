<?php

namespace Mgamal92\FilamentDemoGenerator\Support;

use Faker\Generator;
use Illuminate\Support\Facades\DB;

class FakeValueGenerator
{
    public static function for(string $field, string $type, Generator $faker, ?string $table = null): mixed
    {
        $field = strtolower($field);

        // Check if it's an image field
        if (self::isImageField($field)) {
            return "https://picsum.photos/seed/{$field}-{$faker->unique()->numberBetween(1, 1000)}/500/500";
        }

        // Check keyword-based generators
        foreach (self::customGenerators() as $keyword => $generator) {
            if (str_contains($field, $keyword)) {
                return $generator($faker);
            }
        }

        // Handle ENUM
        if ($type === 'enum' && $table) {
            $enumValues = self::getEnumValues($table, $field);
            if (!empty($enumValues)) {
                return $faker->randomElement($enumValues);
            }
        }

        // Fallback to type-based generators
        return self::typeGenerators()[$type]($faker) ?? $faker->word();
    }

    protected static function getEnumValues(string $table, string $column): array
    {
        $type = DB::selectOne("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);

        if (!$type || !str_starts_with($type->Type, 'enum(')) {
            return [];
        }

        preg_match('/^enum\((.*)\)$/', $type->Type, $matches);

        return isset($matches[1])
            ? array_map(fn ($value) => trim($value, " '"), explode(',', $matches[1]))
            : [];
    }

    protected static function customGenerators(): array
    {
        return config('filament-demo-generator.field_keywords', []);
    }

    protected static function typeGenerators(): array
    {
        return config('filament-demo-generator.column_types', []);
    }

    protected static function isImageField(string $attribute): bool
    {
        $imageKeywords = config('filament-demo-generator.image_fields', []);

        foreach ($imageKeywords as $keyword) {
            if (str_contains($attribute, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }
}
