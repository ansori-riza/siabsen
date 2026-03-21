<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalSekolahResource\Pages;
use App\Models\JadwalSekolah;
use App\Support\DayOfWeekMapper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JadwalSekolahResource extends Resource
{
    protected static ?string $model = JadwalSekolah::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Jadwal Sekolah';
    protected static ?string $modelLabel = 'Jadwal';
    protected static ?string $pluralModelLabel = 'Jadwal';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('role_target')
                    ->label('Untuk')
                    ->options([
                        'murid' => 'Murid',
                        'guru' => 'Guru',
                    ])
                    ->required()
                    ->default('murid'),
                Forms\Components\Select::make('hari')
                    ->label('Hari')
                    ->options(DayOfWeekMapper::options())
                    ->required(),
                Forms\Components\TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->required()
                    ->seconds(false)
                    ->default('07:00'),
                Forms\Components\TimePicker::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->required()
                    ->seconds(false)
                    ->default('15:00'),
                Forms\Components\TextInput::make('toleransi_menit')
                    ->label('Toleransi Keterlambatan (menit)')
                    ->numeric()
                    ->default(15)
                    ->minValue(0)
                    ->maxValue(60)
                    ->suffix('menit'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role_target')
                    ->label('Untuk')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'murid' => 'info',
                        'guru' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->formatStateUsing(fn (int $state): string => DayOfWeekMapper::toLabel($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('toleransi_menit')
                    ->label('Toleransi')
                    ->suffix(' menit'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_target')
                    ->label('Untuk')
                    ->options([
                        'murid' => 'Murid',
                        'guru' => 'Guru',
                    ]),
                Tables\Filters\SelectFilter::make('hari')
                    ->label('Hari')
                    ->options(DayOfWeekMapper::options()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('role_target')
            ->defaultSort('hari');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalSekolahs::route('/'),
            'create' => Pages\CreateJadwalSekolah::route('/create'),
            'edit' => Pages\EditJadwalSekolah::route('/{record}/edit'),
        ];
    }
}
