<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerangkatResource\Pages;
use App\Models\Perangkat;
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

    public static function form(Form $form): Form
    {
        return $form
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
                    ->label('Tipe')
                    ->options([
                        'gerbang' => 'Gerbang',
                        'kelas' => 'Ruang Kelas',
                    ])
                    ->required()
                    ->default('gerbang'),
                Forms\Components\TextInput::make('device_key')
                    ->label('Device Key')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Key akan digenerate otomatis'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
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
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gerbang' => 'success',
                        'kelas' => 'info',
                        default => 'gray',
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
                Tables\Columns\TextColumn::make('last_ping')
                    ->label('Last Ping')
                    ->since()
                    ->placeholder('Belum pernah'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
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