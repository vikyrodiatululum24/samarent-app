<?php

namespace App\Services\Overtime\Strategies;

use App\Models\DriverAttendence;
use App\Models\SetSalary;

interface OvertimeStrategyInterface
{
    public function calculate(DriverAttendence $attendance, SetSalary $policy, array $context): array;
}
