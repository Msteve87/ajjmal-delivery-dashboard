<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\SubOrder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\JmOrderStatus;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\NewOrderNotification;
use App\Filament\Resources\SubOrderResource\Pages;
use App\Notifications\NewDeliveryTaskNotification;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubOrderResource\RelationManagers;

class SubOrderResource extends Resource
{
    protected static ?string $model = SubOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('filament/navigations.orders');
    // }

    public static function getModelLabel(): string
    {
        return __('filament/resources.sub_order.label');
    }

    // public static function getNavigationLabel(): string
    // {
    //     return __('filament/resources.sub_order.plural_label');
    // }

    public static function getPluralLabel(): ?string
    {
        return __('filament/resources.sub_order.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->orderBy('tracking_id', 'desc'))
            ->defaultGroup(
                Group::make('order.reference')
                    ->collapsible()
            )
            ->columns([
                Tables\Columns\TextColumn::make('tracking_id')
                    ->label(__('filament/resources.sub_order.schema.tracking_id'))
                    ->sortable()
                    ->searchable(['tracking_id']),

                Tables\Columns\TextColumn::make('order.reference')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('products.seller_logo')
                    ->label(__('filament/resources.sub_order.schema.seller_logo'))
                    ->getStateUsing(fn($record) => $record->products[0]['details']['seller']['logo'] ?? '-')
                    ->circular(),

                Tables\Columns\TextColumn::make('products.seller_name')
                    ->label(__('filament/resources.sub_order.schema.seller_name'))
                    ->getStateUsing(fn($record) => $record->products[0]['details']['seller']['name'] ?? '-')
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('total_discounts')
                    ->label(__('filament/resources.sub_order.schema.total_discounts'))
                    ->money('lyd', locale: 'en')
                    ->color(fn($state) => $state != 0 ? 'danger' : null)
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
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
                            ->label(__('filament/resources.sub_order.actions.change_status'))
                            ->icon('heroicon-m-pencil-square')
                            ->form([
                                Forms\Components\Select::make('sub_order_status_id')
                                    ->label(__('filament/resources.sub_order.form.new_status'))
                                    ->options(
                                        function () {
                                            $locale = app()->getLocale();
                                            $column = $locale === 'ar' ? 'name_ar' : 'name';

                                            return \App\Models\SubOrderStatus::pluck($column, 'id');
                                        }
                                    )
                                    ->required(),
                            ])
                            ->action(function (array $data, SubOrder $record): void {
                                $orderService = app(\App\Services\OrderService::class);

                                $orderService->updateJmStatusOrder($record->tracking_id, $data['sub_order_status_id']);

                                $record->update([
                                    'sub_order_status_id' => $data['sub_order_status_id'],
                                ]);

                                event(new \App\Events\OrderUpdated($record, auth()->user(), 'order_update'));

                            })
                            ->visible(fn() => auth()->user()->hasPermissionTo('update.order.status'))
                    ),

                Tables\Columns\TextColumn::make('driver.first_name')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->driver_id
                        ? $record->driver?->first_name . ' ' . $record->driver?->last_name
                        : 'N/A'
                    )
                    ->label(__('filament/resources.sub_order.schema.driver_name'))
                    ->sortable()
                    ->searchable(['drivers.first_name', 'drivers.last_name'])
                    ->default('-'),

                Tables\Columns\TextColumn::make('date_add')
                    ->label(__('filament/resources.sub_order.schema.date_add'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                // Tables\Columns\TextColumn::make('created_at')
                //     ->label(__('filament/resources.sub_order.schema.created_at'))
                //     ->dateTime('d M Y H:i')
                //     ->sortable(),

                // Tables\Columns\TextColumn::make('updated_at')
                //     ->label(__('filament/resources.sub_order.schema.updated_at'))
                //     ->dateTime('d M Y H:i')
                //     ->sortable(),
            ])
            ->filters([
                SelectFilter::make('sub_order_status_id')
                    ->label(__('filament/resources.sub_order.schema.status'))
                    ->options(fn() => \App\Models\SubOrderStatus::pluck(
                        app()->getLocale() === 'ar' ? 'name_ar' : 'name',
                        'id'
                    ))
                    ->multiple()
                    ->searchable()
                    ->placeholder('Select status'),

                Filter::make('date_add')
                    ->label(__('filament/resources.sub_order.schema.date_add'))
                    ->form([
                        Section::make(__('filament/resources.sub_order.schema.date_add'))
                            ->schema([
                                DatePicker::make('from')->label('من'),
                                DatePicker::make('until')->label('إلى'),
                            ])
                            ->collapsible(false)
                            ->columns(1),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date_add', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('date_add', '<=', $date));
                    }),

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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->modalHeading(__('filament/resources.sub_order.actions.order_details'))
                        ->modalContent(fn($record) => view('filament.orders.sub-orders', ['record' => $record])),

                    Tables\Actions\Action::make('Delivery Task')
                        ->label(__('filament/resources.sub_order.actions.assign_driver'))
                        ->icon('heroicon-o-truck')
                        ->modalHeading(__('filament/resources.sub_order.actions.assign_delivery_task'))
                        ->modalButton(__('filament/resources.order.actions.assign'))
                        ->requiresConfirmation()
                        ->form([
                            \Filament\Forms\Components\Select::make('driver')
                                ->label(__('filament/resources.sub_order.form.select_drivers'))
                                ->options(
                                    \App\Models\Driver::where('is_active', true)->get()
                                        ->mapWithKeys(fn($driver) => [
                                            $driver->id => $driver->first_name . ' ' . $driver->last_name
                                        ])
                                )
                                ->searchable(),
                        ])
                        ->action(function (Model $record, array $data) {
                            $record->driver_id = $data['driver'];

                            $record->save();

                            Notification::make()
                                ->title(__('filament/resources.order.notifications.delivery_task_assigned'))
                                ->success()
                                ->send();
                        })
                        ->visible(fn() => auth()->user()->hasPermissionTo('assign.delivery.tasks')),

                    Tables\Actions\Action::make('Notify Driver')
                        ->label(__('filament/resources.sub_order.actions.notify_driver'))
                        ->icon('heroicon-o-bell-alert')
                        ->modalHeading(__('filament/resources.sub_order.actions.assign_delivery_task'))
                        ->modalButton(__('filament/resources.order.actions.assign'))
                        ->requiresConfirmation()
                        ->form([
                            \Filament\Forms\Components\Select::make('drivers')
                                ->label(__('filament/resources.sub_order.form.select_drivers'))
                                ->multiple()
                                ->options(
                                    \App\Models\Driver::where('is_active', true)->get()
                                        ->mapWithKeys(fn($driver) => [
                                            $driver->id => $driver->first_name . ' ' . $driver->last_name
                                        ])
                                )
                                ->searchable(),
                        ])
                        ->action(function (Model $record, array $data) {
                            collect($data['drivers'])->each(function ($driverId) use ($record) {
                                $driver = \App\Models\Driver::find($driverId);
                                $driver->notify(new NewDeliveryTaskNotification($record));
                            });

                            Notification::make()
                                ->title(__('filament/resources.sub_order.notifications.drivers_notified'))
                                ->success()
                                ->send();
                        })
                        ->visible(fn() => auth()->user()->hasPermissionTo('assign.delivery.tasks')),

                    Tables\Actions\Action::make('Withdraw order')
                        ->label(__('filament/resources.sub_order.actions.withdraw_order_from_driver'))
                        ->icon('heroicon-o-exclamation-circle')
                        ->modalHeading(__('filament/resources.sub_order.actions.withdraw_order_from_driver'))
                        ->modalButton(__('filament/resources.sub_order.actions.withdraw_order_from_driver'))
                        ->requiresConfirmation()
                        ->action(function (Model $record, array $data) {
                            $record->driver_id = null;
                            $record->save();

                            event(new \App\Events\OrderUpdated($record, auth()->user(), 'withdraw_order'));

                            Notification::make()
                                ->title('تم إلغاء تعيين السائق بنجاح')
                                ->success()
                                ->send();
                        })
                        ->disabled(
                            fn(Model $record) => is_null($record->driver_id)
                            || $record->sub_order_status_id != JmOrderStatus::processingInProgress->value
                            && $record->sub_order_status_id != JmOrderStatus::awaitingCashOnDelivery->value

                        )
                        ->visible(fn() => auth()->user()->hasPermissionTo('assign.delivery.tasks'))
                ]),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubOrders::route('/'),
            'create' => Pages\CreateSubOrder::route('/create'),
            // 'edit' => Pages\EditSubOrder::route('/{record}/edit'),
        ];
    }
}
