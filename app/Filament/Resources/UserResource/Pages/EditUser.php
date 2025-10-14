<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Forms;
use Filament\Actions;
use Filament\Forms\Form;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\MultiSelect::make('permissions')
                    ->label(__('permissions.label'))
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()->hasPermissionTo('delete.user')),
        ];
    }
}
