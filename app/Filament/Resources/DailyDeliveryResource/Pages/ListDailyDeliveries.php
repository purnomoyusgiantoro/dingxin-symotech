<?php

namespace App\Filament\Resources\DailyDeliveryResource\Pages;

use App\Filament\Resources\DailyDeliveryResource;
use App\Services\ExcelDeliveryImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListDailyDeliveries extends ListRecords
{
    protected static string $resource = DailyDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Input Manual Bawaan')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();
                    return $data;
                }),

            Actions\Action::make('uploadExcel')
                ->label('Upload File Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Upload Bawaan Sopir dari File Excel')
                ->modalDescription('Format kolom Excel: [A: Tanggal, B: Kode Sopir, C: Nominal Bawaan (Rp), D: Keterangan/Rute]. Gunakan template yang disediakan.')
                ->form([
                    Forms\Components\FileUpload::make('excel_file')
                        ->label('Pilih File Excel / CSV (.xlsx, .xls, .csv)')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->disk('local')
                        ->directory('temp_imports')
                        ->required(),
                ])
                ->action(function (array $data, ExcelDeliveryImportService $importService): void {
                    $filePath = Storage::disk('local')->path($data['excel_file']);

                    try {
                        $result = $importService->import($filePath, auth()->id());

                        // Hapus file temporary setelah import
                        @unlink($filePath);

                        if ($result['success']) {
                            $msg = "Berhasil memproses {$result['total_processed']} baris ({$result['imported']} baru, {$result['updated']} diperbarui).";
                            if (!empty($result['errors'])) {
                                $msg .= " Catatan peringatan: " . implode('; ', array_slice($result['errors'], 0, 3));
                            }

                            Notification::make()
                                ->title('Import Excel Berhasil')
                                ->body($msg)
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body($result['message'] ?? 'Terjadi kesalahan validasi.')
                                ->danger()
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Kesalahan Memproses File')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('downloadTemplate')
                ->label('Download Template Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function (ExcelDeliveryImportService $importService): BinaryFileResponse {
                    $templatePath = $importService->generateTemplate();
                    return response()->download($templatePath, 'template_bawaan_sopir.xlsx');
                }),
        ];
    }
}
