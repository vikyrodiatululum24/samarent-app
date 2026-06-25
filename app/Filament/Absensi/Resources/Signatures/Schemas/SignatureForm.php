<?php

namespace App\Filament\Absensi\Resources\Signatures\Schemas;

use App\Models\Branch;
use App\Models\Project;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SignatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project & Cabang')
                ->schema([
                    Select::make('nama')
                        ->options([
                            'reimbursement' => 'Reimbursement',
                            'overtime' => 'Overtime',
                            'driver_attendance' => 'Driver Attendance',
                        ])
                        ->label('Jenis Laporan')
                        ->required()
                        ->columnSpanFull(),
                        Select::make('project_id')
                        ->required()
                        ->label('Project')
                        ->options(Project::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->createOptionForm([TextInput::make('name')->label('Nama Project')->required()->maxLength(255)])
                        ->createOptionAction(function ($action) {
                            $action->modalHeading('Tambah Project Baru');
                        })
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            $set('branch_id', null);
                        }),
                        Select::make('branch_id')->required()->label('Branch')->options(fn(Get $get) => Branch::query()->when($get('project_id'), fn($query, $projectId) => $query->where('project_id', $projectId))->orderBy('name')->pluck('name', 'id'))->searchable()->disabled(fn(Get $get) => blank($get('project_id')))->live()->afterStateUpdated(fn(Set $set) => $set('division_id', null)),
                ])
                ->columnSpanFull(),
            Section::make('Rule Signature')
                ->schema([
                    Repeater::make('rule_signatures')
                        ->relationship()
                        ->schema([
                            TextInput::make('rules')
                                ->label('Rules')
                                ->required()
                                ->placeholder('Contoh: Approver 1'),
                        ])
                        ->label('Rule Signature')
                        ->required()
                        ->columns(1)
                        ->deleteAction(fn ($action) => $action->requiresConfirmation())
                        ->collapsible()
                        ->defaultItems(1),
                ])
                ->columnSpanFull(),
        ]);
    }
}
