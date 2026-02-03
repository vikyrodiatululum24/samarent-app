<?php

namespace App\Filament\Resources\ReimbursementResource\Pages;

use App\Filament\Resources\ReimbursementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReimbursements extends ListRecords
{
    protected static string $resource = ReimbursementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(function (): string {
                    $filters = $this->tableFilters;
                    $dari = $filters['created_at']['dari'] ?? null;
                    $sampai = $filters['created_at']['sampai'] ?? null;

                    $params = [];
                    if ($dari) $params['dari'] = $dari;
                    if ($sampai) $params['sampai'] = $sampai;

                    return route('reimbursement.print-pdf', $params);
                })
                ->openUrlInNewTab(),
            Actions\CreateAction::make(),
        ];
    }
}
