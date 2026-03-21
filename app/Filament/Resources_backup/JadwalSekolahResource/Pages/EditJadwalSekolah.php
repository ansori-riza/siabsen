<?php

namespace App\Filament\Resources\JadwalSekolahResource\Pages;

use App\Filament\Resources\JadwalSekolahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalSekolah extends EditRecord
{
    protected static string $resource = JadwalSekolahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
