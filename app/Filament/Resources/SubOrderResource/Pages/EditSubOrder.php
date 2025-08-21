<?php

namespace App\Filament\Resources\SubOrderResource\Pages;

use App\Filament\Resources\SubOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubOrder extends EditRecord
{
    protected static string $resource = SubOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
