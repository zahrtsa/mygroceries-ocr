@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    {{-- List Belanja Hari Ini --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">List Belanja Hari Ini ({{ \Carbon\Carbon::today()->format('d-m-Y') }})</h2>
        @if($listBelanjaHariIni->count() > 0)
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Nama Barang</th>
                        <th class="border px-4 py-2">Qty</th>
                        <th class="border px-4 py-2">Harga Satuan</th>
                        <th class="border px-4 py-2">Total Harga</th>
                        <th class="border px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listBelanjaHariIni as $daftar)
                        @foreach($daftar->itemBelanjas as $item)
                        <tr>
                            <td class="border px-4 py-2">{{ $item->nama_barang }}</td>
                            <td class="border px-4 py-2">{{ $item->qty }}</td>
                            <td class="border px-4 py-2">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="border px-4 py-2">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td class="border px-4 py-2">{{ $item->status }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Tidak ada belanja hari ini.</p>
        @endif
    </div>

    {{-- Ringkasan Harian --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">Ringkasan Harian</h2>
        <ul class="list-disc list-inside">
            <li>Total Barang: {{ $totalBarang }}</li>
            <li>Total Belanja: Rp {{ number_format($totalBelanja, 0, ',', '.') }}</li>
            <li>Barang Sudah Dibeli: {{ $barangSudahDibeli }}</li>
            <li>Barang Belum Dibeli: {{ $barangBelumDibeli }}</li>
        </ul>
    </div>

    {{-- Pengeluaran Bulan Ini --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">Pengeluaran Bulan Ini ({{ \Carbon\Carbon::now()->format('F Y') }})</h2>
        @if($pengeluaranBulanIni)
            <p>Total Pengeluaran: Rp {{ number_format($pengeluaranBulanIni->total_pengeluaran, 0, ',', '.') }}</p>
            <p>Saldo Bersih: Rp {{ number_format($pengeluaranBulanIni->saldo_bersih, 0, ',', '.') }}</p>
        @else
            <p>Data pengeluaran bulan ini belum tersedia.</p>
        @endif
    </div>
</div>
@endsection
