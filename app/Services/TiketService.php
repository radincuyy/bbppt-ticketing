<?php

namespace App\Services;

use App\Models\Tiket;
use App\Models\Status;
use App\Models\AuditTrail;
use App\Models\Lampiran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TiketService
{
    /**
     * Membuat tiket baru beserta lampirannya.
     */
    public function createTiket(array $data, ?array $attachments = null): Tiket
    {
        // Set default status (Baru)
        $defaultStatus = Status::first();
        
        $tiketData = [
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'],
            'id_kategori' => $data['id_kategori'],
            'id_prioritas' => $data['id_prioritas'],
            'id_status' => $defaultStatus->id_status,
            'id_pengguna' => Auth::id(),
        ];

        // Create Tiket
        $tiket = Tiket::create($tiketData);

        // Handle Attachments
        if ($attachments) {
            foreach ($attachments as $file) {
                $this->storeAttachment($tiket, $file);
            }
        }

        // Audit Trail
        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Membuat tiket baru');

        return $tiket;
    }

    /**
     * Menyimpan file lampiran.
     */
    protected function storeAttachment(Tiket $tiket, $file): void
    {
        $path = $file->store('attachments/tiket', 'public');
        
        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        
        Lampiran::create([
            'id_tiket' => $tiket->id_tiket,
            'nama_file' => $safeName,
            'path_file' => $path,
            'tipe_file' => $file->getMimeType(),
        ]);
    }

    /**
     * Memperbarui data tiket.
     */
    public function updateTiket(Tiket $tiket, array $data): Tiket
    {
        $oldStatus = $tiket->id_status;
        
        $tiket->update($data);

        // Audit Trail jika status berubah
        if (isset($data['id_status']) && $data['id_status'] != $oldStatus) {
            $newStatus = Status::find($data['id_status']);
            AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Mengubah status menjadi: ' . $newStatus->nama_status);
        }

        return $tiket;
    }

    /**
     * Menugaskan teknisi ke tiket.
     */
    public function assignTeknisi(Tiket $tiket, string $teknisiId): void
    {
        $teknisi = User::findOrFail($teknisiId);
        $tiket->menetapkanTeknisi($teknisiId);

        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Menugaskan tiket ke: ' . $teknisi->name);
    }

    /**
     * Mengajukan persetujuan ke Manager.
     */
    public function requestApproval(Tiket $tiket): void
    {
        $tiket->mengajukanPersetujuan();
        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Mengajukan persetujuan Manager');
    }

    /**
     * Menyetujui tiket (Approve).
     */
    public function approveTiket(Tiket $tiket): void
    {
        // Ubah status ke "Dalam Proses"
        $dalamProsesStatus = Status::where('nama_status', 'Dalam Proses')->first();
        if ($dalamProsesStatus) {
            $tiket->merubahStatus($dalamProsesStatus->id_status);
        }

        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Menyetujui tiket');
    }

    /**
     * Menolak tiket
     */
    public function rejectTiket(Tiket $tiket, string $alasan): void
    {
        $tiket->menutupTiket();
        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Menolak tiket: ' . $alasan);
    }

    /**
     * Menambahkan komentar.
     */
    public function addKomentar(Tiket $tiket, string $isiKomentar): void
    {
        $tiket->menambahKomentar(Auth::id(), $isiKomentar);
        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Menambah komentar');
    }

    /**
     * Menutup tiket.
     */
    public function closeTiket(Tiket $tiket): void
    {
        $tiket->menutupTiket();
        AuditTrail::catat($tiket->id_tiket, Auth::id(), 'Menutup tiket');
    }
}
