<?php

namespace App\Infrastructure\Nats;


use Illuminate\Support\Facades\Log;
use Nats\Connection;
use Nats\ConnectionOptions;

class Nats
{
    /** @var Connection */
    protected $client;

    public function __construct(string $host, string $port)
    {
        $options = new ConnectionOptions([
            'host' => $host,
            'port' => $port
        ]);

        $client = new Connection($options);
        $client->connect(-1);
        $this->client = $client;
        Log::info(sprintf('Connected to Nats %s:%s', $host, $port));
    }

    public function getClient(): Connection
    {
        return $this->client;
    }
}
