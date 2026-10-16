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
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->string('aktor_tipe');
            $table->unsignedBigInteger('aktor_id')->nullable();
            $table->string('aksi');
            $table->text('deskripsi');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(['aktor_tipe', 'aktor_id']);
            $table->index('aksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
