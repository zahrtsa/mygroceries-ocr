@extends('layouts.app')

@section('content')
@php
    $tanggalTotals = $tanggalTotals ?? collect();
@endphp

<div class="max-w-6xl mx-auto px-4 lg:px-0"
     x-data="receiptPage()"
     x-init="init({!! $tanggalTotals->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}, '{{ now()->format('Y-m-d') }}')"
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
                {{-- Datepicker custom --}}
                <div class="w-full lg:w-1/3"
                     x-data="customDatepicker('{{ now()->format('Y-m-d') }}')"
                     x-init="
                        init();
                        $watch('value', function (val) {
                            $root.selectedDate = val || $root.today;
                            $root.updateInfo();
                        });
                     "
                >
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
                            readonly
                            x-on:click="toggle()"
                            x-on:keydown.escape.window="open = false"
                            x-model="displayValue"
                            placeholder="dd/mm/yyyy"
                            class="w-full pl-10 pr-10 py-2.5 text-xs md:text-sm text-slate-800 placeholder:text-slate-400
                                   rounded-full border border-slate-200 bg-slate-50 shadow-sm cursor-pointer
                                   focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        >

                        <input type="hidden" name="tanggal_belanja" x-model="value">

                        <button type="button"
                                x-show="value"
                                x-on:click="clear()"
                                class="absolute inset-y-0 right-8 flex items-center text-xs text-slate-400 hover:text-slate-600">
                            ✕
                        </button>

                        <div x-show="open"
                             x-transition
                             x-on:click.outside="open = false"
                             class="absolute z-50 mt-2 w-72 bg-white rounded-2xl shadow-lg border border-slate-100 p-3">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button"
                                        class="p-1.5 rounded-full hover:bg-slate-100"
                                        x-on:click="prevMonth()">
                                    <i class="fa-solid fa-chevron-left text-xs text-slate-500"></i>
                                </button>
                                <div class="text-sm font-semibold text-slate-800" x-text="monthLabel"></div>
                                <button type="button"
                                        class="p-1.5 rounded-full hover:bg-slate-100"
                                        x-on:click="nextMonth()">
                                    <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-7 gap-1 mb-1">
                                <template x-for="d in ['Su','Mo','Tu','We','Th','Fr','Sa']" :key="d">
                                    <div class="text-[11px] text-center text-slate-400" x-text="d"></div>
                                </template>
                            </div>

                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="blank in blanks" :key="'b'+blank">
                                    <div class="h-7 text-xs"></div>
                                </template>

                                <template x-for="day in days" :key="'d'+day">
                                    <button type="button"
                                            class="h-7 w-7 mx-auto flex items-center justify-center text-xs rounded-full transition"
                                            :class="{
                                                'bg-rose-500 text-white font-semibold': isSelected(day),
                                                'bg-slate-900 text-white font-semibold': isToday(day) && !isSelected(day),
                                                'text-slate-700 hover:bg-slate-100': !isToday(day) && !isSelected(day)
                                            }"
                                            x-text="day"
                                            x-on:click="select(day)">
                                    </button>
                                </template>
                            </div>

                            <div class="mt-2 flex justify-end">
                                <button type="button"
                                        class="px-3 py-1.5 rounded-full text-[11px] font-medium text-rose-600 hover:bg-rose-50"
                                        x-on:click="selectToday()">
                                    Today
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 text-[11px]">
                        <p class="text-gray-500">
                            Tanggal terpilih:
                            <span class="font-semibold text-gray-800" x-text=" $root.formattedDate "></span>
                        </p>
                        <p class="mt-1"
                           :class="$root.hasTotal ? 'text-emerald-600' : 'text-gray-500'">
                            <template x-if="$root.hasTotal">
                                <span>
                                    Total belanja di tanggal ini:
                                    <span class="font-semibold" x-text="$root.formattedTotal"></span>
                                </span>
                            </template>
                            <template x-if="!$root.hasTotal">
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
    function customDatepicker(initial) {
        return {
            open: false,
            value: '',
            displayValue: '',
            month: 0,
            year: 0,
            days: [],
            blanks: [],
            monthNames: [
                'January','February','March','April','May','June',
                'July','August','September','October','November','December'
            ],

            get monthLabel() {
                return this.monthNames[this.month] + ' ' + this.year;
            },

            init: function () {
                var iso = initial || new Date().toISOString().slice(0,10);
                this.fromString(iso);
                this.buildCalendar();
            },

            toggle: function () { this.open = !this.open; },
            clear:  function () { this.value = ''; this.displayValue = ''; },

            fromString: function (iso) {
                var parts = iso.split('-');
                if (parts.length !== 3) return;
                this.year  = parseInt(parts[0], 10);
                this.month = parseInt(parts[1], 10) - 1;
                var day    = parseInt(parts[2], 10);
                this.value = iso;
                this.displayValue = this.formatDisplay(new Date(this.year, this.month, day));
            },

            formatDisplay: function (date) {
                if (!date || isNaN(date)) return '';
                var d  = String(date.getDate()).padStart(2, '0');
                var m  = String(date.getMonth() + 1).padStart(2, '0');
                var yy = date.getFullYear();
                return d + '/' + m + '/' + yy;
            },

            buildCalendar: function () {
                var firstDay    = new Date(this.year, this.month, 1).getDay();
                var daysInMonth = new Date(this.year, this.month + 1, 0).getDate();

                this.blanks = [];
                for (var i = 0; i < firstDay; i++) this.blanks.push(i);

                this.days = [];
                for (var d = 1; d <= daysInMonth; d++) this.days.push(d);
            },

            prevMonth: function () {
                if (this.month === 0) { this.month = 11; this.year--; }
                else { this.month--; }
                this.buildCalendar();
            },

            nextMonth: function () {
                if (this.month === 11) { this.month = 0; this.year++; }
                else { this.month++; }
                this.buildCalendar();
            },

            isToday: function (day) {
                var today = new Date();
                var d = new Date(this.year, this.month, day);
                return today.toDateString() === d.toDateString();
            },

            isSelected: function (day) {
                if (!this.value) return false;
                var d = new Date(this.year, this.month, day);
                var iso = d.toISOString().slice(0,10);
                return iso === this.value;
            },

            select: function (day) {
                var d = new Date(this.year, this.month, day);
                this.value = d.toISOString().slice(0,10);
                this.displayValue = this.formatDisplay(d);
                this.open = false;
            },

            selectToday: function () {
                var today = new Date();
                this.year  = today.getFullYear();
                this.month = today.getMonth();
                this.buildCalendar();
                this.select(today.getDate());
            }
        }
    }

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
                this.selectedDate = today;
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
                return 'Rp ' + (n || 0).toLocaleString('id-ID');
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
</script>
@endpush
