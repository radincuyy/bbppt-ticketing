@extends('layouts.app')

@section('title', 'Laporan')
@section('header', 'Laporan Monitoring & Evaluasi')
@section('subheader', 'Unduh rekapitulasi data tiket dan kinerja layanan')

@section('content')
<div class="space-y-6">
    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Filter Laporan</h3>
        
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Awal</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ $startDate }}"
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            
            <!-- End Date -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ $endDate }}"
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $statusId == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition inline-flex items-center justify-center gap-2">
                    <i class="ti ti-filter"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="ti ti-ticket text-2xl text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Tiket</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="ti ti-clock text-2xl text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Tiket Open</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['open'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="ti ti-circle-check text-2xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Tiket Closed</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['closed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Unduh Laporan</h3>
        
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('reports.export-excel', request()->query()) }}" 
               class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                <i class="ti ti-file-spreadsheet text-xl"></i>
                Download Excel (CSV)
            </a>
            
            <a href="{{ route('reports.export-pdf', request()->query()) }}" 
               target="_blank"
               class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition inline-flex items-center gap-2">
                <i class="ti ti-file-type-pdf text-xl"></i>
                Download PDF
            </a>
        </div>
        
        <p class="text-sm text-slate-500 mt-3">
            Laporan periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </p>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Daftar Tiket ({{ $tickets->count() }} data)</h3>
        </div>
        
        @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">No. Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Pemohon</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-mono text-sm text-blue-600 hover:underline">
                                {{ $ticket->ticket_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ Str::limit($ticket->title, 40) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $ticket->category->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" style="background-color: {{ $ticket->priority->color ?? '#ccc' }}15; color: {{ $ticket->priority->color ?? '#666' }}">
                                {{ $ticket->priority->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" style="background-color: {{ $ticket->status->color ?? '#ccc' }}15; color: {{ $ticket->status->color ?? '#666' }}">
                                {{ $ticket->status->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $ticket->requester->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <i class="ti ti-file-off text-6xl text-slate-200 mb-4"></i>
            <h3 class="text-lg font-medium text-slate-700 mb-2">Tidak ada data</h3>
            <p class="text-slate-500">Tidak ditemukan tiket pada periode yang dipilih</p>
        </div>
        @endif
    </div>

    <!-- Statistics by Category & Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- By Category -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Tiket per Kategori</h3>
            @if($stats['by_category']->count() > 0)
            <div class="space-y-3">
                @foreach($stats['by_category'] as $name => $count)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">{{ $name }}</span>
                    <span class="font-semibold text-slate-800">{{ $count }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-slate-500 text-sm">Tidak ada data</p>
            @endif
        </div>

        <!-- By Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Tiket per Status</h3>
            @if($stats['by_status']->count() > 0)
            <div class="space-y-3">
                @foreach($stats['by_status'] as $name => $count)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">{{ $name }}</span>
                    <span class="font-semibold text-slate-800">{{ $count }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-slate-500 text-sm">Tidak ada data</p>
            @endif
        </div>
    </div>
</div>
@endsection
