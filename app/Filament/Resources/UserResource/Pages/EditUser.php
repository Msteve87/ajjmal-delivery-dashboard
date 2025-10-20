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
                    ->unique(ignoreRecord: true)
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
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $component->state($record->getPermissionNames()->toArray());
                        }
                    })
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $oldPermissions = $record->getPermissionNames()->toArray();
                        $record->syncPermissions($state);
                        $newPermissions = $record->getPermissionNames()->toArray();
                        $added = array_diff($newPermissions, $oldPermissions);
                        $removed = array_diff($oldPermissions, $newPermissions);
                        if (!empty($added) || !empty($removed)) {
                            $description = "قام المستخدم " . auth()->user()->name . " بتحديث صلاحيات المستخدم {$record->name}";
                            if ($added) {
                                $description .= " (تمت إضافة: " . implode(', ', $added) . ")";
                            }
                            if ($removed) {
                                $description .= " (تمت إزالة: " . implode(', ', $removed) . ")";
                            }

                            $activity = __('activitylogs.names.permissions_updated', [], 'ar');

                            activity($activity)
                                ->performedOn($record)
                                ->causedBy(auth()->user())
                                ->log($description);
                        }
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
