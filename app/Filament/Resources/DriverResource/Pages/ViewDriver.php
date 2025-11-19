<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Tables;
use App\Models\Driver;
use App\Models\SubOrder;
use App\Models\Settlement;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverSubOrdersExport;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\DriverResource;
use Filament\Tables\Columns\Summarizers\Sum;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
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
                    ->delivered()
                    ->unsettled()
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
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_add')
                    ->label(__('filament/resources.sub_order.schema.date_add'))
                    ->dateTime(),

                Tables\Columns\TextColumn::make('delivered_at')
                    ->label(__('filament/resources.sub_order.schema.delivered_at'))
                    ->dateTime(),
            ])
            ->filters([
                Filter::make('delivered_at')
                    ->label(__('filament/resources.sub_order.schema.delivered_at'))
                    ->form([
                        Section::make(__('filament/resources.sub_order.schema.delivered_at'))
                            ->schema([
                                DatePicker::make('from')->label('من'),
                                DatePicker::make('until')->label('إلى'),
                            ])
                            ->collapsible(false)
                            ->columns(1),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('delivered_at', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('delivered_at', '<=', $date));
                    }),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('settle')
                        ->label(__('filament/resources.sub_order.actions.settle_orders.label'))
                        ->icon('heroicon-o-banknotes')
                        ->requiresConfirmation()
                        ->modalHeading(__('filament/resources.sub_order.actions.settle_orders.label'))
                        ->modalSubheading(__('filament/resources.sub_order.actions.settle_orders.body'))
                        ->action(function (Collection $records) {
                            $settelment = Settlement::create(['driver_id' => $this->record->id]);

                            foreach ($records as $record) {
                                $record->settlement_id = $settelment->id;
                                $record->save();
                            }

                            //$fileName = 'driver_' . $this->record->id . '_suborders_' . now()->format('Ymd_His') . '.pdf';
                
                            return Excel::download(new DriverSubOrdersExport($records), 'settelemnts.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                        })
                        ->visible(fn() => auth()->user()->hasPermissionTo('add.settlement'))
                        ->deselectRecordsAfterCompletion()
                        ->color('success'),
                ])
            ])
            ->defaultSort('created_at', 'desc');
    }
}
