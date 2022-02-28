<?php

namespace App\Services\Leave;

use App\Repositories\LeaveApplicationRepository;

class Application
{
    protected $applicationRepo;

    public function __construct(LeaveApplicationRepository $applicationRepository)
    {
        $this->applicationRepo = $applicationRepository;
    }

    /**
     * get leave application form list
     *
     * @return array<int, array{
     *      id: int,
     *      staff_id: string,
     *      application_date: date, // format YYYY-MM-DD
     *      leave_type: string,
     *      start_leave_date: date, // format YYYY-MM-DD
     *      end_leave_date: date, // format YYYY-MM-DD
     *      leave_hours: float,
     *      status: int,
     *      approver: string, // approver's name
     *      approval_date: date, // format YYYY-MM-DD
     * }>
     */
    public function getFormList(): array
    {
        return $this->applicationRepo->getList();
    }

    /**
     * get a leave application form by id
     *
     * @param integer $id
     * @return array{
     *      id: int,
     *      staff_id: string,
     *      application_date: date, // format YYYY-MM-DD
     *      leave_type: string,
     *      start_leave_date: date, // format YYYY-MM-DD
     *      end_leave_date: date, // format YYYY-MM-DD
     *      leave_hours: float,
     *      status: int,
     *      approver: string, // approver's name
     *      approval_date: date, // format YYYY-MM-DD
     *      staff: array{
     *          id: int,
     *          name: string,
     *          department_id: int,
     *          is_probation: boolean,
     *      }
     * }
     */
    public function getFormById(int $id): array
    {
        return $this->applicationRepo->findById($id);
    }

    /**
     * set the status of a leave application form
     *
     * @param integer $id
     * @param integer $status
     * @param string $approverName
     * @return boolean
     */
    public function setStatus(int $id, int $status, string $approverName): bool
    {
        // if the affected rows is 0, which means the conditions in the where clause are not sufficient
        // 如果 update 語法影響的列數是 0，代表在 where 裡面有一些判斷是不符合的
        $affectedRows = $this->applicationRepo->updateStatus($id, $status, $approverName);
        return ($affectedRows === 1) ? true : false;
    }
}
