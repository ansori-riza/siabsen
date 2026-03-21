<?php

namespace App\Filament\Resources\PerangkatResource\Pages;

use App\Filament\Resources\PerangkatResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Perangkat;

class CreatePerangkat extends CreateRecord
{
    protected static string $resource = PerangkatResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['device_key'] = Perangkat::generateDeviceKey();
        return $data;
    }
}
