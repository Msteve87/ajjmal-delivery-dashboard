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
        return __('filament/resources.local-rate.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources.local-rate.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament/resources.local-rate.config_title'))
                    ->description(__('filament/resources.local-rate.description'))
                    ->schema([
                        Forms\Components\TagsInput::make('areas')
                            ->label(__('filament/resources.local-rate.schema.areas'))
                            ->placeholder('أضف منطقة..')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('home_rate')
                            ->label(__('filament/resources.local-rate.schema.home_rate'))
                            ->numeric()
                            ->prefix('LYD')
                            ->required()
                            ->maxValue(config('rates.max_rate')),

                        Forms\Components\TextInput::make('locker_rate')
                            ->label(__('filament/resources.local-rate.schema.locker_rate'))
                            ->numeric()
                            ->prefix('LYD')
                            ->required()
                            ->maxValue(config('rates.max_rate')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('edit.rates');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('areas')
                    ->label(__('filament/resources.local-rate.columns.areas'))
                    ->badge()
                    ->separator(',')
                    ->searchable(),
                Tables\Columns\TextColumn::make('home_rate')
                    ->label(__('filament/resources.local-rate.columns.home_rate'))
                    ->money('LYD', locale: 'en')
                    ->sortable(),
                Tables\Columns\TextColumn::make('locker_rate')
                    ->label(__('filament/resources.local-rate.columns.locker_rate'))
                    ->money('LYD', locale: 'en')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament/resources.local-rate.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->hasPermissionTo('edit.rates')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasPermissionTo('edit.rates')),
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
