<?php

namespace App\Filament\Resources\LawPolicyRecords;

use App\Filament\Resources\LawPolicyRecords\Pages\CreateLawPolicyRecord;
use App\Filament\Resources\LawPolicyRecords\Pages\EditLawPolicyRecord;
use App\Filament\Resources\LawPolicyRecords\Pages\ListLawPolicyRecords;
use App\Filament\Resources\LawPolicyRecords\Schemas\LawPolicyRecordForm;
use App\Filament\Resources\LawPolicyRecords\Tables\LawPolicyRecordsTable;
use App\Models\LawPolicyRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LawPolicyRecordResource extends Resource
{
    protected static ?string $model = LawPolicyRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LawPolicyRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LawPolicyRecordsTable::configure($table);
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
            'index' => ListLawPolicyRecords::route('/'),
            'create' => CreateLawPolicyRecord::route('/create'),
            'edit' => EditLawPolicyRecord::route('/{record}/edit'),
        ];
    }
}
