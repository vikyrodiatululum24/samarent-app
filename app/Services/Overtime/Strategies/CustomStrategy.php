<?php

namespace App\Services\Overtime\Strategies;

use App\Models\DriverAttendence;
use App\Models\SetSalary;
use App\Services\Overtime\Strategies\OvertimeStrategyInterface;

class CustomStrategy implements OvertimeStrategyInterface
{
    public function __construct(
        private readonly FlatStrategy $flatStrategy,
        private readonly GovernmentStrategy $governmentStrategy,
    ) {
    }

    public function calculate(DriverAttendence $attendance, SetSalary $policy, array $context): array
    {
        $baseType = data_get($policy->rules, 'overtime.base_type', 'flat');

        $result = $baseType === 'government'
            ? $this->governmentStrategy->calculate($attendance, $policy, $context)
            : $this->flatStrategy->calculate($attendance, $policy, $context);

        $customRules = data_get($policy->rules, 'custom_rules', []);
        $appliedRules = [];

        foreach ($customRules as $rule) {
            if (! $this->matchesConditions($rule['conditions'] ?? [], $context)) {
                continue;
            }

            foreach ($rule['actions'] ?? [] as $action) {
                $result = $this->applyAction($result, $action);
            }

            $appliedRules[] = $rule['name'] ?? 'custom_rule';
        }

        $result['calculation_detail']['strategy'] = 'custom';
        $result['calculation_detail']['base_type'] = $baseType;
        $result['calculation_detail']['applied_rules'] = $appliedRules;

        return $result;
    }

    private function matchesConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $expected = $condition['value'] ?? null;
            $actual = data_get($context, $field);

            if (! $this->compare($actual, $operator, $expected)) {
                return false;
            }
        }

        return true;
    }

    private function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '=' => $actual == $expected,
            '!=' => $actual != $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            'in' => in_array($actual, (array) $expected, true),
            default => false,
        };
    }

    private function applyAction(array $result, array $action): array
    {
        $type = $action['type'] ?? null;
        $value = $action['value'] ?? 0;
        $overtimeHours = (float) ($result['overtime_hours'] ?? 0);
        $rate = (float) ($result['rate'] ?? 0);

        return match ($type) {
            'set_rate' => $this->recalculate($result, (float) $value, $overtimeHours, 'set_rate'),
            'multiply_rate' => $this->recalculate($result, $rate * (float) $value, $overtimeHours, 'multiply_rate'),
            'deduct_hours' => $this->recalculate($result, $rate, max(0, $overtimeHours - (float) $value), 'deduct_hours'),
            'add_allowance' => $this->addAllowance($result, (float) $value),
            'round_overtime' => $this->roundOvertime($result, (int) $value),
            'set_overtime' => $this->recalculate($result, $rate, (float) $value, 'set_overtime'),
            default => $result,
        };
    }

    private function recalculate(array $result, float $rate, float $overtimeHours, string $action): array
    {
        $result['rate'] = $rate;
        $result['overtime_hours'] = $overtimeHours;
        $result['overtime_pay'] = round($rate * $overtimeHours, 2);
        $result['calculation_detail']['custom_action'] = $action;

        return $result;
    }

    private function addAllowance(array $result, float $value): array
    {
        $result['overtime_pay'] = round((float) ($result['overtime_pay'] ?? 0) + $value, 2);
        $result['calculation_detail']['custom_action'] = 'add_allowance';

        return $result;
    }

    private function roundOvertime(array $result, int $precision): array
    {
        $result['overtime_hours'] = round((float) ($result['overtime_hours'] ?? 0), $precision);
        $result['overtime_pay'] = round((float) ($result['rate'] ?? 0) * (float) $result['overtime_hours'], 2);
        $result['calculation_detail']['custom_action'] = 'round_overtime';

        return $result;
    }
}
