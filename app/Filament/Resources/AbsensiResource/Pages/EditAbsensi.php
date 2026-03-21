<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\EditRecord;

class EditAbsensi extends EditRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldStatus = $this->record->status;
        $newStatus = $data['status'];
        
        if ($oldStatus !== $newStatus) {
            // Log perubahan ke audit log
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => Absensi::class,
                'model_id' => $this->record->id,
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $newStatus, 'keterangan' => $data['keterangan']],
                'ip_address' => request()->ip(),
            ]);
        }
        
        return $data;
    }
}
