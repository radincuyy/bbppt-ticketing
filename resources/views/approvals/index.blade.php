@extends('layouts.app')

@section('title', 'Persetujuan Tiket')
@section('header', 'Persetujuan Tiket')
@section('subheader', 'Kelola tiket yang memerlukan persetujuan Manager TI')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="ti ti-clock text-2xl text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingTickets->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="ti ti-circle-check text-2xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Disetujui (Bulan Ini)</p>
                    <p class="text-2xl font-bold text-green-600">{{ $approvedCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="ti ti-circle-x text-2xl text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Ditolak (Bulan Ini)</p>
                    <p class="text-2xl font-bold text-red-600">{{ $rejectedCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals List -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Tiket Menunggu Persetujuan</h3>
        </div>
        
        @if($pendingTickets->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach($pendingTickets as $ticket)
            <div class="p-6 hover:bg-slate-50 transition" x-data="{ showModal: false }">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="font-mono text-sm text-blue-600">{{ $ticket->ticket_number }}</span>
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="background-color: {{ $ticket->priority->color }}15; color: {{ $ticket->priority->color }}">
                                {{ $ticket->priority->name }}
                            </span>
                        </div>
                        <h4 class="font-semibold text-slate-800 mb-1">{{ $ticket->title }}</h4>
                        <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ Str::limit($ticket->description, 200) }}</p>
                        
                        @if($ticket->approval_notes)
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-3">
                            <p class="text-xs font-medium text-purple-700 mb-1">
                                <i class="ti ti-message-circle"></i> Alasan Pengajuan:
                            </p>
                            <p class="text-sm text-purple-800">{{ $ticket->approval_notes }}</p>
                        </div>
                        @endif
                        
                        <div class="flex items-center gap-4 text-sm text-slate-500">
                            <span class="flex items-center gap-1">
                                <i class="ti ti-user"></i>
                                {{ $ticket->requester->name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="ti ti-category"></i>
                                {{ $ticket->category->name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="ti ti-calendar"></i>
                                {{ $ticket->created_at->format('d M Y H:i') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('tickets.show', $ticket) }}" 
                           class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition inline-flex items-center gap-2">
                            <i class="ti ti-eye"></i>
                            Detail
                        </a>
                        
                        <button @click="showModal = 'approve'"
                                class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                            <i class="ti ti-check"></i>
                            Setujui
                        </button>
                        
                        <button @click="showModal = 'reject'"
                                class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition inline-flex items-center gap-2">
                            <i class="ti ti-x"></i>
                            Tolak
                        </button>
                    </div>
                </div>
                
                <!-- Approve Modal -->
                <div x-show="showModal === 'approve'" 
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
                        
                        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="ti ti-circle-check text-3xl text-green-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800">Setujui Tiket</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $ticket->ticket_number }}</p>
                            </div>
                            
                            <form action="{{ route('approvals.approve', $ticket) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Tambahkan catatan persetujuan..."></textarea>
                                </div>
                                
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                                        <i class="ti ti-check"></i>
                                        Setujui
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Reject Modal -->
                <div x-show="showModal === 'reject'" 
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
                        
                        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="ti ti-circle-x text-3xl text-red-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800">Tolak Tiket</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $ticket->ticket_number }}</p>
                            </div>
                            
                            <form action="{{ route('approvals.reject', $ticket) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
                                </div>
                                
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition inline-flex items-center gap-2">
                                        <i class="ti ti-x"></i>
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <i class="ti ti-circle-check text-6xl text-slate-200 mb-4"></i>
            <h3 class="text-lg font-medium text-slate-700 mb-2">Tidak ada tiket menunggu</h3>
            <p class="text-slate-500">Semua tiket sudah diproses</p>
        </div>
        @endif
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
