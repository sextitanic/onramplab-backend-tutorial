<?php

namespace App\Repositories;

use App\Models\LeaveApplication;

class LeaveApplicationRepository
{
    protected $application;

    public function __construct(LeaveApplication $leaveApplication)
    {
        $this->application = $leaveApplication;
    }

    public function getList(int $status = null): array
    {
        $query = $this->application;

        if (is_null($status) === false) {
            $query = $query->where('status', $status);
        }

        return $query->get()->toArray();
    }

    public function findById(int $id): array
    {
        $query = $this->application
            ->with('staff')
            ->lockForUpdate()
            ->find($id);

        if (empty($query)) {
            return [];
        }

        return $query->toArray();
    }

    public function updateStatus(int $id, int $status, string $approverName): int
    {
        $affectedRows = $this->application
            ->where('id', $id)
            ->where('status', '<>', $status)
            ->update([
                'status' => $status,
                'approver' => $approverName,
                'approval_date' => date('Y-m-d H:i:s'),
            ]);
        return $affectedRows;
    }
}
