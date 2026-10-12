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
        Schema::create('resource_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_key')->unique();
            $table->string('issue_area');
            $table->string('state');
            $table->string('geography_level')->default('State');
            $table->string('programme')->nullable();
            $table->string('funder_mda');
            $table->string('resource_category');
            $table->string('funding_stage')->nullable();
            $table->decimal('amount', 18, 2);
            $table->char('currency', 3)->default('NGN');
            $table->unsignedSmallInteger('allocation_year')->nullable();
            $table->string('period')->nullable();
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
        Schema::dropIfExists('resource_records');
    }
};
