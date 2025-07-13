<?php

namespace Mgamal92\FilamentDemoGenerator\Actions;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class GenerateDemoDataAction extends Action
{
    public static function make(string|null $name = 'generate-demo-data'): static
    {
        return parent::make($name)
            ->label('Generate Demo Data')
            ->modalHeading('Generate Demo Records')
            ->form([
                \Filament\Forms\Components\TextInput::make('count')
                    ->label('How many records?')
                    ->numeric()
                    ->default(10)
                    ->required(),
            ])
            ->action(function (array $data, $livewire) {
                $model = $livewire->getModel();
                $count = $data['count'] ?? 10;
                $faker = Faker::create();

                for ($i = 0; $i < $count; $i++) {
                    $record = new $model();

                    foreach ($record->getFillable() as $field) {
                        $table = $record->getTable();
                        if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                            continue;
                        }

                        $type = Schema::getColumnType($table, $field);

                        $record->$field = self::generateFakeValue($field, $type, $faker);

                    }

                    $record->save();
                }

                Notification::make()
                    ->title("{$count} records generated.")
                    ->success()
                    ->send();
            });
    }

    protected static function generateFakeValue(string $field, string $type, \Faker\Generator $faker): mixed
    {
        $field = strtolower($field);

        // 1. Field name match
        foreach (self::customGenerators() as $keyword => $generator) {
            if (str_contains($field, $keyword)) {
                return $generator($faker);
            }
        }

        // 2. Fallback to field type
        return self::typeGenerators()[$type]($faker) ?? $faker->word();
    }

    protected static function customGenerators(): array
    {
        return config('filament-demo-generator.field_keywords', []);
    }

    protected static function typeGenerators(): array
    {
        return config('filament-demo-generator.column_types', []);
    }
}
