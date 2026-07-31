<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NorekResource\Pages;
use App\Models\Norek;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NorekResource extends Resource
{
    protected static ?string $model = Norek::class;

    protected static ?string $navigationLabel = 'Data Rekening';
    protected static ?string $label = 'Data Rekening';
    protected static ?string $pluralLabel = 'Data Rekening';

    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Rekening')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Nama rekening sudah terdaftar.',
                    ])
                    ->maxLength(255),
                Forms\Components\TextInput::make('norek')
                    ->required()
                    ->label('Nomor Rekening')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Nomor rekening sudah terdaftar.',
                    ])
                    ->maxLength(255),
                Forms\Components\Select::make('bank')
                    ->label('Bank')
                    ->options([
                        // Bank BUMN / Himbara
                        'MANDIRI' => 'Bank Mandiri',
                        'BRI' => 'Bank Rakyat Indonesia (BRI)',
                        'BNI' => 'Bank Negara Indonesia (BNI)',
                        'BTN' => 'Bank Tabungan Negara (BTN)',
                        'BSI' => 'Bank Syariah Indonesia (BSI)',
                        
                        // Bank Swasta Nasional Terbesar
                        'BCA' => 'Bank Central Asia (BCA)',
                        'DANAMON' => 'Bank Danamon',
                        'PERMATA' => 'Bank Permata',
                        'CIMB' => 'Bank CIMB Niaga',
                        'PANIN' => 'Bank Panin',
                        'MAYBANK' => 'Maybank Indonesia',
                        'OCBC' => 'OCBC NISP',
                        'MEGA' => 'Bank Mega',
                        'SINARMAS' => 'Bank Sinarmas',
                        'BUKOPIN' => 'KB Bank (Bukopin)',
                        
                        // Bank Digital / Populer
                        'JAGO' => 'Bank Jago',
                        'ALLO' => 'Allo Bank',
                        'SEABANK' => 'SeaBank',
                        'NEO' => 'Bank Neo Commerce',
                        'BLU' => 'blu by BCA Digital',
                        'JENIUS' => 'Jenius (BTPN)',
                        
                        // Bank Pembangunan Daerah (BPD)
                        'BJB' => 'Bank BJB',
                        'DKI' => 'Bank DKI',
                        'JATENG' => 'Bank Jateng',
                        'JATIM' => 'Bank Jatim',
                        'BALI' => 'Bank BPD Bali',
                        'DIY' => 'Bank BPD DIY',
                        'SUMUT' => 'Bank Sumut',
                        'NAGARI' => 'Bank Nagari',
                        'RIAU_KEPRI' => 'Bank Riau Kepri Syariah',
                        'SUMSEL_BABEL' => 'Bank Sumsel Babel',
                        'LAMPUNG' => 'Bank Lampung',
                        'KALSEL' => 'Bank Kalsel',
                        'KALBAR' => 'Bank Kalbar',
                        'KALTIMTARA' => 'Bank Kaltimtara',
                        'SULSELBAR' => 'Bank Sulselbar',
                        'SUTRA' => 'Bank Sultra',
                        'NTB' => 'Bank NTB Syariah',
                        'NTT' => 'Bank NTT',
                        'PAPUA' => 'Bank Papua',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Rekening'),
                Tables\Columns\TextColumn::make('norek')
                    ->searchable()
                    ->label('Nomor Rekening'),
                Tables\Columns\TextColumn::make('bank')
                    ->searchable()
                    ->label('Bank'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNoreks::route('/'),
            'create' => Pages\CreateNorek::route('/create'),
            'edit' => Pages\EditNorek::route('/{record}/edit'),
        ];
    }
}
