<?php

namespace App\Filament\Absensi\Resources\ProjectResource\RelationManagers;

use App\Models\Division;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DivisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'divisions';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('branch_id')
                ->label('Branch')
                ->options(fn (): array => $this->getOwnerRecord()->branches()->pluck('name', 'id')->all())
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
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
            ->headerActions([
                Actions\Action::make('createDivision')
                    ->label('Create Division')
                    ->icon('heroicon-o-plus')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(fn (): array => $this->getOwnerRecord()->branches()->pluck('name', 'id')->all())
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        $branchId = (int) $data['branch_id'];

                        $isAllowed = $this->getOwnerRecord()->branches()->whereKey($branchId)->exists();

                        if (! $isAllowed) {
                            Notification::make()
                                ->title('Branch tidak valid untuk project ini.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Division::query()->create([
                            'project_id' => $this->getOwnerRecord()->id,
                            'branch_id' => $branchId,
                            'name' => $data['name'],
                        ]);

                        Notification::make()
                            ->title('Division berhasil dibuat.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(fn (): array => $this->getOwnerRecord()->branches()->pluck('name', 'id')->all())
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ]),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
