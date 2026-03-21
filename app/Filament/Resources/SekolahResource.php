<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SekolahResource\Pages;
use App\Models\Sekolah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SekolahResource extends Resource
{
    protected static ?string $model = Sekolah::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Sekolah';
    protected static ?string $pluralModelLabel = 'Daftar Sekolah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sekolah')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('alamat')
                            ->maxLength(500),
                        Forms\Components\TextInput::make('kepala_sekolah')
                            ->label('Kepala Sekolah')
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make('Pengaturan Tampilan')
                    ->schema([
                        Forms\Components\ColorPicker::make('theme_color')
                            ->label('Warna Tema')
                            ->default('#1971C2'),
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('sekolah-logos'),
                    ]),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('npsn')
                    ->label('NPSN'),
                Tables\Columns\TextColumn::make('kepala_sekolah')
                    ->label('Kepala Sekolah'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('aktif')
                    ->query(fn ($query) => $query->where('is_active', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSekolahs::class,
            'create' => Pages\CreateSekolah::class,
            'edit' => Pages\EditSekolah::class,
        ];
    }
}