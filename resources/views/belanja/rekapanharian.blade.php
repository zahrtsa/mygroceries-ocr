@extends('layouts.app')

@section('content')
<div class="relative max-w-3xl mx-auto my-12 bg-white/95 rounded-3xl shadow-2xl border border-rose-100 px-0 pb-10 pt-7 overflow-visible">

    <!-- Decorative gradient blob -->
    <div class="absolute -top-12 -left-12 w-36 h-36 bg-gradient-to-tr from-[#ed000c] via-rose-400 to-white rounded-full blur-2xl opacity-20 z-0"></div>

    <!-- Header -->
    <div class="flex items-center gap-4 mb-7 pl-7">
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#ed000c] to-rose-300 flex items-center justify-center shadow-lg ring-4 ring-rose-300/10 animate-bounce-slow">
            <i class="fa fa-calendar-day text-white text-2xl"></i>
        </div>
        <h2 class="text-xl sm:text-2xl font-extrabold bg-gradient-to-r from-[#ed000c] to-rose-400 bg-clip-text text-transparent drop-shadow">Rekap Harian</h2>
    </div>

    <!-- Form Pilih Tanggal -->
    <form method="GET" action="{{ route('belanja.rekapanharian') }}"
          class="mb-7 flex gap-3 items-center pl-7 pr-7">
        <label for="tanggal" class="text-gray-700 font-semibold shrink-0">Pilih Tanggal:</label>
        <select id="tanggal" name="tanggal"
            class="px-4 py-2 rounded-xl border border-rose-200 bg-white/80 focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/15 shadow transition w-fit text-base">
            @foreach($optionsTanggal as $tgl)
                <option value="{{ $tgl }}" @selected($tanggal == $tgl)>
                    {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}
                </option>
            @endforeach
        </select>
        <button type="submit"
            class="bg-gradient-to-r from-[#ed000c] to-rose-400 hover:from-rose-500 hover:to-[#ed000c] text-white px-6 py-2 rounded-xl shadow transition flex items-center gap-2 font-semibold">
            <i class="fa fa-search"></i>
        </button>
    </form>

    <!-- Total Belanja Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-2 mb-4 pl-7 pr-7">
        <div class="text-lg font-semibold text-gray-800">
            <span class="text-gray-500">Tanggal:</span>
            <span class="text-[#ed000c] font-medium">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span>
        </div>
        <div class="text-xl font-bold text-[#ed000c] tracking-wide bg-[#ed000c]/10 px-4 py-2 rounded-2xl shadow-sm">
            Total Belanja: Rp {{ number_format($totalBelanja, 0, ',', '.') }}
        </div>
    </div>

    <!-- Table -->
    <div class="px-4">
        <div class="rounded-2xl border border-rose-100 overflow-x-auto">
            <table class="w-full text-center">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gradient-to-r from-[#ed000c] to-rose-400 text-white font-bold rounded-tl-xl">Nama Barang</th>
                        <th class="py-3 px-4 bg-gradient-to-r from-[#ed000c] to-rose-400 text-white font-bold">Qty</th>
                        <th class="py-3 px-4 bg-gradient-to-r from-[#ed000c] to-rose-400 text-white font-bold">Harga Satuan</th>
                        <th class="py-3 px-4 bg-gradient-to-r from-[#ed000c] to-rose-400 text-white font-bold">Total Harga</th>
                        <th class="py-3 px-4 bg-gradient-to-r from-[#ed000c] to-rose-400 text-white font-bold rounded-tr-xl">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="odd:bg-white even:bg-rose-50 hover:bg-[#ed000c]/5 transition-all border-b border-rose-100">
                        <td class="py-2 px-4 text-left font-medium text-[#ed000c]">{{ $item->nama_barang }}</td>
                        <td class="py-2 px-4">{{ $item->qty }}</td>
                        <td class="py-2 px-4">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-2 px-4 font-bold text-gray-900">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td class="py-2 px-4">
                            <span class="px-3 py-1 rounded-full font-semibold shadow-sm text-sm
                                {{ $item->status == 'Sudah Dibeli'
                                    ? 'bg-gradient-to-r from-emerald-400/25 via-emerald-100/50 to-white text-emerald-700 ring-2 ring-emerald-200'
                                    : 'bg-gradient-to-r from-yellow-300/40 via-yellow-100/70 to-white text-yellow-700 ring-2 ring-yellow-200' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-7 text-gray-400 bg-rose-50 rounded-b-2xl">Tidak ada data belanja pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Total Belanja Footer -->
    <div class="mt-7 flex justify-end px-8">
        <span class="text-lg sm:text-xl font-bold text-[#ed000c] bg-gradient-to-r from-[#ed000c]/10 to-rose-100 py-2 px-8 rounded-full shadow">Total Belanja: Rp {{ number_format($totalBelanja, 0, ',', '.') }}</span>
    </div>
</div>

{{-- Fancy bounce for header icon --}}
<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(-3px);}
    50% { transform: translateY(7px);}
}
.animate-bounce-slow { animation: bounce-slow 2.5s infinite; }
</style>
@endsection
