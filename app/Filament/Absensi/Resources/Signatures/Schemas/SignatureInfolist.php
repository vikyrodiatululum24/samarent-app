<?php

namespace App\Filament\Absensi\Resources\Signatures\Schemas;

use App\Models\RuleSignature;
use App\Models\Signature;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SignatureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project & Cabang')
                ->schema([TextEntry::make('project.name')->label('Project')->placeholder('-'), TextEntry::make('branch.name')->label('Branch')->placeholder('-')])
                ->columnSpanFull()
                ->columns(2),
            Section::make('Info Signature')
                ->schema([TextEntry::make('nama')->placeholder('-')->label('Jenis Laporan')->formatStateUsing(fn($state) => ucwords(strtolower($state))), IconEntry::make('is_active')->boolean()])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Authorization')
                ->schema([ViewEntry::make('authorizations')->view('filament.absensi.signatures.pages.authorizations')->columnSpanFull()])
                ->headerActions([
                    Action::make('manageSignature')
                        ->label('Kelola Signature')
                        ->icon('heroicon-o-pencil-square')
                        ->slideOver()
                        ->form([
                            Select::make('rule_signature_id')
                                ->label('Authorization')
                                ->options(fn($record) => $record->rule_signatures()->pluck('rules', 'id'))
                                ->live()
                                ->required()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $ruleSignature = RuleSignature::with('signatures')->find($state);

                                    $set(
                                        'signatures',
                                        $ruleSignature?->signatures
                                            ->map(
                                                fn($signature) => [
                                                    'id' => $signature->id,
                                                    'nama' => $signature->nama,
                                                    'jabatan' => $signature->jabatan,
                                                    'nip' => $signature->nip,
                                                    'ttd' => $signature->ttd ? [$signature->ttd] : [],
                                                    'is_active' => $signature->is_active,
                                                ],
                                            )
                                            ->toArray() ?? [],
                                    );
                                }),

                            Repeater::make('signatures')
                                ->schema([
                                    Hidden::make('id'),
                                    TextInput::make('nama')->required(),
                                    TextInput::make('jabatan'),
                                    TextInput::make('nip'),
                                    FileUpload::make('ttd')
                                        ->label('Tanda Tangan')
                                        ->image()
                                        ->directory('signatures')
                                        ->disk('public')
                                        ->imagePreviewHeight('64px')
                                        ->resize(50)
                                        ->optimize('webp')
                                        ->maxSize(2048)
                                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/svg']),
                                    Toggle::make('is_active'),
                                ])
                                ->reorderable(false),
                        ])
                        ->action(function ($record, $form, $data) {
                            foreach ($data['signatures'] as $index => $signature) {
                                $oldSignature = Signature::find($signature['id']);
                                $rule = RuleSignature::find($data['rule_signature_id']);
                                $rule->signatures()->updateOrCreate(
                                    [
                                        'id' => $signature['id'] ?? null,
                                    ],
                                    [
                                        'nama' => $signature['nama'],
                                        'jabatan' => $signature['jabatan'],
                                        'nip' => $signature['nip'],
                                        'ttd' => is_array($signature['ttd'] ?? null) ? $signature['ttd'][0] ?? null : $signature['ttd'],
                                        'urutan' => $index + 1 ?? 0,
                                        'is_active' => $signature['is_active'] ?? true,
                                    ],
                                );

                                // if image change delete old image
                                if ($signature['id'] && $signature['ttd'] !== $oldSignature->ttd) {
                                    Storage::disk('public')->delete($oldSignature->ttd);
                                }

                                $ids[] = $signature['id'];
                            }

                            if (!empty($ids)) {
                                Signature::whereNotIn('id', $ids)->where('rule_signature_id', $data['rule_signature_id'])->get()->each->delete();
                            } else {
                                Signature::where('rule_signature_id', $data['rule_signature_id'])->get()->each->delete();
                            }
                        }),
                ])
                ->columnSpanFull(),
        ]);
    }
}
