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
        Schema::create('servicios_conductores', function (Blueprint $table) {
            $table->bigInteger('servicio_id')->unsigned()->unique();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('servicio_id')->references('id')->on('servicios');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_conductores');
    }
};
