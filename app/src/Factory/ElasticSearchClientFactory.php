<?php

namespace App\Factory;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchClientFactory
{
    private Client $client;

    public function __construct(string $elasticsearchHost, string $elasticsearchPort)
    {
        $this->client = ClientBuilder::create()
            ->setHosts(["$elasticsearchHost:$elasticsearchPort"])
            ->build();
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
