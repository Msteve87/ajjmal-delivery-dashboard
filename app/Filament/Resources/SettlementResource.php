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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverSubOrdersExport;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettlementResource\Pages;
use App\Filament\Resources\SettlementResource\RelationManagers;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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

                TextColumn::make('driver')
                    ->getStateUsing(fn($record) => $record->driver ? $record->driver->first_name . ' ' . $record->driver->last_name : '-')
                    ->searchable(),

                TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\ViewAction::make(),

                Action::make('export_excel')
                    ->label('Export to Excel')
                    ->button()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {

                        $subOrders = SubOrder::where('settlement_id', $record->id)->get();

                        $fileName = 'driver_' . $record->id . '_suborders_' . now()->format('Ymd_His') . '.pdf';

                        return Excel::download(new DriverSubOrdersExport($subOrders), $fileName, \Maatwebsite\Excel\Excel::MPDF);

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
