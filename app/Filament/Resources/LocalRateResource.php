<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LocalRateResource\Pages;
use App\Models\LocalRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LocalRateResource extends Resource
{
    protected static ?string $model = LocalRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return 'Local Delivery Rate';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Local Delivery Rates';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Delivery Rate Configuration')
                    ->description('Set the delivery rates for specific areas based on delivery type.')
                    ->schema([
                        Forms\Components\TagsInput::make('areas')
                            ->label('Areas')
                            ->placeholder('Add an area...')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('home_rate')
                            ->label('Home Delivery Rate')
                            ->numeric()
                            ->prefix('LYD')
                            ->required(),
                        Forms\Components\TextInput::make('locker_rate')
                            ->label('Smart Locker Rate')
                            ->numeric()
                            ->prefix('LYD')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areas')
                    ->label('Areas')
                    ->badge()
                    ->separator(',')
                    ->searchable(),
                Tables\Columns\TextColumn::make('home_rate')
                    ->label('Home Rate')
                    ->money('LYD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('locker_rate')
                    ->label('Locker Rate')
                    ->money('LYD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLocalRates::route('/'),
        ];
    }
}
