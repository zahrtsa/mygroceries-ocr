@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800">
                    Detail Struk
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Lihat gambar struk dan teks hasil ekstraksi OCR.
                </p>
            </div>
            <a href="{{ route('belanja.receipts.index') }}"
               class="inline-flex items-center text-sm text-gray-600 hover:text-rose-600">
                <i class="fa fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col md:flex-row gap-6">
            {{-- Gambar struk --}}
            <div class="md:w-1/3">
                <p class="text-sm font-semibold text-gray-700 mb-2">Gambar Struk</p>
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                    <img src="{{ asset('storage/' . $receipt->file_path) }}"
                         alt="Struk {{ $receipt->filename }}"
                         class="w-full h-auto object-contain">
                </div>
                <p class="mt-2 text-xs text-gray-400 break-all">
                    {{ $receipt->filename }}
                </p>

                @if($receipt->daftarBelanja)
                    <p class="mt-1 text-xs text-gray-500">
                        Terhubung ke List Belanja:
                        <span class="font-semibold text-gray-700">
                            {{ $receipt->daftarBelanja->tanggal_belanja->format('d M Y') }}
                        </span>
                    </p>
                @endif

                <p class="mt-1 text-xs text-gray-400">
                    Diunggah: {{ $receipt->created_at->format('d M Y H:i') }}
                </p>
            </div>

            {{-- Data & teks OCR --}}
            <div class="md:w-2/3 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500">Total (dipakai sistem)</p>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $receipt->total_amount !== null
                                ? 'Rp ' . number_format((float)$receipt->total_amount, 0, ',', '.')
                                : 'Tidak ditemukan' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500">Subtotal</p>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $receipt->subtotal_amount !== null
                                ? 'Rp ' . number_format((float)$receipt->subtotal_amount, 0, ',', '.')
                                : 'Tidak ditemukan' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500">Total (hasil OCR mentah)</p>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $receipt->ocr_total !== null
                                ? 'Rp ' . number_format((float)$receipt->ocr_total, 0, ',', '.')
                                : '-' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500">Status OCR</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $receipt->status_ocr }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        Teks hasil ekstraksi OCR
                    </p>
                    @if($receipt->extracted_text)
                        <pre class="text-xs bg-gray-50 border border-gray-200 rounded-lg p-3 whitespace-pre-wrap max-h-80 overflow-auto">
{{ $receipt->extracted_text }}
                        </pre>
                    @else
                        <p class="text-xs text-gray-500">
                            Tidak ada teks yang tersimpan dari OCR.
                        </p>
                    @endif
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('belanja.receipts.edit', $receipt) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-semibold bg-[#ed1c24] text-white rounded-lg hover:bg-rose-700">
                        <i class="fa fa-pen mr-2"></i> Edit Total/Subtotal
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection
