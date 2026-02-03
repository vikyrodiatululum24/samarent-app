<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReimbursementResource\Pages;
use App\Filament\Resources\ReimbursementResource\RelationManagers;
use App\Models\Reimbursement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

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

                Forms\Components\Section::make('Data Odometer Awal')
                    ->schema([
                        Forms\Components\TextInput::make('km_awal')
                            ->label('KM Awal')
                            ->numeric()
                            ->required()
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
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->extraInputAttributes([
                                'capture' => 'environment',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

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
                            ->extraInputAttributes([
                                'capture' => 'environment',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Detail Perjalanan')
                    ->schema([
                        Forms\Components\TextInput::make('tujuan_perjalanan')
                            ->label('Tujuan Perjalanan')
                            ->maxLength(255)
                            ->placeholder('Masukkan tujuan perjalanan'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->maxLength(65535)
                            ->placeholder('Masukkan keterangan tambahan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Dana')
                    ->schema([
                        Forms\Components\TextInput::make('dana_masuk')
                            ->label('Dana Masuk')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->minValue(0)
                            ->step(1000),

                        Forms\Components\TextInput::make('dana_keluar')
                            ->label('Dana Keluar')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->minValue(0)
                            ->step(1000),
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
                    ->action(
                        Tables\Actions\Action::make('view_foto_awal')
                            ->modalContent(function (Reimbursement $record) {
                                $imageUrl = \Storage::url($record->foto_odometer_awal);
                                return new HtmlString('
                                    <div class="flex items-center justify-center p-4">
                                        <img
                                            src="' . $imageUrl . '"
                                            alt="Foto Odometer Awal"
                                            class="max-w-full h-auto rounded-lg shadow-lg"
                                            style="max-height: 80vh; object-fit: contain;"
                                        />
                                    </div>
                                ');
                            })
                            ->modalWidth('xl')
                            ->modalHeading(fn (Reimbursement $record) => 'Foto Odometer Awal - ' . number_format($record->km_awal) . ' KM')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                    ),

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
                    ->action(
                        Tables\Actions\Action::make('view_foto_akhir')
                            ->modalContent(function (Reimbursement $record) {
                                if (!$record->foto_odometer_akhir) {
                                    return new HtmlString('<p class="text-center p-4">Belum ada foto</p>');
                                }
                                $imageUrl = \Storage::url($record->foto_odometer_akhir);
                                return new HtmlString('
                                    <div class="flex items-center justify-center p-4">
                                        <img
                                            src="' . $imageUrl . '"
                                            alt="Foto Odometer Akhir"
                                            class="max-w-full h-auto rounded-lg shadow-lg"
                                            style="max-height: 80vh; object-fit: contain;"
                                        />
                                    </div>
                                ');
                            })
                            ->modalWidth('xl')
                            ->modalHeading(fn (Reimbursement $record) => 'Foto Odometer Akhir' . ($record->km_akhir ? ' - ' . number_format($record->km_akhir) . ' KM' : ''))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                    )
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('tujuan_perjalanan')
                    ->label('Tujuan')
                    ->searchable()
                    ->limit(30)
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
