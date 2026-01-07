@extends('layouts.app')

@section('title', 'Daftar Tiket')
@section('header', 'Daftar Tiket')
@section('subheader', 'Kelola dan monitor semua tiket layanan TI')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <form action="{{ route('tickets.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
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
            
            <!-- Status Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Category Filter -->
            <div class="w-48">
                <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                <select name="category" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Priority Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas</label>
                <select name="priority" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua</option>
                    @foreach($priorities as $priority)
                    <option value="{{ $priority->id }}" {{ request('priority') == $priority->id ? 'selected' : '' }}>
                        {{ $priority->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition inline-flex items-center gap-2">
                    <i class="ti ti-filter"></i>
                    Filter
                </button>
                <a href="{{ route('tickets.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition inline-flex items-center gap-2">
                    <i class="ti ti-refresh"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Quick Action -->
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-500">
            Menampilkan {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} dari {{ $tickets->total() }} tiket
        </p>
        <a href="{{ route('tickets.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all">
            <i class="ti ti-plus"></i>
            Buat Tiket Baru
        </a>
        
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Tiket</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Perihal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Kendala</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ditugaskan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-mono text-sm text-blue-600 hover:underline">
                                {{ $ticket->ticket_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-slate-800 hover:text-blue-600 truncate block">
                                    {{ $ticket->title }}
                                </a>
                                <p class="text-sm text-slate-500">{{ $ticket->requester->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">{{ $ticket->category->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="background-color: {{ $ticket->priority->color }}15; color: {{ $ticket->priority->color }}">
                                {{ $ticket->priority->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="background-color: {{ $ticket->status->color }}15; color: {{ $ticket->status->color }}">
                                {{ $ticket->status->name }}
                            </span>
                            @if($ticket->needs_approval && $ticket->approval_status === 'pending')
                            <span class="ml-1 inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                Menunggu Approval
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->assignedTo)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr($ticket->assignedTo->name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-slate-600">{{ $ticket->assignedTo->name }}</span>
                            </div>
                            @else
                            <span class="text-sm text-slate-400 italic">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $ticket->created_at->format('d M Y') }}
                            <span class="block text-xs text-slate-400">{{ $ticket->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <i class="ti ti-eye"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $tickets->withQueryString()->links() }}
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <i class="ti ti-ticket-off text-6xl text-slate-200 mb-4"></i>
            <h3 class="text-lg font-medium text-slate-700 mb-2">Tidak ada tiket</h3>
            <p class="text-slate-500 mb-4">Belum ada tiket yang sesuai dengan filter Anda</p>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition">
                <i class="ti ti-plus"></i>
                Buat Tiket Baru
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
