<?php

namespace App\Filament\Resources\Bastks\Tables;

use App\Models\Bastk;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class BastksTableBaru
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_bastk')
                    ->label('No BASTK')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('unit.nopol')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn ($record) => $record->unit ? "{$record->unit->type} - {$record->unit->nopol}" : '-'),
                TextColumn::make('kepada')
                    ->label('Kepada')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('tgl_serah')
                    ->label('Tgl Serah')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('tgl_kembali')
                    ->label('Tgl Kembali')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('nama_penyerah')
                    ->label('Penyerah')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('nama_penerima')
                    ->label('Penerima')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                ViewColumn::make('is_submitted_customer')
                    ->label('Submit Customer')
                    ->view('filament.resources.bastks.columns.submit-customer-button')
                    ->disabledClick()
                    ->sortable(),
                TextColumn::make('kondisi_unit')
                    ->label('Kondisi Unit')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_submitted_customer')
                    ->label('Submit Customer')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Disubmit')
                    ->falseLabel('Belum Disubmit'),
            ])
            ->actions([
                Action::make('toggle_submit_customer')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Bastk $record): string => $record->is_submitted_customer ? 'Batalkan Submit Customer?' : 'Tandai Sudah Submit Customer?')
                    ->modalDescription(fn (Bastk $record): string => $record->is_submitted_customer
                        ? 'Apakah Anda yakin ingin membatalkan status submit customer untuk BASTK ini?'
                        : 'Apakah Anda yakin ingin menandai data BASTK ini telah disubmit ke customer?')
                    ->modalSubmitActionLabel(fn (Bastk $record): string => $record->is_submitted_customer ? 'Ya, Batalkan' : 'Ya, Submit')
                    ->modalSubmitAction(fn ($action, Bastk $record) => $action->color($record->is_submitted_customer ? 'danger' : 'success'))
                    ->action(function (Bastk $record): void {
                        $newState = ! $record->is_submitted_customer;
                        $record->update(['is_submitted_customer' => $newState]);

                        Notification::make()
                            ->title($newState ? 'BASTK berhasil ditandai sudah disubmit ke customer' : 'Status submit customer berhasil dibatalkan')
                            ->success()
                            ->send();
                    })
                    ->extraAttributes(['class' => 'hidden']),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_submit_customer')
                        ->label('Tandai Submit Customer')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_submitted_customer' => true]);
                            Notification::make()
                                ->title('BASTK terpilih berhasil ditandai sudah disubmit ke customer')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('bulk_unsubmit_customer')
                        ->label('Batalkan Submit Customer')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_submitted_customer' => false]);
                            Notification::make()
                                ->title('Status submit customer untuk BASTK terpilih berhasil dibatalkan')
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
