<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Guru';
    protected static ?string $pluralModelLabel = 'Daftar Guru';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('jabatan')
                            ->maxLength(100),
                        Forms\Components\Select::make('employment_type')
                            ->label('Jenis Kepegawaian')
                            ->options([
                                'tetap' => 'Tetap',
                                'tidak_tetap' => 'Tidak Tetap',
                                'kontrak' => 'Kontrak',
                                'part_time' => 'Part-time',
                                'lainnya' => 'Lainnya',
                            ])
                            ->default('tidak_tetap')
                            ->required(),
                        Forms\Components\TextInput::make('employment_detail')
                            ->label('Detail Kepegawaian')
                            ->placeholder('Contoh: Ustadz Tetap, Guru Pondok, Guru Yayasan')
                            ->helperText('Opsional: boleh diisi seperti “Ustadz Tetap”, “Musyrif”, “Pengasuh”, dst.')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Kartu & Biometrik')
                    ->schema([
                        Forms\Components\TextInput::make('rfid_uid')
                            ->label('RFID UID')
                            ->placeholder('Scan kartu RFID')
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('fingerprint_id')
                            ->label('Fingerprint ID (Slot 1-162)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(162)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enroll fingerprint di device'),
                    ])->columns(2),

                Forms\Components\Section::make('Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('hp')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->directory('guru-fotos')
                    ->maxSize(2048),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan'),
                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Kepegawaian')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'tetap' => 'Tetap',
                        'tidak_tetap' => 'Tidak Tetap',
                        'kontrak' => 'Kontrak',
                        'part_time' => 'Part-time',
                        'lainnya' => 'Lainnya',
                        default => '-',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'tetap' => 'success',
                        'kontrak' => 'info',
                        'part_time' => 'warning',
                        'lainnya' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('rfid_uid')
                    ->label('RFID')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('fingerprint_id')
                    ->label('Fingerprint')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('Kepegawaian')
                    ->options([
                        'tetap' => 'Tetap',
                        'tidak_tetap' => 'Tidak Tetap',
                        'kontrak' => 'Kontrak',
                        'part_time' => 'Part-time',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                Tables\Filters\Filter::make('belum_enroll')
                    ->label('Belum Enroll')
                    ->query(fn ($query) => $query->whereNull('rfid_uid')->whereNull('fingerprint_id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}
