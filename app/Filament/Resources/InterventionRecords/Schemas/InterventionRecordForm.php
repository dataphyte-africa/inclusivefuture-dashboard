<?php

namespace App\Filament\Resources\InterventionRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InterventionRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('record_key')->required(), TextInput::make('issue_area')->required(),
            TextInput::make('state')->required(), TextInput::make('geography_level'), TextInput::make('name')->required(),
            TextInput::make('intervention_type')->required(), TextInput::make('implementer'),
            TextInput::make('target_population'), TextInput::make('start_date'), TextInput::make('end_date'),
            TextInput::make('coverage_geography'), TextInput::make('implementation_status'),
            Textarea::make('description')->columnSpanFull(), TextInput::make('source_name'),
            TextInput::make('source_url')->url(), TextInput::make('source_reference'),
            Textarea::make('notes')->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required()
                ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false),
        ])->columns(2);
    }
}
