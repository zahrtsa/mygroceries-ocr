@extends('layouts.app')

@section('content')
    <div class="w-full flex justify-center px-4 py-4 md:py-6">
        <div class="w-full max-w-4xl">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                        Edit Total & Subtotal Struk
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Sesuaikan nilai total dan subtotal jika hasil OCR kurang tepat.
                    </p>
                </div>
                <a href="{{ route('belanja.receipts.index') }}"
                   class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-rose-500">
                    <i class="fa fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>

            {{-- Error validasi --}}
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl">
                    <ul class="text-sm list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CARD LEBAR --}}
            <div class="w-full bg-white border border-slate-200 rounded-3xl shadow-sm px-5 py-6 md:px-7 md:py-7">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start">
                    {{-- Kiri: info struk --}}
                    <div class="md:col-span-2 space-y-4">
                        <div class="flex md:block items-center gap-4">
                            <div class="w-24 h-24 md:w-32 md:h-32 lg:w-36 lg:h-36 rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden">
                                <img src="{{ asset('storage/' . $receipt->file_path) }}"
                                     alt="Struk"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 md:mt-3 space-y-1 text-sm">
                                <p class="font-semibold text-slate-900 break-all">
                                    {{ $receipt->filename }}
                                </p>

                                @if($receipt->daftarBelanja)
                                    <p class="text-xs text-slate-500">
                                        Tanggal belanja:
                                        <span class="font-semibold text-slate-800">
                                            {{ $receipt->daftarBelanja->tanggal_belanja->format('d M Y') }}
                                        </span>
                                    </p>
                                @endif

                                <p class="text-xs text-slate-400">
                                    Diunggah: {{ $receipt->created_at->format('d M Y H:i') }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    Total OCR:
                                    <span class="font-semibold text-slate-800">
                                        {{ $receipt->ocr_total !== null
                                            ? 'Rp ' . number_format((float)$receipt->ocr_total, 0, ',', '.')
                                            : '-' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan: form capsule --}}
                    <div class="md:col-span-3 space-y-5">
                        <form action="{{ route('belanja.receipts.update', $receipt) }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            {{-- Total --}}
                            <div class="space-y-2">
                                <label for="total_amount" class="block text-sm font-medium text-slate-800">
                                    Total (dipakai untuk budget)
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 text-sm text-slate-500">
                                        Rp
                                    </span>
                                    <input
                                        id="total_amount"
                                        type="text"
                                        name="total_amount"
                                        value="{{ old('total_amount', $receipt->total_amount !== null ? number_format($receipt->total_amount, 0, ',', '.') : '') }}"
                                        placeholder="misal: 152.500"
                                        class="w-full rounded-full border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm
                                               text-slate-900 placeholder:text-slate-400
                                               focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                                    >
                                </div>
                                <p class="text-xs text-slate-400">
                                    Gunakan angka saja, boleh memakai titik/koma. Contoh: 125.000
                                </p>
                            </div>

                            {{-- Subtotal --}}
                            <div class="space-y-2">
                                <label for="subtotal_amount" class="block text-sm font-medium text-slate-800">
                                    Subtotal (opsional)
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 text-sm text-slate-500">
                                        Rp
                                    </span>
                                    <input
                                        id="subtotal_amount"
                                        type="text"
                                        name="subtotal_amount"
                                        value="{{ old('subtotal_amount', $receipt->subtotal_amount !== null ? number_format($receipt->subtotal_amount, 0, ',', '.') : '') }}"
                                        placeholder="misal: 140.000"
                                        class="w-full rounded-full border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm
                                               text-slate-900 placeholder:text-slate-400
                                               focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                                    >
                                </div>
                                <p class="text-xs text-slate-400">
                                    Boleh dikosongkan jika tidak diperlukan.
                                </p>
                            </div>

                            {{-- Tombol capsule --}}
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-1">
                                <button type="submit"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5
                                               rounded-full text-sm font-semibold text-white
                                               bg-rose-500 hover:bg-rose-600 shadow-sm
                                               focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-1">
                                    Simpan perubahan
                                </button>

                                <button
                                    type="button"
                                    onclick="if(confirm('Kembalikan total & subtotal ke nilai hasil OCR?')) { document.getElementById('reset-ocr-form').submit(); }"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5
                                           rounded-full text-sm font-medium
                                           border border-slate-300 bg-white text-slate-700 hover:bg-slate-50
                                           focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-1">
                                    Reset ke hasil OCR
                                </button>
                            </div>
                        </form>

                        {{-- Form reset OCR --}}
                        <form id="reset-ocr-form"
                              action="{{ route('belanja.receipts.reset-ocr', $receipt) }}"
                              method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
