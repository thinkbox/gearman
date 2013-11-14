<?php

namespace Codecontrol\Gearman;

use Codecontrol\PHPUnitHelper\TestCase;

class GearmanWorkerAdapterTest extends TestCase
{
    const HOST = '::1';

    const PORT = '4731';

    public function setUp()
    {
        $this->worker = $this->createGearmanWorker();
        $this->adapter = new GearmanWorkerAdapter($this->worker);
    }

    /**
     * @test
     */
    public function checkInitialState()
    {
        $this->ensureServerIsAdded();

        new GearmanWorkerAdapter($this->worker, self::HOST, self::PORT);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function exceptionWillBeThrownForInvalidCallback()
    {
        $this->adapter->bind('not_callable', 'function_name');
    }

    /**
     * @test
     */
    public function callbackFunctionShouldBeAdded()
    {
        $functionName = 'function_name';

        $this->ensureFunctionIsAdded($functionName);

        $this->adapter->bind(function () {}, $functionName);
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function ifWorkerReturnNonSuccessStatusCodeExceptionWillBeThrown()
    {
        $this->mockWorkerWork();
        $this->mockWorkerNonSuccessErrorCode();
        $this->mockWorkerError();

        $this->adapter->bind(function () {}, 'function_name');
    }

    private function ensureFunctionIsAdded($functionName)
    {
        $this
            ->worker
            ->expects($this->once())
            ->method('addFunction')
            ->with($functionName);
    }

    private function mockWorkerError()
    {
        $this
            ->worker
            ->expects($this->once())
            ->method('error')
            ->will($this->returnValue('An error occured'));
    }

    private function mockWorkerNonSuccessErrorCode()
    {
        $this
            ->worker
            ->expects($this->once())
            ->method('returnCode')
            ->will($this->returnValue(GEARMAN_SUCCESS + 100));
    }

    private function mockWorkerWork()
    {
        $this
            ->worker
            ->expects($this->once())
            ->method('work')
            ->will($this->returnValue(true));
    }

    private function ensureServerIsAdded()
    {
        $this
            ->worker
            ->expects($this->once())
            ->method('addServer')
            ->with(self::HOST, self::PORT);
    }

    private function createGearmanWorker()
    {
        return $this->createMockFor('\GearmanWorker');
    }
}
