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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pesanan')->unique()->constrained('pesanan', 'id_pesanan')->onDelete('restrict');
            $table->string('order_id_midtrans')->unique();
            $table->string('metode_pembayaran')->nullable();
            $table->unsignedBigInteger('jumlah_bayar');
            $table->string('status_pembayaran')->default('menunggu');
            $table->timestamp('batas_bayar');
            $table->timestamp('waktu_bayar')->nullable();
            $table->timestamps();

            $table->index('status_pembayaran');
            $table->index('batas_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
