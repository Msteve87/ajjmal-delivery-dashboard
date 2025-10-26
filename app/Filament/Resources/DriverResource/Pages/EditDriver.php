<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Forms;
use Filament\Actions;
use Filament\Forms\Form;
use App\Rules\MinAgeRule;
use Filament\Forms\Components;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DriverResource;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('filament/resources.driver.schema.first_name'))
                            ->required(),

                        Forms\Components\TextInput::make('last_name')
                            ->label(__('filament/resources.driver.schema.last_name'))
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('filament/resources.driver.schema.phone'))
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('gender')
                            ->label(__('filament/resources.driver.schema.gender'))
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required(),

                        Forms\Components\Select::make('driver_type')
                            ->label(__('filament/resources.driver.schema.driver_type'))
                            ->options([
                                'employee' => 'موظف',
                                'independent' => 'مستقل',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('dob')
                            ->label(__('filament/resources.driver.schema.date_of_birth'))
                            ->required()
                            ->rules([
                                'date',
                                new MinAgeRule(18),
                            ]),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('delivery_status')
                            ->label(__('filament/resources.driver.schema.delivery_status'))
                            ->options([
                                'available' => 'Available',
                                'not_available' => 'Not Available',
                            ])
                            ->default('available'),

                        // Forms\Components\Select::make('status')
                        //     ->label(__('filament/resources.driver.schema.status'))
                        //     ->options([
                        //         'pending' => 'Pending',
                        //         'processing' => 'Processing',
                        //         'approved' => 'Approved',
                        //         'rejected' => 'Rejected',
                        //     ])
                        //     ->default('pending'),

                        Forms\Components\TextInput::make('passport_no')
                            ->label(__('filament/resources.driver.schema.passport_number'))
                            ->nullable(),

                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        // Forms\Components\TextInput::make('passport_no')
                        //     ->label(__('filament/resources.driver.schema.passport_number'))
                        //     ->nullable(),

                        Forms\Components\TextInput::make('criminal_case')
                            ->label(__('filament/resources.driver.schema.criminal_case'))
                            ->nullable(),

                        Forms\Components\TextInput::make('national_no')
                            ->label(__('filament/resources.driver.schema.national_number'))
                            ->nullable(),

                        Forms\Components\TextInput::make('license_no')
                            ->label(__('filament/resources.driver.form.license_no'))
                            ->nullable(),
                    ]),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('filament/resources.driver.schema.is_active'))
                    ->default(false),

                Forms\Components\Repeater::make('documents')
                    ->label(__('filament/resources.driver.form.documents'))
                    ->relationship()
                    ->columnSpanFull()
                    ->addable(false)
                    ->schema([
                        Forms\Components\FileUpload::make('license')
                            ->label(__('filament/resources.driver.form.license_attachment'))
                            ->disk('local')
                            ->visibility('private')
                            ->directory('driver_documents')
                            ->nullable(),

                        // Forms\Components\FileUpload::make('passport')
                        //     ->label(__('filament/resources.driver.form.passport_attachment'))
                        //     ->disk('local')
                        //     ->directory('driver_documents')
                        //     ->nullable(),

                        Forms\Components\FileUpload::make('criminal_case')
                            ->label(__('filament/resources.driver.form.criminal_case_attachment'))
                            ->disk('local')
                            ->directory('driver_documents')
                            ->nullable(),

                        Forms\Components\FileUpload::make('vehicle_registration')
                            ->label(__('filament/resources.driver.form.vehicle_registration_attachment'))
                            ->disk('local')
                            ->directory('driver_documents')
                            ->nullable(),

                        Forms\Components\FileUpload::make('vehicle_license')
                            ->label(__('filament/resources.driver.form.vehicle_license_attachment'))
                            ->disk('local')
                            ->directory('driver_documents')
                            ->nullable(),
                    ])
                    ->deletable(false)
                    ->columns(2),
            ]);
    }
}
