<?php

namespace App\Filament\Absensi\Resources;

use App\Filament\Absensi\Resources\ProjectDriverManagementResource\Pages;
use App\Filament\Absensi\Resources\ProjectDriverManagementResource\RelationManagers\ProjectDriversRelationManager;
use App\Models\Project;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectDriverManagementResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationLabel = 'Project Driver';

    protected static ?string $pluralLabel = 'Project Driver';

    protected static ?string $slug = 'project-drivers';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Project')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detail Project')
                ->schema([
                    TextEntry::make('name')
                        ->label('Nama Project'),
                    TextEntry::make('drivers_count')
                        ->label('Jumlah Driver')
                        ->getStateUsing(fn (Project $record) => $record->drivers()->count()),
                ])
                ->columns(2),
        ])
        ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('drivers_count')
                    ->label('Jumlah Driver')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectDriversRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('drivers')->orderBy('drivers_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectDriverManagements::route('/'),
            'view' => Pages\ViewProjectDriverManagement::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
