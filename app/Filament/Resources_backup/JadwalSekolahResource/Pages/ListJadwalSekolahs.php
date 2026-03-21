<?php

namespace App\Filament\Resources\JadwalSekolahResource\Pages;

use App\Filament\Resources\JadwalSekolahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalSekolahs extends ListRecords
{
    protected static string $resource = JadwalSekolahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
