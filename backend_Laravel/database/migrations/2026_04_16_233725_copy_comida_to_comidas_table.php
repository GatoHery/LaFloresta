<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Copiar datos de comida a comidas solo si la tabla comida existe
        if (Schema::hasTable('comida')) {
            DB::statement("INSERT INTO comidas (id, nombre, precio, tipo)
                         SELECT id, nombre, precio, tipo FROM comida");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar datos copiados solo si ambas tablas existen
        if (Schema::hasTable('comida') && Schema::hasTable('comidas')) {
            DB::statement("DELETE FROM comidas WHERE id IN (SELECT id FROM comida)");
        }
    }
};
