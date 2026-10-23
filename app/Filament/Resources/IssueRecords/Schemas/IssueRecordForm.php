<?php

namespace App\Filament\Resources\IssueRecords\Schemas;

use Filament\Forms\Components\Repeater;
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
            Repeater::make('interventionLinks')->relationship()->label('Direct intervention relationships')->schema([
                Select::make('intervention_record_id')->relationship('interventionRecord', 'name', modifyQueryUsing: fn ($query) => $query->where('status', 'published'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->record_key} · {$record->name} · {$record->state}")
                    ->searchable()->preload()->required(),
                Select::make('relationship_type')->options(['responds_to' => 'Responds to this issue record', 'delivers_for' => 'Delivers services for this issue record'])->required(),
                TextInput::make('source_reference')->label('Relationship source / reference')->required(),
            ])->columns(3)->columnSpanFull(),
            Repeater::make('resourceLinks')->relationship()->label('Direct resource relationships')->schema([
                Select::make('resource_record_id')->relationship('resourceRecord', 'programme', modifyQueryUsing: fn ($query) => $query->where('status', 'published'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->record_key} · ".($record->programme ?: $record->resource_category)." · {$record->state}")
                    ->searchable()->preload()->required(),
                Select::make('relationship_type')->options(['funds' => 'Funds this issue response', 'supports' => 'Supports this issue response'])->required(),
                TextInput::make('source_reference')->label('Relationship source / reference')->required(),
            ])->columns(3)->columnSpanFull(),
            Repeater::make('lawPolicyLinks')->relationship()->label('Direct law and policy relationships')->schema([
                Select::make('law_policy_record_id')->relationship('lawPolicyRecord', 'instrument_title', modifyQueryUsing: fn ($query) => $query->where('status', 'published'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->record_key} · {$record->instrument_title} · {$record->jurisdiction}")
                    ->searchable()->preload()->required(),
                Select::make('relationship_type')->options(['governs' => 'Governs this issue record', 'addresses' => 'Addresses this issue record'])->required(),
                TextInput::make('source_reference')->label('Relationship source / reference')->required(),
            ])->columns(3)->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required()
                ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false),
        ])->columns(2);
    }
}
