<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NorekResource\Pages;
use App\Filament\Resources\NorekResource\RelationManagers;
use App\Models\Norek;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NorekResource extends Resource
{
    protected static ?string $model = Norek::class;

    protected static ?string $navigationLabel = 'Data Rekening';
    protected static ?string $label = 'Data Rekening';
    protected static ?string $pluralLabel = 'Data Rekening';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
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
                        'BCA' => 'BCA',
                        'MANDIRI' => 'MANDIRI',
                        'BRI' => 'BRI',
                        'BNI' => 'BNI',
                        'PERMATA' => 'PERMATA',
                        'BTN' => 'BTN',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
