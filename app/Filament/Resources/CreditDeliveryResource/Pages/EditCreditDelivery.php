<?php

namespace App\Filament\Resources\CreditDeliveryResource\Pages;

use App\Filament\Resources\CreditDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditDelivery extends EditRecord
{
    protected static string $resource = CreditDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
