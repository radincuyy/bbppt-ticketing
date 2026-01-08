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

        <!-- Completed Tickets -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tiket Selesai</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-success flex items-center justify-center">
                    <i class="ti ti-circle-check text-2xl text-white"></i>
                </div>
            </div>
        </div>

               
        @else

        <!-- Completed Tickets -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tiket Selesai</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl gradient-success flex items-center justify-center">
                    <i class="ti ti-circle-check text-2xl text-white"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Charts Section -->
    @if(Auth::user()->hasAnyRole(['TeamLead', 'ManagerTI']))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart: Tiket per Status -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Tiket per Status</h3>
            <div class="h-64">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>

        <!-- Chart: Tiket per Kategori -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Tiket per Kategori</h3>
            <div class="h-64">
                <canvas id="chartKategori"></canvas>
            </div>
        </div>
    </div>

    <!-- Kinerja Teknisi -->
    @if($kinerjaTeknisi->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Kinerja Layanan TI</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Total Ditugaskan</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Sedang Dikerjakan</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Selesai</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Tingkat Penyelesaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($kinerjaTeknisi as $staff)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $staff['role'] == 'Helpdesk' ? 'bg-blue-100' : 'bg-blue-100' }} flex items-center justify-center">
                                    <span class="text-sm font-medium {{ $staff['role'] == 'Helpdesk' ? 'text-blue-600' : 'text-blue-600' }}">{{ substr($staff['nama'], 0, 1) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-800">{{ $staff['nama'] }}</span>
                                    <span class="ml-2 inline-flex px-2 py-0.5 text-xs font-medium rounded {{ $staff['role'] == 'Helpdesk' ? 'bg-blue-100 text-blue-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $staff['role'] }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-slate-800">{{ $staff['total'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-slate-800">
                                {{ $staff['dikerjakan'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-slate-800">
                                {{ $staff['selesai'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $staff['persentase'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-slate-600">{{ $staff['persentase'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    <!-- Attention Tickets -->
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
            @foreach($attentionTickets as $tiket)
            <a href="{{ route('tickets.show', $tiket->id_tiket) }}" class="block bg-white rounded-xl p-4 border border-red-100 hover:border-red-200 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono text-slate-500">{{ $tiket->nomor_tiket }}</span>
                        <h4 class="font-medium text-slate-800">{{ $tiket->judul }}</h4>
                        <p class="text-sm text-slate-500">{{ $tiket->pengguna->name }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-600">
                        {{ $tiket->prioritas->nama_prioritas }}
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
                    @foreach($recentTickets as $tiket)
                    <tr class="hover:bg-slate-50 transition cursor-pointer" onclick="window.location='{{ route('tickets.show', $tiket->id_tiket) }}'">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm text-blue-600">{{ $tiket->nomor_tiket }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <p class="font-medium text-slate-800 truncate">{{ $tiket->judul }}</p>
                                <p class="text-sm text-slate-500">{{ $tiket->pengguna->name }}</p>
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
                            {{ $tiket->tanggal_dibuat->format('d M Y H:i') }}
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

@if(Auth::user()->hasAnyRole(['TeamLead', 'ManagerTI']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller
    const statusData = @json($stats['by_status'] ?? []);
    const kategoriData = @json($stats['by_category'] ?? []);
    
    // Chart: Tiket per Status (Doughnut)
    if (statusData.length > 0) {
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.nama_status),
                datasets: [{
                    data: statusData.map(item => item.tiket_count),
                    backgroundColor: statusData.map(item => item.color || '#6B7280'),
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 12, family: 'Inter' }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
    
    // Chart: Tiket per Kategori (Bar)
    if (kategoriData.length > 0) {
        new Chart(document.getElementById('chartKategori'), {
            type: 'bar',
            data: {
                labels: kategoriData.map(item => item.nama_kategori),
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: kategoriData.map(item => item.tiket_count),
                    backgroundColor: '#3B82F6',
                    borderRadius: 6,
                    barThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { stepSize: 1 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: 'Inter' }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endsection
