<?php

namespace App\Filament\Resources\LocalRateResource\Pages;

use App\Filament\Resources\LocalRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLocalRates extends ManageRecords
{
    protected static string $resource = LocalRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
