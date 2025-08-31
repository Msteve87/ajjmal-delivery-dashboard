<?php

namespace App\Filament\Resources\SubOrderResource\Pages;

use App\Filament\Resources\SubOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubOrders extends ListRecords
{
    protected static string $resource = SubOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
