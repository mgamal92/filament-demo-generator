<?php

namespace Mgamal92\FilamentDemoGenerator\Actions;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;
use Mgamal92\FilamentDemoGenerator\Events\DemoDataGenerated;
use Mgamal92\FilamentDemoGenerator\Support\DemoDataTracker;
use Mgamal92\FilamentDemoGenerator\Support\FakeValueGenerator;
use Mgamal92\FilamentDemoGenerator\Support\ModelGenerator;
use Mgamal92\FilamentDemoGenerator\Support\RelationResolver;

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
                $relations = RelationResolver::getBelongsToRelations(new $model());

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
                                $relatedId = ModelGenerator::generate($relatedModelClass, $faker);

                                if (!$relatedId) {
                                    continue;
                                }
                            }

                            $record->$field = $relatedId;
                            continue;
                        }

                        $table = $record->getTable();
                        $type = Schema::getColumnType($table, $field);
                        $record->$field = FakeValueGenerator::for($field, $type, $faker, $table);
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
}
