<?php

namespace App\Filament\Resources\Bbms;

use App\Filament\Resources\Bbms\Pages\CreateBbm;
use App\Filament\Resources\Bbms\Pages\EditBbm;
use App\Filament\Resources\Bbms\Pages\ListBbms;
use App\Filament\Resources\Bbms\Pages\ViewBbm;
use App\Filament\Resources\Bbms\RelationManagers;
use App\Models\Bbm;
use App\Models\Unit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BbmResource extends Resource
{
    protected static ?string $model = Bbm::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Unit';
    protected static ?string $pluralLabel = 'Data BBM';
    protected static ?string $navigationLabel = 'Data BBM';



    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->native(false),

                Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn(Unit $record) => "{$record->nopol} - {$record->merk} {$record->type}")
                    ->placeholder('Pilih Unit berdasarkan Nopol'),

                FileUpload::make('barcode_bbm')
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
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                TextColumn::make('unit.nopol')
                    ->label('Nopol')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('unit.merk')
                    ->label('Merk')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('unit.type')
                    ->label('Type')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                ImageColumn::make('barcode_bbm')
                    ->url(fn(Bbm $record): ?string => $record->barcode_bbm ? asset('storage/' . $record->barcode_bbm) : null)
                    ->openUrlInNewTab()
                    ->label('Foto Barcode')
                    ->circular()
                    ->size(60),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->schema([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    section::make()
                        ->schema([
                    Section::make('Informasi BBM')
                        ->schema([
                            TextEntry::make('tanggal')
                                ->label('Tanggal')
                                ->date('d F Y'),

                            TextEntry::make('unit.nopol')
                                ->label('Nomor Polisi')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('unit.merk')
                                ->label('Merk'),

                            TextEntry::make('unit.type')
                                ->label('Type'),

                            TextEntry::make('unit.jenis')
                                ->label('Jenis'),

                            TextEntry::make('unit.tahun')
                                ->label('Tahun'),
                        ])
                        ->columns(2),

                    Section::make('Informasi Tambahan')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Dibuat Pada')
                                ->dateTime('d F Y, H:i:s'),

                            TextEntry::make('updated_at')
                                ->label('Diperbarui Pada')
                                ->dateTime('d F Y, H:i:s'),
                        ])
                        ->columns(2)
                        ->collapsed(),
                        ])
                    ->columnSpanFull(),
                    Section::make('Foto Barcode BBM')
                        ->schema([
                            ImageEntry::make('barcode_bbm')
                                ->label('')
                                ->hiddenLabel()
                                ->size(400),
                        ])
                        ->grow(false),

                ])
                    // ->schema([

                    // ])
                    ->from('md'),
            ])
            ->columns(1);
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
            'index' => ListBbms::route('/'),
            'create' => CreateBbm::route('/create'),
            'view' => ViewBbm::route('/{record}'),
            'edit' => EditBbm::route('/{record}/edit'),
        ];
    }
}

