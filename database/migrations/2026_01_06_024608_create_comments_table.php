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
        Schema::create('komentar', function (Blueprint $table) {
            $table->id('id_komentar');
            $table->unsignedBigInteger('id_tiket');
            $table->unsignedBigInteger('id_pengguna');
            $table->text('isi_komentar');
            $table->timestamp('tanggal_kirim')->useCurrent();
            
            // Foreign Keys
            $table->foreign('id_tiket')->references('id_tiket')->on('tiket')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentar');
    }
};
