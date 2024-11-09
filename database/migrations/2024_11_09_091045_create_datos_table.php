<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dato', function (Blueprint $table) {
            $table->id(); // Llave primaria
            $table->integer('id_catalogo'); // Llave foránea a catalogo
            $table->unsignedBigInteger('id_partida'); // Llave foránea a partida
            $table->double('debe', 15, 2)->nullable(); // Campo debe
            $table->double('haber', 15, 2)->nullable(); // Campo haber
            $table->timestamps(); // created_at y updated_at
        
            // Definir llaves foráneas
            $table->foreign('id_catalogo')->references('codigo')->on('catalogo')->onDelete('no action');
            $table->foreign('id_partida')->references('id')->on('partida')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dato');
    }
};
