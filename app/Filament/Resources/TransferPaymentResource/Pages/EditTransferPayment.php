<?php

namespace App\Filament\Resources\TransferPaymentResource\Pages;

use App\Filament\Resources\TransferPaymentResource;
use App\Services\SettlementCalculationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferPayment extends EditRecord
{
    protected static string $resource = TransferPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Refresh kalkulasi harian
        app(SettlementCalculationService::class)->calculateDriverDaily(
            $this->record->driver_id,
            $this->record->date
        );
    }
}
