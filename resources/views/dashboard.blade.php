@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Selamat datang, ' . Auth::user()->name)

@section('content')
<div class="space-y-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Tickets -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Tiket</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-primary flex items-center justify-center">
                    <i class="ti ti-ticket text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tiket Aktif</p>
                    <p class="text-3xl font-bold text-amber-600 mt-2">{{ $stats['open'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-warning flex items-center justify-center">
                    <i class="ti ti-clock text-2xl text-white"></i>
                </div>
            </div>
        </div>

        @if(Auth::user()->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI']))
        <!-- Unassigned Tickets -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Belum Ditugaskan</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['unassigned'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-danger flex items-center justify-center">
                    <i class="ti ti-notes-off text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Resolved Today -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Selesai Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['resolved_today'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-success flex items-center justify-center">
                    <i class="ti ti-circle-check text-2xl text-white"></i>
                </div>
            </div>
        </div>
        @else
        <!-- In Progress Tickets (for Requester) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['in_progress'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-primary flex items-center justify-center">
                    <i class="ti ti-progress text-2xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Closed Tickets (for Requester) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tiket Selesai</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['closed'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-success flex items-center justify-center">
                    <i class="ti ti-circle-check text-2xl text-white"></i>
                </div>
            </div>
        </div>
        @endif

        @if(Auth::user()->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI']) && isset($stats['pending_approval']))
        <!-- Pending Approvals -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Menunggu Persetujuan</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['pending_approval'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center">
                    <i class="ti ti-clock-pause text-2xl text-white"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('tickets.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-105">
            <i class="ti ti-plus text-xl"></i>
            Buat Tiket Baru
        </a>
        
        <a href="{{ route('tickets.index') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-white text-slate-700 font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 transition-all duration-300">
            <i class="ti ti-list text-xl"></i>
            Lihat Semua Tiket
        </a>
    </div>

    <!-- Attention Tickets (for Staff) -->
    @if($attentionTickets->count() > 0)
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl p-6 border border-red-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                <i class="ti ti-alert-triangle text-xl text-red-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Perlu Perhatian</h3>
                <p class="text-sm text-slate-500">Tiket prioritas tinggi yang belum ditangani</p>
            </div>
        </div>
        
        <div class="space-y-3">
            @foreach($attentionTickets as $ticket)
            <a href="{{ route('tickets.show', $ticket) }}" class="block bg-white rounded-xl p-4 border border-red-100 hover:border-red-200 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono text-slate-500">{{ $ticket->ticket_number }}</span>
                        <h4 class="font-medium text-slate-800">{{ $ticket->title }}</h4>
                        <p class="text-sm text-slate-500">{{ $ticket->requester->name }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full" style="background-color: {{ $ticket->priority->color }}20; color: {{ $ticket->priority->color }}">
                        {{ $ticket->priority->name }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Tickets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Tiket Terbaru</h3>
        </div>
        
        @if($recentTickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Perihal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentTickets as $ticket)
                    <tr class="hover:bg-slate-50 transition cursor-pointer" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm text-blue-600">{{ $ticket->ticket_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <p class="font-medium text-slate-800 truncate">{{ $ticket->title }}</p>
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
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $ticket->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <i class="ti ti-ticket-off text-6xl text-slate-300 mb-4"></i>
            <p class="text-slate-500">Belum ada tiket</p>
            <a href="{{ route('tickets.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Buat tiket pertama Anda</a>
        </div>
        @endif
    </div>
</div>
@endsection
