@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-6xl mx-auto px-4 lg:px-6">

        {{-- HEADER --}}
        <div class="mb-4">
            <h1 class="mt-1 text-3xl font-semibold text-slate-900">
                Pengaturan Akun
            </h1>
            <p class="mt-1 text-sm text-slate-600">
                Atur informasi profil dan preferensi keuangan kamu agar pengalaman belanja jadi lebih nyaman.
            </p>
        </div>

        {{-- ALERT SUCCESS --}}
        @if(session('success'))
            <div class="mb-3 rounded-full border border-emerald-400 bg-emerald-50 px-4 py-2 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- ALERT ERROR --}}
        @if ($errors->any())
            <div class="mb-3 rounded-2xl border border-red-400 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-medium mb-1">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CARD UTAMA --}}
        <div class="rounded-3xl bg-white shadow-xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 lg:px-8 py-3.5">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Data Profil
                    </h2>
                    <p class="text-xs text-slate-500">
                        Ubah data akun yang dipakai untuk MyGroceries.
                    </p>
                </div>
                <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
            </div>

            <form action="{{ route('settings.update', $user->id) }}" method="POST" class="px-5 lg:px-8 py-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- INFORMASI PRIBADI --}}
                <div class="space-y-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Informasi Pribadi
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $user->name) }}"
                                class="block w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0 @error('name') border-red-500 @enderror"
                                placeholder="Nama lengkap kamu"
                            >
                        </div>

                        <div>
                            <label for="username" class="block text-xs font-semibold text-slate-700 mb-1">
                                Username
                            </label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="{{ old('username', $user->username) }}"
                                class="block w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0 @error('username') border-red-500 @enderror"
                                placeholder="username_kamu"
                            >
                        </div>
                    </div>

                    {{-- Email: lebar penuh, sejajar grid --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $user->email) }}"
                            class="block w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0 @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com"
                        >
                    </div>
                </div>

                {{-- PREFERENSI KEUANGAN --}}
                <div class="space-y-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Preferensi Keuangan
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pendapatan_bulanan" class="block text-xs font-semibold text-slate-700 mb-1">
                                Pendapatan Bulanan
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-xs text-slate-400">
                                    Rp
                                </span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="pendapatan_bulanan"
                                    id="pendapatan_bulanan"
                                    value="{{ old('pendapatan_bulanan', $user->pendapatan_bulanan) }}"
                                    class="block w-full rounded-full border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0 @error('pendapatan_bulanan') border-red-500 @enderror"
                                    placeholder="0"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="budget_bulanan" class="block text-xs font-semibold text-slate-700 mb-1">
                                Budget Bulanan
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-xs text-slate-400">
                                    Rp
                                </span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="budget_bulanan"
                                    id="budget_bulanan"
                                    value="{{ old('budget_bulanan', $user->budget_bulanan) }}"
                                    class="block w-full rounded-full border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0 @error('budget_bulanan') border-red-500 @enderror"
                                    placeholder="0"
                                >
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">
                                Opsional. Kosongkan jika belum ingin mengatur batas belanja.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- KEAMANAN AKUN --}}
                <div class="space-y-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        Keamanan Akun
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Password Baru (opsional)
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="block w-full rounded-full border border-slate-300 bg-white px-4 pr-12 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0 @error('password') border-red-500 @enderror"
                                    placeholder="••••••••"
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password', this)"
                                    class="absolute inset-y-0 right-4 flex items-center text-slate-400 hover:text-slate-600"
                                >
                                    <i class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                                Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="block w-full rounded-full border border-slate-300 bg-white px-4 pr-12 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-0"
                                    placeholder="Ulangi password baru"
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    class="absolute inset-y-0 right-4 flex items-center text-slate-400 hover:text-slate-600"
                                >
                                    <i class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="pt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-500">
                        Simpan perubahan untuk memperbarui data akun kamu.
                    </p>
                    <div class="flex items-center gap-3 justify-end">
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center rounded-full border border-slate-300 px-5 py-2 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 transition">
                            Kembali
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-full bg-red-500 px-5 py-2 text-xs font-semibold text-white shadow-md shadow-red-500/40 hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-500/60 focus:ring-offset-2 focus:ring-offset-white transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
