<?php

namespace App\Filament\Resources\ResourceRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResourceRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('record_key')->required(), TextInput::make('issue_area')->required(),
            TextInput::make('state')->required(), TextInput::make('geography_level'), TextInput::make('programme'),
            TextInput::make('funder_mda')->required(), TextInput::make('resource_category')->required(),
            TextInput::make('funding_stage'), TextInput::make('amount')->numeric()->required(),
            TextInput::make('currency')->default('NGN')->required(), TextInput::make('allocation_year')->numeric(),
            TextInput::make('period'), TextInput::make('source_name'), TextInput::make('source_url')->url(),
            TextInput::make('source_reference'), Textarea::make('notes')->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required()
                ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false),
        ])->columns(2);
    }
}
