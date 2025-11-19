<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white shadow-md flex flex-col">
        <div class="p-4 text-xl font-bold border-b">{{ config('app.name', 'Laravel') }}</div>
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded hover:bg-gray-200">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-2 rounded hover:bg-gray-200">
                        <i class="fas fa-cog mr-2"></i> Setting
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full p-2 rounded hover:bg-gray-200">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col">

        {{-- Navbar --}}
        <header class="bg-white shadow-md flex items-center justify-between px-6 py-3">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-8">
                <span class="font-bold text-lg">{{ config('app.name', 'Laravel') }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <button class="relative">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">3</span>
                </button>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>

    </div>

</div>

</body>
</html>
