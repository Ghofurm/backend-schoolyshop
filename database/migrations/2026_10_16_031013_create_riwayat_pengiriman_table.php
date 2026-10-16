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
        Schema::create('riwayat_pengiriman', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_pengiriman')->constrained('pengiriman', 'id_pengiriman')->onDelete('cascade');
            $table->string('status');
            $table->string('keterangan');
            $table->timestamp('waktu_update')->useCurrent();
            $table->timestamps();

            $table->index('id_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengiriman');
    }
};
