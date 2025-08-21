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

                Tables\Columns\TextColumn::make('orderStatus.name')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('driver.first_name')
                    ->label('Driver')
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
