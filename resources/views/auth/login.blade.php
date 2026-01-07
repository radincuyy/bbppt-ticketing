<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Ticketing BBPPT</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-20 left-20 w-64 h-64 bg-white/10 rounded-full float-animation"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-white/10 rounded-full float-animation" style="animation-delay: -3s;"></div>
            <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full float-animation" style="animation-delay: -1.5s;"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-16">
                <div class="mb-8">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center pulse-glow">
                        <img class="w-16 h-16 text-white" src="{{ asset('images/logoidth.png') }}" alt="Logo">
                    </div>
                </div>
                
                <h1 class="text-4xl font-bold text-white mb-4">
                    Sistem Ticketing<br>
                    <span class="text-blue-200">Layanan TI</span>
                </h1>
                
                <p class="text-blue-100 text-lg max-w-md mb-8">
                    Balai Besar Pengujian Perangkat Telekomunikasi (BBPPT) - Sistem pengelolaan permintaan layanan TI yang terintegrasi.
                </p>
    
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 items-center justify-center mb-4">
                        <i class="ti ti-ticket text-3xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Ticketing BBPPT</h1>
                </div>
                
                <!-- Login Card -->
                <div class="glass-card rounded-3xl shadow-xl p-8 border border-slate-200">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-slate-800">Selamat Datang</h2>
                        <p class="text-slate-500 mt-2">Masuk ke akun Anda untuk melanjutkan</p>
                    </div>
                    
                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ti ti-mail text-slate-400"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="block w-full pl-12 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-red-500 @enderror"
                                       placeholder="nama@bbppt.go.id"
                                       required>
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ti ti-lock text-slate-400"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="block w-full pl-12 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                       placeholder="••••••••"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-600">Ingat saya</span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-[1.02] inline-flex items-center justify-center gap-2">
                            <i class="ti ti-login"></i>
                            Masuk
                        </button>
                    </form>
                </div>
                
                <!-- Footer -->
                <p class="text-center text-sm text-slate-500 mt-8">
                    © {{ date('Y') }} BBPPT - Balai Besar Pengujian Perangkat Telekomunikasi
                </p>
            </div>
        </div>
    </div>
</body>
</html>
