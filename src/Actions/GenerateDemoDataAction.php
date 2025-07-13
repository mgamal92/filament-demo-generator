<?php

namespace Mgamal92\FilamentDemoGenerator\Actions;

use Faker\Generator;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mgamal92\FilamentDemoGenerator\Events\DemoDataGenerated;
use Mgamal92\FilamentDemoGenerator\Support\DemoDataTracker;

class GenerateDemoDataAction extends Action
{
    public static function make(string|null $name = 'generate-demo-data'): static
    {
        return parent::make($name)
            ->label(function ($livewire) {
                $model = $livewire->getModel();
                return DemoDataTracker::has($model)
                    ? 'Delete Demo Data'
                    : 'Generate Demo Data';
            })
            ->color(function ($livewire) {
                return DemoDataTracker::has($livewire->getModel())
                    ? 'danger'
                    : 'success';
            })
            ->modalHeading(function ($livewire) {
                $model = $livewire->getModel();

                return DemoDataTracker::has($model)
                    ? 'Confirm Deletion of Demo Data'
                    : 'Generate Demo Records';
            })
            ->form(function ($livewire) {
                $model = $livewire->getModel();

                if (DemoDataTracker::has($model)) {
                    return [];
                }

                return [
                    TextInput::make('count')
                        ->label('How many records?')
                        ->numeric()
                        ->default(10)
                        ->required(),
                ];
            })
            ->requiresConfirmation(function ($livewire) {
                return DemoDataTracker::has($livewire->getModel());
            })
            ->action(function (array $data, $livewire) {
                $generatedIds = [];
                $model = $livewire->getModel();
                $faker = Faker::create();
                $relations = self::getBelongsToRelations(new $model());

                if (DemoDataTracker::has($model)) {
                    DeleteDemoDataAction::handle($model);
                    return;
                }

                $count = $data['count'] ?? 10;

                for ($i = 0; $i < $count; $i++) {
                    $record = new $model();

                    foreach ($record->getFillable() as $field) {
                        if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                            continue;
                        }

                        if (array_key_exists($field, $relations)) {
                            $relatedModelClass = get_class($relations[$field]['relation']->getRelated());
                            $relatedId = $relatedModelClass::inRandomOrder()->value((new $relatedModelClass())->getKeyName());

                            if (!$relatedId) {
                                $relatedId = self::generateRelatedModel($relatedModelClass, $faker);
                                if (!$relatedId) {
                                    continue;
                                }
                            }

                            $record->$field = $relatedId;
                            continue;
                        }

                        $table = $record->getTable();
                        $type = Schema::getColumnType($table, $field);
                        $record->$field = self::generateFakeValue($field, $type, $faker, $table);
                    }

                    $record->save();
                    DemoDataTracker::add($model, $record->getKey());
                    $generatedIds[] = $record->getKey();
                }

                event(new DemoDataGenerated($model, $count, $generatedIds ?? []));

                Notification::make()
                    ->title("{$count} records generated.")
                    ->success()
                    ->send();
            });
    }

    protected static function generateRelatedModel(string $modelClass, \Faker\Generator $faker): ?int
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
            $model->$field = self::generateFakeValue($field, $type, $faker);
        }

        $model->save();

        return $model->getKey();
    }

    protected static function generateFakeValue(string $field, string $type, Generator $faker, ?string $table = null): mixed
    {
        $field = strtolower($field);

        foreach (self::customGenerators() as $keyword => $generator) {
            if (str_contains($field, $keyword)) {
                return $generator($faker);
            }
        }

        if ($type === 'enum') {
            if ($table) {
                $enumValues = self::getEnumValues($table, $field);
                if (!empty($enumValues)) {
                    return $faker->randomElement($enumValues);
                }
            }
        }

        return self::typeGenerators()[$type]($faker) ?? $faker->word();
    }

    protected static function getBelongsToRelations(object $model): array
    {
        $relations = [];

        $class = get_class($model);
        $methods = get_class_methods($class);

        foreach ($methods as $method) {
            if (str_starts_with($method, '__')) {
                continue;
            }

            try {
                $reflection = new \ReflectionMethod($class, $method);

                if (
                    $reflection->isPublic()
                    && !$reflection->isStatic()
                    && $reflection->getNumberOfParameters() === 0
                    && $reflection->getDeclaringClass()->getName() === $class
                ) {
                    $return = $reflection->invoke($model);

                    if ($return instanceof BelongsTo) {
                        $relations[$return->getForeignKeyName()] = [
                            'relation' => $return,
                            'method' => $method,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $relations;
    }

    protected static function customGenerators(): array
    {
        return config('filament-demo-generator.field_keywords', []);
    }

    protected static function typeGenerators(): array
    {
        return config('filament-demo-generator.column_types', []);
    }

    protected static function getEnumValues(string $table, string $column): array
    {
        $type = DB::selectOne("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);

        if (!$type || !str_starts_with($type->Type, 'enum(')) {
            return [];
        }

        preg_match('/^enum\((.*)\)$/', $type->Type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        return array_map(function ($value) {
            return trim($value, " '");
        }, explode(',', $matches[1]));
    }

}
