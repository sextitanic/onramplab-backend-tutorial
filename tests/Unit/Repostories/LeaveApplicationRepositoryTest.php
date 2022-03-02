<?php

namespace Tests\Unit\Repostories;

use App\Models\LeaveApplication;
use App\Repositories\LeaveApplicationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveApplicationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repo;
    protected $status = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = app(LeaveApplicationRepository::class);
        $this->status = config('constants.leave-application.status');
    }

    /**
     * at beginning, the table is empty
     *
     * @return void
     */
    public function testGetListRowsCount()
    {
        // the data of rows should be 0
        // 應該只有 0 筆資料
        $list = $this->repo->getList();
        $this->assertCount(0, $list);

        // insert one record into table
        // 新增一筆資料進資料表
        LeaveApplication::factory()->create(['staff_id' => 1]);

        // the data of rows should be 1
        // 應該會有 1 筆資料
        $list = $this->repo->getList();
        $this->assertCount(1, $list);
    }

    /**
     * update the status with a different status
     * e.g. from pending to approved
     *
     * @return void
     */
    public function testUpdateWithDifferentStatus()
    {
        // insert one record into table
        // 新增一筆請假單進資料表
        $pendingApplication = LeaveApplication::factory()->create(['staff_id' => 1]);
        $affectedRows = $this->repo->updateStatus($pendingApplication->id, $this->status['approved'], 'test');
        $this->assertEquals(1, $affectedRows);

        // make sure the status of the application form is approved
        // 確認請假單的狀態是 approved
        $approved = LeaveApplication::find($pendingApplication->id);
        $this->assertEquals($this->status['approved'], $approved['status']);

        // insert one record into table
        // 新增一筆請假單進資料表
        $pendingApplication = LeaveApplication::factory()->create(['staff_id' => 1]);
        $affectedRows = $this->repo->updateStatus($pendingApplication->id, $this->status['rejected'], 'test');
        $this->assertEquals(1, $affectedRows);

        // make sure the status of the application form is rejected
        // 確認請假單的狀態是 rejected
        $rejected = LeaveApplication::find($pendingApplication->id);
        $this->assertEquals($this->status['rejected'], $rejected['status']);
    }

    /**
     * update the status with the same status
     * e.g. from approved to approved
     *
     * @return void
     */
    public function testUpdateWithSameStatus()
    {
        // insert an approved application form
        // 新增一筆已經批准的請假單進資料表
        $approvedApplication = LeaveApplication::factory()
            ->create([
                'staff_id' => 1,
                'status' => $this->status['approved'],
            ]);
        $affectedRows = $this->repo->updateStatus($approvedApplication->id, $this->status['approved'], 'test');
        $this->assertEquals(0, $affectedRows);

        // insert a rejected application form
        // 新增一筆已經被拒絕的請假單進資料表
        $approvedApplication = LeaveApplication::factory()
            ->create([
                'staff_id' => 1,
                'status' => $this->status['rejected'],
            ]);
        $affectedRows = $this->repo->updateStatus($approvedApplication->id, $this->status['rejected'], 'test');
        $this->assertEquals(0, $affectedRows);
    }
}
