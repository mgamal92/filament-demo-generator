<?php

namespace Mgamal92\FilamentDemoGenerator\Actions;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Mgamal92\FilamentDemoGenerator\Support\DemoDataTracker;

class DeleteDemoDataAction extends Action
{
    public static function handle(string $modelClass): void
    {
        $ids = DemoDataTracker::get($modelClass);

        if (empty($ids)) {
            Notification::make()
                ->title('No demo data found.')
                ->warning()
                ->send();
            return;
        }

        $modelClass::whereIn((new $modelClass)->getKeyName(), $ids)->delete();
        DemoDataTracker::clear($modelClass);

        Notification::make()
            ->title(count($ids) . ' demo records deleted.')
            ->success()
            ->send();
    }
}
