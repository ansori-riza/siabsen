<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use App\Models\Sekolah;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Monitoring Absensi';
    protected static ?string $modelLabel = 'Absensi';
    protected static ?string $pluralModelLabel = 'Absensi';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Absensi')
                    ->schema([
                        Forms\Components\DateTimePicker::make('waktu_absen')
                            ->label('Waktu Absen')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'hadir' => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'alpha' => 'Alpha',
                                'izin' => 'Izin',
                            ])
                            ->required(),
                        Forms\Components\Select::make('metode')
                            ->label('Metode')
                            ->options([
                                'rfid' => 'RFID',
                                'fingerprint' => 'Fingerprint',
                                'manual' => 'Manual',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->placeholder('Alasan perubahan status'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('waktu_absen', today()))
            ->columns([
                Tables\Columns\TextColumn::make('subject.nama')
                    ->label('Nama')
                    ->getStateUsing(function ($record) {
                        return $record->subject?->nama ?? 'Unknown';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph('subject', ['App\Models\Guru', 'App\Models\Murid'], function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Sebagai')
                    ->formatStateUsing(fn (string $state): string => $state === 'App\Models\Guru' ? Sekolah::getGuruLabel() : Sekolah::getStudentLabel())
                    ->badge()
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
                    ->formatStateUsing(function (mixed $state): string {
                        $value = $state instanceof BackedEnum ? $state->value : (string) $state;

                        return strtoupper($value);
                    })
                    ->color(function (mixed $state): string {
                        $value = $state instanceof BackedEnum ? $state->value : (string) $state;

                        return match ($value) {
                        'rfid' => 'info',
                        'fingerprint' => 'success',
                        'manual' => 'warning',
                        default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('perangkat.nama')
                    ->label('Perangkat')
                    ->placeholder('Manual'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'alpha' => 'Alpha',
                        'izin' => 'Izin',
                    ]),
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'masuk' => 'Masuk',
                        'pulang' => 'Pulang',
                    ]),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Sebagai')
                    ->options([
                        'App\Models\Guru' => Sekolah::getGuruLabel(),
                        'App\Models\Murid' => Sekolah::getStudentLabel(),
                    ]),
                Tables\Filters\SelectFilter::make('metode')
                    ->label('Metode')
                    ->options([
                        'rfid' => 'RFID',
                        'fingerprint' => 'Fingerprint',
                        'manual' => 'Manual',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No delete bulk action for absensi
                ]),
            ])
            ->defaultSort('waktu_absen', 'desc')
            ->poll('10s'); // Auto-refresh every 10 seconds
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
