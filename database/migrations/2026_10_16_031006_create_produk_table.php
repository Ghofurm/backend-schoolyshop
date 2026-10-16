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
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('id_penjual')->constrained('penjual', 'id_penjual')->onDelete('restrict');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('restrict');
            $table->foreignId('id_pengajuan')->nullable()->unique()->constrained('pengajuan_produk', 'id_pengajuan')->onDelete('restrict');
            $table->string('nama_produk');
            $table->text('deskripsi');
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('stok');
            $table->unsignedInteger('berat_gram');
            $table->string('foto');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->index('status_aktif');
            $table->index('id_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
