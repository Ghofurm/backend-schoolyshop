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
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->foreignId('id_user')->constrained('user', 'id_user')->onDelete('cascade');
            $table->string('nama_penerima');
            $table->string('no_hp_penerima');
            $table->text('alamat_lengkap');
            $table->string('area_id_biteship');
            $table->string('kode_pos');
            $table->boolean('alamat_utama')->default(false);
            $table->timestamps();

            $table->index('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};
