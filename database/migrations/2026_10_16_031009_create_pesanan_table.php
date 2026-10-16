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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->foreignId('id_user')->constrained('user', 'id_user')->onDelete('restrict');
            $table->foreignId('id_alamat')->constrained('alamat', 'id_alamat')->onDelete('restrict');
            $table->string('kode_pesanan')->unique();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('ongkir');
            $table->unsignedBigInteger('total_bayar');
            $table->string('status_pesanan')->default('menunggu_pembayaran');
            $table->string('jenis_konfirmasi')->nullable(); // manual, otomatis
            $table->string('kurir_dipilih');
            $table->string('layanan_dipilih');
            $table->timestamp('tgl_pesanan')->useCurrent();
            $table->timestamp('tgl_selesai')->nullable();
            $table->timestamps();

            $table->index('status_pesanan');
            $table->index('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
