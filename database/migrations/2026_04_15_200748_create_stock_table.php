<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Catálogo Maestro
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable()->index(); 
            $table->string('kind')->index(); // 'EPP', 'EQUIPMENT', 'INVENTORY'
            
            $table->string('name'); 
            $table->bigInteger('code')->nullable()->index();
            
            $table->decimal('initial_stock', 12, 2)->default(0); // Punto de partida histórico
            $table->integer('min_stock')->nullable()->default(0);
            $table->string('area', 100)->nullable();
            
            $table->timestamp('created_at', 0)->useCurrent();
            $table->string('temp_source', 20)->nullable(); 
        });

        // 2. Transacciones
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stock')->cascadeOnDelete();
            
            $table->string('movement', 10)->index(); // 'IN' o 'OUT'
            $table->decimal('qty', 12, 2);
            $table->decimal('unit_price', 15, 2)->default(0);
            
            $table->integer('user_id')->nullable()->index();
            $table->integer('employee_id')->nullable()->index(); 
            $table->bigInteger('order_id')->nullable()->index(); 
            
            $table->text('notes')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_nit', 50)->nullable();
            $table->string('img_path')->nullable(); 
            
            $table->timestamp('created_at', 0)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('stock');
    }
};