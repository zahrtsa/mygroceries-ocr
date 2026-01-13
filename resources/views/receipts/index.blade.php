@extends('layouts.app')

@section('content')
@php
    $tanggalTotals = $tanggalTotals ?? collect();
@endphp

<div class="max-w-6xl mx-auto px-4 lg:px-0"
     x-data="receiptPage()"
    x-init='init(@json($tanggalTotals), "{{ now()->format("Y-m-d") }}")'
     x-on:tanggal-belanja-changed.window="setSelected($event.detail)"
>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800">
                Upload & Daftar Struk Belanja
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Upload struk untuk diproses OCR dan otomatis mengurangi budget bulanan kamu.
            </p>
        </div>
    </div>

    {{-- Error validasi --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Upload --}}
    <div class="bg-white rounded-3xl shadow-sm mb-8 border border-gray-100">
        <form id="uploadForm"
              action="{{ route('belanja.receipts.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="flex flex-col gap-4 lg:gap-6 px-5 md:px-6 lg:px-8 py-5">
            @csrf

            <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                {{-- Datepicker jQuery UI --}}
                <div class="w-full lg:w-1/3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Tanggal Belanja
                    </label>
                    <p class="text-[11px] text-gray-500 mb-2">
                        Pilih tanggal transaksi pada struk. Kalau dikosongkan, sistem akan memakai hari ini.
                    </p>

                    <div class="relative max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center">
                            <i class="fa-regular fa-calendar text-gray-400 text-sm"></i>
                        </div>

                        <input
                            type="text"
                            name="tanggal_belanja"
                            class="datePicker w-full pl-10 pr-3 py-2.5 text-xs md:text-sm text-slate-800
                                   rounded-full border border-slate-200 bg-white shadow-sm
                                   focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            autocomplete="off"
                            placeholder="yyyy-mm-dd"
                            value="{{ old('tanggal_belanja', now()->format('Y-m-d')) }}"
                        >
                    </div>

                    <div class="mt-2 text-[11px]">
                        <p class="text-gray-500">
                            Tanggal terpilih:
                            <span class="font-semibold text-gray-800" x-text="formattedDate"></span>
                        </p>
                        <p class="mt-1"
                           :class="hasTotal ? 'text-emerald-600' : 'text-gray-500'">
                            <template x-if="hasTotal">
                                <span>
                                    Total belanja di tanggal ini:
                                    <span class="font-semibold" x-text="formattedTotal"></span>
                                </span>
                            </template>
                            <template x-if="!hasTotal">
                                <span>
                                    Belum ada belanja yang tercatat di tanggal ini. Struk yang diupload akan membuat catatan baru.
                                </span>
                            </template>
                        </p>
                    </div>
                </div>

                {{-- Dropzone --}}
                <div class="w-full lg:flex-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Pilih Gambar Struk
                    </label>

                    <div id="dropzone"
                         class="relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-5 text-center cursor-pointer transition hover:border-[#ed1c24] hover:bg-rose-50">
                        <input
                            id="receipt_image"
                            type="file"
                            name="receipt_image"
                            accept="image/*"
                            required
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        >

                        <div class="pointer-events-none flex flex-col items-center w-full">
                            <div id="previewWrapper" class="w-full mb-3 hidden">
                                <img id="previewImage"
                                     src=""
                                     alt="Preview struk"
                                     class="mx-auto max-h-40 w-auto object-contain rounded-lg border border-gray-200 bg-white">
                            </div>

                            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow">
                                <i class="fa-solid fa-receipt text-[#ed1c24]"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-800">
                                Drop file di sini atau klik untuk pilih
                            </p>
                            <p id="fileName" class="mt-1 text-xs text-gray-600">
                                Belum ada file yang dipilih.
                            </p>
                            <p class="mt-1 text-[11px] text-gray-400">
                                Format: JPG, JPEG, PNG. Maksimal 4 MB.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="mt-4 flex justify-end">
                <button id="uploadBtn" type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-[#ed1c24] hover:bg-rose-700
                               text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-md shadow-[#ed1c24]/30
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg id="upload-spinner"
                         class="animate-spin -ml-1 mr-1 h-5 w-5 text-white hidden"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="uploadBtnText" class="text-center">
                        Upload & Proses OCR
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Struk --}}
    <div class="pb-10">
        @if($receipts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($receipts as $receipt)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col">
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mb-2">
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2.5 py-1 border border-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z" />
                                    <path d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9z" />
                                </svg>
                                {{ ($receipt->transaction_date ?? $receipt->created_at)->format('d M Y') }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-medium text-emerald-700 border border-emerald-100">
                                OCR {{ $receipt->status_ocr ?? 'Selesai' }}
                            </span>
                        </div>

                        <button type="button"
                                class="w-full h-40 overflow-hidden rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 transition"
                                onclick="openPreview('{{ asset('storage/' . $receipt->file_path) }}')">
                            <img src="{{ asset('storage/' . $receipt->file_path) }}"
                                 alt="Struk"
                                 class="w-full h-40 object-cover">
                        </button>

                        <div class="mt-3 space-y-1.5">
                            <p class="text-xs font-semibold text-gray-800 break-all">
                                {{ $receipt->filename }}
                            </p>

                            @if($receipt->daftarBelanja)
                                <p class="text-xs text-gray-500">
                                    Tanggal belanja:
                                    <span class="font-medium text-gray-700">
                                        {{ $receipt->daftarBelanja->tanggal_belanja->format('d M Y') }}
                                    </span>
                                </p>
                            @endif

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <p class="text-gray-500">Total</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $receipt->total_amount !== null
                                            ? 'Rp ' . number_format((float)$receipt->total_amount, 0, ',', '.')
                                            : 'Tidak ditemukan' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Subtotal</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $receipt->subtotal_amount !== null
                                            ? 'Rp ' . number_format((float)$receipt->subtotal_amount, 0, ',', '.')
                                            : 'Tidak ditemukan' }}
                                    </p>
                                </div>
                            </div>

                            <p class="text-[11px] text-gray-400">
                                Diunggah: {{ $receipt->created_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-2">
                            <div class="flex gap-2">
                                <a href="{{ route('belanja.receipts.edit', $receipt) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-[11px] font-medium bg-rose-500 text-white rounded-full hover:bg-rose-600">
                                    Edit
                                </a>

                                <form action="{{ route('belanja.receipts.reset-ocr', $receipt) }}"
                                      method="POST"
                                      onsubmit="return confirm('Kembalikan total ke hasil OCR?')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-[11px] font-medium bg-gray-50 text-gray-700 rounded-full border border-gray-200 hover:bg-gray-100">
                                        Reset OCR
                                    </button>
                                </form>
                            </div>

                            <button type="button"
                                    onclick="confirmDelete({{ $receipt->id }})"
                                    class="px-3 py-1.5 text-[11px] font-medium text-red-600 border border-red-500 rounded-full hover:bg-red-50">
                                Hapus
                            </button>

                            <form id="delete-form-{{ $receipt->id }}"
                                  action="{{ route('belanja.receipts.destroy', $receipt) }}"
                                  method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-dashed border-gray-300 text-center">
                <p class="text-gray-600 font-medium">
                    Belum ada struk yang diupload.
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Mulai dengan mengupload struk belanja di atas.
                </p>
            </div>
        @endif
    </div>
</div>

<div id="previewModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-transparent max-w-3xl w-full mx-4">
        <div class="relative">
            <button type="button"
                    onclick="closePreview()"
                    class="absolute top-2 right-2 z-30 bg-white/95 text-gray-700 p-2 rounded-full shadow">
                ✕
            </button>
            <img id="previewImg"
                 src=""
                 alt="Preview"
                 class="w-full max-h-[80vh] object-contain rounded-lg shadow-lg bg-white">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function receiptPage() {
        return {
            totalsByDate: {},
            today: '',
            selectedDate: '',
            formattedDate: '',
            formattedTotal: '',
            hasTotal: false,

            init: function (totals, today) {
                this.totalsByDate = totals || {};
                this.today = today;

                var input = document.querySelector('.datePicker');
                if (input && input.value) {
                    this.selectedDate = input.value;
                } else {
                    this.selectedDate = today;
                }

                this.updateInfo();
            },

            setSelected: function (dateStr) {
                this.selectedDate = dateStr || this.today;
                this.updateInfo();
            },

            updateInfo: function () {
                var date = this.selectedDate || this.today;
                this.formattedDate = this.formatHuman(date);
                var total = this.totalsByDate[date] || 0;
                this.hasTotal = total > 0;
                this.formattedTotal = this.formatCurrency(total);
            },

            formatHuman: function (dateStr) {
                if (!dateStr) return '-';
                var d = new Date(dateStr);
                if (isNaN(d)) return dateStr;
                return d.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            },

           formatCurrency: function (n) {
                n = Number(n || 0);

                // bulatkan ke rupiah (tanpa desimal)
                n = Math.round(n);

                // pisah ribuan dengan titik, tanpa koma
                return 'Rp ' + n.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

        }
    }

    function openPreview(url) {
        var modal = document.getElementById('previewModal');
        var img   = document.getElementById('previewImg');
        img.src = url;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closePreview() {
        var modal = document.getElementById('previewModal');
        var img   = document.getElementById('previewImg');
        img.src = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('previewModal').classList.contains('hidden')) {
            closePreview();
        }
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Struk?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    var dropzone    = document.getElementById('dropzone');
    var fileInput   = document.getElementById('receipt_image');
    var fileNameTxt = document.getElementById('fileName');
    var previewImg  = document.getElementById('previewImage');
    var previewWrap = document.getElementById('previewWrapper');

    function handleFile(file) {
        if (!file) return;
        fileNameTxt.textContent = file.name;

        var reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewWrap.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    if (dropzone && fileInput) {
        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropzone.classList.add('border-[#ed1c24]', 'bg-rose-50');
        });
        dropzone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            dropzone.classList.remove('border-[#ed1c24]', 'bg-rose-50');
        });
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('border-[#ed1c24]', 'bg-rose-50');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                handleFile(e.dataTransfer.files[0]);
            }
        });
        fileInput.addEventListener('change', function (e) {
            if (e.target.files && e.target.files[0]) {
                handleFile(e.target.files[0]);
            } else {
                fileNameTxt.textContent = 'Belum ada file yang dipilih.';
                previewImg.src = '';
                previewWrap.classList.add('hidden');
            }
        });
    }

    document.getElementById('uploadForm')?.addEventListener('submit', function () {
        var btn     = document.getElementById('uploadBtn');
        var spinner = document.getElementById('upload-spinner');
        var btnText = document.getElementById('uploadBtnText');

        if (btn.disabled) return;
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Memproses...';
    });

    // Inisialisasi jQuery UI Datepicker + kirim event ke Alpine
    $(function() {
        $('.datePicker').datepicker({
            dateFormat: 'yy-mm-dd',
            showOtherMonths: true,
            selectOtherMonths: true,
            onSelect: function(dateText) {
                const root = this.closest('[x-data]');
                if (root) {
                    root.dispatchEvent(new CustomEvent('tanggal-belanja-changed', {
                        detail: dateText,
                        bubbles: true
                    }));
                }
            }
        }).on('change', function() {
            const dateText = this.value;
            const root = this.closest('[x-data]');
            if (root) {
                root.dispatchEvent(new CustomEvent('tanggal-belanja-changed', {
                    detail: dateText,
                    bubbles: true
                }));
            }
        });
    });
</script>
@endpush
