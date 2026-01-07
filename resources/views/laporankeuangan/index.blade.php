@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 sm:py-10 bg-slate-50">
    <div class="max-w-5xl mx-auto px-4" x-data="{ openYear: false }">

        {{-- HEADER --}}
        <div class="mb-6 sm:mb-8">
            <div class="mt-2 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900 flex items-center gap-2">
                        Laporan Pengeluaran
                        <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                            {{ $tahun }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-slate-600 max-w-xl">
                        Ringkasan pendapatan, pengeluaran, dan saldo bersih untuk satu tahun penuh.
                    </p>
                </div>
            </div>
        </div>

        {{-- 2 CARD: RINGKASAN & DONUT --}}
        <div class="mb-6 grid grid-cols-1 gap-5 md:grid-cols-2">
            {{-- Ringkasan tahun --}}
            <div class="rounded-2xl bg-white shadow-md border border-slate-200/80 p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-chart-line text-xs"></i>
                    </span>
                    Ringkasan Tahun {{ $tahun }}
                </h2>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-emerald-50/60 border border-emerald-100 px-3 py-2">
                        <dt class="text-[11px] font-semibold tracking-wide text-emerald-700 uppercase">
                            Total Pendapatan
                        </dt>
                        <dd class="mt-1 text-sm font-semibold text-emerald-800">
                            Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="rounded-xl bg-sky-50/70 border border-sky-100 px-3 py-2">
                        <dt class="text-[11px] font-semibold tracking-wide text-sky-700 uppercase">
                            Budget Belanja
                        </dt>
                        <dd class="mt-1 text-sm font-semibold text-sky-800">
                            Rp {{ number_format($budget_belanja, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="rounded-xl bg-rose-50/80 border border-rose-100 px-3 py-2">
                        <dt class="text-[11px] font-semibold tracking-wide text-rose-700 uppercase">
                            Total Pengeluaran
                        </dt>
                        <dd class="mt-1 text-sm font-semibold text-rose-700">
                            Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2">
                        <dt class="text-[11px] font-semibold tracking-wide text-slate-600 uppercase">
                            Saldo Bersih
                        </dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">
                            Rp {{ number_format($saldo_bersih, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Donut chart: mobile+desktop row, tablet column --}}
            <div class="rounded-2xl bg-white shadow-md border border-slate-200/80 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-50 text-rose-500">
                            <i class="fa-solid fa-chart-pie text-xs"></i>
                        </span>
                        Komposisi Keuangan
                    </h2>
                    <p class="text-xs text-slate-500">
                        Pendapatan vs belanja vs saldo
                    </p>
                </div>

                {{-- base: row; sm (tablet): col; lg (desktop): row lagi --}}
                <div class="flex items-center gap-4
                            flex-row
                            sm:flex-col
                            lg:flex-row">

                    {{-- Chart --}}
                    <div class="flex items-center justify-center
                                w-auto
                                sm:w-full
                                lg:w-auto">
                        <div class="w-28 h-28 xs:w-32 xs:h-32 sm:w-40 sm:h-40 lg:w-32 lg:h-32">
                            <canvas id="donut-chart"></canvas>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex-1 space-y-2 text-xs
                                w-auto
                                sm:w-full
                                lg:w-auto">
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
        <div class="rounded-3xl bg-white shadow-xl border border-slate-200/80 overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-4 sm:px-6 py-4 md:flex-row md:items-center md:justify-between bg-slate-50/70">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">
                        Detail Bulanan {{ $tahun }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Total belanja per bulan sepanjang tahun.
                    </p>
                </div>

                {{-- FILTER TAHUN --}}
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
                                <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/80 transition">
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
