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
        Schema::create('pengembalian_dana', function (Blueprint $table) {
            $table->id('refund_id');
            $table->foreignId('retur_id')->constrained('retur', 'retur_id')->onDelete('restrict');
            $table->foreignId('pembayaran_id')->constrained('pembayaran', 'id_pembayaran')->onDelete('restrict');
            $table->unsignedBigInteger('nominal_barang');
            $table->unsignedBigInteger('potongan_biaya_retur')->default(0);
            $table->unsignedBigInteger('nominal_ongkir_dikembalikan')->default(0);
            $table->unsignedBigInteger('nominal_refund');
            $table->string('metode_refund'); // midtrans, manual
            $table->string('refund_id_midtrans')->nullable();
            $table->string('rekening_tujuan')->nullable();
            $table->string('status')->default('diproses'); // diproses, selesai, selesai_manual, gagal
            $table->timestamp('tanggal_proses')->nullable();
            $table->timestamps();

            $table->index('retur_id');
            $table->index('pembayaran_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_dana');
    }
};
