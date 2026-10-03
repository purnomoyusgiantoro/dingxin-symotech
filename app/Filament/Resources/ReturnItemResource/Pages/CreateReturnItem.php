<?php

namespace App\Filament\Resources\ReturnItemResource\Pages;

use App\Filament\Resources\ReturnItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReturnItem extends CreateRecord
{
    protected static string $resource = ReturnItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
