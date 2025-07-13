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

        if (str_contains($field, 'email')) {
            return $faker->unique()->safeEmail();
        }

        if (str_contains($field, 'name')) {
            return $faker->name();
        }

        if (str_contains($field, 'title')) {
            return $faker->sentence(3);
        }

        if (str_contains($field, 'description') || str_contains($field, 'content')) {
            return $faker->paragraph();
        }

        if (str_contains($field, 'phone')) {
            return $faker->phoneNumber();
        }

        if (str_contains($field, 'image') || str_contains($field, 'avatar')) {
            return $faker->imageUrl(300, 300);
        }

        return match ($type) {
            'string', 'text'        => $faker->sentence(),
            'integer', 'bigint'     => $faker->numberBetween(1, 1000),
            'boolean'               => $faker->boolean(),
            'date'                  => now()->addDays(rand(-1000, 1000))->format('Y-m-d'),
            'datetime', 'timestamp' => now()->addSeconds(rand(-10000000, 10000000)),
            'float', 'decimal'      => $faker->randomFloat(2, 0, 1000),
            default                 => $faker->word(),
        };
    }
}
