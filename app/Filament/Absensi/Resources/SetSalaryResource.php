<?php

namespace App\Filament\Absensi\Resources;

use App\Enums\OvertimePolicyType;
use App\Filament\Absensi\Resources\SetSalaryResource\Pages\CreateSetSalary;
use App\Filament\Absensi\Resources\SetSalaryResource\Pages\EditSetSalary;
use App\Filament\Absensi\Resources\SetSalaryResource\Pages\ListSetSalaries;
use App\Filament\Absensi\Resources\SetSalaryResource\Pages\ViewSetSalary;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Project;
use App\Models\SetSalary;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SetSalaryResource extends Resource
{
    protected static ?string $model = SetSalary::class;

    protected static ?string $label = 'Set Salary';

    protected static ?string $pluralLabel = 'Set Salaries';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data Overtime';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Overtime Policy')
                ->tabs([
                    Tab::make('General')
                        ->schema([
                            Section::make('Scope')
                                ->schema([
                                    Forms\Components\Select::make('project_id')
                                        ->label('Project')
                                        ->options(function () {
                                            $query = Project::query()->orderBy('name');

                                            $user = Auth::user();
                                            $isSuperUser = $user?->email === 'centralakun@samarent.com';
                                            $ownedProjectName = $user?->manager?->perusahaan;

                                            if (! $isSuperUser && filled($ownedProjectName)) {
                                                $query->where('name', $ownedProjectName);
                                            }

                                            return $query->pluck('name', 'id');
                                        })
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('branch_id', null);
                                            $set('division_id', null);
                                        }),
                                    Forms\Components\Select::make('branch_id')
                                        ->label('Branch')
                                        ->options(fn(Get $get) => Branch::query()
                                            ->when($get('project_id'), fn(Builder $query, $projectId) => $query->where('project_id', $projectId))
                                            ->orderBy('name')
                                            ->pluck('name', 'id'))
                                        ->searchable()
                                        ->disabled(fn(Get $get) => blank($get('project_id')))
                                        ->live()
                                        ->afterStateUpdated(fn(Set $set) => $set('division_id', null)),
                                    Forms\Components\Select::make('division_id')
                                        ->label('Division')
                                        ->options(fn(Get $get) => Division::query()
                                            ->when($get('project_id'), fn(Builder $query, $projectId) => $query->where('project_id', $projectId))
                                            ->when($get('branch_id'), fn(Builder $query, $branchId) => $query->where('branch_id', $branchId))
                                            ->orderBy('name')
                                            ->pluck('name', 'id'))
                                        ->searchable()
                                        ->disabled(fn(Get $get) => blank($get('branch_id'))),
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Select::make('policy_type')
                                        ->required()
                                        ->options([
                                            OvertimePolicyType::Flat->value => OvertimePolicyType::Flat->label(),
                                            OvertimePolicyType::Government->value => OvertimePolicyType::Government->label(),
                                            // OvertimePolicyType::Custom->value => OvertimePolicyType::Custom->label(),
                                        ])
                                        ->live(),
                                    Forms\Components\Toggle::make('is_active')
                                        ->default(true),
                                    Forms\Components\DatePicker::make('effective_date')
                                        ->required(),
                                    Forms\Components\DatePicker::make('expired_date'),
                                ])
                                ->columns(2),
                        ]),
                    Tab::make('Workdays')
                        ->schema([
                            Forms\Components\Repeater::make('rules.workdays')
                                ->schema([
                                    Forms\Components\Select::make('day')
                                        ->options([
                                            'monday' => 'Monday',
                                            'tuesday' => 'Tuesday',
                                            'wednesday' => 'Wednesday',
                                            'thursday' => 'Thursday',
                                            'friday' => 'Friday',
                                            'saturday' => 'Saturday',
                                            'sunday' => 'Sunday',
                                        ])
                                        ->required(),
                                    Forms\Components\TextInput::make('hours')
                                        ->inputMode('numeric')
                                        ->rules(['regex:/^\d+(\.\d{1,2})?$/'])
                                        ->validationMessages(['regex' => 'The hours field must be a valid number with up to 2 decimal places.'])
                                        ->required()
                                        ->default(8),
                                ])
                                ->columns(2)
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Break Rule')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Toggle::make('rules.break_rule.enabled')->default(true),
                                Forms\Components\TextInput::make('rules.break_rule.after_hours')->inputMode('numeric')
                                    ->rules(['regex:/^\d+(\.\d{1,2})?$/'])
                                    ->validationMessages(['regex' => 'The hours field must be a valid number with up to 2 decimal places.'])->default(5),
                                Forms\Components\TextInput::make('rules.break_rule.deduct_hours')->inputMode('numeric')
                                    ->rules(['regex:/^\d+(\.\d{1,2})?$/'])
                                    ->validationMessages(['regex' => 'The hours field must be a valid number with up to 2 decimal places.'])->default(1),
                            ]),
                        ]),
                    Tab::make('Flat Rule')
                        ->visible(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Flat->value)
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('rules.overtime.weekday_rate')
                                    ->inputMode('numeric')
                                    ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Flat->value)
                                    ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                    ->validationMessages([
                                        'regex' => 'Nominal Estimasi harus berupa angka.',
                                    ])
                                    ->prefix('Rp ')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->default(0),
                                Forms\Components\TextInput::make('rules.overtime.holiday_rate')
                                    ->inputMode('numeric')
                                    ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                    ->validationMessages(['regex' => 'The rate field must be a valid number.'])
                                    ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Flat->value)
                                    ->prefix('Rp ')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->default(0),
                            ]),
                        ]),
                    Tab::make('Government Rule')
                        ->visible(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Government->value)
                        ->schema([
                            Forms\Components\TextInput::make('rules.overtime.hourly_salary')
                                ->inputMode('numeric')
                                ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                ->validationMessages(['regex' => 'The hourly salary field must be a valid number.'])
                                ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Government->value)
                                ->prefix('Rp ')
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->default(0),
                            Forms\Components\Repeater::make('rules.overtime.weekday')
                                ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Government->value)
                                ->minItems(1)
                                ->schema([
                                    Forms\Components\TextInput::make('from')->inputMode('numeric')
                                        ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                        ->validationMessages(['regex' => 'The from field must be a valid number.'])
                                        ->required()->default(1),
                                    Forms\Components\TextInput::make('to')->inputMode('numeric')
                                        ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                        ->validationMessages(['regex' => 'The to field must be a valid number.'])
                                        ->stripCharacters('.'),
                                    Forms\Components\TextInput::make('multiplier')->inputMode('numeric')
                                        ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                        ->validationMessages(['regex' => 'The multiplier field must be a valid number.'])
                                        ->required()
                                        ->default(1.5),
                                ])
                                ->columns(3)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('rules.overtime.holiday')
                                ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Government->value)
                                ->minItems(1)
                                ->schema([
                                    Forms\Components\TextInput::make('from')->inputMode('numeric')
                                        ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                        ->validationMessages(['regex' => 'The from field must be a valid number.'])
                                        ->required()
                                        ->default(1),
                                    Forms\Components\TextInput::make('to')->inputMode('numeric')
                                        ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                        ->validationMessages(['regex' => 'The to field must be a valid number.']),
                                    Forms\Components\TextInput::make('multiplier')->inputMode('numeric')
                                        ->rules(['regex:/^[0-9]+(\.[0-9]+)?$/'])
                                        ->validationMessages(['regex' => 'The multiplier field must be a valid number.'])
                                        ->required()->default(2),
                                ])
                                ->columns(3)
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Custom Rule')
                        ->visible(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Custom->value)
                        ->schema([
                            Forms\Components\Select::make('rules.overtime.base_type')
                                ->options([
                                    'flat' => 'Flat',
                                    'government' => 'Government',
                                ])
                                ->default('flat')
                                ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Custom->value),
                            Forms\Components\Repeater::make('rules.custom_rules')
                                ->required(fn(Get $get): bool => $get('policy_type') === OvertimePolicyType::Custom->value)
                                ->minItems(1)
                                ->schema([
                                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                    Forms\Components\Repeater::make('conditions')
                                        ->schema([
                                            Forms\Components\Select::make('field')
                                                ->options([
                                                    'day' => 'Day',
                                                    'worked_hours' => 'Worked Hours',
                                                    'overtime_hours' => 'Overtime Hours',
                                                    'clock_in' => 'Clock In',
                                                    'clock_out' => 'Clock Out',
                                                    'is_holiday' => 'Is Holiday',
                                                ])
                                                ->required(),
                                            Forms\Components\Select::make('operator')
                                                ->options([
                                                    '=' => '=',
                                                    '!=' => '!=',
                                                    '>' => '>',
                                                    '<' => '<',
                                                    '>=' => '>=',
                                                    '<=' => '<=',
                                                    'in' => 'in',
                                                ])
                                                ->required(),
                                            Forms\Components\TextInput::make('value')->required(),
                                        ])
                                        ->columns(3)
                                        ->columnSpanFull(),
                                    Forms\Components\Repeater::make('actions')
                                        ->schema([
                                            Forms\Components\Select::make('type')
                                                ->options([
                                                    'set_rate' => 'Set Rate',
                                                    'multiply_rate' => 'Multiply Rate',
                                                    'deduct_hours' => 'Deduct Hours',
                                                    'add_allowance' => 'Add Allowance',
                                                    'round_overtime' => 'Round Overtime',
                                                    'set_overtime' => 'Set Overtime',
                                                ])
                                                ->required(),
                                            Forms\Components\TextInput::make('value')->required(),
                                        ])
                                        ->columns(2)
                                        ->columnSpanFull(),
                                ])
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('division.name')
                    ->label('Division')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('policy_type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return Filament::getCurrentPanel()?->getId() === 'absensi' ? static::$navigationGroup : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSetSalaries::route('/'),
            'create' => CreateSetSalary::route('/create'),
            'view' => ViewSetSalary::route('/{record}'),
            'edit' => EditSetSalary::route('/{record}/edit'),
        ];
    }
}
