<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Usamos 'text' temporalmente para facilitar la migración
     * de datos desde MySQL con DBeaver.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cambiamos json por text para evitar el error de tipado 42804
            $table->text('permissions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
