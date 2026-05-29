<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\DivisionResource\Pages;
use App\Filament\Absensi\Resources\DivisionResource\RelationManagers\SetSalariesRelationManager;
use App\Models\Division;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data Overtime';

    protected static ?string $label = 'Division';

    protected static ?string $pluralLabel = 'Divisions';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('project_id')
                ->relationship('project', 'name')
                ->required()
                ->searchable()
                ->label('Project'),
            Forms\Components\Select::make('branch_id')
                ->label('Branch')
                ->options(fn (callable $get): array => \App\Models\Branch::where('project_id', $get('project_id'))->pluck('name', 'id')->all())
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return Filament::getCurrentPanel()?->getId() === 'absensi' ? static::$navigationGroup : null;
    }

    public static function getRelations(): array
    {
        return [
            SetSalariesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }
}
