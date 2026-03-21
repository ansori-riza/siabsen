<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Sekolah;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentAbsensi extends BaseWidget
{
    protected static ?string $heading = 'Absensi Terbaru';
    protected int|string|array $columnSpan = 2;
    protected ?string $pollingInterval = '10s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->whereDate('waktu_absen', today())
                    ->with(['subject'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('subject.nama')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Sebagai')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'App\Models\Guru' ? Sekolah::getGuruLabel() : Sekolah::getStudentLabel())
                    ->color(fn (string $state): string => $state === 'App\Models\Guru' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'pulang' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('waktu_absen')
                    ->label('Waktu')
                    ->time('H:i:s'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'terlambat' => 'warning',
                        'alpha' => 'danger',
                        'izin' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('metode')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RFID' => 'info',
                        'fingerprint' => 'success',
                        'manual' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record): string => "/admin/absensis/{$record->id}/edit"),
            ])
            ->paginated(false);
    }
}
