<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BbmResource\Pages;
use App\Filament\Resources\BbmResource\RelationManagers;
use App\Models\Bbm;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BbmResource extends Resource
{
    protected static ?string $model = Bbm::class;

    protected static ?string $navigationGroup = 'Unit';
    protected static ?string $pluralLabel = 'Data BBM';
    protected static ?string $navigationLabel = 'Data BBM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->native(false),

                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (Unit $record) => "{$record->nopol} - {$record->merk} {$record->type}")
                    ->placeholder('Pilih Unit berdasarkan Nopol'),

                Forms\Components\FileUpload::make('barcode_bbm')
                    ->label('Foto Barcode BBM')
                    ->image()
                    ->imageEditor()
                    ->directory('bbm/barcode')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit.nopol')
                    ->label('Nopol')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit.merk')
                    ->label('Merk')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit.type')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('barcode_bbm')
                    ->label('Foto Barcode')
                    ->circular()
                    ->size(60),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi BBM')
                    ->schema([
                        Components\TextEntry::make('tanggal')
                            ->label('Tanggal')
                            ->date('d F Y'),

                        Components\TextEntry::make('unit.nopol')
                            ->label('Nomor Polisi')
                            ->badge()
                            ->color('info'),

                        Components\TextEntry::make('unit.merk')
                            ->label('Merk'),

                        Components\TextEntry::make('unit.type')
                            ->label('Type'),

                        Components\TextEntry::make('unit.jenis')
                            ->label('Jenis'),

                        Components\TextEntry::make('unit.tahun')
                            ->label('Tahun'),
                    ])
                    ->columns(2),

                Components\Section::make('Foto Barcode BBM')
                    ->schema([
                        Components\ImageEntry::make('barcode_bbm')
                            ->label('')
                            ->hiddenLabel()
                            ->size(400),
                    ]),

                Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i:s'),

                        Components\TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime('d F Y, H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsed(),
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
            'index' => Pages\ListBbms::route('/'),
            'create' => Pages\CreateBbm::route('/create'),
            'view' => Pages\ViewBbm::route('/{record}'),
            'edit' => Pages\EditBbm::route('/{record}/edit'),
        ];
    }
}
