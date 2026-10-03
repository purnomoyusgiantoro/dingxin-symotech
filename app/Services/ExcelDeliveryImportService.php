<?php

namespace App\Services;

use App\Models\DailyDelivery;
use App\Models\Driver;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelDeliveryImportService
{
    /**
     * Parse file Excel / CSV dan import data barang bawaan harian sopir.
     *
     * @param string $filePath Path file fisik di server
     * @param int|null $userId User ID admin yang melakukan upload
     * @return array Hasil import [imported, updated, failed, errors]
     */
    public function import(string $filePath, ?int $userId = null): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return [
                'success' => false,
                'message' => 'File kosong atau tidak memiliki baris data.',
                'imported' => 0,
                'errors' => ['File Excel tidak memiliki baris data setelah header.'],
            ];
        }

        $imported = 0;
        $updated = 0;
        $errors = [];
        $driversCache = Driver::all()->keyBy(function ($item) {
            return strtoupper(trim($item->driver_code));
        });

        // Baris 1 biasanya header: Tanggal | Kode Sopir | Nominal Bawaan | Keterangan/Rute
        $isFirstRow = true;
        $rowNumber = 0;

        foreach ($rows as $row) {
            $rowNumber++;
            if ($isFirstRow) {
                $isFirstRow = false;
                continue; // Skip header
            }

            $rawDate = trim((string)($row['A'] ?? ''));
            $rawCode = strtoupper(trim((string)($row['B'] ?? '')));
            $rawAmount = trim((string)($row['C'] ?? ''));
            $routeNotes = trim((string)($row['D'] ?? ''));

            if (empty($rawDate) && empty($rawCode) && empty($rawAmount)) {
                continue; // Lewati baris kosong
            }

            // 1. Validasi Kode Sopir
            if (!isset($driversCache[$rawCode])) {
                $errors[] = "Baris $rowNumber: Kode sopir '{$rawCode}' tidak ditemukan di sistem.";
                continue;
            }
            $driver = $driversCache[$rawCode];

            // 2. Parse Tanggal
            try {
                if (is_numeric($rawDate)) {
                    // Excel timestamp serial date
                    $parsedDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate))->format('Y-m-d');
                } else {
                    $parsedDate = Carbon::parse($rawDate)->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                $errors[] = "Baris $rowNumber: Format tanggal '{$rawDate}' tidak valid.";
                continue;
            }

            // 3. Bersihkan Nominal Rupiah (misal "Rp 10.000.000" -> 10000000)
            $cleanAmount = preg_replace('/[^0-9]/', '', $rawAmount);
            $amount = (float)$cleanAmount;

            if ($amount <= 0) {
                $errors[] = "Baris $rowNumber: Nominal bawaan harus lebih besar dari 0.";
                continue;
            }

            // 4. Upsert Daily Delivery
            $record = DailyDelivery::updateOrCreate(
                [
                    'driver_id' => $driver->id,
                    'date' => $parsedDate,
                ],
                [
                    'amount' => $amount,
                    'route_notes' => !empty($routeNotes) ? $routeNotes : 'Import Excel',
                    'created_by' => $userId,
                ]
            );

            if ($record->wasRecentlyCreated) {
                $imported++;
            } else {
                $updated++;
            }

            // Sync daily settlement
            app(SettlementCalculationService::class)->calculateDriverDaily($driver->id, $parsedDate);
        }

        return [
            'success' => true,
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => count($errors),
            'total_processed' => $imported + $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Buat file Excel template contoh untuk diunduh Admin.
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Bawaan Sopir');

        // Header
        $headers = ['Tanggal (YYYY-MM-DD)', 'Kode Sopir', 'Nominal Bawaan (Rp)', 'Keterangan/Rute'];
        $sheet->fromArray([$headers], null, 'A1');

        // Style header bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Contoh Data
        $today = Carbon::today()->format('Y-m-d');
        $sampleData = [
            [$today, 'TGL1.2', 10000000, 'Rute Kota Tegal - Margadana'],
            [$today, 'TGL3.4', 8500000, 'Rute Mejasem - Kramat'],
            [$today, 'BRS1.2', 12000000, 'Rute Brebes Kota - Jatibarang'],
            [$today, 'BRS3.4', 9000000, 'Rute Bumiayu - Tonjong'],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempDir = storage_path('app/templates');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir . '/template_bawaan_sopir.xlsx';
        $writer->save($filePath);

        return $filePath;
    }
}
