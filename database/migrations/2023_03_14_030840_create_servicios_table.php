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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estado_servicio_id')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('precio');
            $table->point('origen');
            $table->point('destino');
            $table->timestamps();

            $table->foreign('estado_servicio_id')->references('id')->on('estados_servicios');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
