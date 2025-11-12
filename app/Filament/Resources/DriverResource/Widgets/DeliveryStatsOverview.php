<?php

namespace App\Filament\Resources\DriverResource\Widgets;

use App\Models\Driver;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class DeliveryStatsOverview extends BaseWidget
{
    public ?Driver $record = null;

    protected function getStats(): array
    {
        $driverService = app()->make(\App\Services\DriverService::class);

        return [
            Stat::make('Total Cash', value: $driverService->getDeliveriesTotalCashOnHand($this->record->id)),
            Stat::make('Total Online', $driverService->getDeliveriesTotalOnline($this->record->id)),
            Stat::make('Delivery fees due', value: $driverService->getDeliveriesFeesDue($this->record->id)),
        ];
    }
}
