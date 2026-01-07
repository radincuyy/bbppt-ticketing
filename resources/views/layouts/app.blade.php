<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Ticketing BBPPT</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-slate-50 text-slate-800 antialiased">
    <div class="flex h-full" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 shadow-sm transition-transform duration-300"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <img src="{{ asset('images/logoidth.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-bold text-slate-800">Ticketing</h1>
                    <p class="text-xs text-slate-500">BBPPT</p>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="px-4 py-6 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-dashboard text-xl"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('tickets.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('tickets.*') && !request()->routeIs('tickets.create') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-list text-xl"></i>
                    Daftar Ticket
                </a>

                <a href="{{ route('tickets.create') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('tickets.create') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-plus text-xl"></i>
                    Form Pengajuan Ticket
                </a>
                
                @can('approvals.view')
                <a href="{{ route('approvals.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('approvals.*') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-circle-check text-xl"></i>
                    Persetujuan
                </a>
                @endcan
                
                @if(Auth::user()->hasAnyRole(['ManagerTI', 'TeamLead']))
                <a href="{{ route('reports.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-report-analytics text-xl"></i>
                    Laporan
                </a>
                @endif
                
                @can('master.categories.manage')
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</p>
                </div>
                
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-category text-xl"></i>
                    Kategori
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti ti-users text-xl"></i>
                    Pengguna
                </a>
                @endcan
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Header -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">@yield('header', 'Dashboard')</h2>
                        <p class="text-sm text-slate-500">@yield('subheader', 'Selamat datang di sistem ticketing BBPPT')</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- User Menu -->
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ Auth::user()->roles->first()?->name ?? 'User' }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition">
                                    <i class="ti ti-logout text-xl"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-8 mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                <i class="ti ti-circle-check text-xl text-green-500"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                <i class="ti ti-alert-circle text-xl text-red-500"></i>
                {{ session('error') }}
            </div>
            @endif

            <!-- Page Content -->
            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>
