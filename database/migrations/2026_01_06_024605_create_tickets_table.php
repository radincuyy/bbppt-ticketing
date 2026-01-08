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
        Schema::create('tiket', function (Blueprint $table) {
            $table->id('id_tiket');
            $table->unsignedBigInteger('id_pengguna'); // Requester
            $table->unsignedBigInteger('id_teknisi')->nullable(); // Assigned technician
            $table->unsignedBigInteger('id_status');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_prioritas');
            $table->string('nomor_tiket')->unique();
            $table->string('judul');
            $table->text('deskripsi');
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_diperbarui')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign Keys
            $table->foreign('id_pengguna')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_teknisi')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_status')->references('id_status')->on('status')->onDelete('restrict');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('restrict');
            $table->foreign('id_prioritas')->references('id_prioritas')->on('prioritas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};
