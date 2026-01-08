<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Ticketing BBPPT</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full font-sans bg-slate-50 text-slate-800 antialiased">
    <div class="flex h-full" x-data="{ sidebarOpen: false }">
        
        <!-- Overlay Mobile -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"></div>
        
        <!-- Sidebar Navigasi -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#1F2937] border-r border-slate-200 shadow-lg lg:shadow-sm transition-transform duration-300"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Logo -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                        <img src="{{ asset('images/logoidth.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="font-bold text-slate-200">Ticketing</h1>
                        <p class="text-xs text-slate-400">BBPPT</p>
                    </div>
                </div>
                <!-- Tombol Tutup Sidebar (Mobile) -->
                <button @click="sidebarOpen = false" class="lg:hidden p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>
            
            <!-- Menu Navigasi -->
            <nav class="px-4 py-6 space-y-1 overflow-y-auto h-[calc(100vh-80px)]">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-dashboard text-xl"></i>
                    Dashboard
                </a>
                
                {{-- Menu Daftar Tiket: Helpdesk, TeamLead, Manager, Pemohon --}}
                @if(Auth::user()->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI']) || !Auth::user()->hasRole('Technician'))
                <a href="{{ route('tickets.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('tickets.index') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-ticket text-xl"></i>
                    Daftar Tiket
                </a>
                @endif
                
                {{-- Menu Daftar Tugas: Helpdesk & Teknisi --}}
                @if(Auth::user()->hasAnyRole(['Helpdesk', 'Technician']))
                <a href="{{ route('tasks.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('tasks.*') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-list-check text-xl"></i>
                    Daftar Tugas
                </a>
                @endif

                <a href="{{ route('tickets.create') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('tickets.create') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-plus text-xl"></i>
                    Form Pengajuan Tiket
                </a>
                
                @can('approvals.view')
                <a href="{{ route('approvals.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('approvals.*') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-circle-check text-xl"></i>
                    Persetujuan
                </a>
                @endcan
                
                @if(Auth::user()->hasAnyRole(['ManagerTI', 'TeamLead']))
                <a href="{{ route('reports.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-report-analytics text-xl"></i>
                    Laporan
                </a>
                @endif
                
                @can('master.categories.manage')
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Administrator</p>
                </div>
                
                <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 text-primary-1000' : 'text-slate-200 hover:bg-white/5' }}">
                    <i class="ti ti-users text-xl"></i>
                    Kelola Pengguna
                </a>
                @endcan
            </nav>
        </aside>

        <!-- Konten Utama -->
        <div class="flex-1 lg:ml-64 min-h-screen">
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200">
                <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                    <div class="flex items-center gap-4">
                        <!-- Tombol Hamburger (Mobile) -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                            <i class="ti ti-menu-2 text-xl"></i>
                        </button>
                        <div>
                            <h2 class="text-lg lg:text-xl font-semibold text-slate-800">@yield('header', 'Dashboard')</h2>
                            <p class="text-xs lg:text-sm text-slate-500 hidden sm:block">@yield('subheader', 'Selamat datang di sistem ticketing BBPPT')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 lg:gap-4">
                        <!-- Menu Pengguna -->
                        <div class="flex items-center gap-2 lg:gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ Auth::user()->roles->first()?->name ?? 'Pengguna' }}</p>
                            </div>
                            <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition" title="Keluar">
                                    <i class="ti ti-logout text-xl"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Pesan Notifikasi -->
            @if(session('success'))
            <div class="mx-4 lg:mx-8 mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                <i class="ti ti-circle-check text-xl text-green-500"></i>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-4 lg:mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                <i class="ti ti-alert-circle text-xl text-red-500"></i>
                <span class="text-sm">{{ session('error') }}</span>
            </div>
            @endif

            <!-- Isi Halaman -->
            <main class="p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>
