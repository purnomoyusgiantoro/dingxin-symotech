<?php

namespace App\Filament\Resources\CreditDeliveryResource\Pages;

use App\Filament\Resources\CreditDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditDeliveries extends ListRecords
{
    protected static string $resource = CreditDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Input Faktur Kredit Manual'),
        ];
    }
}
