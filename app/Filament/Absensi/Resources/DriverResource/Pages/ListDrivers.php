<?php

namespace App\Filament\Absensi\Resources\DriverResource\Pages;

use App\Filament\Absensi\Resources\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printForm')
                ->label('Print Form Driver')
                ->url(route('filament.driver.print-form-driver'))
                ->openUrlInNewTab()
                ->icon('heroicon-o-printer')
                ->color('success'),
            Actions\Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-document-arrow-up')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File Excel')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv'
                        ])
                ])
                ->action(function (array $data) {
                    $file = storage_path('app/public/' . $data['file']);
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DriversImport, $file);
                        \Filament\Notifications\Notification::make()
                            ->title('Sukses')
                            ->body('Data driver berhasil di-import.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal')
                            ->body('Terjadi kesalahan saat mengimport data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
