<?php

namespace App\Filament\Manager\Resources\DriverResource\RelationManagers;

use App\Models\Reimbursement;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class ReimbursementsRelationManager extends RelationManager
{
    protected static string $relationship = 'reimbursements';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('keterangan')
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->date('d/m/Y')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('keterangan')->label('Keterangan')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('type')->label('Tipe')->formatStateUsing(fn(string $state): string => strtoupper($state))->sortable()->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('km_awal')->label('KM Awal')->numeric()->sortable()->suffix(' KM'),

                Tables\Columns\ImageColumn::make('foto_odometer_awal')
                    ->label('Foto KM Awal')
                    ->circular()
                    ->size(60)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->url(fn(Reimbursement $record) => $record->foto_odometer_awal ? Storage::url($record->foto_odometer_awal) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('km_akhir')->label('KM Akhir')->numeric()->sortable()->suffix(' KM')->placeholder('-'),

                Tables\Columns\ImageColumn::make('foto_odometer_akhir')
                    ->label('Foto KM Akhir')
                    ->circular()
                    ->size(60)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->url(fn(Reimbursement $record) => $record->foto_odometer_akhir ? Storage::url($record->foto_odometer_akhir) : null)
                    ->openUrlInNewTab()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tujuan_perjalanan')->label('Tujuan')->searchable()->limit(30)->toggleable(isToggledHiddenByDefault: false)->placeholder('-'),

                Tables\Columns\ImageColumn::make('nota')
                    ->label('Foto Nota')
                    ->circular()
                    ->size(60)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->url(fn(Reimbursement $record) => $record->nota ? Storage::url($record->nota) : null)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('metode_pembayaran')->label('Metode Pembayaran')->searchable()->placeholder('-'),

                Tables\Columns\TextColumn::make('dana_masuk')->label('Dana Masuk')->money('IDR')->sortable()->placeholder('-'),

                Tables\Columns\TextColumn::make('dana_keluar')->label('Dana Keluar')->money('IDR')->sortable()->placeholder('-'),

                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Diupdate')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([Forms\Components\DatePicker::make('dari')->label('Dari Tanggal')->native(false), Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal')->native(false)])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['dari'], fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date))->when($data['sampai'], fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date));
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
                Action::make('export')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        $filters = $this->tableFilters;
                        if (empty($filters['date']['dari']) || empty($filters['date']['sampai'])) {
                            // Jika filter tanggal belum lengkap, tampilkan notifikasi error
                            Notification::make()->title('Filter Diperlukan')->body('Harap isi filter tanggal terlebih dahulu sebelum melakukan export.')->danger()->send();
                            return;
                        }
                        $dari = $filters['date']['dari'] ?? null;
                        $sampai = $filters['date']['sampai'] ?? null;
                        $userId = $this->ownerRecord->user_id;

                        $params = ['user_id' => $userId];
                        if ($dari) {
                            $params['dari'] = $dari;
                        }
                        if ($sampai) {
                            $params['sampai'] = $sampai;
                        }
                        return redirect()->route('manager.reimbursement.print-pdf', $params);
                    })
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->fillForm(function ($record) {
                        $normalizePath = function (?string $path): ?string {
                            if (blank($path)) {
                                return null;
                            }

                            return str_replace('storage/', '', $path);
                        };

                        return [
                            'date' => $record->date,
                            'type' => $record->type,
                            'km_awal' => $record->km_awal,
                            'km_akhir' => $record->km_akhir,
                            'foto_odometer_awal' => $normalizePath($record->foto_odometer_awal),
                            'foto_odometer_akhir' => $normalizePath($record->foto_odometer_akhir),
                            'tujuan_perjalanan' => $record->tujuan_perjalanan,
                            'keterangan' => $record->keterangan,
                            'metode_pembayaran' => $record->metode_pembayaran,
                            'nota' => $normalizePath($record->nota),
                            'dana_masuk' => $record->dana_masuk,
                            'dana_keluar' => $record->dana_keluar,
                        ];
                    })
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Reimbursement')
                            ->options([
                                'bbm' => 'BBM',
                                'tol' => 'Tol',
                                'parkir' => 'Parkir',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->live()
                            ->columnSpanFull()
                            ->placeholder('Pilih tipe reimbursement'),

                        Section::make('Data Odometer Awal')
                            ->schema([
                                Forms\Components\TextInput::make('km_awal')
                                    ->label('KM Awal')
                                    ->inputMode('numeric')
                                    ->rules(['regex:/^[0-9]+$/'])
                                    ->validationMessages([
                                        'regex' => 'KM Awal harus berupa angka.',
                                    ])
                                    ->minValue(0)
                                    ->suffix('KM')
                                    ->placeholder('Masukkan KM awal'),

                                Forms\Components\FileUpload::make('foto_odometer_awal')
                                    ->label('Foto Odometer Awal')
                                    ->image()
                                    ->resize(50)
                                    ->imageEditor()
                                    ->directory('reimbursement/odometer-awal')
                                    ->visibility('public')
                                    ->imagePreviewHeight('250')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn(Get $get) => $get('type') === 'bbm'),

                        Section::make('Data Odometer Akhir')
                            ->schema([
                                Forms\Components\TextInput::make('km_akhir')
                                    ->label('KM Akhir')
                                    ->inputMode('numeric')
                                    ->rules(['regex:/^[0-9]+$/'])
                                    ->validationMessages([
                                        'regex' => 'KM Akhir harus berupa angka.',
                                    ])
                                    ->minValue(0)
                                    ->suffix('KM')
                                    ->placeholder('Masukkan KM akhir')
                                    ->gt('km_awal'),

                                Forms\Components\FileUpload::make('foto_odometer_akhir')
                                    ->label('Foto Odometer Akhir')
                                    ->image()
                                    ->imageEditor()
                                    ->resize(50)
                                    ->directory('reimbursement/odometer-akhir')
                                    ->visibility('public')
                                    ->imagePreviewHeight('250')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->visible(fn(Get $get) => $get('type') === 'bbm'),

                        Section::make('Detail Perjalanan')
                            ->schema([Forms\Components\TextInput::make('tujuan_perjalanan')->label('Tujuan Perjalanan')->required()->maxLength(255)->placeholder('Contoh: Bandung'), Forms\Components\Textarea::make('keterangan')->label('Keterangan')->required()->rows(3)->maxLength(65535)->placeholder('Contoh: Pengisian bahan bakar untuk perjalanan dinas')->columnSpanFull()])
                            ->collapsible(),

                        Section::make('Pembayaran')
                            ->schema([
                                Forms\Components\Select::make('metode_pembayaran')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'cash' => 'Cash',
                                        'fleet_card' => 'Fleet Card',
                                    ])
                                    ->required()
                                    ->placeholder('Pilih metode pembayaran'),
                                Forms\Components\FileUpload::make('nota')
                                    ->label('Foto Nota')
                                    ->required()
                                    ->image()
                                    ->imageEditor()
                                    ->resize(50)
                                    ->directory('reimbursement/nota')
                                    ->visibility('public')
                                    ->imagePreviewHeight('250')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),

                        Section::make('Dana')
                            ->schema([
                                Forms\Components\TextInput::make('dana_masuk')
                                    ->label('Dana Masuk')
                                    ->inputMode('numeric')
                                    ->step(0.01)
                                    ->prefix('Rp')
                                    ->placeholder('0')
                                    ->minValue(0),

                                Forms\Components\TextInput::make('dana_keluar')
                                    ->label('Dana Keluar')
                                    ->inputMode('numeric')
                                    ->step(0.01)
                                    ->prefix('Rp')
                                    ->placeholder('0')
                                    ->minValue(0),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->collapsible(),
                    ])
                    ->action(function(array $data, $record) {
                        $normalizePath = function (?string $path): ?string {
                            if (blank($path)) {
                                return null;
                            }

                            return str_replace('storage/', '', $path);
                        };

                        if (! empty($data['type']) && $data['type'] !== 'bbm') {
                            $data['km_awal'] = null;
                            $data['km_akhir'] = null;
                            $data['foto_odometer_awal'] = null;
                            $data['foto_odometer_akhir'] = null;
                        }

                        $record->update([
                            'type' => $data['type'],
                            'km_awal' => $data['km_awal'],
                            'km_akhir' => $data['km_akhir'],
                            'foto_odometer_awal' => $normalizePath($data['foto_odometer_awal']),
                            'foto_odometer_akhir' => $normalizePath($data['foto_odometer_akhir']),
                            'tujuan_perjalanan' => $data['tujuan_perjalanan'],
                            'keterangan' => $data['keterangan'],
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            'nota' => $normalizePath($data['nota']),
                            'dana_masuk' => $data['dana_masuk'],
                            'dana_keluar' => $data['dana_keluar'],
                            'date' => $data['date'],
                        ]);
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->bulkActions([
                BulkAction::make('export')
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
