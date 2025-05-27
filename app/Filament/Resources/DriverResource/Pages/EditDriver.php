<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use App\Rules\MinAgeRule;
use Filament\Forms\Components;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DriverResource;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('first_name')
                    ->label(__('filament/resources.driver.schema.first_name')),

                Components\TextInput::make('last_name')
                    ->label(__('filament/resources.driver.schema.last_name')),

                Components\TextInput::make('phone')
                    ->label(__('filament/resources.driver.schema.phone'))
                    ->unique(ignoreRecord: true),

                Components\TextInput::make('password')
                    ->label(__('filament/resources.driver.schema.password'))
                    ->password(),

                Components\Select::make('gender')
                    ->label(__('filament/resources.driver.schema.gender'))
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

                Components\Select::make('driver_type')
                    ->label(__('filament/resources.driver.schema.driver_type'))
                    ->options([
                        'employee' => 'Employee',
                        'independent' => 'Independent',
                    ]),

                Components\DatePicker::make('dob')
                    ->label(__('filament/resources.driver.schema.date_of_birth'))
                    ->rules([
                        'date',
                        new MinAgeRule(18),
                    ]),

                Components\TextInput::make('passport_no')
                    ->label(__('filament/resources.driver.schema.passport_number'))
                    ->nullable(),

                Components\TextInput::make('criminal_case')
                    ->label(__('filament/resources.driver.schema.criminal_case'))
                    ->nullable(),

                Components\TextInput::make('national_no')
                    ->label(__('filament/resources.driver.schema.national_number'))
                    ->nullable(),

                Components\Select::make('delivery_status')
                    ->label(__('filament/resources.driver.schema.delivery_status'))
                    ->options([
                        'available' => 'Available',
                        'not_available' => 'Not Available',
                    ])
                    ->default('available'),

                Components\Select::make('status')
                    ->label(__('filament/resources.driver.schema.status'))
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),

                Components\Toggle::make('is_active')
                    ->label(__('filament/resources.driver.schema.is_active'))
                    ->default(false),
            ]);
    }
}
