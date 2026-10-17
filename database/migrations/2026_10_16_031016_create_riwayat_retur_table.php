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
        Schema::create('riwayat_retur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_id')->constrained('retur', 'retur_id')->onDelete('cascade');
            $table->string('status');
            $table->string('keterangan')->nullable();
            $table->timestamp('waktu_update')->useCurrent();
            $table->timestamps();

            $table->index('retur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_retur');
    }
};
