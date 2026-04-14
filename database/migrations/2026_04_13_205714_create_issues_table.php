<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla principal unificada
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            // Discriminador: mnt, it, tickets, sst, hr, etc.
            $table->string('module_type', 20)->index();
            $table->unsignedBigInteger('legacy_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('assignee_id')->nullable()->index();
            $table->unsignedBigInteger('asset_id')->nullable()->index();

            $table->text('facility');
            $table->text('kind');
            $table->string('subtype', 50)->default('Corrective');
            $table->string('priority', 20);
            $table->text('description');
            $table->string('status', 10)->default('Open');
            $table->integer('rating')->default(0);
            $table->text('root_cause')->nullable();

            // Campo JSONB para flexibilidad total (industrial-grade)
            $table->jsonb('metadata')->nullable();

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('closed_at')->nullable();
        });

        // Tabla de ítems de gestión
        Schema::create('issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->text('notes')->nullable();
            $table->string('attends', 20)->nullable();
            $table->string('complexity', 20)->nullable();
            $table->integer('duration')->nullable();
            $table->string('attendant', 100)->nullable();

            $table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_items');
        Schema::dropIfExists('issues');
    }
};
