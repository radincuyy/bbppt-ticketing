@extends('layouts.app')

@section('title', $tiket->nomor_tiket)
@section('header', 'Detail Tiket')
@section('subheader', $tiket->nomor_tiket)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Ticket Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-sm font-mono text-blue-600">{{ $tiket->nomor_tiket }}</span>
                        <h2 class="text-xl font-bold text-slate-800 mt-1">{{ $tiket->judul }}</h2>
                        <div class="flex flex-wrap items-center gap-4 mt-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500">Status:</span>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $tiket->status->color }}20; color: {{ $tiket->status->color }}">
                                    {{ $tiket->status->nama_status }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500">Prioritas:</span>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $tiket->prioritas->color }}20; color: {{ $tiket->prioritas->color }}">
                                    {{ $tiket->prioritas->nama_prioritas }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-500">Kategori:</span>
                                <span class="text-sm font-medium text-slate-700">{{ $tiket->kategori->nama_kategori }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-sm font-medium text-slate-500 mb-3">Deskripsi</h3>
                <div class="prose prose-slate max-w-none">
                    {!! nl2br(e($tiket->deskripsi)) !!}
                </div>
                
                @if($tiket->lampiran->count() > 0)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-medium text-slate-500 mb-3">Lampiran</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($tiket->lampiran as $lampiran)
                        <a href="{{ Storage::url($lampiran->path_file) }}" 
                           target="_blank"
                           class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                            @if($lampiran->isImage())
                            <img src="{{ Storage::url($lampiran->path_file) }}" alt="{{ $lampiran->nama_file }}" class="w-10 h-10 object-cover rounded">
                            @else
                            <div class="w-10 h-10 rounded bg-slate-200 flex items-center justify-center">
                                <i class="ti ti-file text-xl text-slate-500"></i>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate">{{ $lampiran->nama_file }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Komentar & Aktivitas</h3>
            </div>
            
            <div class="divide-y divide-slate-100">
                @forelse($komentars as $komentar)
                <div class="p-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                            {{ strtoupper(substr($komentar->pengguna->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-medium text-slate-800">{{ $komentar->pengguna->name }}</span>
                                <span class="text-xs text-slate-400">{{ $komentar->tanggal_kirim->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-600">{!! nl2br(e($komentar->isi_komentar)) !!}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <i class="ti ti-messages text-6xl text-slate-200 mb-3"></i>
                    <p class="text-slate-500">Belum ada komentar</p>
                </div>
                @endforelse
            </div>

            <!-- Add Comment Form -->
            @if($tiket->status->nama_status !== 'Closed')
            <div class="p-6 border-t border-slate-100 bg-slate-50">
                <form action="{{ route('komentar.store', $tiket->id_tiket) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <textarea name="isi_komentar" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                  placeholder="Tulis komentar atau balasan..."
                                  required></textarea>
                        
                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition inline-flex items-center gap-2">
                                <i class="ti ti-send"></i>
                                Kirim
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Ticket Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Informasi Tiket</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase">Pemohon</span>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                            {{ strtoupper(substr($tiket->pengguna->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $tiket->pengguna->name }}</p>
                            <p class="text-xs text-slate-500">{{ $tiket->pengguna->email }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-medium text-slate-400 uppercase">Ditugaskan Ke</span>
                    @if($tiket->teknisi)
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-xs font-semibold">
                            {{ strtoupper(substr($tiket->teknisi->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $tiket->teknisi->name }}</p>
                            <p class="text-xs text-slate-500">
                                @if($tiket->teknisi->hasRole('Helpdesk'))
                                    Staff Helpdesk
                                @elseif($tiket->teknisi->hasRole('Technician'))
                                    Staff Teknisi
                                @else
                                    {{ $tiket->teknisi->getRoleNames()->first() ?? 'Staff' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-slate-500 mt-1 italic">Belum ditugaskan</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase">Dibuat</span>
                        <p class="text-sm text-slate-700 mt-1">{{ $tiket->tanggal_dibuat->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-slate-400 uppercase">Terakhir Update</span>
                        <p class="text-sm text-slate-700 mt-1">{{ $tiket->tanggal_diperbarui->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($tiket->status->nama_status !== 'Closed')
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Aksi</h3>
            
            <div class="space-y-3">
                @can('tickets.assign')
                <!-- Assign Form -->
                <form action="{{ route('tickets.assign', $tiket->id_tiket) }}" method="POST" class="space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700">Tugaskan ke Staff</label>
                    <select name="id_teknisi" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Pilih Staff</option>
                        @if($helpdeskStaff->count() > 0)
                        <optgroup label="Staff Helpdesk">
                            @foreach($helpdeskStaff as $staff)
                            <option value="{{ $staff->id }}" {{ $tiket->id_teknisi == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                        @if($technicians->count() > 0)
                        <optgroup label="Staff Teknisi">
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ $tiket->id_teknisi == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-user-plus"></i>
                        Tugaskan
                    </button>
                </form>
                @endcan

                @if(Auth::user()->can('tickets.update.all') || (Auth::user()->can('tickets.update.assigned') && $tiket->id_teknisi === Auth::id()))
                <!-- Update Status -->
                <form action="{{ route('tickets.update', $tiket->id_tiket) }}" method="POST" class="space-y-4 pt-3 border-t border-slate-100">
                    @csrf
                    @method('PUT')
                    
                    <!-- Status (Closed dan Menunggu Persetujuan hanya via tombol khusus) -->
                    @if(Auth::user()->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI']))
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select name="id_kategori" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($kategoris as $cat)
                            <option value="{{ $cat->id_kategori }}" {{ $tiket->id_kategori == $cat->id_kategori ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prioritas</label>
                        <select name="id_prioritas" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($prioritass as $prio)
                            <option value="{{ $prio->id_prioritas }}" {{ $tiket->id_prioritas == $prio->id_prioritas ? 'selected' : '' }}>
                                {{ $prio->nama_prioritas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="id_status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($statuses as $status)
                                @if(!in_array($status->nama_status, ['Closed', 'Menunggu Persetujuan']))
                                <option value="{{ $status->id_status }}" {{ $tiket->id_status == $status->id_status ? 'selected' : '' }}>
                                    {{ $status->nama_status }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy"></i>
                        Update
                    </button>
                </form>
                @endif

                <!-- Request Approval -->
                @if(Auth::user()->hasAnyRole(['Helpdesk', 'Technician']))
                <form action="{{ route('tickets.request-approval', $tiket->id_tiket) }}" method="POST" class="pt-3 border-t border-slate-100">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Ajukan persetujuan ke Manager?')"
                            class="w-full px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-send"></i>
                        Ajukan Persetujuan
                    </button>
                </form>
                @endif

                <!-- Close Ticket -->
                @if(Auth::user()->can('tickets.close.all') || (Auth::user()->can('tickets.close.own') && $tiket->id_pengguna === Auth::id()))
                <form action="{{ route('tickets.close', $tiket->id_tiket) }}" method="POST" class="pt-3 border-t border-slate-100">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menutup tiket ini?')"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition inline-flex items-center justify-center gap-2">
                        <i class="ti ti-x"></i>
                        Tutup Tiket
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <a href="{{ route('tickets.index') }}" class="block text-center px-4 py-3 bg-slate-100 text-slate-600 font-medium rounded-xl hover:bg-slate-200 transition inline-flex items-center justify-center gap-2">
            <i class="ti ti-arrow-left"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
