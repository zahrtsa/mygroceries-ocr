@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 sm:py-10">
    <div class="max-w-5xl mx-auto px-4" x-data="{ openYear: false }">

        {{-- HEADER --}}
        <div class="mb-6 sm:mb-8">
            <p class="text-[11px] font-semibold tracking-[0.28em] text-emerald-500 uppercase">
                LAPORAN
            </p>
            <div class="mt-1 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">
                        Rekap Pengeluaran
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Ringkasan pendapatan, pengeluaran, dan saldo bersih berdasarkan tahun yang kamu pilih.
                    </p>
                </div>
            </div>
        </div>

        {{-- 2 CARD: RINGKASAN & DONUT --}}
        <div class="mb-6 grid grid-cols-1 gap-5 md:grid-cols-2">
            {{-- Ringkasan tahun --}}
            <div class="rounded-2xl bg-white shadow-md border border-slate-200 p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">
                    Ringkasan Tahun {{ $tahun }}
                </h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Total Pendapatan</dt>
                        <dd class="font-semibold text-emerald-600">
                            Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Budget Belanja</dt>
                        <dd class="font-semibold text-sky-600">
                            Rp {{ number_format($budget_belanja, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Total Pengeluaran</dt>
                        <dd class="font-semibold text-red-600">
                            Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Saldo Bersih</dt>
                        <dd class="font-semibold text-slate-900">
                            Rp {{ number_format($saldo_bersih, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Donut chart --}}
            <div class="rounded-2xl bg-white shadow-md border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Komposisi Keuangan
                    </h2>
                    <p class="text-xs text-slate-500">
                        Pendapatan vs Belanja vs Saldo
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-32 h-32 mx-auto">
                        <canvas id="donut-chart"></canvas>
                    </div>
                    <div class="flex-1 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-slate-600">Pendapatan</span>
                            </div>
                            <span class="font-semibold text-slate-900">
                                {{ $persen_pendapatan }}%
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                <span class="text-slate-600">Pengeluaran</span>
                            </div>
                            <span class="font-semibold text-slate-900">
                                {{ $persen_pengeluaran }}%
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-slate-500"></span>
                                <span class="text-slate-600">Saldo</span>
                            </div>
                            <span class="font-semibold text-slate-900">
                                {{ $persen_saldo }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD TABEL BULAN / TOTAL BELANJA --}}
        <div class="rounded-2xl bg-white shadow-xl border border-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-4 sm:px-6 py-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Detail Bulanan {{ $tahun }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Daftar total belanja setiap bulan dalam tahun berjalan.
                    </p>
                </div>

                {{-- FILTER TAHUN: custom dropdown mirip Rekap Harian --}}
                <form
                    action="{{ route('belanja.pengeluaran.index') }}"
                    method="GET"
                    id="filter-year-form-bottom"
                    class="w-full md:w-auto"
                >
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Pilih Tahun
                    </label>

                    <div class="w-full sm:w-52">
                        <div class="relative">
                            {{-- tombol tampilan tahun --}}
                            <button type="button"
                                    @click="openYear = !openYear"
                                    class="w-full flex items-center justify-between rounded-full border border-slate-200 bg-white
                                           px-3 py-2 text-xs sm:text-sm text-slate-800 shadow-sm hover:border-[#2563eb]
                                           focus:outline-none focus:ring-2 focus:ring-[#2563eb]/25 transition">
                                <span class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-slate-400"></i>
                                    <span>{{ $tahun }}</span>
                                </span>
                                <i class="fa fa-chevron-down text-[11px] text-slate-400"
                                   :class="{ 'rotate-180': openYear }"></i>
                            </button>

                            {{-- dropdown list tahun --}}
                            <div x-show="openYear"
                                 @click.away="openYear = false"
                                 x-transition
                                 class="absolute z-20 mt-1 w-full rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
                                <div class="max-h-56 overflow-y-auto py-1 text-xs sm:text-sm">
                                    @foreach($daftar_tahun as $t)
                                        @php
                                            $active = (int)$tahun === (int)$t;
                                        @endphp
                                        <button
                                            type="submit"
                                            name="tahun"
                                            value="{{ $t }}"
                                            class="w-full text-left px-3 py-2 flex items-center justify-between
                                                   {{ $active ? 'bg-slate-100 text-[#2563eb] font-semibold' : 'text-slate-700 hover:bg-slate-50' }}"
                                            @click="openYear = false"
                                        >
                                            <span>{{ $t }}</span>
                                            @if($active)
                                                <i class="fa fa-check text-[11px]"></i>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-4 sm:px-6 py-4 overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-[11px] sm:text-xs font-semibold text-slate-500">
                            <th class="py-2">Bulan</th>
                            <th class="py-2 text-right">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!$adaDataTahunIni)
                            <tr>
                                <td colspan="2" class="py-4 text-center text-sm text-slate-500">
                                    Belum ada data untuk tahun ini.
                                </td>
                            </tr>
                        @else
                            @foreach($laporan_bulanan as $row)
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition">
                                    <td class="py-2 text-slate-800">
                                        {{ $row['bulan'] }}
                                    </td>
                                    <td class="py-2 text-right text-slate-900 font-medium">
                                        Rp {{ number_format($row['total_belanja'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('donut-chart');
    if (!ctx) return;

    const data = {
        labels: ['Pendapatan', 'Pengeluaran', 'Saldo'],
        datasets: [{
            data: [
                {{ $persen_pendapatan }},
                {{ $persen_pengeluaran }},
                {{ $persen_saldo }},
            ],
            backgroundColor: [
                'rgba(16, 185, 129, 0.95)',
                'rgba(239, 68, 68, 0.95)',
                'rgba(100, 116, 139, 0.95)',
            ],
            borderWidth: 0,
            hoverOffset: 4,
        }]
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.9)',
                    bodyFont: { size: 11 },
                    padding: 8,
                    callbacks: {
                        label: function (context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
