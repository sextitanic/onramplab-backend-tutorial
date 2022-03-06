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
}
