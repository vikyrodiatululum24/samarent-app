<?php

namespace App\Filament\Manager\Resources\DriverResource\RelationManagers;

use App\Models\Reimbursement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ReimbursementsRelationManager extends RelationManager
{
    protected static string $relationship = 'reimbursements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('keterangan')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('km_awal')
                    ->label('KM Awal')
                    ->numeric()
                    ->sortable()
                    ->suffix(' KM'),

                Tables\Columns\ImageColumn::make('foto_odometer_awal')
                    ->label('Foto KM Awal')
                    ->circular()
                    ->size(60)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->url(fn (Reimbursement $record) => $record->foto_odometer_awal ? Storage::url($record->foto_odometer_awal) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('km_akhir')
                    ->label('KM Akhir')
                    ->numeric()
                    ->sortable()
                    ->suffix(' KM')
                    ->placeholder('-'),

                Tables\Columns\ImageColumn::make('foto_odometer_akhir')
                    ->label('Foto KM Akhir')
                    ->circular()
                    ->size(60)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->url(fn (Reimbursement $record) => $record->foto_odometer_akhir ? Storage::url($record->foto_odometer_akhir) : null)
                    ->openUrlInNewTab()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tujuan_perjalanan')
                    ->label('Tujuan')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->placeholder('-'),

                Tables\Columns\ImageColumn::make('nota')
                    ->label('Foto Nota')
                    ->circular()
                    ->size(60)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->url(fn (Reimbursement $record) => $record->nota ? Storage::url($record->nota) : null)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('dana_masuk')
                    ->label('Dana Masuk')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('dana_keluar')
                    ->label('Dana Keluar')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d/m/Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $filters = $this->tableFilters;
                        $dari = $filters['created_at']['dari'] ?? null;
                        $sampai = $filters['created_at']['sampai'] ?? null;
                        $userId = $this->ownerRecord->user_id;

                        $params = ['user_id' => $userId];
                        if ($dari) $params['dari'] = $dari;
                        if ($sampai) $params['sampai'] = $sampai;
                        return redirect()->route('manager.reimbursement.print-pdf', $params);
                    })
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export')
                    ->label('Export Selected')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $ids = $records->pluck('id')->toArray();
                        $userId = $records->first()->user_id;
                        return redirect()->route('manager.reimbursement.print-pdf', ['ids' => implode(',', $ids), 'user_id' => $userId]);
                    })
                    ->openUrlInNewTab(),
            ]);
    }
}
