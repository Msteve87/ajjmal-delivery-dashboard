<?php

namespace App\Filament\Resources\DriverResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeliveryStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Cash', 1040),
            Stat::make('Total Online', 250),
            Stat::make('Total Shipping', 75),
        ];
    }
}
