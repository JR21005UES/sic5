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
        Schema::create('catalogo', function (Blueprint $table) {
            $table->integer('codigo')->primary();
            $table->timestamps();
            $table->string('nombre');
            $table->string('descripcion');
            $table->unsignedBigInteger('naturaleza_id');
            
            // Definir la clave foránea
            $table->foreign('naturaleza_id')->references('id')->on('naturaleza')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo');
    }
};