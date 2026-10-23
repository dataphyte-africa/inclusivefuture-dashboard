<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_intervention_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('intervention_record_id')->constrained()->cascadeOnDelete();
            $table->string('relationship_type', 60);
            $table->string('source_reference')->nullable();
            $table->timestamps();
            $table->unique(['issue_record_id', 'intervention_record_id'], 'issue_intervention_unique');
        });

        Schema::create('issue_resource_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_record_id')->constrained()->cascadeOnDelete();
            $table->string('relationship_type', 60);
            $table->string('source_reference')->nullable();
            $table->timestamps();
            $table->unique(['issue_record_id', 'resource_record_id'], 'issue_resource_unique');
        });

        Schema::create('issue_law_policy_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('law_policy_record_id')->constrained()->cascadeOnDelete();
            $table->string('relationship_type', 60);
            $table->string('source_reference')->nullable();
            $table->timestamps();
            $table->unique(['issue_record_id', 'law_policy_record_id'], 'issue_law_policy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_law_policy_links');
        Schema::dropIfExists('issue_resource_links');
        Schema::dropIfExists('issue_intervention_links');
    }
};
