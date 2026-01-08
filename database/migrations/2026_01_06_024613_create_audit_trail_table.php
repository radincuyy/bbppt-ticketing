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
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_tiket')->nullable();
            $table->unsignedBigInteger('id_pengguna')->nullable();
            $table->string('aktivitas');
            $table->timestamp('timestamp')->useCurrent();
            
            $table->foreign('id_tiket')->references('id_tiket')->on('tiket')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trail');
    }
};
