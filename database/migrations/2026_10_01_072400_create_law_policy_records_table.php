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
        Schema::create('law_policy_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_key')->unique();
            $table->string('issue_area');
            $table->string('state')->nullable();
            $table->string('geography_level')->default('State');
            $table->string('instrument_title');
            $table->string('instrument_category');
            $table->string('law_policy_family')->nullable();
            $table->string('jurisdiction')->default('State');
            $table->string('status_label');
            $table->string('enactment_date')->nullable();
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
        Schema::dropIfExists('law_policy_records');
    }
};
