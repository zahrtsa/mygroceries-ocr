@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-12 bg-white shadow-xl rounded-2xl p-8">
    <h2 class="text-xl font-extrabold text-[#ed000c] mb-7 flex items-center gap-3">
        <span class="inline-flex items-center justify-center h-8 w-8 bg-[#ed000c]/10 rounded-full">
            <i class="fa fa-plus-circle text-[#ed000c]"></i>
        </span>
        Tambah Item Belanja
    </h2>
    <form action="{{ route('belanja.item.store') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label class="block font-semibold mb-1" for="nama_barang">Nama Barang</label>
            <input type="text" name="nama_barang" id="nama_barang"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-[#ed000c] focus:ring focus:ring-[#ed000c]/30"
                   placeholder="Contoh: Indomie Goreng" required autofocus>
        </div>
        <div class="flex gap-4">
            <div class="w-1/3">
                <label class="block font-semibold mb-1" for="qty">Qty</label>
                <input type="number" name="qty" id="qty" min="1"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-[#ed000c] focus:ring-[#ed000c]/30"
                       placeholder="1" required>
            </div>
            <div class="w-2/3">
                <label class="block font-semibold mb-1" for="harga_satuan">Harga Satuan (Rp)</label>
                <input type="number" name="harga_satuan" id="harga_satuan" min="0" step="100"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-[#ed000c] focus:ring-[#ed000c]/30"
                       placeholder="Contoh: 5000" required>
            </div>
        </div>
        <div class="flex justify-end mt-10">
            <button type="submit" class="bg-[#ed000c] text-white px-6 py-2 rounded-lg shadow hover:bg-red-600 transition flex items-center gap-2">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@include('sweetalert::alert')
@endsection