<?php

namespace App\Filament\Absensi\Resources\KehadiranDriverResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OvertimePayRelationManager extends RelationManager
{
    protected static string $relationship = 'OvertimePay';

    public function form(Form $form): Form
    {
        return $form->schema([
            // Forms\Components\DatePicker::make('tanggal')->label('Tanggal')->required(),

            // Forms\Components\TextInput::make('hari')->label('Hari')->required(),
            // Forms\Components\Select::make('shift')
            //     ->label('Shift')
            //     ->options([
            //         'Weekday' => 'Weekday',
            //         'Holiday' => 'Holiday',
            //     ]),

            // Forms\Components\TimePicker::make('from_time')->label('Dari Jam')->required(),

            // Forms\Components\TimePicker::make('to_time')->label('Sampai Jam')->required(),

            // Forms\Components\TimePicker::make('ot_hours_time')->label('Jam OT')->required(),
            // Forms\Components\TextInput::make('ot_1x')->label('OT 1x')->numeric()->minValue(0),

            // Forms\Components\TextInput::make('ot_2x')->label('OT 2x')->numeric()->minValue(0),

            // Forms\Components\TextInput::make('ot_3x')->label('OT 3x')->numeric()->minValue(0),

            // Forms\Components\TextInput::make('ot_4x')->label('OT 4x')->numeric()->minValue(0),
            // Forms\Components\TextInput::make('calculated_ot_hours')->label('Total Jam OT')->numeric()->minValue(0),
            // Forms\Components\TextInput::make('ot_amount')->label('Jumlah OT')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->minValue(0),
            // Forms\Components\TextInput::make('transport')->label('Transport')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->minValue(0),
            Forms\Components\TextInput::make('monthly_allowance')->label('Tunjangan Bulanan')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->minValue(0),
            Forms\Components\TextInput::make('out_of_town')->label('Dinas Luar')->numeric()->minValue(0)->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->maxLength(255),
            Forms\Components\TextInput::make('overnight')->label('Menginap')->numeric()->minValue(0)->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->maxLength(255),
            Forms\Components\Textarea::make('remarks')->label('Keterangan')->columnSpanFull()->maxLength(65535),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('driver')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date()->label('Tanggal')->sortable(),
                Tables\Columns\TextColumn::make('hari')->label('Hari')->sortable(),
                Tables\Columns\TextColumn::make('shift')->label('Shift')->sortable(),
                Tables\Columns\TextColumn::make('from_time')->label('Dari Jam')->sortable(),
                Tables\Columns\TextColumn::make('to_time')->label('Sampai Jam')->sortable(),
                Tables\Columns\TextColumn::make('ot_hours_time')->label('Jam OT')->sortable(),
                Tables\Columns\TextColumn::make('ot_1x')->label('OT 1.5x')->sortable(),
                Tables\Columns\TextColumn::make('ot_2x')->label('OT 2x')->sortable(),
                Tables\Columns\TextColumn::make('ot_3x')->label('OT 3x')->sortable(),
                Tables\Columns\TextColumn::make('ot_4x')->label('OT 4x')->sortable(),
                Tables\Columns\TextColumn::make('calculated_ot_hours')->label('Total Jam OT')->sortable(),
                Tables\Columns\TextColumn::make('amount_per_hour')->label('Amount/Hour')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('ot_amount')->label('Jumlah OT')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('transport')->label('Transport')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('monthly_allowance')->label('Tunjangan Bulanan')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('out_of_town')->label('Dinas Luar')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('overnight')->label('Menginap')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('remarks')->label('Keterangan')->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
    Tables\Actions\Action::make('edit')
        ->label('Edit')
        ->icon('heroicon-o-pencil')
        ->form([
                        Forms\Components\TextInput::make('monthly_allowance')->label('Tunjangan Bulanan')->numeric()->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(',')->minValue(0),
            Forms\Components\TextInput::make('out_of_town')->label('Dinas Luar')->numeric()->minValue(0)->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\TextInput::make('overnight')->label('Menginap')->numeric()->minValue(0)->prefix('Rp ')->mask(RawJs::make('$money($input)'))->stripCharacters(','),
            Forms\Components\Textarea::make('remarks')->label('Keterangan')->columnSpanFull()->maxLength(65535),
        ])
        ->fillForm(fn ($record) => $record->toArray())
        ->action(function ($record, array $data) {
            $record->update($data);
            Notification::make()
                ->title('Overtime Pay updated successfully')
                ->success()
                ->send();
        }),
])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([])]);
    }
}
