<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyGroceries') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">

<!-- Navbar full width -->
<nav class="w-full bg-white shadow flex items-center justify-between px-8 py-4">
    <!-- Logo kiri berupa gambar -->
    <div class="flex items-center gap-3">
        <img src="{{ asset('img/logo-title_MyGroceries.png') }}" alt="MyGroceries Logo" class="h-12 w-auto object-contain my-0"/>
    </div>
    <!-- Notifikasi kanan -->
    <div class="flex items-center gap-6">
        <button class="relative">
            <i class="fa fa-bell text-2xl text-gray-700"></i>
            <span class="absolute top-0 right-0 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">3</span>
        </button>
    </div>
</nav>

<!-- Sidebar + Content -->
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#ed000c] flex flex-col p-6 shadow-lg">
        <!-- Profil User -->
        <div class="mb-6">
            <span class="font-semibold text-white">{{ Auth::user()->name }}</span><br>
            <span class="text-white text-sm">{{ Auth::user()->email }}</span>
        </div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded transition hover:bg-white/20 {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                        <i class="fa fa-home mr-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded transition hover:bg-white/20">
                        <i class="fas fa-list mr-3"></i> List Belanja
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded transition hover:bg-white/20">
                        <i class="fa fa-history mr-3"></i> Rekapan Harian
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded transition hover:bg-white/20">
                        <i class="fa fa-file-invoice-dollar mr-3"></i> Laporan Keuangan
                    </a>
                </li>
            </ul>
        </nav>
        <div class="mt-8 border-t border-white/30 pt-6">
            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded hover:bg-white/20 text-white">
                <i class="fa fa-cog mr-3"></i> Setting
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2 mt-2 rounded hover:bg-white/20 text-white text-left">
                    <i class="fa fa-sign-out-alt mr-3"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>