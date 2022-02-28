<?php

namespace App\Repositories;

use App\Models\StaffLeave;

class StaffLeaveRepository
{
    protected $leave;

    public function __construct(StaffLeave $staffLeave)
    {
        $this->leave = $staffLeave;
    }

    public function getBalance(int $staffId): array
    {
        $query = $this->leave->where('staff_id', $staffId)
                ->where('year', date('Y'))
                ->lockForUpdate()
                ->first();

        if (empty($query)) {
            return [];
        }

        return $query->toArray();
    }

    public function incrementBalance(int $staffId, string $leaveType, float $hours): int
    {
        return $this->leave->where('staff_id', $staffId)
                ->where('year', date('Y'))
                ->increment($leaveType, $hours);
    }
}
