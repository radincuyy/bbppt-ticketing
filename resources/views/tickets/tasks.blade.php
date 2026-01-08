@extends('layouts.app')

@section('title', 'Daftar Tugas')
@section('header', 'Daftar Tugas')
@section('subheader', 'Tiket yang ditugaskan kepada Anda')

@section('content')
<div class="space-y-6">
    <!-- Filter Pencarian -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-2">Cari</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ti ti-search text-slate-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Nomor tiket atau judul..."
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            </div>
            
            <!-- Filter Status -->
            <div class="w-40">
                <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->id_status }}" {{ request('status') == $status->id_status ? 'selected' : '' }}>
                        {{ $status->nama_status }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter Kategori -->
            <div class="w-48">
                <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                <select name="kategori" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id_kategori }}" {{ request('kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter Prioritas -->
            <div class="w-40">
                <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas</label>
                <select name="prioritas" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua</option>
                    @foreach($prioritass as $prioritas)
                    <option value="{{ $prioritas->id_prioritas }}" {{ request('prioritas') == $prioritas->id_prioritas ? 'selected' : '' }}>
                        {{ $prioritas->nama_prioritas }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Tombol Aksi -->
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition inline-flex items-center gap-2">
                    <i class="ti ti-filter"></i>
                    Filter
                </button>
                <a href="{{ route('tasks.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition inline-flex items-center gap-2">
                    <i class="ti ti-refresh"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Info Singkat -->
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-500">
            Menampilkan {{ $tikets->firstItem() ?? 0 }} - {{ $tikets->lastItem() ?? 0 }} dari {{ $tikets->total() }} tugas
        </p>        
    </div>

    <!-- Tabel Tugas -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @if($tikets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Tiket</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemohon</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tikets as $tiket)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $tiket->id_tiket) }}" class="font-mono text-sm text-blue-600 hover:underline">
                                {{ $tiket->nomor_tiket }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $tiket->id_tiket) }}" class="font-medium text-slate-800 hover:text-blue-600 truncate block max-w-xs">
                                {{ $tiket->judul }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr($tiket->pengguna->name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-slate-600">{{ $tiket->pengguna->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">{{ $tiket->kategori->nama_kategori }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="background-color: {{ $tiket->prioritas->color }}20; color: {{ $tiket->prioritas->color }}">
                                {{ $tiket->prioritas->nama_prioritas }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="background-color: {{ $tiket->status->color }}20; color: {{ $tiket->status->color }}">
                                {{ $tiket->status->nama_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $tiket->tanggal_dibuat->format('d M Y') }}
                            <span class="block text-xs text-slate-400">{{ $tiket->tanggal_dibuat->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('tickets.show', $tiket->id_tiket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <i class="ti ti-eye"></i>
                                Kerjakan
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $tikets->withQueryString()->links() }}
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <i class="ti ti-checkbox text-6xl text-green-300 mb-4"></i>
            <h3 class="text-lg font-medium text-slate-700 mb-2">Tidak ada tugas</h3>
            <p class="text-slate-500">Belum ada tiket yang ditugaskan kepada Anda</p>
        </div>
        @endif
    </div>
</div>
@endsection
