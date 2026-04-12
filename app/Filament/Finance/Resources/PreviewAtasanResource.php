<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\PreviewAtasanResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class PreviewAtasanResource extends PengajuanResource
{
    protected static ?string $navigationLabel = 'Preview Atasan';

    protected static ?string $label = 'Preview Atasan';

    protected static ?string $pluralLabel = 'Preview Atasan';

    protected static ?string $slug = 'preview-atasan';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('bos_joulmer')
            ->whereHas('bos_joulmer');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreviewAtasans::route('/'),
            'view' => Pages\ViewPreviewAtasan::route('/{record}'),
            'proses' => Pages\ProsesPreviewAtasan::route('/{record}/proses'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Preview Atasan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Preview Atasan';
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        if (!is_string($modelClass) || !class_exists($modelClass)) {
            return '0';
        }

        return (string) $modelClass::query()
            ->whereHas('bos_joulmer')
            ->where('keterangan_proses', 'pengajuan atasan')
            ->whereHas('bos_joulmer', function ($query) {
                $query->where('is_approved', 'approved');
            })
            ->count();
    }
}
