<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Absensi\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Absensi\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Absensi\Resources\ProjectResource\RelationManagers\BranchesRelationManager;
use App\Filament\Absensi\Resources\ProjectResource\RelationManagers\DivisionsRelationManager;
use App\Models\Project;
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

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data Overtime';

    protected static ?string $navigationLabel = 'Project';

    protected static ?string $pluralLabel = 'Projects';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branches_count')
                    ->counts('branches')
                    ->label('Branches'),
                Tables\Columns\TextColumn::make('divisions_count')
                    ->counts('divisions')
                    ->label('Divisions'),
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
            BranchesRelationManager::class,
            DivisionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
