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
        Schema::create('naturaleza', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('es_cuenta_r');
            $table->boolean('deudor_acreedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naturaleza'); 
    }
};
