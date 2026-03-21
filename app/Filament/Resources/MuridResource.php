<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MuridResource\Pages;
use App\Models\Murid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MuridResource extends Resource
{
    protected static ?string $model = Murid::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Murid';
    protected static ?string $modelLabel = 'Murid';
    protected static ?string $pluralModelLabel = 'Murid';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Murid')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'l' => 'Laki-laki',
                                'p' => 'Perempuan',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto')
                            ->image()
                            ->directory('murid-foto'),
                    ])->columns(2),

                Forms\Components\Section::make('Identitas RFID')
                    ->schema([
                        Forms\Components\TextInput::make('rfid_uid')
                            ->label('RFID UID')
                            ->placeholder('Tap kartu RFID untuk scan')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('scan')
                                    ->label('Scan')
                                    ->icon('heroicon-m-qr-code')
                                    ->color('primary')
                            ),
                    ])->columns(1),

                Forms\Components\Section::make('Informasi Orang Tua')
                    ->schema([
                        Forms\Components\TextInput::make('nama_ortu')
                            ->label('Nama Orang Tua')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hp_ortu')
                            ->label('No HP Orang Tua')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->formatStateUsing(fn (string $state): string => $state === 'l' ? 'L' : 'P'),
                Tables\Columns\IconColumn::make('rfid_uid')
                    ->label('RFID')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama'),
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'l' => 'Laki-laki',
                        'p' => 'Perempuan',
                    ]),
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
            ->defaultSort('nama', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMurids::route('/'),
            'create' => Pages\CreateMurid::route('/create'),
            'edit' => Pages\EditMurid::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['kelas']);
    }
}