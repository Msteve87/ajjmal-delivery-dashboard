<?php
namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'awaiting' => 'accent',
                        'in_progress' => 'warning',
                        'delivered' => 'success',
                        'canceled' => 'danger',
                    }),

                TextColumn::make('total_shipping')
                    ->label('Total Shipping'),

                TextColumn::make('total_paid')
                    ->label('Total Paid'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([

                        ToggleButtons::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting' => 'awaiting',
                                'delivered' => 'Delivered',
                                'canceled' => 'Canceld'
                            ])
                            ->icons([
                                'draft' => 'heroicon-o-pencil',
                                'scheduled' => 'heroicon-o-clock',
                                'published' => 'heroicon-o-check-circle',
                            ])
                            ->gridDirection('row')
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
