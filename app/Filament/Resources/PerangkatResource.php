<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerangkatResource\Pages;
use App\Models\Perangkat;
use App\Models\Sekolah;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PerangkatResource extends Resource
{
    protected static ?string $model = Perangkat::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Perangkat';
    protected static ?string $modelLabel = 'Perangkat';
    protected static ?string $pluralModelLabel = 'Perangkat';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Perangkat')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Perangkat')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Gerbang Utama'),
                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->maxLength(200)
                            ->placeholder('Contoh: Depan Sekolah'),
                        Forms\Components\Select::make('tipe')
                            ->label('Tipe Lokasi')
                            ->options([
                                'gerbang' => 'Gerbang',
                                'kelas' => 'Ruang Kelas',
                            ])
                            ->required()
                            ->default('gerbang')
                            ->live(),
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->options(Kelas::pluck('nama', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => $get('tipe') === 'kelas'),
                    ])->columns(2),

                Forms\Components\Section::make('Vendor & Fungsi')
                    ->schema([
                        Forms\Components\Select::make('vendor_type')
                            ->label('Vendor')
                            ->options([
                                'esp32' => 'ESP32 Custom',
                                'zkteco' => 'ZKTeco',
                                'hikvision' => 'Hikvision',
                                'solution' => 'Solution',
                                'other' => 'Lainnya',
                            ])
                            ->required()
                            ->default('esp32')
                            ->live(),
                        Forms\Components\Select::make('tipe_fungsi')
                            ->label('Fungsi Absensi')
                            ->options([
                                'in' => 'Masuk',
                                'out' => 'Keluar',
                                'both' => 'Masuk & Keluar',
                            ])
                            ->required()
                            ->default('both'),
                    ])->columns(2),

                Forms\Components\Section::make('Konfigurasi Jaringan')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->maxLength(45)
                            ->placeholder('192.168.1.100')
                            ->required(fn ($get) => in_array($get('vendor_type'), ['zkteco', 'hikvision', 'solution'])),
                        Forms\Components\TextInput::make('port')
                            ->label('Port')
                            ->numeric()
                            ->default(4370)
                            ->placeholder('4370')
                            ->required(fn ($get) => $get('vendor_type') === 'solution'),
                    ])->columns(2)
                    ->visible(fn ($get) => in_array($get('vendor_type'), ['zkteco', 'hikvision', 'solution'])),

                Forms\Components\Section::make('Autentikasi & Status')
                    ->schema([
                        Forms\Components\Select::make('sekolah_id')
                            ->label('Sekolah')
                            ->options(Sekolah::pluck('nama', 'id'))
                            ->default(fn () => auth()->user()?->sekolah_id)
                            ->required(),
                        Forms\Components\TextInput::make('device_key')
                            ->label('Device Key')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state ?: 'Akan digenerate otomatis')
                            ->helperText('Key digenerate otomatis saat simpan')
                            ->copyable()
                            ->copyMessage('Device Key disalin!'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->placeholder('Belum ditentukan'),
                Tables\Columns\BadgeColumn::make('vendor_type')
                    ->label('Vendor')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'esp32' => 'ESP32',
                        'zkteco' => 'ZKTeco',
                        'hikvision' => 'Hikvision',
                        'solution' => 'Solution',
                        default => 'Lainnya',
                    })
                    ->colors([
                        'info' => 'esp32',
                        'warning' => 'zkteco',
                        'success' => 'hikvision',
                        'danger' => 'solution',
                        'gray' => 'other',
                    ]),
                Tables\Columns\BadgeColumn::make('tipe_fungsi')
                    ->label('Fungsi')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'both' => 'Masuk & Keluar',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('device_key')
                    ->label('Device Key')
                    ->copyable()
                    ->copyMessage('Device Key disalin!')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn (string $state): string => match ($state) {
                        'online' => 'heroicon-o-signal',
                        'offline' => 'heroicon-o-signal-slash',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'offline' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_ping')
                    ->label('Last Ping')
                    ->since()
                    ->placeholder('Belum pernah'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_type')
                    ->label('Vendor')
                    ->options([
                        'esp32' => 'ESP32 Custom',
                        'zkteco' => 'ZKTeco',
                        'hikvision' => 'Hikvision',
                        'solution' => 'Solution',
                        'other' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('tipe_fungsi')
                    ->label('Fungsi')
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'both' => 'Masuk & Keluar',
                    ]),
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'gerbang' => 'Gerbang',
                        'kelas' => 'Ruang Kelas',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('regenerate')
                    ->label('Regenerate Key')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate Device Key?')
                    ->modalDescription('Device Key yang lama tidak akan bisa digunakan lagi.')
                    ->action(function (Perangkat $record): void {
                        $record->device_key = Perangkat::generateDeviceKey();
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerangkats::route('/'),
            'create' => Pages\CreatePerangkat::route('/create'),
            'edit' => Pages\EditPerangkat::route('/{record}/edit'),
        ];
    }
}