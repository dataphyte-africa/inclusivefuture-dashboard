<?php

namespace App\Filament\Resources\InterventionRecords;

use App\Filament\Resources\InterventionRecords\Pages\CreateInterventionRecord;
use App\Filament\Resources\InterventionRecords\Pages\EditInterventionRecord;
use App\Filament\Resources\InterventionRecords\Pages\ListInterventionRecords;
use App\Filament\Resources\InterventionRecords\Schemas\InterventionRecordForm;
use App\Filament\Resources\InterventionRecords\Tables\InterventionRecordsTable;
use App\Models\InterventionRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InterventionRecordResource extends Resource
{
    protected static ?string $model = InterventionRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return InterventionRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InterventionRecordsTable::configure($table);
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
            'index' => ListInterventionRecords::route('/'),
            'create' => CreateInterventionRecord::route('/create'),
            'edit' => EditInterventionRecord::route('/{record}/edit'),
        ];
    }
}
