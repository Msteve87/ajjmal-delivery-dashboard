<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ActivityResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ActivityResource\RelationManagers;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('Log Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__(''))
                    ->limit(50)
                    ->searchable(),

                // TextColumn::make('subject_type')
                //     ->label('Subject Type')
                //     ->sortable(),

                // TextColumn::make('subject_id')
                //     ->label('Subject ID'),

                TextColumn::make('causer_type')
                    ->label('Causer Type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Driver' => 'السائق',
                        'App\Models\User' => 'المستخدم',
                        default => $state,
                    }),

                TextColumn::make('causer_id')
                    ->label('Causer ID'),

                // TextColumn::make('properties')
                //     ->label('Properties')
                //     ->limit(50)
                //     ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
