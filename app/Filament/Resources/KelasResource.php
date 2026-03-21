<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Models\Kelas;
use App\Models\Sekolah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Data Kelas';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Kelas';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Kelas')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Contoh: 7A, 8B, 9C'),
                Forms\Components\TextInput::make('tingkat')
                    ->label('Tingkat')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->default(7),
                Forms\Components\Select::make('wali_kelas_id')
                    ->label(Sekolah::getClassGuardianLabel())
                    ->relationship('waliKelas', 'nama')
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih ' . strtolower(Sekolah::getGuruLabel()) . ' sebagai ' . strtolower(Sekolah::getClassGuardianLabel())),
                Forms\Components\TextInput::make('kapasitas')
                    ->label('Kapasitas')
                    ->numeric()
                    ->default(30)
                    ->minValue(1)
                    ->maxValue(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('waliKelas.nama')
                    ->label(Sekolah::getClassGuardianLabel())
                    ->placeholder('Belum ditentukan'),
                Tables\Columns\TextColumn::make('kapasitas')
                    ->label('Kapasitas'),
                Tables\Columns\TextColumn::make('murids_count')
                    ->label('Jumlah Murid')
                    ->counts('murids'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options([
                        7 => 'Kelas 7',
                        8 => 'Kelas 8',
                        9 => 'Kelas 9',
                        10 => 'Kelas 10',
                        11 => 'Kelas 11',
                        12 => 'Kelas 12',
                    ]),
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
            ->defaultSort('tingkat', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['waliKelas']);
    }
}
