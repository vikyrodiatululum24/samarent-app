<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReimbursementResource\Pages;
use App\Filament\Resources\ReimbursementResource\RelationManagers;
use App\Models\Reimbursement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ReimbursementResource extends Resource
{
    protected static ?string $model = Reimbursement::class;
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $pluralLabel = 'Reimbursement';
    protected static ?string $navigationLabel = 'Reimbursement';

    // Make this resource available in all panels
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
                    ->required(),

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

                Forms\Components\Section::make('Data Odometer Awal')
                    ->schema([
                        Forms\Components\TextInput::make('km_awal')
                            ->label('KM Awal')
                            ->numeric()
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
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('type') === 'bbm'),

                Forms\Components\Section::make('Data Odometer Akhir')
                    ->schema([
                        Forms\Components\TextInput::make('km_akhir')
                            ->label('KM Akhir')
                            ->numeric()
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
                    ->columns(2)
                    ->collapsible()
                    ->visible(fn (Forms\Get $get) => $get('type') === 'bbm'),

                Forms\Components\Section::make('Detail Perjalanan')
                    ->schema([
                        Forms\Components\TextInput::make('tujuan_perjalanan')
                            ->label('Tujuan Perjalanan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Bandung'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->required()
                            ->rows(3)
                            ->maxLength(65535)
                            ->placeholder('Contoh: Pengisian bahan bakar untuk perjalanan dinas')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Nota Pembayaran')
                    ->schema([
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

                Forms\Components\Section::make('Dana')
                    ->schema([
                        Forms\Components\TextInput::make('dana_masuk')
                            ->label('Dana Masuk')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->minValue(0),

                        Forms\Components\TextInput::make('dana_keluar')
                            ->label('Dana Keluar')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->minValue(0),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id()))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export')
                    ->label('Export Selected')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $ids = $records->pluck('id')->toArray();
                        return redirect()->route('reimbursement.print-pdf', ['ids' => implode(',', $ids)]);
                    })
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReimbursements::route('/'),
            'create' => Pages\CreateReimbursement::route('/create'),
            'view' => Pages\ViewReimbursement::route('/{record}'),
            'edit' => Pages\EditReimbursement::route('/{record}/edit'),
        ];
    }
}
