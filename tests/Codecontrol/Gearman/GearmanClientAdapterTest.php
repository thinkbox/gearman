<?php

namespace Codecontrol\Gearman;

use Codecontrol\PHPUnitHelper\TestCase;

class GearmanClientAdapterTest extends TestCase
{
    const HOST = '::1';

    const PORT = '4731';

    public function setUp()
    {
        $this->client = $this->createGearmanClient();
    }

    /**
     * @test
     */
    public function checkInitialState()
    {
        $this->ensureServerIsAdded();

        new GearmanClientAdapter($this->client, self::HOST, self::PORT);
    }

    /**
     * @test
     */
    public function jobCanBeDoneInBackground()
    {
        $functionName = 'test_function';
        $data = array('foo' => 'bar');

        $this->mockJobInBackground($functionName, $data);

        $adapter = new GearmanClientAdapter($this->client);
        $adapter->doBackground($functionName, $data);
    }

    private function mockJobInBackground($functionName, $data)
    {
        $this
            ->client
            ->expects($this->once())
            ->method('doBackground')
            ->with($functionName, serialize($data));
    }

    private function ensureServerIsAdded()
    {
        $this
            ->client
            ->expects($this->once())
            ->method('addServer')
            ->with(self::HOST, self::PORT);
    }

    private function createGearmanClient()
    {
        return $this->createMockFor('\GearmanClient');
    }
}
