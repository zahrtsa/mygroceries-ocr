@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Grid utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">

        {{-- Card: Ringkasan Harian (dipindah ke atas, full width) --}}
        <div class="lg:col-span-3 rounded-2xl bg-white shadow-md border border-slate-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#ed000c]/10 text-[#ed000c]">
                        <i class="fa fa-calendar-check"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Ringkasan Harian
                        </h2>
                        <p class="text-xs text-slate-500">
                            Status daftar belanja dan total pengeluaran hari ini.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <p class="text-xs text-slate-500">Total Barang</p>
                    <p class="mt-1 text-lg md:text-xl font-semibold text-slate-900 break-words">
                        {{ $totalBarang }}
                    </p>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
                    <p class="text-xs text-emerald-600">Barang Sudah Dibeli</p>
                    <p class="mt-1 text-lg md:text-xl font-semibold text-emerald-700 break-words">
                        {{ $barangSudahDibeli }}
                    </p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
                    <p class="text-xs text-amber-600">Barang Belum Dibeli</p>
                    <p class="mt-1 text-lg md:text-xl font-semibold text-amber-700 break-words">
                        {{ $barangBelumDibeli }}
                    </p>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 flex flex-col justify-between">
                    <p class="text-xs text-rose-600">Total Belanja Hari Ini</p>
                    <p class="mt-1 text-lg md:text-xl font-semibold text-rose-700 break-words">
                        Rp{{ number_format($totalBelanja, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Card: List Belanja Hari Ini (2 kolom: belum & sudah dibeli) --}}
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-md border border-slate-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#ed000c]/10 text-[#ed000c]">
                        <i class="fa fa-list"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            List Belanja Hari Ini
                        </h2>
                        <p class="text-xs text-slate-500">
                            Barang yang sudah dan belum dibeli untuk hari ini.
                        </p>
                    </div>
                </div>
            </div>

            @php
                // Flatten semua item hari ini ke satu koleksi
                $itemsHariIni = collect();
                foreach ($listBelanjaHariIni as $daftar) {
                    foreach ($daftar->itemBelanjas as $item) {
                        $itemsHariIni->push($item);
                    }
                }

                $belumDibeli = $itemsHariIni->where('status', '!=', 'Sudah Dibeli');
                $sudahDibeli = $itemsHariIni->where('status', 'Sudah Dibeli');
            @endphp

            <div class="mt-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3 sm:px-4 py-3 max-h-80 md:max-h-72 lg:max-h-64 overflow-y-auto">
                @if($itemsHariIni->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-3">
                        Tidak ada belanja hari ini.
                    </p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 text-sm">
                        {{-- Kolom: Belum Dibeli --}}
                        <div>
                            <p class="mb-2 text-xs font-semibold text-amber-700 uppercase tracking-wide">
                                Belum Dibeli
                            </p>
                            @forelse($belumDibeli as $item)
                                <div class="flex items-center justify-between py-1.5 border-b last:border-0 border-slate-100">
                                    <span class="text-sm text-slate-800">
                                        {{ $item->nama_barang }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        Qty: {{ $item->qty }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">
                                    Semua barang sudah dibeli 
                                </p>
                            @endforelse
                        </div>

                        {{-- Kolom: Sudah Dibeli --}}
                        <div>
                            <p class="mb-2 text-xs font-semibold text-emerald-700 uppercase tracking-wide">
                                Sudah Dibeli
                            </p>
                            @forelse($sudahDibeli as $item)
                                <div class="flex items-center justify-between py-1.5 border-b last:border-0 border-slate-100">
                                    <span class="text-sm italic text-slate-400">
                                        {{ $item->nama_barang }}
                                    </span>
                                    <span class="text-xs text-emerald-600">
                                        Qty: {{ $item->qty }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">
                                    Belum ada barang yang ditandai sudah dibeli.
                                </p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card: Pengeluaran Bulan Ini + donut + budget bulanan --}}
        <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#ed000c]/10 text-[#ed000c]">
                        <i class="fa fa-chart-pie"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Pengeluaran Bulan Ini
                        </h2>
                        <p class="text-xs text-slate-500">
                            Perbandingan pendapatan, pengeluaran, dan budget bulanan.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Donut chart --}}
            <div class="mt-2 rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-3 h-48 md:h-56 flex items-center justify-center">
                <div class="w-28 h-28 sm:w-32 sm:h-32 md:w-40 md:h-40">
                    <canvas id="pengeluaran-bulan-ini-chart"></canvas>
                </div>
            </div>

            @php
                $budgetBulanan    = $budget_bulanan ?? 0;
                $totalPendapatan  = $total_pendapatan ?? 0;
                $totalPengeluaran = $total_pengeluaran ?? 0;

                $persenBudget = $budgetBulanan > 0
                    ? min(100, round($totalPengeluaran / $budgetBulanan * 100))
                    : 0;

                $kelasPersenBudget =
                    $budgetBulanan == 0 ? 'text-slate-500' :
                    ($persenBudget < 70 ? 'text-emerald-600' :
                    ($persenBudget < 90 ? 'text-amber-500' : 'text-red-600'));
            @endphp

            <div class="mt-3 text-right text-xs sm:text-sm text-slate-600 space-y-1">
                <div>
                    Pendapatan:
                    <span class="font-semibold text-emerald-600">
                        Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    Pengeluaran:
                    <span class="font-semibold text-[#ed000c]">
                        Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    Budget bulanan:
                    <span class="font-semibold text-slate-800">
                        Rp{{ number_format($budgetBulanan, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    Terpakai:
                    <span class="font-semibold {{ $kelasPersenBudget }}">
                        {{ $budgetBulanan > 0 ? $persenBudget . '%' : '-' }}
                    </span>
                    dari budget bulanan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('pengeluaran-bulan-ini-chart');
    if (!ctx) return;

    const pendapatan    = {{ (int)($total_pendapatan ?? 0) }};
    const pengeluaran   = {{ (int)($total_pengeluaran ?? 0) }};
    const budgetBulanan = {{ (int)($budget_bulanan ?? 0) }};
    const sisa          = Math.max(pendapatan - pengeluaran, 0);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pendapatan', 'Pengeluaran', 'Sisa Pendapatan', 'Budget Bulanan'],
            datasets: [{
                data: [pendapatan, pengeluaran, sisa, budgetBulanan],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.95)',   // pendapatan
                    'rgba(239, 68, 68, 0.95)',    // pengeluaran
                    'rgba(148, 163, 184, 0.9)',   // sisa pendapatan
                    'rgba(251, 191, 36, 0.95)',   // budget bulanan
                ],
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    padding: 8,
                    bodyFont: { size: 11 },
                    callbacks: {
                        label: function (ctx) {
                            const val = ctx.parsed || 0;
                            return ctx.label + ': Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
