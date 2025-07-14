<?php

namespace Mgamal92\FilamentDemoGenerator\Support;

use Faker\Generator;
use Illuminate\Support\Facades\Schema;

class ModelGenerator
{
    public static function generate(string $modelClass, Generator $faker): ?int
    {
        $model = new $modelClass();
        $table = $model->getTable();

        foreach ($model->getFillable() as $field) {
            if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            if (!Schema::hasColumn($table, $field)) {
                continue;
            }

            $type = Schema::getColumnType($table, $field);
            $model->$field = FakeValueGenerator::for($field, $type, $faker, $table);
        }

        $model->save();

        return $model->getKey();
    }
}
