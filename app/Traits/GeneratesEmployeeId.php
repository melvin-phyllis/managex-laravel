<?php

namespace App\Traits;

use App\Models\User;

trait GeneratesEmployeeId
{
    protected function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $year = date('Y');
        $prefixWithYear = $prefix.$year;
        $prefixLen = strlen($prefixWithYear);

        $lastEmployee = User::where('employee_id', 'like', "{$prefixWithYear}%")
            ->orderByRaw('CAST(SUBSTRING(employee_id, '.($prefixLen + 1).') AS UNSIGNED) DESC')
            ->first();

        if ($lastEmployee) {
            $lastNumber = intval(substr($lastEmployee->employee_id, $prefixLen));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $employeeId = $prefixWithYear.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        while (User::where('employee_id', $employeeId)->exists()) {
            $nextNumber++;
            $employeeId = $prefixWithYear.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $employeeId;
    }
}
