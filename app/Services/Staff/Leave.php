<?php

namespace App\Services\Staff;

use App\Models\StaffLeave;

class Leave
{
    public function __construct()
    {
        //
    }

    /**
     * get a staff's leaves balance
     *
     * @param integer $staffId
     * @return array{
     *      staff_id: int,
     *      year: string,
     *      annual: float,
     *      sick: float,
     *      personal: float,
     * }
     */
    public function getBalance(int $staffId): array
    {
        return StaffLeave::where('staff_id', $staffId)
                ->where('year', date('Y'))
                ->lockForUpdate()
                ->first()
                ->toArray();
    }

    /**
     * subtract the balance from applied hours
     *
     * @param integer $staffId
     * @param string $leaveType
     * @param float $applyHours
     * @return boolean
     */
    public function subtractBalance(int $staffId, string $leaveType, float $applyHours): bool
    {
        $affectedRows = StaffLeave::where('staff_id', $staffId)
                ->where('year', date('Y'))
                ->decrement($leaveType, $applyHours);
        return ($affectedRows === 1) ? true : false;
    }
}
