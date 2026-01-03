@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto my-8 sm:my-10 px-4" x-data="{ openDate: false }">

    <div class="relative bg-white rounded-3xl shadow-xl border border-rose-100 overflow-hidden">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-rose-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-[#ed000c] flex items-center justify-center shadow-md text-white">
                    <i class="fa fa-calendar-day text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">
                        Rekap Harian
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Lihat detail belanja untuk tanggal yang kamu pilih.
                    </p>
                </div>
            </div>
        </div>

        {{-- FORM PILIH TANGGAL (dropdown custom, auto submit) --}}
        <form method="GET" action="{{ route('belanja.rekapanharian') }}" class="px-6 pt-5 pb-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                {{-- Label kiri --}}
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#ed000c]/10 text-[#ed000c]">
                        <i class="fa fa-calendar-alt text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Tanggal rekap
                        </p>
                        <p class="text-[11px] text-slate-500">
                            Pilih tanggal belanja yang ingin kamu lihat.
                        </p>
                    </div>
                </div>

                {{-- Input + dropdown custom --}}
                <div class="w-full sm:w-1/2 sm:max-w-sm sm:ml-auto">
                    <div class="relative" >
                        {{-- tombol tampilan tanggal --}}
                        <button type="button"
                                @click="openDate = !openDate"
                                class="w-full flex items-center justify-between rounded-xl border border-slate-200 bg-white
                                       px-3 py-2.5 text-sm text-slate-800 shadow-sm hover:border-[#ed000c]
                                       focus:outline-none focus:ring-2 focus:ring-[#ed000c]/25 transition">
                            <span class="flex items-center gap-2">
                                <i class="fa fa-calendar-alt text-slate-400"></i>
                                <span class="truncate">
                                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                                </span>
                            </span>
                            <i class="fa fa-chevron-down text-[11px] text-slate-400"
                               :class="{ 'rotate-180': openDate }"></i>
                        </button>

                        {{-- dropdown list tanggal --}}
                        <div x-show="openDate"
                             @click.away="openDate = false"
                             x-transition
                             class="absolute z-20 mt-1 w-full rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
                            <div class="max-h-56 overflow-y-auto py-1">
                                @foreach($optionsTanggal as $tgl)
                                    @php
                                        $label = \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y');
                                        $active = $tanggal == $tgl;
                                    @endphp
                                    <button
                                        type="submit"
                                        name="tanggal"
                                        value="{{ $tgl }}"
                                        class="w-full text-left px-3 py-2 text-xs sm:text-sm flex items-center justify-between
                                               {{ $active ? 'bg-rose-50 text-[#ed000c] font-semibold' : 'text-slate-700 hover:bg-slate-50' }}"
                                        @click="openDate = false"
                                    >
                                        <span class="truncate">{{ $label }}</span>
                                        @if($active)
                                            <i class="fa fa-check text-[11px]"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        {{-- INFO TANGGAL --}}
        <div class="px-6 pt-2 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="text-sm text-slate-700">
                <span class="text-slate-500">Tanggal:</span>
                <span class="ml-1 font-semibold text-[#ed000c]">
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="px-4 pb-4">
            <div class="rounded-2xl border border-rose-100 overflow-x-auto">
                <table class="w-full text-xs sm:text-sm md:text-base text-center">
                    <thead>
                        <tr class="bg-gradient-to-r from-[#ed000c] to-rose-400 text-white text-[11px] sm:text-xs uppercase tracking-wide">
                            <th class="py-3 px-4 text-left rounded-tl-2xl">Nama Barang</th>
                            <th class="py-3 px-4">Qty</th>
                            <th class="py-3 px-4">Harga Satuan</th>
                            <th class="py-3 px-4">Total Harga</th>
                            <th class="py-3 px-4 rounded-tr-2xl">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="border-b border-rose-100 odd:bg-white even:bg-rose-50/60 hover:bg-rose-50 transition">
                                <td class="py-2.5 px-4 md:pl-6 text-left font-medium text-slate-800">
                                    {{ $item->nama_barang }}
                                </td>
                                <td class="py-2.5 px-4 text-slate-700">
                                    {{ $item->qty }}
                                </td>
                                <td class="py-2.5 px-4 text-slate-700">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 font-semibold text-slate-900">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] sm:text-[11px] font-semibold
                                               inline-flex items-center gap-1 border
                                               {{ $item->status == 'Sudah Dibeli'
                                                   ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                   : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full
                                                     {{ $item->status == 'Sudah Dibeli' ? 'bg-emerald-500' : 'bg-amber-400' }}">
                                        </span>
                                        <span class="inline md:hidden">
                                            {{ $item->status == 'Sudah Dibeli' ? 'Sudah' : 'Belum' }}
                                        </span>
                                        <span class="hidden md:inline">
                                            {{ $item->status }}
                                        </span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-7 text-slate-400 bg-rose-50 rounded-b-2xl">
                                    Tidak ada data belanja pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $items->onEachSide(1)->links() }}
                </div>
            @endif
        </div>

        {{-- FOOTER TOTAL --}}
        <div class="px-6 pb-6 flex justify-end">
            <div class="inline-flex items-center rounded-full bg-[#ed000c]/5 border border-rose-100
                        px-5 sm:px-6 py-2 text-sm sm:text-base font-semibold text-[#ed000c] shadow-sm">
                Total Belanja: Rp {{ number_format($totalBelanja, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>
@endsection
