<?php

namespace Codecontrol\Gearman;

use \GearmanJob as GearmanJob;
use \GearmanWorker as GearmanWorker;

class GearmanWorkerAdapter
{
    const HOST = '127.0.0.1';

    const PORT = '4730';

    /**
     * @var GearmanWorker
     */
    private $worker;

    /**
     * @param GearmanWorker $worker
     * @param string        $host
     * @param string        $port
     */
    public function __construct(
        GearmanWorker $worker,
        $host = self::HOST,
        $port = self::PORT
    ) {
        $worker->addServer($host, $port);

        $this->worker = $worker;
    }

    /**
     * @param callable $callback
     * @param string   $functionName
     *
     * @throws \InvalidArgumentException
     */
    public function bind($callback, $functionName)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callback provided');
        }

        $this->worker->addFunction(
            $functionName,
            function (GearmanJob $job) use ($callback) {
                $callback(unserialize($job->workload()));
            }
        );

        $this->startWorker();
    }

    /**
     * @throws \RuntimeException
     */
    private function startWorker()
    {
        while ($this->worker->work()) {
            if (GEARMAN_SUCCESS != $this->worker->returnCode()) {
                throw new \RuntimeException($this->worker->error());
            }
        }
    }
}
