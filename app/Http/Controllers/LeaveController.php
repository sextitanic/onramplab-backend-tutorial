<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyLeaveRequest;
use App\Services\Leave\Application as LeaveApplication;
use App\Services\Staff\Leave as StaffLeave;

class LeaveController extends Controller
{
    /**
     * get the list of the leave application
     * 取得請假單列表
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request, LeaveApplication $leaveApplication)
    {
        try {
            $data = $leaveApplication->getFormList();
        } catch (\Exception $e) {
            return $this->apiResponse('500', $e->getMessage(), [], 500);
        }
        return $this->apiResponse('200', 'success', $data);
    }

    public function reject(VerifyLeaveRequest $request, int $id, LeaveApplication $leaveApplication)
    {
        try {
            // get leave application
            // 取得請假單
            $application = $leaveApplication->getFormById($id);
            if (empty($application)) {
                return $this->apiResponse('404', "application id {$id} not found", [], 404);
            }

            // if the status of the application is not 0: pending, it should return failed
            // 如果請假單的申請狀態不是 0: 待審核，那就應該回錯誤
            if ($leaveApplication->isPending($id) === false) {
                return $this->apiResponse('409', "application id {$id} is not pending.", [], 409);
            }

            // update the application status to 2: rejected
            // 把請假單狀態設為 2: 不通過
            $leaveApplication->reject($id, $request->input('approver'));
        } catch (\Throwable $e) {
            Log::error('reject leave error, ' . $e->getMessage(), $request->input());
            return $this->apiResponse('500', "Internal service error", [], 500);
        }

        return $this->apiResponse();
    }

    public function cancel(VerifyLeaveRequest $request, int $id, LeaveApplication $leaveApplication)
    {
        try {
            // get leave application
            // 取得請假單
            $application = $leaveApplication->getFormById($id);
            if (empty($application)) {
                return $this->apiResponse('404', "application id {$id} not found", [], 404);
            }

            // if the status of the application is not 0: pending, it should return failed
            // 如果請假單的申請狀態不是 0: 待審核，那就應該回錯誤
            if ($leaveApplication->isPending($id) === false) {
                return $this->apiResponse('409', "application id {$id} is not pending.", [], 409);
            }

            // update the application status to 3: canceled
            // 把請假單狀態設為 3: 取消
            $leaveApplication->cancel($id, $request->input('approver'));
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
    public function approve(
        VerifyLeaveRequest $request,
        int $id,
        LeaveApplication $leaveApplication,
        StaffLeave $staffLeave,
    ) {
        try {
            DB::beginTransaction();

            // get leave application
            // 取得請假單
            $application = $leaveApplication->getFormById($id);
            if (empty($application)) {
                return $this->apiResponse('404', "application id {$id} not found", [], 404);
            }

            // if the status of the application is not 0: pending, it should return failed
            // 如果請假單的申請狀態不是 0: 待審核，那就應該回錯誤
            if ($leaveApplication->isPending($id) === false) {
                return $this->apiResponse('409', "application id {$id} is not pending.", [], 409);
            }

            // only the staff who after the probation can take an annual leave and sick leave
            // 特休假和病假要通過試用期的員工才能請
            if ($application['leave_type'] === 'annual' || $application['leave_type'] === 'sick') {
                if ($application['staff']['is_probation'] === true) {
                    return $this->apiResponse('409-4', "staff {$application['staff']['name']} is in probation, could not take a annual leave", [], 409);
                }
            }

            // compare balance hours to apply hours, the balance hours should be greater than apply hours
            // 剩餘的請假時數應該要比申請的時數還多
            $leaveApplyHours = $application['leave_hours'];
            if ($staffLeave->isSufficient($application['staff']['id'], $application['leave_type'], $leaveApplyHours) === false) {
                return $this->apiResponse(
                    code: '409-5',
                    message: "staff {$application['staff']['name']} {$application['leave_type']} leaves is not enough",
                    statusCode: 409,
                );
            }

            // update the application status to 1: approved
            // 把請假單狀態設為 1: 已核准
            $setResult = $leaveApplication->approve($id, $request->input('approver'));
            if ($setResult === false) {
                return $this->apiResponse('409-6', "the status of application id {$id} has been changed", [], 409);
            }

            // subtract leaves
            // 把員工的休假時數扣掉
            $subtractResult = $staffLeave->subtractBalance($application['staff']['id'], $application['leave_type'], $leaveApplyHours);
            if ($subtractResult === false) {
                return $this->apiResponse('409-7', "staff {$application['staff']['name']} leaves hours has been changed", [], 409);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('approval leave error, ' . $e->getMessage(), $request->input());
            return $this->apiResponse('500', "Internal service error", [], 500);
        }

        try {
            // send a email to supervisor.
            // 寄信給主管

            // if the leave needs to pass probation
            // 如果這個假是需要過試用期才能夠請的
            if ($application['leave_type'] === 'annual' || $application['leave_type'] === 'sick') {
                // send a email to manager.
                // 還要寄信給管理者
            }
        } catch (\Throwable $e) {
            Log::error('approval leave error, ' . $e->getMessage(), $request->input());
            return $this->apiResponse('500', "Internal service error", [], 500);
        }

        return $this->apiResponse();
    }
}
