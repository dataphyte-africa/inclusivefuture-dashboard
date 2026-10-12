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
        Schema::create('issue_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_key')->unique();
            $table->string('issue_area');
            $table->string('indicator');
            $table->string('state');
            $table->string('geography_level')->default('State');
            $table->decimal('value', 16, 4)->nullable();
            $table->string('unit')->nullable();
            $table->string('population_group')->nullable();
            $table->string('disaggregation')->nullable();
            $table->string('period')->nullable();
            $table->string('data_type')->nullable();
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
        Schema::dropIfExists('issue_records');
    }
};
