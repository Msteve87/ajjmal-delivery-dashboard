<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\SubOrder;
use Filament\Forms\Form;
use App\Models\Settlement;
use Filament\Tables\Table;
use Doctrine\DBAL\Schema\Column;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverSubOrdersExport;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettlementResource\Pages;
use App\Filament\Resources\SettlementResource\RelationManagers;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getModelLabel(): string
    {
        return __('filament/resources.settlement.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources.settlement.plural_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament/resources.settlement.plural_label');
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
                TextColumn::make('id'),

                TextColumn::make('driver.first_name')
                    ->label('Driver')
                    ->formatStateUsing(fn($state, $record) => $record->driver?->first_name && $record->driver?->last_name
                        ? $record->driver->first_name . ' ' . $record->driver->last_name
                        : ($record->driver?->first_name ?? ($record->driver?->last_name ?? '-')))
                    ->searchable(['drivers.first_name', 'drivers.last_name']),

                TextColumn::make('created_at'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('From date'),
                        DatePicker::make('until')->label('To date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([

                Tables\Actions\ViewAction::make(),

                Action::make('export_excel')
                    ->label('Export to Excel')
                    ->button()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {

                        $subOrders = SubOrder::where('settlement_id', $record->id)->get();

                        //$fileName = 'driver_' . $record->id . '_suborders_' . now()->format('Ymd_His') . '.pdf';

                        //return Excel::download(new DriverSubOrdersExport($subOrders), $fileName, \Maatwebsite\Excel\Excel::MPDF);

                        return Excel::download(new DriverSubOrdersExport($subOrders),'settelemnts.xlsx', \Maatwebsite\Excel\Excel::XLSX);

                    })
                    ->color('success')
                    ->deselectRecordsAfterCompletion(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(false)
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSettlements::route('/'),
            'create' => Pages\CreateSettlement::route('/create'),
            'view' => Pages\ViewSettlement::route('/{record}/view')
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('add.settlement');
    }
}
