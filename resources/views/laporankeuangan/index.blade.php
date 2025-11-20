@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-2 py-4 bg-gradient-to-br from-gray-50 via-white to-gray-100">

    <!-- Header & Search -->
    <div class="w-full max-w-5xl flex flex-col md:flex-row justify-between items-center gap-3 mb-6">
        <div class="w-full md:w-auto text-center md:text-left">
            <h1 class="font-extrabold text-2xl sm:text-3xl text-gray-800 mb-1 leading-tight">Laporan Keuangan</h1>
            <div class="text-gray-400 font-normal text-sm">Rekap pengeluaran & pemasukan kamu bulan ini</div>
        </div>
        <form class="flex items-center bg-white border border-gray-200 rounded-full px-5 py-2 shadow-sm gap-2 w-full md:w-auto max-w-xs">
            <input
                type="text"
                class="w-full bg-transparent outline-none border-0 focus:ring-0 text-gray-700 text-sm"
                placeholder="Cari laporan...">
            <button class="text-gray-400 hover:text-rose-500 transition" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="w-full max-w-5xl grid grid-cols-1 sm:grid-cols-3 gap-5 mb-7">
        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 via-white to-emerald-100 border border-emerald-100 p-6 text-center shadow-sm">
            <div class="font-medium text-emerald-700 mb-1">Pendapatan Tahun Ini</div>
            <div class="text-2xl font-bold text-emerald-700 tracking-wide">Rp {{ number_format($total_pendapatan,0,',','.') }}</div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-rose-50 via-white to-rose-100 border border-rose-100 p-6 text-center shadow-sm">
            <div class="font-medium text-rose-600 mb-1">Budget Belanja Tahun</div>
            <div class="text-2xl font-bold text-rose-600 tracking-wide">Rp {{ number_format($budget_belanja,0,',','.') }}</div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-indigo-50 via-white to-indigo-100 border border-indigo-100 p-6 text-center shadow-sm">
            <div class="font-medium text-indigo-600 mb-1">Saldo Bersih Akhir</div>
            <div class="text-2xl font-bold text-indigo-600 tracking-wide">Rp {{ number_format($saldo_bersih,0,',','.') }}</div>
        </div>
    </div>

    <!-- Main Section: Grid 2 kolom -->
    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-7 bg-white/90 rounded-2xl shadow-lg border border-gray-100 p-7">
        <!-- Table Bulanan -->
        <div class="flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-gray-700 text-base">Belanja Bulanan</span>
                <div>
                    <button class="rounded-full px-3 py-1 border border-gray-300 text-lg text-gray-400 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition"><i class="fa-solid fa-plus"></i></button>
                    <button class="ml-2 px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm border transition">Edit</button>
                </div>
            </div>
            <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-2 px-4 font-semibold text-gray-500">Bulan</th>
                            <th class="py-2 px-4 font-semibold text-gray-500">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan_bulanan as $laporan)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $laporan['bulan'] }}</td>
                            <td class="py-2 px-4 text-gray-700">Rp {{ number_format($laporan['total_belanja'],0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Diagram Donut -->
        <div class="flex flex-col items-center justify-center gap-4">
            <span class="font-semibold text-gray-700 text-base mb-3">Diagram Bulanan</span>
            <div class="flex flex-row gap-5 w-full justify-center">
                <!-- Donut Pendapatan -->
                <div class="bg-gradient-to-tr from-emerald-50 via-white to-emerald-100 rounded-xl border border-emerald-100 shadow px-5 py-4 flex flex-col items-center w-32">
                    <span class="text-xs text-emerald-600 mb-1">Pendapatan</span>
                    <div class="relative w-16 h-16 mb-1">
                        <svg class="absolute" width="64" height="64">
                            <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="7" fill="none"/>
                            <circle cx="32" cy="32" r="28"
                                stroke="#10b981"
                                stroke-width="7"
                                stroke-dasharray="{{ (2 * 3.14 * 28) }}"
                                stroke-dashoffset="{{ (2 * 3.14 * 28) - ((2 * 3.14 * 28) * $persen_pendapatan / 100) }}"
                                fill="none"
                                style="transition:stroke-dashoffset 0.5s;"></circle>
                        </svg>
                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-xl font-bold text-emerald-700">{{ $persen_pendapatan }}%</span>
                    </div>
                </div>
                <!-- Donut Pengeluaran -->
                <div class="bg-gradient-to-tr from-rose-50 via-white to-rose-100 rounded-xl border border-rose-100 shadow px-5 py-4 flex flex-col items-center w-32">
                    <span class="text-xs text-rose-600 mb-1">Pengeluaran</span>
                    <div class="relative w-16 h-16 mb-1">
                        <svg class="absolute" width="64" height="64">
                            <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="7" fill="none"/>
                            <circle cx="32" cy="32" r="28"
                                stroke="#f43f5e"
                                stroke-width="7"
                                stroke-dasharray="{{ (2 * 3.14 * 28) }}"
                                stroke-dashoffset="{{ (2 * 3.14 * 28) - ((2 * 3.14 * 28) * $persen_pengeluaran / 100) }}"
                                fill="none"
                                style="transition:stroke-dashoffset 0.5s;"></circle>
                        </svg>
                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-xl font-bold text-rose-600">{{ $persen_pengeluaran }}%</span>
                    </div>
                </div>
                <!-- Donut Saldo -->
                <div class="bg-gradient-to-tr from-indigo-50 via-white to-indigo-100 rounded-xl border border-indigo-100 shadow px-5 py-4 flex flex-col items-center w-32">
                    <span class="text-xs text-indigo-600 mb-1">Saldo Akhir</span>
                    <div class="relative w-16 h-16 mb-1">
                        <svg class="absolute" width="64" height="64">
                            <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="7" fill="none"/>
                            <circle cx="32" cy="32" r="28"
                                stroke="#6366f1"
                                stroke-width="7"
                                stroke-dasharray="{{ (2 * 3.14 * 28) }}"
                                stroke-dashoffset="{{ (2 * 3.14 * 28) - ((2 * 3.14 * 28) * $persen_saldo / 100) }}"
                                fill="none"
                                style="transition:stroke-dashoffset 0.5s;"></circle>
                        </svg>
                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-xl font-bold text-indigo-600">{{ $persen_saldo }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Tahun -->
    <div class="w-full max-w-5xl flex justify-end mt-10">
        <span class="rounded-lg px-5 py-2 bg-white border border-gray-200 text-gray-500 text-sm font-semibold shadow-sm">Tahun {{ $tahun }}</span>
    </div>
</div>
@endsection
