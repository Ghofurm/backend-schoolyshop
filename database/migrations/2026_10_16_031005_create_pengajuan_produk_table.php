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
        Schema::create('pengajuan_produk', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->foreignId('id_penjual')->constrained('penjual', 'id_penjual')->onDelete('restrict');
            $table->foreignId('id_admin')->nullable()->constrained('admin', 'id_admin')->onDelete('restrict');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('restrict');
            $table->string('nama_produk');
            $table->text('deskripsi');
            $table->unsignedBigInteger('harga_usulan');
            $table->unsignedInteger('stok_usulan');
            $table->string('foto');
            $table->string('status_pengajuan')->default('menunggu'); // menunggu, disetujui, ditolak
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('tgl_pengajuan')->useCurrent();
            $table->timestamps();

            $table->index('status_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_produk');
    }
};
