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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->integer('statingPoint')
                ->references('id')->on('governorates')
                ->onDelete('cascade');
            $table->integer('targetPoint')
                ->references('id')->on('governorates')
                ->onDelete('cascade');
            $table->integer('numberPassengers');
            $table->dateTime('startingTime');
            $table->dateTime('endingTime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
