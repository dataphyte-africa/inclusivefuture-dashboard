<?php

namespace App\Filament\Resources\LawPolicyRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LawPolicyRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('record_key')->required(), TextInput::make('issue_area')->required(),
            TextInput::make('state'), TextInput::make('geography_level'), TextInput::make('instrument_title')->required(),
            TextInput::make('instrument_category')->required(), TextInput::make('law_policy_family'),
            TextInput::make('jurisdiction')->default('State')->required(), TextInput::make('status_label')->required(),
            TextInput::make('enactment_date'), TextInput::make('source_name'), TextInput::make('source_url')->url(),
            TextInput::make('source_reference'), Textarea::make('notes')->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required()
                ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false),
        ])->columns(2);
    }
}
