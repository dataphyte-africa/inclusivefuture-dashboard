<?php

namespace App\Filament\Resources\IssueRecords;

use App\Filament\Resources\IssueRecords\Pages\CreateIssueRecord;
use App\Filament\Resources\IssueRecords\Pages\EditIssueRecord;
use App\Filament\Resources\IssueRecords\Pages\ListIssueRecords;
use App\Filament\Resources\IssueRecords\Schemas\IssueRecordForm;
use App\Filament\Resources\IssueRecords\Tables\IssueRecordsTable;
use App\Models\IssueRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IssueRecordResource extends Resource
{
    protected static ?string $model = IssueRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return IssueRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IssueRecordsTable::configure($table);
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
            'index' => ListIssueRecords::route('/'),
            'create' => CreateIssueRecord::route('/create'),
            'edit' => EditIssueRecord::route('/{record}/edit'),
        ];
    }
}
