<?php

namespace App\Services\Staff;

use App\Models\StaffLeave;
use App\Repositories\StaffLeaveRepository;

class Leave
{
    protected $leaveRepo;

    public function __construct(StaffLeaveRepository $staffLeaveRepository)
    {
        $this->leaveRepo = $staffLeaveRepository;
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
        return $this->leaveRepo->getBalance($staffId);
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
        $affectedRows = $this->leaveRepo->incrementBalance($staffId, $leaveType, -$applyHours);
        return ($affectedRows === 1) ? true : false;
    }
}
