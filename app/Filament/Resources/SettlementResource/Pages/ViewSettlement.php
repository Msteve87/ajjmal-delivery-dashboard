<?php

namespace App\Filament\Resources\SettlementResource\Pages;

use Filament\Tables;
use App\Models\Driver;
use App\Models\SubOrder;
use App\Models\Settlement;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverSubOrdersExport;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\DriverResource;
use Filament\Tables\Columns\Summarizers\Sum;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Filament\Resources\SettlementResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewSettlement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SettlementResource::class;

    protected static string $view = 'filament.resources.settlement-resource.pages.view-settlement';

    public Settlement $record;

    public function getBreadcrumb(): ?string
    {
        return __('filament/resources.settlement.plural_label');
    }

    public function getTitle(): string
    {
        return __('filament/resources.settlement.view-page');
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export to Excel')
                ->button()
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $subOrders = SubOrder::where('settlement_id', $this->record->id)->get();
                    $fileName = 'driver_' . $this->record->id . '_suborders_' . now()->format('Ymd_His') . '.pdf';
                    return Excel::download(
                        new DriverSubOrdersExport($subOrders),
                        $fileName,
                        \Maatwebsite\Excel\Excel::MPDF
                    );
                })
                ->color('success')
        ];
    }

    public function mount($record): void
    {
        $this->record = $record instanceof Settlement
            ? $record
            : Settlement::findOrFail($record);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                SubOrder::query()
                    ->whereNotNull('delivered_at')
                    ->where('settlement_id', $this->record->id)
            )->columns([
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
                        ->summarize(
                            Sum::make()
                                ->label(__('filament/resources.sub_order.schema.total_shipping'))
                                ->numeric(locale: 'en')
                        )
                        ->sortable(),

                    Tables\Columns\TextColumn::make('base_price')
                        ->label(__('filament/resources.sub_order.schema.base_price'))
                        ->money('lyd', locale: 'en')
                        ->summarize(
                            Sum::make()
                                ->label(__('filament/resources.sub_order.schema.total_price_of_orders'))
                                ->numeric(locale: 'en')
                        )
                        ->sortable(),

                    Tables\Columns\TextColumn::make('total')
                        ->label(__('filament/resources.sub_order.schema.total'))
                        ->money('lyd', locale: 'en')
                        ->summarize(
                            Sum::make()
                                ->label(__('filament/resources.sub_order.schema.total_amount'))
                                ->numeric(locale: 'en')
                        )
                        ->sortable(),

                    // Tables\Columns\IconColumn::make('is_picked_up')
                    //     ->label(__('filament/resources.sub_order.schema.is_picked_up'))
                    //     ->boolean(),

                    Tables\Columns\TextColumn::make('order.address')
                        ->label(__('filament/resources.sub_order.schema.address'))
                        ->getStateUsing(fn($record) => $record->order->address ?? '-')
                        ->sortable(),

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
                        ->searchable(),

                    Tables\Columns\TextColumn::make('date_add')
                        ->label(__('filament/resources.sub_order.schema.date_add'))
                        ->dateTime(),

                    Tables\Columns\TextColumn::make('delivered_at')
                        ->label(__('filament/resources.sub_order.schema.delivered_at'))
                        ->dateTime(),
                ]);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->hasPermissionTo('add.settlement');
    }
}
