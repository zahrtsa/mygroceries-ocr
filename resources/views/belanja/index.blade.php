@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto mt-10 mb-8">

    <!-- Title & Add Button -->
    <div class="flex flex-col-reverse justify-between sm:flex-row sm:items-center gap-5 mb-7">
        <h2 class="font-bold text-2xl text-[#ed000c] flex items-center gap-2 tracking-tight">
            <span class="inline-flex items-center rounded-xl bg-gradient-to-r from-[#ed000c]/20 via-rose-400/10 to-rose-400/30 px-2 py-1 drop-shadow">
                <i class="fas fa-list"></i>
            </span>
            List Belanja <span class="text-gray-400 font-medium text-lg">| {{ $tanggalBelanja->format('d F Y') }}</span>
        </h2>
        <a href="{{ route('belanja.item.create') }}"
           class="bg-gradient-to-r from-[#ed000c] to-rose-400 hover:from-rose-500 hover:to-[#ed000c] text-white px-5 py-2.5 rounded-xl shadow-lg font-semibold flex items-center gap-2 text-base transition-all">
            <i class="fa fa-plus"></i> Tambah Item
        </a>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('belanja.item.index') }}" class="flex mb-5 gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang atau kategori..."
               class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/20 transition text-base bg-white shadow-sm"/>
        <button type="submit"
            class="bg-gradient-to-r from-[#ed000c] to-rose-400 text-white px-5 py-2 rounded-xl hover:from-rose-500 hover:to-[#ed000c] transition shadow-lg flex items-center gap-2 font-semibold text-base">
            <i class="fa fa-search"></i> Cari
        </button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto mt-1 rounded-2xl shadow-lg border border-gray-100 bg-white/95">
        <table class="min-w-full text-sm text-gray-700">
            <thead>
                <tr>
                    <th class="p-3 font-semibold text-left bg-gradient-to-r from-[#ed000c]/80 to-rose-400/70 text-white border-none rounded-tl-2xl">Nama Barang</th>
                    <th class="p-3 font-semibold text-center bg-gradient-to-r from-[#ed000c]/80 to-rose-400/70 text-white border-none">Qty</th>
                    <th class="p-3 font-semibold text-center bg-gradient-to-r from-[#ed000c]/80 to-rose-400/70 text-white border-none">Harga Satuan</th>
                    <th class="p-3 font-semibold text-center bg-gradient-to-r from-[#ed000c]/80 to-rose-400/70 text-white border-none">Total Harga</th>
                    <th class="p-3 font-semibold text-center bg-gradient-to-r from-[#ed000c]/80 to-rose-400/70 text-white border-none">Status</th>
                    <th class="p-3 font-semibold text-center bg-gradient-to-r from-[#ed000c]/80 to-rose-400/70 text-white border-none rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itemBelanjas as $item)
                <tr class="transition-all hover:bg-gradient-to-r hover:from-rose-50 hover:to-[#ed000c]/10 border-b">
                    <td class="p-3">
                        <span class="font-medium group-hover:text-[#ed000c]">{{ $item->nama_barang }}</span>
                    </td>
                    <td class="p-3 text-center">{{ $item->qty }}</td>
                    <td class="p-3 text-center">Rp{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="p-3 text-center font-bold text-gray-900">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td class="p-3 text-center">
                        <form method="POST" action="{{ route('belanja.item.update', $item->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" name="status" value="{{ $item->status == 'Sudah Dibeli' ? 'Belum Dibeli' : 'Sudah Dibeli' }}"
                                    class="px-4 py-1 rounded-full font-bold shadow-sm focus:outline-none transition-all
                                    bg-white/60 ring-2 ring-offset-2
                                        {{ $item->status == 'Sudah Dibeli' ? 'ring-emerald-400 text-emerald-700 hover:bg-emerald-50'
                                                                         : 'ring-yellow-400 text-yellow-600 hover:bg-yellow-50' }}">
                                {{ $item->status }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3 flex justify-center gap-1">
                        <a href="{{ route('belanja.item.edit', $item->id) }}"
                            class="inline-flex items-center px-3 py-1 rounded-full bg-white/80 border border-sky-300 text-sky-500 hover:bg-sky-100 hover:text-sky-700 shadow transition font-bold"
                            title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('belanja.item.destroy', $item->id) }}" method="POST" class="inline-block delete-item-form">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1 rounded-full bg-white/80 border border-rose-400 text-rose-600 hover:bg-rose-100 hover:text-rose-700 shadow transition font-bold"
                                title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-5 text-center text-base text-gray-400 bg-gradient-to-r from-rose-100/70 via-white to-[#ed000c]/30 rounded-b-lg">Tidak ada barang belanja untuk hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
