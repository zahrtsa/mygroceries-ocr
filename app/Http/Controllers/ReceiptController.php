<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\DaftarBelanja;
use App\Models\PengeluaranBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    // LIST STRUK (GET /belanja/receipts)
    public function index()
    {
        $user = Auth::user();

        $receipts = Receipt::with('daftarBelanja')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // siapkan data tanggal & total (key: Y-m-d)
        $tanggalTotals = $receipts->groupBy(function ($r) {
            // pakai transaction_date kalau ada, fallback ke tanggal_belanja di relasi, lalu created_at
            $date = $r->transaction_date
                ?? optional($r->daftarBelanja)->tanggal_belanja
                ?? $r->created_at;

            return optional($date)->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum('total_amount'); // jumlahkan total per hari
        })->filter(); // buang key null / 0 total

        return view('receipts.index', [
            'receipts'      => $receipts,
            'tanggalTotals' => $tanggalTotals,
        ]);
    }

    // SIMPAN STRUK + OCR
    public function store(Request $request)
    {
        $request->validate([
            'receipt_image'     => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'daftar_belanja_id' => 'nullable|exists:daftar_belanjas,id',
            'tanggal_belanja'   => 'nullable|date',
        ]);

        $user = Auth::user();

        // Tentukan tanggal belanja: dari input (format Y-m-d dari datepicker) atau default hari ini
        if ($request->filled('tanggal_belanja')) {
            $tanggalBelanja = Carbon::createFromFormat('Y-m-d', $request->tanggal_belanja)->startOfDay();
        } else {
            $tanggalBelanja = Carbon::today()->startOfDay();
        }

        // Cari atau buat DaftarBelanja untuk tanggal tsb (hindari duplikat)
        $daftarBelanja = DaftarBelanja::firstOrCreate(
            [
                'user_id'         => $user->id,
                'tanggal_belanja' => $tanggalBelanja,
            ],
            [
                'total_belanja' => 0,
            ]
        );

        // paksa struk ini terkait ke daftar_belanja tersebut
        $request->merge(['daftar_belanja_id' => $daftarBelanja->id]);

        // Simpan file dengan nama berdasarkan tanggal upload
        $file      = $request->file('receipt_image');
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');
        $filename  = 'receipt_' . $timestamp . '.' . $extension;

        // simpan ke storage/app/public/receipts
        $path = $file->storeAs('receipts', $filename, 'public');

        // OCR
        $ocrText = $this->runOCR(storage_path('app/public/' . $path));

        logger("==== OCR RESULT BEGIN ====");
        logger($ocrText);
        logger("==== OCR RESULT END ====");

        $ocr_total    = $this->extractTotal($ocrText);
        $ocr_subtotal = $this->extractSubtotal($ocrText);

        logger("Extracted Total: " . ($ocr_total ?? 'NULL'));
        logger("Extracted Subtotal: " . ($ocr_subtotal ?? 'NULL'));

        if ($ocr_total === null) {
            logger("Warning: Total not extracted from OCR text.");
        }

        $receipt = Receipt::create([
            'user_id'           => $user->id,
            'daftar_belanja_id' => $daftarBelanja->id,
            'filename'          => $filename,
            'file_path'         => $path,
            'transaction_date'  => $tanggalBelanja,
            'extracted_text'    => $ocrText,

            'ocr_total'   => $ocr_total,
            'ocr_subtotal'=> $ocr_subtotal,

            'total_amount'    => $ocr_total,
            'subtotal_amount' => $ocr_subtotal,
            'status_ocr'      => 'Selesai',
        ]);

        // Update total_belanja di daftar_belanja (kalau ada total)
        if ($receipt->total_amount !== null) {
            $daftarBelanja->update([
                'total_belanja' => $receipt->total_amount,
            ]);
        }

        // Update pengeluaran bulanan (budget) dari total struk
        if ($receipt->total_amount) {
            $this->updatePengeluaranBulanan($receipt, $receipt->total_amount);
        }

        return redirect()->route('belanja.receipts.index')
            ->with('success', 'Struk berhasil diproses!');
    }

    // SHOW DETAIL
    public function show(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        return view('receipts.show', compact('receipt'));
    }

    // FORM EDIT
    public function edit(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        return view('receipts.edit', compact('receipt'));
    }

    // UPDATE MANUAL (TOTAL/SUBTOTAL)
    public function update(Request $request, Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        $request->validate([
            'total_amount'    => 'required|string',
            'subtotal_amount' => 'nullable|string',
        ]);

        $oldTotal = $receipt->total_amount ?? 0;

        $total    = $this->normalizeDecimal($request->total_amount);
        $subtotal = $request->subtotal_amount
            ? $this->normalizeDecimal($request->subtotal_amount)
            : null;

        if ($total === null) {
            return back()->withErrors(['total_amount' => 'Total harus berupa angka yang valid.']);
        }

        $receipt->update([
            'total_amount'    => $total,
            'subtotal_amount' => $subtotal,
        ]);

        // Update daftar_belanja jika terhubung
        if ($receipt->daftarBelanja) {
            $receipt->daftarBelanja->update([
                'total_belanja' => $total,
            ]);
        }

        // Update pengeluaran bulanan (pakai selisih)
        $selisih = $total - $oldTotal;
        if ($selisih != 0) {
            $this->updatePengeluaranBulanan($receipt, $selisih);
        }

        return redirect()->route('belanja.receipts.index')
            ->with('success', 'Perubahan berhasil disimpan!');
    }

    // HAPUS STRUK
    public function destroy(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        if ($receipt->file_path && Storage::disk('public')->exists($receipt->file_path)) {
            Storage::disk('public')->delete($receipt->file_path);
        }

        // Kurangi pengeluaran bulanan
        if ($receipt->total_amount) {
            $this->updatePengeluaranBulanan($receipt, -1 * $receipt->total_amount);
        }

        $receipt->delete();

        return redirect()->route('belanja.receipts.index')
            ->with('success', 'Struk berhasil dihapus!');
    }

    // RESET TOTAL / SUBTOTAL KE HASIL OCR
    public function resetToOCR(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);

        $oldTotal = $receipt->total_amount ?? 0;
        $newTotal = $receipt->ocr_total ?? 0;

        $receipt->update([
            'total_amount'    => $receipt->ocr_total,
            'subtotal_amount' => $receipt->ocr_subtotal,
        ]);

        if ($receipt->daftarBelanja) {
            $receipt->daftarBelanja->update([
                'total_belanja' => $newTotal,
            ]);
        }

        $selisih = $newTotal - $oldTotal;
        if ($selisih != 0) {
            $this->updatePengeluaranBulanan($receipt, $selisih);
        }

        return back()->with('success', 'Total berhasil dikembalikan ke hasil OCR!');
    }

    // ==================== OCR CORE ====================

    private function runOCR($imagePath)
    {
        try {
            return (new TesseractOCR($imagePath))
                ->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe') // sesuaikan di server kamu
                ->psm(6)
                ->oem(3)
                ->lang('eng', 'ind')
                ->run();
        } catch (\Exception $e) {
            logger("OCR Error: " . $e->getMessage());
            return '';
        }
    }

    private function extractTotal(string $text): ?float
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        $text = preg_replace('/[^\x20-\x7E\t\n\r]/', '', $text);
        $text = str_replace(["\t", "\n", "\r"], ' ', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));

        logger("Cleaned OCR text for total extraction: " . $text);

        $patterns = [
            '/total\s*[:\-]?\s*([0-9\.,\s]*[0-9])/i',
            '/bayar\s*[:\-]?\s*([0-9\.,\s]*[0-9])/i',
            '/total\s*bayar\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
            '/total\s*belanja\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
            '/jumlah\s*bayar\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
            '/grand\s*total\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
            '/amount\s*due\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
            '/tota[l1i]\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
            '/totalbayar\s*[:\-]?\s*(rp)?\s*([0-9\.,\s]*[0-9])/i',
        ];

        foreach ($patterns as $pattern) {
            logger("Checking pattern for total: " . $pattern);
            if (preg_match($pattern, $text, $m)) {
                $value = trim(end($m));
                logger("Pattern matched for total: {$pattern} with raw value: {$value}");
                $normalized = $this->normalizeDecimal($value);
                logger("Normalized value for total: " . ($normalized ?? 'NULL'));
                if ($normalized !== null && $this->isValidTotal($normalized)) {
                    logger("Valid total extracted: " . $normalized);
                    return $normalized;
                }
            }
        }

        logger("No pattern matched for total, using fallback.");

        preg_match_all('/(\d[\d\.,]*)/', $text, $matches);

        $validNumbers = [];
        foreach ($matches[1] as $numStr) {
            logger("Processing candidate: " . $numStr);
            $normalized = $this->normalizeDecimal($numStr);
            logger("Normalized candidate: " . ($normalized ?? 'NULL'));
            if ($normalized !== null && $this->isValidTotal($normalized)) {
                $validNumbers[] = $normalized;
                logger("Valid candidate for total: " . $normalized);
            }
        }

        if (!empty($validNumbers)) {
            $selected = max($validNumbers);
            logger("Selected total from fallback: " . $selected);
            return $selected;
        }

        logger("No valid total found.");
        return null;
    }

    private function extractSubtotal(string $text): ?float
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        $text = preg_replace('/[^\x20-\x7E\t\n\r]/', '', $text);
        $text = str_replace(["\t", "\n", "\r"], ' ', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));

        $patterns = [
            '/sub\s*total\s*[:\-]?\s*([0-9\.,\s]*[0-9])/i',
            '/subtotal\s*[:\-]?\s*([0-9\.,\s]*[0-9])/i',
            '/sub\s*bayar\s*[:\-]?\s*([0-9\.,\s]*[0-9])/i',
        ];

        foreach ($patterns as $pattern) {
            logger("Checking pattern for subtotal: " . $pattern);
            if (preg_match($pattern, $text, $m)) {
                $value = trim(end($m));
                logger("Pattern matched for subtotal: {$pattern} with raw value: {$value}");
                $normalized = $this->normalizeDecimal($value);
                logger("Normalized value for subtotal: " . ($normalized ?? 'NULL'));
                if ($normalized !== null && $normalized >= 1000 && $normalized <= 5000000) {
                    logger("Valid subtotal extracted: " . $normalized);
                    return $normalized;
                }
            }
        }

        logger("No pattern matched for subtotal.");
        return null;
    }

    private function isValidTotal(float $value): bool
    {
        if ($value < 10000 || $value > 10000000) {
            logger("Total rejected: out of range " . $value);
            return false;
        }

        $strValue = (string) $value;

        if (preg_match('/^[3456]\d{12,18}$/', $strValue)) {
            logger("Total rejected: looks like credit card " . $value);
            return false;
        }

        if (preg_match('/^(19|20|21)\d{2}$/', $strValue)) {
            logger("Total rejected: looks like year " . $value);
            return false;
        }

        if (preg_match('/^08\d{8,10}$/', $strValue) || (strlen($strValue) >= 10 && strlen($strValue) <= 12)) {
            logger("Total rejected: looks like phone number " . $value);
            return false;
        }

        if (strlen($strValue) > 12) {
            logger("Total rejected: too long " . $value);
            return false;
        }

        return true;
    }

    private function normalizeDecimal(string $value): ?float
    {
        if (!$value) return null;

        $value = trim($value);
        $value = preg_replace('/[^\d\.,]/', '', $value);

        // format Indonesia: 12.500,00
        if (preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false && strpos($value, '.') === false) {
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, '.') !== false && strpos($value, ',') === false) {
            $value = str_replace('.', '', $value);
        } else {
            $value = str_replace(',', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    // ==================== HELPER BUDGET & AUTH ====================

    private function updatePengeluaranBulanan(Receipt $receipt, float $pertambahan)
    {
        $user = $receipt->user;
        $date = $receipt->transaction_date
            ?? optional($receipt->daftarBelanja)->tanggal_belanja
            ?? now();

        $bulan = $date->format('n');
        $tahun = $date->format('Y');

        $pengeluaran = PengeluaranBulanan::firstOrCreate(
            [
                'user_id' => $user->id,
                'bulan'   => $bulan,
                'tahun'   => $tahun,
            ],
            [
                'total_pengeluaran' => 0,
                'saldo_bersih'      => 0,
            ]
        );

        $pengeluaran->total_pengeluaran += $pertambahan;
        $pengeluaran->saldo_bersih = ($user->budget_bulanan ?? 0) - $pengeluaran->total_pengeluaran;
        $pengeluaran->save();
    }

    private function authorizeReceipt(Receipt $receipt)
    {
        if ($receipt->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
