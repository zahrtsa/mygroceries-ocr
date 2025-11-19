@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- List Belanja Hari Ini -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="font-bold text-lg mb-4 flex items-center gap-2">
                <i class="fa fa-list text-[#ed000c]"></i>
                List Belanja Hari Ini
            </div>
            <ul class="list-disc pl-5">
                @forelse($listBelanjaHariIni as $daftar)
                    @foreach($daftar->itemBelanjas as $item)
                        <li>{{ $item->nama_barang }}</li>
                    @endforeach
                @empty
                    <li>- Tidak ada belanja hari ini -</li>
                @endforelse
            </ul>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="font-bold text-lg mb-4 flex items-center gap-2">
                <i class="fa fa-chart-bar text-[#ed000c]"></i>
                Pengeluaran Bulan Ini
            </div>
            <div class="flex justify-center items-center h-32 bg-[#ed000c]/20 rounded">
                <span class="text-[#ed000c] text-lg font-semibold">Grafik untuk pengeluaran</span>
            </div>
            <div class="mt-3 text-right">
                @if($pengeluaranBulanIni)
                    <span class="text-sm text-gray-700">Total: Rp{{ number_format($pengeluaranBulanIni->total_pengeluaran, 0, ',', '.') }}</span>
                @else
                    <span class="text-sm text-gray-700">Data belum tersedia.</span>
                @endif
            </div>
        </div>

        <!-- Ringkasan Harian -->
        <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
            <div class="font-bold text-lg mb-4 flex items-center gap-2">
                <i class="fa fa-calendar-check text-[#ed000c]"></i>
                Ringkasan Harian
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="mb-2">Total Barang: <span class="font-semibold">{{ $totalBarang }}</span></div>
                    <div class="mb-2">Barang Sudah Dibeli: <span class="font-semibold">{{ $barangSudahDibeli }}</span></div>
                    <div>Barang Belum Dibeli: <span class="font-semibold">{{ $barangBelumDibeli }}</span></div>
                </div>
                <div class="text-right">
                    <div class="mb-2">Total Belanja: <span class="font-semibold">Rp{{ number_format($totalBelanja, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection