<?php

namespace App\Filament\Absensi\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\SetSalary;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use App\Filament\Absensi\Resources\SetSalaryResource\Pages;

class SetSalaryResource extends Resource
{
    protected static ?string $model = SetSalary::class;

    protected static ?string $label = 'Set Salary';

    protected static ?string $pluralLabel = 'Set Salaries';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->required()
                    ->relationship('project', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->default(0)
                    ->label('Amount/Hour')
                    ->helperText('Masukan nilai sesuai dengan nominal gaji per jam'),
                Forms\Components\TextInput::make('overtime1')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Overtime 1'),
                Forms\Components\TextInput::make('overtime2')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Overtime 2'),
                Forms\Components\TextInput::make('overtime3')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Overtime 3'),
                Forms\Components\TextInput::make('overtime4')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Overtime 4'),
                Forms\Components\TextInput::make('transport')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_id')
                    ->label('Project')
                    ->getStateUsing(fn (SetSalary $record) => $record->project ? $record->project->name : '-')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->label('Amount/Hour')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('overtime1')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime2')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime3')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime4')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transport')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListSetSalaries::route('/'),
            'create' => Pages\CreateSetSalary::route('/create'),
            'edit' => Pages\EditSetSalary::route('/{record}/edit'),
        ];
    }
}
