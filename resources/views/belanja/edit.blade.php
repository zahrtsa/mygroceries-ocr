@extends('layouts.app')
@section('content')
<div class="relative max-w-lg mx-auto my-14 pb-6 rounded-3xl overflow-visible">

    <!-- Decorative gradient blobs -->
    <div class="absolute -top-12 -left-12 w-28 h-28 bg-gradient-to-tr from-rose-200 via-[#ed000c] to-rose-400 rounded-full blur-2xl opacity-30 z-0"></div>
    <div class="absolute -bottom-10 -right-10 w-24 h-24 bg-gradient-to-br from-rose-400 via-white to-[#ed000c] rounded-full blur-2xl opacity-30 z-0"></div>

    <div class="relative z-10 bg-white/90 border border-gray-100 rounded-3xl shadow-[0_8px_40px_-4px_#ed000c26] backdrop-blur-sm px-7 pt-8 pb-12">
        <div class="flex items-center gap-3 mb-7">
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#ed000c] via-rose-400 to-rose-300 flex items-center justify-center shadow-lg ring-4 ring-rose-300/10 animate-bounce-slow">
                <i class="fa fa-edit text-white text-xl"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold bg-gradient-to-r from-[#ed000c] to-rose-400 bg-clip-text text-transparent drop-shadow">Edit Item Belanja</h2>
        </div>

        <form action="{{ route('belanja.item.update', $item->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-[15px] font-semibold text-[#ed000c] mb-1 tracking-wide" for="nama_barang">Nama Barang <span class="text-gray-400">*</span></label>
                <input type="text" name="nama_barang" id="nama_barang" value="{{ $item->nama_barang }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-rose-200 bg-white/60 focus:bg-white/90 focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/20 text-base shadow transition placeholder:text-gray-400"
                    placeholder="Contoh: Indomie Goreng" required>
            </div>

            <div class="flex gap-3">
                <div class="w-1/3">
                    <label class="block text-[15px] font-semibold text-[#ed000c] mb-1" for="qty">Qty <span class="text-gray-400">*</span></label>
                    <input type="number" name="qty" id="qty" min="1" value="{{ $item->qty }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-rose-200 bg-white/60 focus:bg-white/90 focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/20 text-base shadow transition placeholder:text-gray-400"
                        placeholder="1" required>
                </div>
                <div class="w-2/3">
                    <label class="block text-[15px] font-semibold text-[#ed000c] mb-1" for="harga_satuan">Harga Satuan (Rp) <span class="text-gray-400">*</span></label>
                    <input type="number" name="harga_satuan" id="harga_satuan" min="0" step="100" value="{{ $item->harga_satuan }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-rose-200 bg-white/60 focus:bg-white/90 focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/20 text-base shadow transition placeholder:text-gray-400"
                        placeholder="Contoh: 5000" required>
                </div>
            </div>

            <div>
                <label class="block text-[15px] font-semibold text-[#ed000c] mb-1" for="status">Status <span class="text-gray-400">*</span></label>
                <select name="status" id="status"
                    class="w-full px-4 py-2.5 rounded-xl border border-rose-200 bg-white/60 focus:bg-white/90 focus:border-[#ed000c] focus:ring-2 focus:ring-[#ed000c]/20 text-base shadow transition">
                    <option value="Sudah Dibeli" {{ $item->status == 'Sudah Dibeli' ? 'selected' : '' }}>Sudah Dibeli</option>
                    <option value="Belum Dibeli" {{ $item->status == 'Belum Dibeli' ? 'selected' : '' }}>Belum Dibeli</option>
                </select>
            </div>

            <div class="flex justify-end mt-10">
                <button type="submit"
                    class="px-8 py-2.5 font-bold rounded-2xl shadow-lg bg-gradient-to-r from-[#ed000c] via-rose-400 to-[#ed000c] text-white flex items-center gap-3 text-lg tracking-wide
                           hover:from-rose-500 hover:to-[#ed000c] focus:outline-none focus:ring-2 focus:ring-[#ed000c]/40 transition-all active:scale-95 border-0">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>
        <div class="absolute left-1/2 -bottom-5 -translate-x-1/2 w-[70%] h-2 rounded-full bg-gradient-to-r from-[#ed000c]/20 via-rose-300/30 to-rose-400/30 blur-sm"></div>
    </div>
</div>
@include('sweetalert::alert')

<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(-2px);}
    50% { transform: translateY(6px);}
}
.animate-bounce-slow { animation: bounce-slow 2.2s infinite; }
</style>
@endsection
