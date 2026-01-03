@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-6 sm:mt-10 mb-8 px-4 sm:px-6">

    {{-- TITLE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <h2 class="font-bold text-2xl text-[#ed000c] flex flex-wrap items-center gap-2 tracking-tight">
            <span class="inline-flex items-center rounded-xl bg-[#ed000c]/10 px-2 py-1">
                <i class="fas fa-list"></i>
            </span>
            <span>List Belanja</span>
            <span class="text-gray-400 font-medium text-lg">
                | {{ $tanggalBelanja->format('d F Y') }}
            </span>
        </h2>

        {{-- Tombol Tambah (desktop: di kanan header, tablet/HP: akan muncul di bawah search) --}}
        <div class="hidden md:block md:self-end">
            <a href="{{ route('belanja.item.create') }}"
               class="inline-flex items-center gap-2 bg-[#ed000c] hover:bg-red-600 text-white
                      px-5 py-2.5 rounded-xl shadow-md font-semibold text-base transition">
                <i class="fa fa-plus"></i>
                Tambah Item
            </a>
        </div>
    </div>

    {{-- SEARCH --}}
    <form method="GET" action="{{ route('belanja.item.index') }}"
          class="flex flex-row items-center gap-2 mb-3">
        <div class="flex-1">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari barang atau kategori..."
                class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-white
                       text-sm md:text-base shadow-sm
                       focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/20 transition"
            />
        </div>
        <button
            type="submit"
            class="inline-flex items-center justify-center gap-2 bg-[#ed000c] hover:bg-red-600
                   text-white px-4 md:px-5 py-2 rounded-xl shadow-md font-semibold
                   text-sm md:text-base transition">
            <i class="fa fa-search"></i>
            <span class="hidden xs:inline">Cari</span>
        </button>
    </form>

    {{-- Tombol Tambah (tablet & HP: di bawah search, desktop: disembunyikan) --}}
    <div class="mb-4 md:hidden">
        <a href="{{ route('belanja.item.create') }}"
           class="inline-flex items-center gap-2 bg-[#ed000c] hover:bg-red-600 text-white
                  px-4 py-2 rounded-xl shadow-md font-semibold text-sm sm:text-base transition">
            <i class="fa fa-plus"></i>
            Tambah Item
        </a>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto rounded-2xl shadow-md border border-slate-100 bg-white">
        <table class="min-w-full text-xs sm:text-sm md:text-base text-slate-700">
            <thead>
                <tr class="bg-gradient-to-r from-[#ed000c]/90 to-rose-400/80
                           text-white text-xs uppercase tracking-wide">
                    <th class="p-3 text-left rounded-tl-2xl">Nama Barang</th>
                    <th class="p-3 text-center">Qty</th>
                    <th class="p-3 text-center">Harga Satuan</th>
                    <th class="p-3 text-center">Total Harga</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itemBelanjas as $item)
                    @php
                        $isSudah = $item->status === 'Sudah Dibeli';
                        $nextStatus = $isSudah ? 'Belum Dibeli' : 'Sudah Dibeli';
                    @endphp

                    <tr class="border-b border-slate-100 odd:bg-white even:bg-rose-50/60
                               hover:bg-rose-50 transition">
                        <td class="p-3 md:pl-6 text-left font-medium text-slate-900">
                            {{ $item->nama_barang }}
                        </td>
                        <td class="p-3 text-center">
                            {{ $item->qty }}
                        </td>
                        <td class="p-3 text-center">
                            Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center font-semibold text-slate-900">
                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center">
                            {{-- TOGGLE STATUS: desktop teks penuh, mobile teks pendek --}}
                            <form method="POST" action="{{ route('belanja.item.update', $item->id) }}">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="nama_barang" value="{{ $item->nama_barang }}">
                                <input type="hidden" name="qty" value="{{ $item->qty }}">
                                <input type="hidden" name="harga_satuan" value="{{ $item->harga_satuan }}">
                                <input type="hidden" name="status" value="{{ $nextStatus }}">

                                <button
                                    type="submit"
                                    class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-full
                                           text-[10px] sm:text-[11px] md:text-[11px]
                                           font-semibold inline-flex items-center gap-1 shadow-sm ring-2
                                           ring-offset-1 transition
                                           {{ $isSudah
                                               ? 'bg-emerald-50 text-emerald-700 ring-emerald-300 hover:bg-emerald-100'
                                               : 'bg-amber-50 text-amber-700 ring-amber-300 hover:bg-amber-100' }}"
                                    title="Klik untuk ubah ke {{ $nextStatus }}">
                                    <span class="h-1.5 w-1.5 rounded-full
                                                 {{ $isSudah ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>

                                    <span class="inline md:hidden">{{ $isSudah ? 'Sudah' : 'Belum' }}</span>
                                    <span class="hidden md:inline">{{ $item->status }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-center gap-2">
                                <a
                                    href="{{ route('belanja.item.edit', $item->id) }}"
                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-full
                                           bg-white border border-sky-300 text-sky-500 hover:bg-sky-50
                                           hover:text-sky-700 shadow-sm text-xs md:text-sm font-semibold transition"
                                    title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('belanja.item.destroy', $item->id) }}"
                                      method="POST"
                                      class="delete-item-form inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-full
                                               bg-white border border-rose-300 text-rose-600 hover:bg-rose-50
                                               hover:text-rose-700 shadow-sm text-xs md:text-sm font-semibold transition"
                                        title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="p-5 text-center text-base text-slate-400 bg-rose-50 rounded-b-2xl">
                            Tidak ada barang belanja untuk hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION: tampil di semua mode view --}}
    @if($itemBelanjas instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $itemBelanjas->onEachSide(1)->links() }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.delete-item-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus item?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ed000c',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
