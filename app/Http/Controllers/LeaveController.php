<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyLeaveRequest;
use App\Models\StaffLeave;
use App\Models\LeaveApplication;

class LeaveController extends Controller
{
    private const APPLICATION_PENDING = 0;
    private const APPLICATION_APPROVED = 1;
    private const APPLICATION_REJECTED = 2;
    private const APPLICATION_CANCELED = 3;

    /**
     * get the list of the leave application
     * 取得請假單列表
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $data = LeaveApplication::get()->toArray();
        } catch (\Exception $e) {
            return $this->apiResponse('500', $e->getMessage(), [], 500);
        }
        return $this->apiResponse('200', 'success', $data);
    }

    public function reject(VerifyLeaveRequest $request, int $id)
    {
        try {
            // get leave application
            // 取得請假單
            $application = LeaveApplication::with('staff')->find($id);
            if (empty($application)) {
                return $this->apiResponse('404', "application id {$id} not found", [], 404);
            }

            // if the status of the application is not 0: pending, it should return failed
            // 如果請假單的申請狀態不是 0: 待審核，那就應該回錯誤
            switch ($application['status']) {
                case self::APPLICATION_APPROVED:
                    return $this->apiResponse('409-1', "application id {$id} has been approved", [], 409);
                case self::APPLICATION_REJECTED:
                    return $this->apiResponse('409-2', "application id {$id} has been rejected", [], 409);
                case self::APPLICATION_CANCELED:
                    return $this->apiResponse('409-3', "application id {$id} has been canceled", [], 409);
            }

            // update the application status to 2: rejected
            // 把請假單狀態設為 2: 不通過
            LeaveApplication::where('id', $id)
                ->update([
                    'status' => self::APPLICATION_REJECTED,
                    'approver' => $request->input('approver'),
                    'approval_date' => date('Y-m-d H:i:s'),
                ]);
        } catch (\Throwable $e) {
            Log::error('reject leave error, ' . $e->getMessage(), $request->input());
            return $this->apiResponse('500', "Internal service error", [], 500);
        }

        return $this->apiResponse();
    }

    public function cancel(VerifyLeaveRequest $request, int $id)
    {
        try {
            // get leave application
            // 取得請假單
            $application = LeaveApplication::with('staff')->find($id);
            if (empty($application)) {
                return $this->apiResponse('404', "application id {$id} not found", [], 404);
            }

            // if the status of the application is not 0: pending, it should return failed
            // 如果請假單的申請狀態不是 0: 待審核，那就應該回錯誤
            switch ($application['status']) {
                case self::APPLICATION_APPROVED:
                    return $this->apiResponse('409-1', "application id {$id} has been approved", [], 409);
                case self::APPLICATION_REJECTED:
                    return $this->apiResponse('409-2', "application id {$id} has been rejected", [], 409);
                case self::APPLICATION_CANCELED:
                    return $this->apiResponse('409-3', "application id {$id} has been canceled", [], 409);
            }

            // update the application status to 3: canceled
            // 把請假單狀態設為 3: 取消
            LeaveApplication::where('id', $id)
                ->update([
                    'status' => self::APPLICATION_CANCELED,
                    'approver' => $request->input('approver'),
                    'approval_date' => date('Y-m-d H:i:s'),
                ]);
        } catch (\Throwable $e) {
            Log::error('cancel leave error, ' . $e->getMessage(), $request->input());
            return $this->apiResponse('500', "Internal service error", [], 500);
        }

        return $this->apiResponse();
    }

    protected function apiResponse(string $code = '200', string $message = 'success', array $data = [], int $statusCode = 200)
    {
        return response()->json(
            [
                'code' => (string) $code,
                'success' => $statusCode === 200 ? true : false,
                'message' => $message,
                'data' => $data,
            ],
            $statusCode
        );
    }

    /**
     * approval a leave application
     * 批准通過請假單
     *
     * @param Request $request
     * @param integer $id
     * @return json
     */
    public function approve(VerifyLeaveRequest $request, int $id)
    {
        try {
            DB::beginTransaction();

            // get leave application
            // 取得請假單
            $application = LeaveApplication::with('staff')->lockForUpdate()->find($id);
            if (empty($application)) {
                return $this->apiResponse('404', "application id {$id} not found", [], 404);
            }

            // if the status of the application is not 0: pending, it should return failed
            // 如果請假單的申請狀態不是 0: 待審核，那就應該回錯誤
            switch ($application['status']) {
                case self::APPLICATION_APPROVED:
                    return $this->apiResponse('409-1', "application id {$id} has been approved", [], 409);
                case self::APPLICATION_REJECTED:
                    return $this->apiResponse('409-2', "application id {$id} has been rejected", [], 409);
                case self::APPLICATION_CANCELED:
                    return $this->apiResponse('409-3', "application id {$id} has been rejected", [], 409);
            }

            // only the staff who after the probation can take an annual leave
            // 特休假要通過試用期的員工才能請
            if ($application['leave_type'] === 'annual') {
                if ($application['staff']['is_probation'] === true) {
                    return $this->apiResponse('409-4', "staff {$application['staff']['name']} is in probation, could not take a annual leave", [], 409);
                }
            }

            // get the staff's leave balance hours
            // 取得員工的剩餘請假時數
            $staffLeaves = StaffLeave::where('staff_id', $application['staff']['id'])
                ->where('year', date('Y'))
                ->lockForUpdate()
                ->first();

            // compare balance hours to apply hours, the balance hours should be greater than apply hours
            // 剩餘的請假時數應該要比申請的時數還多
            $leaveApplyHours = $application['leave_hours'];
            $leaveBalanceHours = $staffLeaves[$application['leave_type']];
            if ($leaveBalanceHours < $leaveApplyHours) {
                return $this->apiResponse('409-5', "staff {$application['staff']['name']} {$application['leave_type']} leaves is not enought", [], 409);
            }

            // update the application status to 1: approved
            // 把請假單狀態設為 1: 已核准
            $affectedRows = LeaveApplication::where('id', $id)
                ->where('status', self::APPLICATION_PENDING)
                ->update([
                    'status' => self::APPLICATION_APPROVED,
                    'approver' => $request->input('approver'),
                    'approval_date' => date('Y-m-d H:i:s'),
                ]);

            // if the affected rows is 0, which means the conditions in the where clause are not sufficient
            // 如果 update 語法影響的列數是 0，代表在 where 裡面有一些判斷是不符合的
            if ($affectedRows === 0) {
                return $this->apiResponse('409-6', "the status of application id {$id} has been changed", [], 409);
            }

            // subtract leaves
            // 把員工的休假時數扣掉
            $affectedRows = StaffLeave::where('staff_id', $application['staff']['id'])
                ->where('year', date('Y'))
                // ->where($application['leave_type'], $leaveBalanceHours)
                ->decrement($application['leave_type'], $leaveApplyHours);
            if ($affectedRows === 0) {
                return $this->apiResponse('409-7', "staff {$application['staff']['name']} leaves hours has been changed", [], 409);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('approval leave error, ' . $e->getMessage(), $request->input());
            return $this->apiResponse('500', "Internal service error", [], 500);
        }

        return $this->apiResponse();
    }
}
