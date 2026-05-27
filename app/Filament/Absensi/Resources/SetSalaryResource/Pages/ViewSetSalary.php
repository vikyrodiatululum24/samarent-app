<?php

namespace App\Filament\Absensi\Resources\SetSalaryResource\Pages;

use App\Filament\Absensi\Resources\SetSalaryResource;
use App\Models\SetSalary;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewSetSalary extends ViewRecord
{
    protected static string $resource = SetSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Ringkasan Kebijakan')
                    ->schema([
                        Components\TextEntry::make('name')
                            ->label('Nama Policy')
                            ->icon('heroicon-m-document-text')
                            ->weight('bold'),
                        Components\TextEntry::make('policy_type')
                            ->label('Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'flat' => 'success',
                                'government' => 'info',
                                'custom' => 'warning',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('is_active')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                        Components\TextEntry::make('effective_date')
                            ->label('Effective Date')
                            ->icon('heroicon-m-calendar-days')
                            ->date('d M Y'),
                        Components\TextEntry::make('expired_date')
                            ->label('Expired Date')
                            ->icon('heroicon-m-calendar')
                            ->date('d M Y')
                            ->placeholder('No expiry'),
                    ])
                    ->columns(3),

                Section::make('Scope')
                    ->schema([
                        Components\TextEntry::make('project.name')
                            ->label('Project')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),
                        Components\TextEntry::make('branch.name')
                            ->label('Branch')
                            ->badge()
                            ->color('gray')
                            ->placeholder('-'),
                        Components\TextEntry::make('division.name')
                            ->label('Division')
                            ->badge()
                            ->color('gray')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Workdays')
                    ->schema([
                        Components\RepeatableEntry::make('rules.workdays')
                            ->label('')
                            ->schema([
                                Components\TextEntry::make('day')
                                    ->label('Day')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn (?string $state): string => ucfirst((string) $state)),
                                Components\TextEntry::make('hours')
                                    ->label('Work Hours')
                                    ->badge()
                                    ->color('success')
                                    ->formatStateUsing(fn ($state): string => ((string) $state) . ' jam'),
                            ])
                            ->columns(2)
                            ->contained(false)
                            ->placeholder('Belum ada workday setting'),
                    ])
                    ->collapsible(),

                Section::make('Break Rule')
                    ->schema([
                        Components\TextEntry::make('break_enabled')
                            ->label('Break Enabled')
                            ->badge()
                            ->state(fn (SetSalary $record): bool => (bool) data_get($record->rules, 'break_rule.enabled', false))
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                        Components\TextEntry::make('break_after_hours')
                            ->label('After Hours')
                            ->state(fn (SetSalary $record): mixed => data_get($record->rules, 'break_rule.after_hours'))
                            ->badge()
                            ->color('warning')
                            ->formatStateUsing(fn ($state): string => filled($state) ? ((string) $state) . ' jam' : '-'),
                        Components\TextEntry::make('break_deduct_hours')
                            ->label('Deduct Hours')
                            ->state(fn (SetSalary $record): mixed => data_get($record->rules, 'break_rule.deduct_hours'))
                            ->badge()
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => filled($state) ? ((string) $state) . ' jam' : '-'),
                    ])
                    ->columns(3),

                Section::make('Flat Rule')
                    ->visible(fn (SetSalary $record): bool => $record->policy_type === 'flat')
                    ->schema([
                        Components\TextEntry::make('flat_weekday_rate')
                            ->label('Weekday Rate')
                            ->state(fn (SetSalary $record): mixed => data_get($record->rules, 'overtime.weekday_rate'))
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) ($state ?? 0), 0, ',', '.')),
                        Components\TextEntry::make('flat_holiday_rate')
                            ->label('Holiday Rate')
                            ->state(fn (SetSalary $record): mixed => data_get($record->rules, 'overtime.holiday_rate'))
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) ($state ?? 0), 0, ',', '.')),
                    ])
                    ->columns(2),

                Section::make('Government Rule')
                    ->visible(fn (SetSalary $record): bool => $record->policy_type === 'government')
                    ->schema([
                        Components\RepeatableEntry::make('rules.overtime.weekday')
                            ->label('Weekday Multiplier')
                            ->schema([
                                Components\TextEntry::make('from')
                                    ->label('From Hour'),
                                Components\TextEntry::make('to')
                                    ->label('To Hour')
                                    ->placeholder('seterusnya'),
                                Components\TextEntry::make('multiplier')
                                    ->label('Multiplier')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn ($state): string => (string) $state . 'x'),
                            ])
                            ->columns(3)
                            ->contained(false)
                            ->placeholder('Belum ada rule weekday'),
                        Components\RepeatableEntry::make('rules.overtime.holiday')
                            ->label('Holiday Multiplier')
                            ->schema([
                                Components\TextEntry::make('from')
                                    ->label('From Hour'),
                                Components\TextEntry::make('to')
                                    ->label('To Hour')
                                    ->placeholder('seterusnya'),
                                Components\TextEntry::make('multiplier')
                                    ->label('Multiplier')
                                    ->badge()
                                    ->color('warning')
                                    ->formatStateUsing(fn ($state): string => (string) $state . 'x'),
                            ])
                            ->columns(3)
                            ->contained(false)
                            ->placeholder('Belum ada rule holiday'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Custom Rule')
                    ->visible(fn (SetSalary $record): bool => $record->policy_type === 'custom')
                    ->schema([
                        Components\TextEntry::make('custom_base_type')
                            ->label('Base Type')
                            ->state(fn (SetSalary $record): string => (string) data_get($record->rules, 'overtime.base_type', '-'))
                            ->badge()
                            ->color('warning')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Components\RepeatableEntry::make('rules.custom_rules')
                            ->label('Rule List')
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('Rule Name')
                                    ->badge()
                                    ->color('primary')
                                    ->weight('bold'),
                                Components\TextEntry::make('conditions')
                                    ->label('Conditions')
                                    ->formatStateUsing(function ($state): string {
                                        if (! is_array($state) || $state === []) {
                                            return '-';
                                        }

                                        return collect($state)
                                            ->map(function (array $condition, int $index): string {
                                                $field = (string) data_get($condition, 'field', '-');
                                                $operator = (string) data_get($condition, 'operator', '-');
                                                $value = data_get($condition, 'value', '-');

                                                return ($index + 1) . ". {$field} {$operator} {$value}";
                                            })
                                            ->implode("\n");
                                    })
                                    ->fontFamily('mono')
                                    ->copyable(),
                                Components\TextEntry::make('actions')
                                    ->label('Actions')
                                    ->formatStateUsing(function ($state): string {
                                        if (! is_array($state) || $state === []) {
                                            return '-';
                                        }

                                        return collect($state)
                                            ->map(function (array $action, int $index): string {
                                                $type = (string) data_get($action, 'type', '-');
                                                $value = data_get($action, 'value', '-');

                                                return ($index + 1) . ". {$type}: {$value}";
                                            })
                                            ->implode("\n");
                                    })
                                    ->fontFamily('mono')
                                    ->copyable(),
                            ])
                            ->columns(1)
                            ->contained(true)
                            ->columnSpanFull()
                            ->placeholder('Belum ada custom rule.'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
