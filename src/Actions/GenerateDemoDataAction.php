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
        return [
            'email'       => fn($faker) => $faker->unique()->safeEmail(),
            'name'        => fn($faker) => $faker->name(),
            'title'       => fn($faker) => $faker->sentence(3),
            'description' => fn($faker) => $faker->paragraph(),
            'content'     => fn($faker) => $faker->paragraph(),
            'phone'       => fn($faker) => $faker->phoneNumber(),
            'image'       => fn($faker) => $faker->imageUrl(300, 300),
            'avatar'      => fn($faker) => $faker->imageUrl(300, 300),
        ];
    }

    protected static function typeGenerators(): array
    {
        return [
            'string'    => fn($faker) => $faker->sentence(),
            'text'      => fn($faker) => $faker->paragraph(),
            'integer'   => fn($faker) => $faker->numberBetween(1, 1000),
            'bigint'    => fn($faker) => $faker->numberBetween(1, 1000),
            'boolean'   => fn($faker) => $faker->boolean(),
            'date'      => fn($faker) => now()->addDays(rand(-1000, 1000))->format('Y-m-d'),
            'datetime'  => fn($faker) => now()->addSeconds(rand(-10000000, 10000000)),
            'timestamp' => fn($faker) => now()->addSeconds(rand(-10000000, 10000000)),
            'float'     => fn($faker) => $faker->randomFloat(2, 0, 1000),
            'decimal'   => fn($faker) => $faker->randomFloat(2, 0, 1000),
        ];
    }
}
