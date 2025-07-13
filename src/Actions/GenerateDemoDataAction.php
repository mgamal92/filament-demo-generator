<?php

namespace Mgamal92\FilamentDemoGenerator\Actions;

use Filament\Tables\Actions\Action;
use Faker\Factory as Faker;

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
                        $record->$field = $faker->word();
                    }

                    $record->save();
                }

                \Filament\Notifications\Notification::make()
                    ->title("{$count} records generated.")
                    ->success()
                    ->send();
            });
    }
}
