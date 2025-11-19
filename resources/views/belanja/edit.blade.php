@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white shadow-md rounded-lg p-8">
    <h2 class="text-xl font-bold text-[#ed000c] mb-5 flex items-center gap-2">
        <i class="fa fa-edit"></i> Edit Item Belanja
    </h2>
    <form action="{{ route('belanja.item.update', $itemBelanja->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <label class="block font-medium mb-1" for="nama_barang">Nama Barang</label>
            <input type="text" name="nama_barang" id="nama_barang"
                   value="{{ $itemBelanja->nama_barang }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-[#ed000c]" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1" for="qty">Qty</label>
            <input type="number" name="qty" id="qty" min="1"
                   value="{{ $itemBelanja->qty }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-[#ed000c]" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1" for="harga_satuan">Harga Satuan (Rp)</label>
            <input type="number" name="harga_satuan" id="harga_satuan" min="0" step="100"
                   value="{{ $itemBelanja->harga_satuan }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-[#ed000c]" required>
        </div>
        <div class="mb-4">
            <label class="block font-medium mb-1" for="status">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-[#ed000c]" required>
                <option value="Sudah Dibeli" {{ $itemBelanja->status == 'Sudah Dibeli' ? 'selected' : '' }}>Sudah Dibeli</option>
                <option value="Belum Dibeli" {{ $itemBelanja->status == 'Belum Dibeli' ? 'selected' : '' }}>Belum Dibeli</option>
            </select>
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-[#ed000c] text-white px-5 py-2 rounded hover:bg-red-600 flex items-center gap-2">
                <i class="fa fa-save"></i> Update
            </button>
        </div>
    </form>
</div>
@include('sweetalert::alert')
@endsection
