<?php

namespace App\Filament\Resources\ResourceRecords;

use App\Filament\Resources\ResourceRecords\Pages\CreateResourceRecord;
use App\Filament\Resources\ResourceRecords\Pages\EditResourceRecord;
use App\Filament\Resources\ResourceRecords\Pages\ListResourceRecords;
use App\Filament\Resources\ResourceRecords\Schemas\ResourceRecordForm;
use App\Filament\Resources\ResourceRecords\Tables\ResourceRecordsTable;
use App\Models\ResourceRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResourceRecordResource extends Resource
{
    protected static ?string $model = ResourceRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ResourceRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResourceRecordsTable::configure($table);
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
            'index' => ListResourceRecords::route('/'),
            'create' => CreateResourceRecord::route('/create'),
            'edit' => EditResourceRecord::route('/{record}/edit'),
        ];
    }
}
