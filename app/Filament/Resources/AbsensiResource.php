<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Murid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $pluralModelLabel = 'Rekap Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'masuk' => 'Masuk',
                        'pulang' => 'Pulang',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'alpha' => 'Alpha',
                        'izin' => 'Izin',
                    ])
                    ->required(),
                Forms\Components\Select::make('metode')
                    ->options([
                        'RFID' => 'RFID',
                        'fingerprint' => 'Fingerprint',
                        'manual' => 'Manual',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('waktu_absen')
                    ->label('Waktu Absen')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Role')
                    ->formatStateUsing(fn (string $state): string => str_contains($state, 'Guru') ? 'Guru' : 'Murid')
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, 'Guru') ? 'success' : 'primary'),
                Tables\Columns\TextColumn::make('subject.nama')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'pulang' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'terlambat' => 'warning',
                        'alpha' => 'danger',
                        'izin' => 'info',
                    }),
                Tables\Columns\TextColumn::make('metode')
                    ->badge(),
                Tables\Columns\TextColumn::make('waktu_absen')
                    ->label('Waktu')
                    ->dateTime('H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'alpha' => 'Alpha',
                        'izin' => 'Izin',
                    ]),
                Tables\Filters\SelectFilter::make('tipe')
                    ->options([
                        'masuk' => 'Masuk',
                        'pulang' => 'Pulang',
                    ]),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['tanggal'],
                                fn ($query, $date) => $query->whereDate('waktu_absen', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::class,
            'create' => Pages\CreateAbsensi::class,
            'edit' => Pages\EditAbsensi::class,
        ];
    }
}