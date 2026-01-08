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
        Schema::table('tiket', function (Blueprint $table) {
            $table->index('id_status', 'idx_tiket_status');
            
            $table->index('id_teknisi', 'idx_tiket_teknisi');
            
            $table->index('id_pengguna', 'idx_tiket_pengguna');
            
            $table->index('id_kategori', 'idx_tiket_kategori');
            
            $table->index('id_prioritas', 'idx_tiket_prioritas');
            
            $table->index('tanggal_dibuat', 'idx_tiket_tanggal');
            
            $table->index(['id_status', 'tanggal_dibuat'], 'idx_tiket_status_tanggal');
            
            $table->index(['id_teknisi', 'id_status'], 'idx_tiket_teknisi_status');
        });
        
        Schema::table('audit_trail', function (Blueprint $table) {
            $table->index('id_tiket', 'idx_audit_tiket');
            $table->index('timestamp', 'idx_audit_timestamp');
        });
        
        Schema::table('komentar', function (Blueprint $table) {
            $table->index('id_tiket', 'idx_komentar_tiket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropIndex('idx_tiket_status');
            $table->dropIndex('idx_tiket_teknisi');
            $table->dropIndex('idx_tiket_pengguna');
            $table->dropIndex('idx_tiket_kategori');
            $table->dropIndex('idx_tiket_prioritas');
            $table->dropIndex('idx_tiket_tanggal');
            $table->dropIndex('idx_tiket_status_tanggal');
            $table->dropIndex('idx_tiket_teknisi_status');
        });
        
        Schema::table('audit_trail', function (Blueprint $table) {
            $table->dropIndex('idx_audit_tiket');
            $table->dropIndex('idx_audit_timestamp');
        });
        
        Schema::table('komentar', function (Blueprint $table) {
            $table->dropIndex('idx_komentar_tiket');
        });
    }
};
