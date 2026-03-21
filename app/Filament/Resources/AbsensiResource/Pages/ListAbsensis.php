<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Models\Absensi;
use App\Models\Sekolah;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_today_csv')
                ->label('Export CSV Hari Ini')
                ->icon('heroicon-m-arrow-down-tray')
                ->visible(fn (): bool => auth()->user() instanceof User && auth()->user()->hasPermission('view_laporan'))
                ->action(fn (): StreamedResponse => $this->exportTodayCsv()),
        ];
    }

    public function exportTodayCsv(): StreamedResponse
    {
        abort_unless(
            auth()->user() instanceof User && auth()->user()->hasPermission('view_laporan'),
            403
        );

        $guruLabel = Sekolah::getGuruLabel();
        $studentLabel = Sekolah::getStudentLabel();

        $records = Absensi::query()
            ->whereDate('waktu_absen', today())
            ->with(['subject', 'perangkat'])
            ->latest('waktu_absen')
            ->get();

        return response()->streamDownload(function () use ($records, $guruLabel, $studentLabel): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama', 'Sebagai', 'Tipe', 'Waktu', 'Status', 'Metode', 'Perangkat']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->subject?->nama ?? '-',
                    $record->subject_type === 'App\Models\Guru' ? $guruLabel : $studentLabel,
                    $record->tipe,
                    optional($record->waktu_absen)?->format('Y-m-d H:i:s'),
                    $record->status,
                    strtoupper((string) $record->metode),
                    $record->perangkat?->nama ?? 'Manual',
                ]);
            }

            fclose($handle);
        }, 'absensi-' . now()->format('Ymd-His') . '.csv');
    }
}
