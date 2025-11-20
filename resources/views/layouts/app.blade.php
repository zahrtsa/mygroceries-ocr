<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo-mygroceriesround.png') }}">
    <title>{{ config('app.name', 'MyGroceries') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: '{{ session('success') }}',
        showConfirmButton: true
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        showConfirmButton: true
    });
</script>
@endif

<!-- Navbar -->
<nav class="fixed top-0 left-0 w-full h-16 bg-white border-b border-gray-200 shadow-sm flex items-center justify-between px-8 z-40">
    <div class="flex items-center gap-4">
        <img src="{{ asset('img/logo-mygroceries.png') }}" alt="MyGroceries Logo" class="h-9 w-auto object-contain rounded-lg border border-gray-200 shadow" />
        <span class="text-xl font-extrabold tracking-tight text-[#ed1c24] drop-shadow-sm">MyGroceries</span>
    </div>
    <div class="flex items-center gap-5">
        <button class="relative">
            <i class="fa fa-bell text-xl text-gray-600 hover:text-[#ed1c24]"></i>
            <span class="absolute -top-1 right-0 bg-rose-500 text-white rounded-full text-[10px] min-w-5 h-5 px-1 flex items-center justify-center border-2 border-white">3</span>
        </button>
        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=fff&background=ed1c24" 
             class="w-10 h-10 rounded-full ring-2 ring-white object-cover shadow" alt="user"/>
    </div>
</nav>

<!-- Sidebar (mulai di bawah navbar) -->
<aside class="fixed top-16 left-0 w-64 bg-[#ed1c24] flex flex-col p-6 h-[calc(100vh-4rem)] border-r border-gray-200 z-30">
    <div class="mb-7">
        <div class="text-white/90 font-semibold text-base leading-tight truncate">{{ Auth::user()->name }}</div>
        <div class="text-white/80 text-xs truncate">{{ Auth::user()->email }}</div>
    </div>
    <nav class="flex-1">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-xl font-medium gap-3 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-bold' : 'text-rose-50' }}">
                    <i class="fa fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('belanja.item.index') }}"
                   class="flex items-center px-3 py-2 rounded-xl font-medium gap-3 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('belanja.daftar.index') ? 'bg-white/10 text-white font-bold' : 'text-rose-50' }}">
                    <i class="fas fa-list"></i> List Belanja
                </a>
            </li>
            <li>
                <a href="{{ route('belanja.rekapanharian') }}"
                   class="flex items-center px-3 py-2 rounded-xl font-medium gap-3 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('belanja.rekapanharian') ? 'bg-white/10 text-white font-bold' : 'text-rose-50' }}">
                    <i class="fa fa-history"></i> Rekapan Harian
                </a>
            </li>
            <li>
                <a href="{{ route('belanja.pengeluaran.index') }}"
                   class="flex items-center px-3 py-2 rounded-xl font-medium gap-3 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('belanja.pengeluaran.index') ? 'bg-white/10 text-white font-bold' : 'text-rose-50' }}">
                    <i class="fa fa-file-invoice-dollar"></i> Laporan Keuangan
                </a>
            </li>
        </ul>
    </nav>
    <div class="mt-8 border-t border-white/20 pt-5">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white/10 text-white/90 transition">
            <i class="fa fa-cog"></i> Setting
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full gap-3 px-3 py-2 mt-2 rounded-xl hover:bg-white/10 text-white/90 text-left transition">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

<!-- Main content -->
<div class="ml-64 pt-16 min-h-screen">
    <main class="p-8 w-full min-h-[calc(100vh-4rem)]">
        @yield('content')
    </main>
</div>
</body>
</html>
