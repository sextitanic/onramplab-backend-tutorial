<?php

namespace Tests\Unit\Services\Leave;

use App\Repositories\LeaveApplicationRepository;
use App\Services\Leave\Application;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    protected $repoMock;
    protected $application;
    protected $status = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->repoMock = Mockery::mock(LeaveApplicationRepository::class);
        $this->instance(LeaveApplicationRepository::class, $this->repoMock);
        $this->status = config('constants.leave-application.status');

        $this->application = app(Application::class);
    }

    /**
     * test update the status of the application form
     * it will return true if a record changed
     * and return false if no record changed
     *
     * @return void
     */
    public function testSetStatus()
    {
        // test the affected rows is 1
        $this->repoMock
            ->shouldReceive('updateStatus')
            ->once()
            ->andReturn(1);
        $updateAffectedOneRow = $this->application->setStatus(1, $this->status['approved'], 'test');
        $this->assertTrue($updateAffectedOneRow);

        // test the affected rows is 0
        $this->repoMock
            ->shouldReceive('updateStatus')
            ->once()
            ->andReturn(0);
        $updateNoAffectedRows = $this->application->setStatus(1, $this->status['approved'], 'test');
        $this->assertFalse($updateNoAffectedRows);
    }

    /**
     * test the approve method is worked.
     *
     * @return void
     */
    public function testApproveStatus()
    {
        $this->partialMockSetStatus(1);
        $approved = app(Application::class)->approve(1, 'test');
        $this->assertTrue($approved);
    }

    /**
     * test the reject method is worked.
     *
     * @return void
     */
    public function testRejectStatus()
    {
        $this->partialMockSetStatus(2);
        $rejected = app(Application::class)->reject(1, 'test');
        $this->assertTrue($rejected);
    }

    /**
     * test the cancel method is worked.
     *
     * @return void
     */
    public function testCancelStatus()
    {
        $this->partialMockSetStatus(3);
        $rejected = app(Application::class)->cancel(1, 'test');
        $this->assertTrue($rejected);
    }

    /**
     * partial mock the setStatus method of the Application class,
     * and validate if the status code matches.
     *
     * @param integer $validStatus
     * @return void
     */
    protected function partialMockSetStatus(int $validStatus)
    {
        $this->partialMock(Application::class, function (MockInterface $callMock) use ($validStatus) {
            $callMock
                ->shouldReceive('setStatus')
                ->andReturnUsing(function (int $id, int $status, string $approverName) use ($validStatus) {
                    if ($status !== $validStatus) {
                        return false;
                    }
                    return true;
                });
        });
    }

    /**
     * test all status of the application form is pending or not.
     *
     * @dataProvider checkIsPendingDataProvider
     */
    public function testIsPending(int $id, bool $expected)
    {
        $this->partialMockGetFormById();
        $actual = app(Application::class)->isPending($id);
        $this->assertEquals($expected, $actual);
    }

    /**
     * testing data set
     */
    public function checkIsPendingDataProvider()
    {
        return [
            [1, true],
            [2, false],
            [3, false],
            [4, false],
        ];
    }

    /**
     * partial mock the getFormById method of the Application class.
     *
     * @return void
     */
    protected function partialMockGetFormById()
    {
        $testingData = [
            1 => ['id' => 1, 'status' => $this->status['pending']],
            2 => ['id' => 2, 'status' => $this->status['approved']],
            3 => ['id' => 3, 'status' => $this->status['rejected']],
            4 => ['id' => 4, 'status' => $this->status['canceled']],
        ];

        $this->partialMock(Application::class, function (MockInterface $callMock) use ($testingData) {
            $callMock
                ->shouldReceive('getFormById')
                ->andReturnUsing(function (int $id) use ($testingData) {
                    return $testingData[$id];
                });
        });
    }
}
