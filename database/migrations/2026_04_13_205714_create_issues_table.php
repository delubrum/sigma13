<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla Principal: issues
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            
            // Clasificación
            $table->integer('old_id')->nullable()->index(); // ID original informativo
            $table->string('kind')->index(); // 'IT' o el valor de mnt/tickets
            $table->string('category')->nullable(); // El dato específico que ya estaba en 'kind'
            
            $table->string('priority');
            $table->string('urgency', 100)->nullable();
            $table->string('facility');
            
            // Relaciones
            $table->integer('reporter_id')->index();
            $table->integer('assignee_id')->nullable()->index();
            $table->integer('asset_id')->nullable()->default(0)->index();
            
            // Contenido
            $table->text('description');
            $table->text('reason')->nullable();
            $table->text('root_cause')->nullable();
            $table->string('sgc_code', 100)->nullable();
            $table->string('reference_url', 100)->nullable();
            $table->text('resolution_notes')->nullable(); 
            
            // Estado y Feedback
            $table->string('status', 20)->nullable()->index();
            $table->string('complexity')->nullable();
            $table->integer('rating')->nullable()->default(0);
            
            // Tiempos
            $table->timestamp('created_at', 0)->useCurrent();
            $table->timestamp('assigned_at', 0)->nullable();
            $table->timestamp('started_at', 0)->nullable();
            $table->timestamp('verified_at', 0)->nullable(); 
            $table->timestamp('ended_at', 0)->nullable();
            $table->timestamp('closed_at', 0)->nullable();

            // Columna técnica temporal para el cruce de items
            $table->string('temp_source', 10)->nullable();
        });

        // 2. Tabla de Detalles: issue_items
        Schema::create('issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();
            $table->integer('user_id')->index();
            $table->timestamp('created_at', 0)->useCurrent();
            
            $table->text('notes')->nullable();
            $table->string('action_taken', 50)->nullable(); 
            $table->string('complexity', 50)->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('attendant_name', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_items');
        Schema::dropIfExists('issues');
    }
};