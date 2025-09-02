<?php
namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\OrderStatus;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Notifications\NewOrderNotification;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    // protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationGroup(): ?string
    {
        return __('filament/navigations.orders');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources.order.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources.order.plural_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament/resources.order.plural_label');
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
            ->columns([
                TextColumn::make('id', )
                    ->label(__('filament/resources.order.schema.id')),

                TextColumn::make('reference')
                    ->label(__('filament/resources.order.schema.reference'))
                    ->searchable(),

                TextColumn::make('payment_method')
                    ->label(__('filament/resources.order.schema.payment_method')),

                TextColumn::make('customer_name')
                    ->label(__('filament/resources.order.schema.customer_name')),

                TextColumn::make('customer_phone')
                    ->label(__('filament/resources.order.schema.customer_phone'))
                    ->searchable(),

                TextColumn::make('driver.first_name')
                    ->formatStateUsing(fn($state, $record) => $record->driver?->first_name . ' ' . $record->driver?->last_name)
                    ->label(__('filament/resources.order.schema.driver_name')),

                TextColumn::make('orderStatus.slug')
                    ->label(__('filament/resources.order.schema.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'awaiting' => 'accent',
                        'in_progress' => 'warning',
                        'delivered' => 'success',
                        'cancelled_by_customer' => 'danger',
                        'cancelled_by_seller' => 'danger',
                    }),

                TextColumn::make('price')
                    ->label(__('filament/resources.order.schema.price')),

                TextColumn::make('total_shipping')
                    ->label(__('filament/resources.order.schema.total_shipping')),

                TextColumn::make('total_paid')
                    ->label(__('filament/resources.order.schema.total_paid')),

                TextColumn::make('delivery_date')
                    ->label(__('filament/resources.order.schema.delivery_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label(__('filament/resources.order.schema.start_time'))
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label(__('filament/resources.order.schema.end_time'))
                    ->time('H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Order Details')
                    ->modalContent(fn($record) => view('filament.orders.view', ['record' => $record])),

                Tables\Actions\EditAction::make()
                    ->form([
                        ToggleButtons::make('order_status_id')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting' => 'Awaiting',
                                'in_progress' => 'In Progress',
                                'delivered' => 'Delivered',
                                'cancelled_by_seller' => 'Canceld by Seller',
                                'cancelled_by_customer' => 'Cancelled by Customer',
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-question-mark-circle',
                                'awaiting' => 'heroicon-o-clock',
                                'in_progress' => 'heroicon-o-truck',
                                'cancelled_by_seller' => 'heroicon-o-x-circle',
                                'cancelled_by_customer' => 'heroicon-o-x-circle',
                                'delivered' => 'heroicon-o-check',
                            ])
                            ->colors([
                                'pending' => 'gray',
                                'awaiting' => 'accent',
                                'in_progress' => 'primary',
                                'delivered' => 'success',
                                'cancelled_by_seller' => 'danger',
                                'cancelled_by_customer' => 'danger',
                            ])
                            ->extraAttributes(['class' => 'max-w-xs m-4 flex h-fit flex-wrap items-center gap-2 rounded-xl py-4'])
                            ->afterStateUpdated(function ($state, $record, $set, $get) {
                                $set('pending_status_change', $state);
                                $set('show_password_field', true);
                            })
                            ->dehydrated(false),

                        TextInput::make('password')
                            ->label('Confirm Password')
                            ->password()
                            ->required()
                            ->visible(fn($get) => $get('show_password_field'))
                            ->rule('current_password')
                            ->afterStateUpdated(function ($record, $get) {
                                $state = $get('pending_status_change');
                                $id = OrderStatus::where('slug', $state)->value('id');
                                $record->update([
                                    'order_status_id' => $id,
                                    'updated_at' => now()
                                ]);
                            })
                            ->dehydrated(false),
                    ]),


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
                            ->options(\App\Models\Driver::all()->pluck('first_name', 'id'))
                            ->searchable(),
                    ])
                    ->action(function (Model $record, array $data) {
                        collect($data['drivers'])->each(function ($driverId) use ($record) {
                            $driver = \App\Models\Driver::find($driverId);
                            $driver->notify(new NewOrderNotification($record));
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
