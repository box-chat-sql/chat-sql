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
        Schema::create('ask_database', function (Blueprint $table) {
            $table->id();
            $table->string('schema');
            $table->ipAddress('visitor');
            $table->text('data');
            $table->text('data_convert');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ask_database');
    }
};
