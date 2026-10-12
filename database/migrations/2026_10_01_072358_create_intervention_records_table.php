<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('intervention_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_key')->unique();
            $table->string('issue_area');
            $table->string('state');
            $table->string('geography_level')->default('State');
            $table->string('name');
            $table->string('intervention_type');
            $table->string('implementer')->nullable();
            $table->string('target_population')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('coverage_geography')->nullable();
            $table->string('implementation_status')->nullable();
            $table->text('description')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['issue_area', 'state', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_records');
    }
};
