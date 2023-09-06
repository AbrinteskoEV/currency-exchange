<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client;

use GuzzleHttp\Client;
use Infrastructure\Client\BaseHttpClient;

class BaseHttpClientMock extends BaseHttpClient
{
    /**
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        parent::__construct($httpClient, '');
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $requestData
     *
     * @return array
     */
    public function sendRequest(string $method, string $endpoint, array $requestData = []): array
    {
        return [true];
    }
}
