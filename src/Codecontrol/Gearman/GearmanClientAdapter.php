<?php

namespace Codecontrol\Gearman;

use \GearmanClient as GearmanClient;

class GearmanClientAdapter
{
    const HOST = '127.0.0.1';

    const PORT = '4730';

    /**
     * @var GearmanClient
     */
    private $client;

    /**
     * @param GearmanClient $client
     * @param string        $host
     * @param string        $port
     */
    public function __construct(
        GearmanClient $client,
        $host = self::HOST,
        $port = self::PORT
    ) {
        $client->addServer($host, $port);

        $this->client = $client;
    }

    /**
     * @param string $functionName
     * @param array  $data
     */
    public function doBackground($functionName, array $data = array())
    {
        $this->client->doBackground($functionName, serialize($data));
    }
}
