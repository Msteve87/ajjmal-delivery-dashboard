<?php
namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\OrderStatus;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

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
                TextColumn::make('jm_order_id', )
                    ->label('Id'),

                TextColumn::make('reference')
                    ->label('reference'),

                TextColumn::make('payment_method')
                    ->label('Payment Method'),

                TextColumn::make('customer_name')
                    ->label('Customer Name'),

                TextColumn::make('driver.first_name')
                    ->label('Driver Name'),

                TextColumn::make('orderStatus.slug')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'awaiting' => 'accent',
                        'in_progress' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    }),

                TextColumn::make('total_shipping')
                    ->label('Total Shipping'),

                TextColumn::make('total_paid')
                    ->label('Total Paid'),

                TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->dateTime()
                    ->sortable()
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
                                'cancelled' => 'Canceld'
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-question-mark-circle',
                                'awaiting' => 'heroicon-o-clock',
                                'in_progress' => 'heroicon-o-truck',
                                'cancelled' => 'heroicon-o-x-circle',
                                'delivered' => 'heroicon-o-check',
                            ])
                            ->colors([
                                'pending' => 'gray',
                                'awaiting' => 'accent',
                                'in_progress' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
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
