<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eim_record_relationships', function (Blueprint $table) {
            $table->id();
            $table->char('relationship_key', 64)->unique();
            $table->string('from_record_type', 40);
            $table->string('from_record_key', 120);
            $table->string('to_record_type', 40);
            $table->string('to_record_key', 120);
            $table->string('relationship_type', 80);
            $table->string('evidence_status', 80)->nullable();
            $table->string('period', 100)->nullable();
            $table->string('source_name')->nullable();
            $table->text('source_url')->nullable();
            $table->text('source_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['from_record_type', 'from_record_key']);
            $table->index(['to_record_type', 'to_record_key']);
        });

        Schema::create('eim_actors', function (Blueprint $table) {
            $table->id();
            $table->string('actor_key', 120)->unique();
            $table->string('actor_name');
            $table->string('actor_type', 120);
            $table->string('jurisdiction', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('eim_actor_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained('eim_actors')->cascadeOnDelete();
            $table->string('role', 180);
            $table->string('linked_record_type', 40);
            $table->string('linked_record_key', 120);
            $table->string('issue_area', 160)->nullable();
            $table->string('funding_stage', 100)->nullable();
            $table->string('source_name')->nullable();
            $table->text('source_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['actor_id', 'role', 'linked_record_type', 'linked_record_key'], 'eim_actor_link_unique');
            $table->index(['linked_record_type', 'linked_record_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eim_actor_links');
        Schema::dropIfExists('eim_actors');
        Schema::dropIfExists('eim_record_relationships');
    }
};
