<?php

namespace App\Filament\Resources\IssueRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IssueRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('record_key')->required(), TextInput::make('issue_area')->required(),
            TextInput::make('indicator')->required(), TextInput::make('state')->required(),
            TextInput::make('geography_level'), TextInput::make('value')->numeric(), TextInput::make('unit'),
            TextInput::make('population_group'), TextInput::make('disaggregation'), TextInput::make('period'),
            TextInput::make('data_type'), TextInput::make('source_name'), TextInput::make('source_url')->url(),
            TextInput::make('source_reference'), Textarea::make('notes')->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required()
                ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false),
        ])->columns(2);
    }
}
