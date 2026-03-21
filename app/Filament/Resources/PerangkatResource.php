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
    protected static ?string $pluralModelLabel = 'Daftar Perangkat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Contoh: Gerbang Utama'),
                Forms\Components\TextInput::make('lokasi')
                    ->maxLength(200)
                    ->placeholder('Contoh: Depan sekolah'),
                Forms\Components\Select::make('tipe')
                    ->options([
                        'gerbang' => 'Gerbang',
                        'kelas' => 'Ruang Kelas',
                    ])
                    ->default('gerbang')
                    ->required(),
                Forms\Components\TextInput::make('device_key')
                    ->label('Device Key')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Auto-generated setelah create'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('lokasi'),
                Tables\Columns\TextColumn::make('tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('device_key')
                    ->label('Device Key')
                    ->copyable()
                    ->limit(20),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'online' => 'heroicon-o-signal',
                        'offline' => 'heroicon-o-signal-slash',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'offline' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('last_ping')
                    ->label('Last Ping')
                    ->dateTime()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->options([
                        'gerbang' => 'Gerbang',
                        'kelas' => 'Ruang Kelas',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('regenerate_key')
                    ->label('Regenerate Key')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Perangkat $record) {
                        $record->device_key = Perangkat::generateDeviceKey();
                        $record->save();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerangkats::class,
            'create' => Pages\CreatePerangkat::class,
            'edit' => Pages\EditPerangkat::class,
        ];
    }
}