<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tiket extends Model
{
    protected $table = 'tiket';
    protected $primaryKey = 'id_tiket';
    
    // Disable default timestamps karena kita pakai nama custom
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'id_teknisi',
        'id_status',
        'id_kategori',
        'id_prioritas',
        'nomor_tiket',
        'judul',
        'deskripsi',
        'tanggal_dibuat',
        'tanggal_diperbarui',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
        'tanggal_diperbarui' => 'datetime',
    ];

    /**
     * Boot function untuk auto-generate nomor tiket.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tiket) {
            if (empty($tiket->nomor_tiket)) {
                $tiket->nomor_tiket = static::generateNomorTiket();
            }
            $tiket->tanggal_dibuat = now();
            $tiket->tanggal_diperbarui = now();
        });

        static::updating(function ($tiket) {
            $tiket->tanggal_diperbarui = now();
        });
    }

    /**
     * Generate nomor tiket unik (Format: TKT-YYYYMMDD-XXXX).
     */
    public static function generateNomorTiket(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $lastTiket = static::whereDate('tanggal_dibuat', now()->toDateString())
            ->orderBy('id_tiket', 'desc')
            ->first();
        
        $sequence = $lastTiket ? intval(substr($lastTiket->nomor_tiket, -4)) + 1 : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    // ==================== RELASI ====================

    /**
     * Relasi ke model Kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi ke model Prioritas.
     */
    public function prioritas(): BelongsTo
    {
        return $this->belongsTo(Prioritas::class, 'id_prioritas', 'id_prioritas');
    }

    /**
     * Relasi ke model Status.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'id_status', 'id_status');
    }

    /**
     * Relasi ke pengguna pembuat tiket (Pemohon).
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }

    /**
     * Relasi ke teknisi yang ditugaskan.
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_teknisi', 'id');
    }

    /**
     * Relasi ke komentar tiket.
     */
    public function komentar(): HasMany
    {
        return $this->hasMany(Komentar::class, 'id_tiket', 'id_tiket')->orderBy('tanggal_kirim');
    }

    /**
     * Relasi ke lampiran tiket.
     */
    public function lampiran(): HasMany
    {
        return $this->hasMany(Lampiran::class, 'id_tiket', 'id_tiket');
    }

    /**
     * Relasi ke jejak audit tiket.
     */
    public function auditTrail(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'id_tiket', 'id_tiket')->orderBy('timestamp', 'desc');
    }

    // ==================== METODE (Sesuai Class Diagram) ====================

    /**
     * Menutup tiket (Set status ke Closed).
     */
    public function menutupTiket(): void
    {
        $statusClosed = Status::where('nama_status', 'Closed')->first();
        if ($statusClosed) {
            $this->update(['id_status' => $statusClosed->id_status]);
        }
    }

    /**
     * Menambahkan komentar baru pada tiket.
     */
    public function menambahKomentar(int $idPengguna, string $isiKomentar): Komentar
    {
        return $this->komentar()->create([
            'id_pengguna' => $idPengguna,
            'isi_komentar' => $isiKomentar,
        ]);
    }

    /**
     * Menambahkan lampiran baru pada tiket.
     */
    public function menambahLampiran(string $namaFile, string $pathFile, string $tipeFile): Lampiran
    {
        return $this->lampiran()->create([
            'nama_file' => $namaFile,
            'path_file' => $pathFile,
            'tipe_file' => $tipeFile,
        ]);
    }

    /**
     * Mengubah status tiket.
     */
    public function merubahStatus(int $idStatus): void
    {
        $this->update(['id_status' => $idStatus]);
    }

    /**
     * Menetapkan teknisi untuk tiket ini.
     */
    public function menetapkanTeknisi(int $idTeknisi): void
    {
        $this->update(['id_teknisi' => $idTeknisi]);
    }

    /**
     * Mengajukan persetujuan ke Manager.
     */
    public function mengajukanPersetujuan(): void
    {
        $statusPending = Status::where('nama_status', 'Menunggu Persetujuan')->first();
        if ($statusPending) {
            $this->update(['id_status' => $statusPending->id_status]);
        }
    }

    // ==================== SCOPE QUERY ====================

    /**
     * Scope untuk memfilter tiket berdasarkan pengguna pembuat.
     */
    public function scopeByPengguna($query, $idPengguna)
    {
        return $query->where('id_pengguna', $idPengguna);
    }

    /**
     * Scope untuk memfilter tiket berdasarkan teknisi yang ditugaskan.
     */
    public function scopeByTeknisi($query, $idTeknisi)
    {
        return $query->where('id_teknisi', $idTeknisi);
    }

    /**
     * Scope untuk memfilter tiket yang belum ditugaskan (Unassigned).
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('id_teknisi');
    }
}
