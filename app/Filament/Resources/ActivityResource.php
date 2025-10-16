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

    public static function getModelLabel(): string
    {
        return __('filament/resources.activitylogs.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources.activitylogs.plural_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament/resources.activitylogs.plural_label');
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
                TextColumn::make('id')
                    ->label(__('filament/resources.activitylogs.schema.id'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label(__('filament/resources.activitylogs.schema.log_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('filament/resources.activitylogs.schema.description'))
                    ->limit(50)
                    ->searchable(),

                // TextColumn::make('subject_type')
                //     ->label('Subject Type')
                //     ->sortable(),

                // TextColumn::make('subject_id')
                //     ->label('Subject ID'),

                TextColumn::make('causer_type')
                    ->label(__('filament/resources.activitylogs.schema.causer_type'))
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Driver' => 'السائق',
                        'App\Models\User' => 'المستخدم',
                        default => $state,
                    }),

                TextColumn::make('causer_id')
                    ->label(__('filament/resources.activitylogs.schema.causer_id')),

                // TextColumn::make('properties')
                //     ->label('Properties')
                //     ->limit(50)
                //     ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('filament/resources.activitylogs.schema.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
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

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('browse.activity_log');
    }
}
