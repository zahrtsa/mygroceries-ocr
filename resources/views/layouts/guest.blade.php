<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyGroceries') }}</title>
    <link rel="icon" href="{{ asset('img/logo-mygroceriesround.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(120deg, #b91c1c 0%, #ef4444 60%, #fff1f2 100%) !important;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased m-0 p-0">
     @if(session('success'))
        <script>
            Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: '{{ session('success') }}',
            // Popup default: di tengah, animasi fade, bukan toast
            showConfirmButton: true,
            });
        </script>
        @endif
        @if(session('error'))
        <script>
            Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            showConfirmButton: true,
            });
        </script>
    @endif


    <!-- (Bisa tetap taruh ini, jika nanti package realrashid sudah berfungsi normal) -->
    @include('sweetalert::alert')
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-2xl">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
