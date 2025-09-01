<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\SubOrder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
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
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_price')
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money('lyd', locale: 'en')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_picked_up')
                    ->boolean(),

                Tables\Columns\TextColumn::make('subOrderStatus.name')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(
                        fn(string $state, SubOrder $record) =>
                        match ($state) {
                            'Processing in progress' => $record->subOrderStatus->color,
                            'Cancellation by customer' => $record->subOrderStatus->color,
                            'Cancellation by merchant' => $record->subOrderStatus->color,
                            'Remote payment accepted' => $record->subOrderStatus->color,
                            'Awaiting bank wire payment' => $record->subOrderStatus->color,
                            'Awaiting check payment' => $record->subOrderStatus->color,
                            'Delivered' => $record->subOrderStatus->color,
                            'Canceled' => $record->subOrderStatus->color,
                            'Shipped' => $record->subOrderStatus->color,
                        }
                    )
                    ->extraAttributes(fn($state, SubOrder $record) => [
                        'style' => 'background-color: '
                            . ($record->subOrderStatus?->color ?? '#6B7280')
                            . '; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem;',
                    ])
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
                            ->label('Change Status')
                            ->icon('heroicon-m-pencil-square')
                            ->form([
                                Forms\Components\Select::make('sub_order_status_id')
                                    ->label('New Status')
                                    ->options(\App\Models\SubOrderStatus::pluck('name', 'id'))
                                    ->required(),
                            ])
                            ->action(function (array $data, SubOrder $record): void {
                                $record->update([
                                    'sub_order_status_id' => $data['sub_order_status_id'],
                                ]);
                            })
                    ),

                Tables\Columns\TextColumn::make('driver.first_name')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->driver_id == $record->order->driver_id
                        ? $record->driver?->first_name . ' ' . $record->driver?->last_name
                        : ($record->order?->driver
                            ? $record->driver->first_name . ' ' . $record->driver->last_name
                            : 'N/A')
                    )
                    ->label(__('filament/resources.order.schema.driver_name'))
                    ->sortable()
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Order Details')
                    ->modalContent(fn($record) => view('filament.orders.sub-orders', ['record' => $record])),

                Tables\Actions\Action::make('Delivery Task')
                    ->label(__('filament/resources.order.actions.assign_driver'))
                    ->icon('heroicon-o-truck')
                    ->modalHeading('Assign Delivery Task')
                    ->modalButton('Assign')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Select::make('drivers')
                            ->label('Select Drivers')
                            ->multiple()
                            ->options(
                                \App\Models\Driver::all()->mapWithKeys(fn($driver) => [
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
                            ->title('Delivery Task Assigned')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
