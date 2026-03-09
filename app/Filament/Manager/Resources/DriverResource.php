<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\DriverResource\RelationManagers\OvertimePaysRelationManager;
use App\Filament\Manager\Resources\DriverResource\Pages;
use App\Filament\Manager\Resources\DriverResource\RelationManagers\DriverAttendenceRelationManager;
use App\Filament\Manager\Resources\DriverResource\RelationManagers\ReimbursementsRelationManager;
use App\Models\Driver;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;
    protected static ?string $pluralModelLabel = 'Driver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->label('Nama Driver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email Driver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->disk('public')
                    ->square()
                    ->label('Foto Driver')
                    ->getStateUsing(fn ($record) => str_replace('storage/', '', $record->photo)),
                    
            ])
            ->filters([
                //
            ])
            ->actions([
                //
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
                Infolists\Components\Section::make('Akun')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')->label('Nama Driver')
                            ->formatStateUsing(fn($state) => $state ?? '-')
                            ->getStateUsing(fn($record) => strtoupper($record->user->name)),
                        Infolists\Components\TextEntry::make('user.email')->label('Email')
                            ->formatStateUsing(fn($state) => $state ?? '-')
                            ->getStateUsing(fn($record) => strtoupper($record->user->email)),
                    ])
                    ->columns(2)
                    ->inlineLabel(),
                Infolists\Components\Section::make('Identitas')
                    ->columns(2)
                    ->inlineLabel()
                    ->schema([
                        Infolists\Components\TextEntry::make('no_wa')->label('No. WhatsApp')->placeholder('Untitled')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->no_wa)),
                        Infolists\Components\TextEntry::make('nik')->label('NIK')->formatStateUsing(fn($state) => $state ?? '-'),
                        Infolists\Components\TextEntry::make('sim')->label('SIM')->formatStateUsing(fn($state) => $state ?? '-'),
                        Infolists\Components\TextEntry::make('jenis_kelamin')->label('Jenis Kelamin')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->jenis_kelamin)),
                        Infolists\Components\TextEntry::make('tempat')->label('Tempat Lahir')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->tempat)),
                        Infolists\Components\TextEntry::make('tanggal_lahir')->label('Tanggal Lahir')->date(),
                        Infolists\Components\TextEntry::make('agama')->label('Agama')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->agama)),
                        Infolists\Components\ImageEntry::make('photo')->label('Foto Driver')->hiddenLabel(),
                    ]),
                Infolists\Components\Section::make('Alamat')
                    ->schema([
                        Infolists\Components\TextEntry::make('alamat')->label('Alamat')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->alamat)),
                        Infolists\Components\TextEntry::make('rt')->label('RT')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->rt)),
                        Infolists\Components\TextEntry::make('rw')->label('RW')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->rw)),
                        Infolists\Components\TextEntry::make('kelurahan')->label('Kelurahan/Desa')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->kelurahan)),
                        Infolists\Components\TextEntry::make('kecamatan')->label('Kecamatan')->formatStateUsing(fn($state) => $state ?? '-')->getStateUsing(fn($record) => strtoupper($record->kecamatan)),
                    ])
                    ->inlineLabel()
                    ->columns(2),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            DriverAttendenceRelationManager::class,
            OvertimePaysRelationManager::class,
            ReimbursementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
            'view' => Pages\ViewDriver::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Menghilangkan tombol create (newDriver)
    }

    public static function canEdit($record): bool
    {
        return false; // Menghilangkan tombol edit pada setiap record
    }

    public static function canDelete($record): bool
    {
        return false; // Menghilangkan tombol delete pada setiap record
    }

    public static function canDeleteAny(): bool
    {
        return false; // Menghilangkan tombol delete untuk bulk action
    }


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        // Ambil data manager terkait user
        $manager = $user->manager;
        // Jika user adalah centralakun@samarent.com, tampilkan semua data
        if ($user->email === 'centralakun@samarent.com') {
            return parent::getEloquentQuery();
        }

        // Jika tidak ada manager, kembalikan query kosong
        if (!$manager) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // ambil id project yang dimiliki manager
        $projectIds = Project::where('name', $manager->perusahaan)->pluck('id')->toArray();

        // Ambil UP dan project yang dimiliki user (manager)
        $projects = array_filter([$manager->perusahaan]);

        $query = parent::getEloquentQuery();

        if (!empty($projects)) {
            // Jika user punya keduanya, filter berdasarkan up dan project
            $query->whereIn('project_id', $projectIds);
        } else {
            // Tidak punya keduanya, kembalikan query kosong
            $query->whereRaw('1 = 0');
        }

        return $query;
    }
}
