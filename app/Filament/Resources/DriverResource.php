<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Driver;
use Filament\Forms\Form;
use App\Rules\MinAgeRule;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\DriverResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DriverResource\RelationManagers;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getModelLabel(): string
    {
        return __('filament/resources.driver.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources.driver.plural_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament/resources.driver.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label(__('filament/resources.driver.schema.first_name'))
                    ->required(),

                Forms\Components\TextInput::make('last_name')
                    ->label(__('filament/resources.driver.schema.last_name'))
                    ->required(),

                Forms\Components\TextInput::make('phone')
                    ->label(__('filament/resources.driver.schema.phone'))
                    ->unique()
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label(__('filament/resources.driver.schema.password'))
                    ->password()
                    ->required(),

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
                        'employee' => 'Employee',
                        'independent' => 'Independent',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('dob')
                    ->label(__('filament/resources.driver.schema.date_of_birth'))
                    ->required()
                    ->rules([
                        'date',
                        new MinAgeRule(18),
                    ]),

                Forms\Components\TextInput::make('passport_no')
                    ->label(__('filament/resources.driver.schema.passport_number'))
                    ->nullable(),

                Forms\Components\TextInput::make('criminal_case')
                    ->label(__('filament/resources.driver.schema.criminal_case'))
                    ->nullable(),

                Forms\Components\TextInput::make('national_no')
                    ->label(__('filament/resources.driver.schema.national_number'))
                    ->nullable(),

                Forms\Components\Select::make('delivery_status')
                    ->label(__('filament/resources.driver.schema.delivery_status'))
                    ->options([
                        'available' => 'Available',
                        'not_available' => 'Not Available',
                    ])
                    ->default('available'),

                Forms\Components\Select::make('status')
                    ->label(__('filament/resources.driver.schema.status'))
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('filament/resources.driver.schema.is_active'))
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Id'),

                TextColumn::make('national_no')
                    ->label('National No'),


                TextColumn::make('full_name')
                    ->label('Driver Name')
                    ->getStateUsing(function ($record) {
                        return $record->first_name . ' ' . $record->last_name;
                    }),

                TextColumn::make('phone')
                    ->label('Phone'),

                TextColumn::make('gender')
                    ->label('Gender'),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('driver_type')
                    ->label('Driver Type'),

                TextColumn::make('delivery_status')
                    ->label('Delivery Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'not_available' => 'warning',
                    }),
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
