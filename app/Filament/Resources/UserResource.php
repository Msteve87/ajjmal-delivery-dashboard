<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getModelLabel(): string
    {
        return __('filament/resources.users.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources.users.plural_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament/resources.users.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn($record) => !$record)
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => $state ? bcrypt($state) : null),

                Forms\Components\MultiSelect::make('permissions')
                    ->label('Permissions')
                    ->options(
                        Permission::all()
                            ->mapWithKeys(fn($p) => [
                                $p->name => __('permissions.' . $p->name)
                            ])
                    )
                    ->visible(fn() => auth()->user()->hasPermissionTo('assign.permission'))
                    ->default(fn($record) => $record?->getPermissionNames() ?? [])
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $record->syncPermissions($state);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make()
                    ->visible(
                        fn($record) =>
                        $record->trashed() &&
                        auth()->user()->hasPermissionTo('delete.user')
                    ),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
