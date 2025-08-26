<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubOrderResource\Pages;
use App\Filament\Resources\SubOrderResource\RelationManagers;
use App\Models\SubOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubOrderResource extends Resource
{
    protected static ?string $model = SubOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            ->defaultGroup('order.reference')
            ->columns([

                Tables\Columns\TextColumn::make('tracking_id')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->money('lyd', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_price')
                    ->money('lyd', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money('lyd', true)
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
                            'awaiting' => 'accent',
                            'in_progress' => 'warning',
                            'delivered' => 'success',
                            'cancelled_by_customer' => 'danger',
                            'cancelled_by_seller' => 'danger',
                        }
                    )
                    ->extraAttributes(fn($state, SubOrder $record) => [
                        'style' => 'background-color: '
                            . ($record->subOrderStatus?->color ?? '#6B7280')
                            . '; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem;',
                    ])
                    ->searchable(),

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
                Tables\Actions\EditAction::make(),
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
            'edit' => Pages\EditSubOrder::route('/{record}/edit'),
        ];
    }
}
