<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Tables;
use App\Models\Driver;
use App\Models\SubOrder;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Resources\DriverResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewDriver extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = DriverResource::class;

    protected static string $view = 'filament.resources.driver-resource.pages.view-driver';

    public Driver $record;


    public function getBreadcrumb(): ?string
    {
        return __('filament/resources.driver.view-page.label');
    }

    public function getTitle(): string
    {
        return __('filament/resources.driver.view-page.label');
    }

    public function getHeaderWidgets(): array
    {
        return [
            DriverResource\Widgets\DeliveryStatsOverview::make([
                'record' => $this->record,
            ]),
        ];
    }


    public function mount($record): void
    {
        $this->record = $record instanceof Driver
            ? $record
            : Driver::findOrFail($record);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                SubOrder::query()
                    ->where('driver_id', $this->record->id)
                    ->deliveredToday()
            )
            ->columns([
                Tables\Columns\TextColumn::make('tracking_id')
                    ->label(__('filament/resources.sub_order.schema.tracking_id'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('products.seller_logo')
                    ->label(__('filament/resources.sub_order.schema.seller_logo'))
                    ->getStateUsing(fn($record) => $record->products[0]['details']['seller']['logo'] ?? '-')
                    ->circular(),

                Tables\Columns\TextColumn::make('products.seller_name')
                    ->label(__('filament/resources.sub_order.schema.seller_name'))
                    ->getStateUsing(fn($record) => $record->products[0]['details']['seller']['name'] ?? '-')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label(__('filament/resources.sub_order.schema.base_price'))
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_price')
                    ->label(__('filament/resources.sub_order.schema.shipping_price'))
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('filament/resources.sub_order.schema.total'))
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_picked_up')
                    ->label(__('filament/resources.sub_order.schema.is_picked_up'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('order.payment_method')
                    ->label(__('filament/resources.sub_order.schema.payment_method'))
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subOrderStatus.name')
                    ->label(__('filament/resources.sub_order.schema.status'))
                    ->getStateUsing(
                        fn($record) => app()->getLocale() === 'ar'
                        ? $record->subOrderStatus->name_ar
                        : $record->subOrderStatus->name
                    )
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state, SubOrder $record) => $record->subOrderStatus->color)
                    ->extraAttributes(fn($state, SubOrder $record) => [
                        'style' => 'background-color: '
                            . ($record->subOrderStatus?->color ?? '#6B7280')
                            . '; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem;',
                    ])
                    ->searchable()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
