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
        Schema::create('retur', function (Blueprint $table) {
            $table->id('retur_id');
            $table->foreignId('pesanan_id')->constrained('pesanan', 'id_pesanan')->onDelete('restrict');
            $table->foreignId('detail_pesanan_id')->constrained('detail_pesanan', 'id_detail')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('user', 'id_user')->onDelete('restrict');
            $table->foreignId('admin_id')->nullable()->constrained('admin', 'id_admin')->onDelete('restrict');
            $table->unsignedInteger('jumlah');
            $table->string('kategori_alasan'); // kesalahan_unit, alasan_user
            $table->text('keterangan');
            $table->json('bukti_foto');
            $table->string('status')->default('menunggu_verifikasi');
            $table->text('alasan_keputusan')->nullable();
            $table->unsignedBigInteger('biaya_retur')->nullable();
            $table->string('penanggung_biaya')->nullable(); // unit, user
            $table->string('biteship_order_id_retur')->nullable();
            $table->string('nomor_resi_retur')->nullable();
            $table->string('kondisi_barang')->nullable();
            $table->boolean('layak_jual')->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_diterima')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('pesanan_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
