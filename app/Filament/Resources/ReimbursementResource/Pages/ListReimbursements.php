<?php

namespace App\Filament\Resources\ReimbursementResource\Pages;

use App\Filament\Resources\ReimbursementResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListReimbursements extends ListRecords
{
    protected static string $resource = ReimbursementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Actions\Action::make('print_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (): void {
                    $filters = $this->tableFilters ?? [];

                    if (empty($filters['created_at']['dari']) || empty($filters['created_at']['sampai'])) {
                        Notification::make()
                            ->title('Filter Diperlukan')
                            ->body('Harap isi filter tanggal terlebih dahulu sebelum melakukan export.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $dari = $filters['created_at']['dari'] ?? null;
                    $sampai = $filters['created_at']['sampai'] ?? null;

                    $params = [];
                    if ($dari) $params['dari'] = $dari;
                    if ($sampai) $params['sampai'] = $sampai;

                    redirect(route('reimbursement.print-pdf', $params));
                }),
                Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->action(function (): void {
                    $filters = $this->tableFilters ?? [];

                    if (empty($filters['created_at']['dari']) || empty($filters['created_at']['sampai'])) {
                        Notification::make()
                            ->title('Filter Diperlukan')
                            ->body('Harap isi filter tanggal terlebih dahulu sebelum melakukan export.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $dari = $filters['created_at']['dari'] ?? null;
                    $sampai = $filters['created_at']['sampai'] ?? null;

                    $params = [];
                    if ($dari) $params['dari'] = $dari;
                    if ($sampai) $params['sampai'] = $sampai;

                    redirect(route('reimbursement.export-excel', $params));
                }),
            ])
            ->label('Export Data')
            ->button()
            ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
