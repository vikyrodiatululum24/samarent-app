<?php

namespace App\Filament\Imports;

use App\Models\Driver;
use App\Models\DriverAttendence;
use Carbon\Carbon;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DriverAttendanceImporter extends Importer
{
    protected static ?string $model = DriverAttendence::class;

    protected ?Driver $driver = null;

    protected bool $recordExists = false;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user_id')
                ->label('User ID Driver')
                ->example('1')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('date')
                ->example('02/04/2026')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('shift')
                ->label('Shift (Opsional)')
                ->example('Holiday')
                ->rules(['nullable', 'string']),
            ImportColumn::make('project_id')
                ->label('Project ID')
                ->example('1')
                ->requiredMapping()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('end_user_id')
                ->label('Start User ID')
                ->example('1')
                ->requiredMapping()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('unit_id')
                ->label('Unit ID')
                ->example('1')
                ->requiredMapping()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('start_km')
                ->label('KM Awal')
                ->example('1000')
                ->requiredMapping()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('time_in')
                ->label('Jam Masuk')
                ->example('08:00')
                ->requiredMapping()
                ->rules(['nullable', 'regex:/^(?:(?:[01]?\d|2[0-3]):[0-5]\d(?::[0-5]\d)?|(?:0?\d|1[0-2]):[0-5]\d(?::[0-5]\d)?\s?(?:AM|PM|am|pm))$/'])
                ->ignoreBlankState(),
            ImportColumn::make('time_out')
                ->label('Jam Pulang')
                ->example('17:00')
                ->requiredMapping()
                ->rules(['nullable', 'regex:/^(?:(?:[01]?\d|2[0-3]):[0-5]\d(?::[0-5]\d)?|(?:0?\d|1[0-2]):[0-5]\d(?::[0-5]\d)?\s?(?:AM|PM|am|pm))$/'])
                ->ignoreBlankState(),
            ImportColumn::make('end_user_out')
                ->label('End User ID')
                ->example('1')
                ->rules(['nullable', 'integer']),
        ];
    }

    public function resolveRecord(): ?DriverAttendence
    {
        $this->driver = Driver::where('user_id', $this->data['user_id'] ?? null)->first();

        if (! $this->driver) {
            throw new RowImportFailedException('Driver tidak ditemukan untuk user_id tersebut.');
        }

        $date = $this->parseExcelDate($this->data['date']);

        $attendance = DriverAttendence::where('driver_id', $this->driver->id)
            ->whereDate('date', $date)
            ->first();

        if (! $attendance) {
            $this->recordExists = false;

            return new DriverAttendence();
        }

        $this->recordExists = true;

        return $attendance;
    }

    public function fillRecord(): void
    {
        $record = $this->record;

        if (! $record) {
            return;
        }

        $record->user_id = $this->driver?->user_id ?? $this->data['user_id'];
        $record->driver_id = $this->driver?->id ?? Driver::where('user_id', $this->data['user_id'])->value('id');
        $record->date = $this->parseExcelDate($this->data['date']);

        if ($this->recordExists) {
            if (! blank($this->data['time_in'] ?? null)) {
                $record->time_in = $this->normalizeDateTime($record->date, $this->data['time_in']);
            }

            if (! blank($this->data['time_out'] ?? null)) {
                $record->time_out = $this->normalizeDateTime($record->date, $this->data['time_out']);
            }

            return;
        }

        $record->project_id = $this->data['project_id'] ?? $this->driver?->project_id;
        $record->end_user_id = $this->data['end_user_id'] ?? null;
        $record->unit_id = $this->data['unit_id'] ?? null;
        $record->start_km = $this->data['start_km'] ?? 0;
        $record->shift = $this->data['shift'] ?? null;
        $record->end_user_out = $this->data['end_user_out'] ?? null;

        foreach (['project_id', 'end_user_id', 'unit_id', 'start_km'] as $field) {
            if (blank($record->{$field})) {
                throw new RowImportFailedException("Kolom {$field} wajib diisi saat membuat data absensi baru.");
            }
        }

        if (blank($this->data['time_in'] ?? null)) {
            throw new RowImportFailedException('Kolom time_in wajib diisi saat membuat data absensi baru.');
        }

        $record->time_in = $this->normalizeDateTime($record->date, $this->data['time_in']);

        if (! blank($this->data['time_out'] ?? null)) {
            $record->time_out = $this->normalizeDateTime($record->date, $this->data['time_out']);
        }

    }

    protected function normalizeShift(string $shiftValue): ?string
    {
        $shiftString = trim(strtolower($shiftValue));

        if ($shiftString === '') {
            return null;
        }

        if (($shiftString)) {
            return $shiftString === 'h' ? 'holiday' : 'weekday';
        }

        $validShifts = ['holiday', 'weekday'];

        if (in_array($shiftString, $validShifts)) {
            return ucfirst($shiftString);
        }

        throw new RowImportFailedException('Nilai shift tidak valid. Contoh nilai yang valid: Holiday, Weekday.');
    }

    protected function normalizeDateTime(string $date, mixed $timeValue): string
    {
        $baseDate = Carbon::parse($date)->toDateString();
        $timeString = trim((string) $timeValue, " \t\n\r\0\x0B\"'");

        if ($timeString === '') {
            return Carbon::parse($baseDate)->startOfDay()->format('Y-m-d H:i:s');
        }

        if (Carbon::hasFormat($timeString, 'Y-m-d H:i:s') || Carbon::hasFormat($timeString, 'Y-m-d H:i')) {
            return Carbon::parse($timeString)->format('Y-m-d H:i:s');
        }

        $normalizedTime = preg_replace('/\s+/', ' ', strtoupper($timeString));
        $timeFormats = [
            'H:i:s',
            'H:i',
            'g:i:s A',
            'g:i A',
            'h:i:s A',
            'h:i A',
        ];

        foreach ($timeFormats as $format) {
            try {
                return Carbon::createFromFormat('Y-m-d ' . $format, $baseDate . ' ' . $normalizedTime)->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                // Try next format.
            }
        }

        throw new RowImportFailedException('Format waktu tidak valid. Gunakan 07:22, 07:22:00, atau 07:00:00 AM.');
    }

    protected function parseExcelDate(mixed $dateValue): string
    {
        $dateString = trim((string) $dateValue);

        if ($dateString === '') {
            throw new RowImportFailedException('Kolom date wajib diisi.');
        }

        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateString)->format('Y-m-d');
            } catch (\Throwable $e) {
                // Try next format.
            }
        }

        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new RowImportFailedException('Format tanggal tidak valid. Gunakan format dd/mm/yyyy, contoh 02/04/2026.');
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import absensi driver selesai. ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimport.';
        }

        return $body;
    }
}
