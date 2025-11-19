@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-bold text-2xl text-[#ed000c] flex items-center gap-2">
            <i class="fas fa-list"></i>
            List Belanja | {{ $tanggalBelanja->format('d F Y') }}
        </h2>
        <a href="{{ route('belanja.item.create') }}" class="bg-[#ed000c] hover:bg-red-600 text-white px-4 py-2 rounded shadow flex items-center gap-2">
            <i class="fa fa-plus"></i>
            Tambah Item
        </a>
    </div>
    <form method="GET" action="{{ route('belanja.item.index') }}" class="flex mb-4 gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari apapun..."
               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/30 transition"/>
        <button type="submit" class="bg-[#ed000c] text-white px-4 py-2 rounded-lg hover:bg-red-600 transition flex items-center gap-2">
            <i class="fa fa-search"></i> Cari
        </button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full rounded-lg shadow text-sm bg-white">
            <thead>
                <tr class="bg-[#ed000c]/10 text-[#ed000c] border-b">
                    <th class="p-3 font-semibold text-left">Nama Barang</th>
                    <th class="p-3 font-semibold text-center">Qty</th>
                    <th class="p-3 font-semibold text-center">Harga Satuan</th>
                    <th class="p-3 font-semibold text-center">Total Harga</th>
                    <th class="p-3 font-semibold text-center">Status</th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itemBelanjas as $item)
                <tr class="border-b hover:bg-[#ed000c]/5 transition">
                    <td class="p-3">{{ $item->nama_barang }}</td>
                    <td class="p-3 text-center">{{ $item->qty }}</td>
                    <td class="p-3 text-center">Rp{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="p-3 text-center">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td class="p-3 text-center">
                        <form method="POST" action="{{ route('belanja.item.update', $item->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" name="status" value="{{ $item->status == 'Sudah Dibeli' ? 'Belum Dibeli' : 'Sudah Dibeli' }}"
                                class="px-4 py-1 rounded-full text-white font-semibold transition duration-150
                                       {{ $item->status == 'Sudah Dibeli' ? 'bg-green-500 hover:bg-green-600' : 'bg-yellow-500 hover:bg-yellow-600' }}">
                                {{ $item->status }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3 text-center space-x-2">
                        <a href="{{ route('belanja.item.edit', $item->id) }}" class="inline-block px-3 py-1 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('belanja.item.destroy', $item->id) }}" method="POST" class="inline-block delete-item-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1 rounded-full bg-red-500 text-white hover:bg-red-600 transition">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-3 text-center text-sm text-gray-500">Tidak ada barang belanja untuk hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@include('sweetalert::alert')

{{-- Script konfirmasi hapus pakai SweetAlert --}}
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
