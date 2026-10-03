<?php

namespace App\Filament\Resources\DailyDeliveryResource\Pages;

use App\Filament\Resources\DailyDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyDelivery extends EditRecord
{
    protected static string $resource = DailyDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
