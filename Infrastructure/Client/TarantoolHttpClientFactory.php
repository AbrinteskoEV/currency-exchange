<?php

declare(strict_types=1);

namespace Infrastructure\Client;

use Domain\Dictionary\Config\FileConfigNameDictionary;
use GuzzleHttp\Client;
use Infrastructure\Service\Config\FileConfigGettingService;

class TarantoolHttpClientFactory
{
    private Client $client;
    private FileConfigGettingService $fileConfigGettingService;

    /**
     * @param Client $client
     * @param FileConfigGettingService $fileConfigGettingService
     */
    public function __construct(Client $client, FileConfigGettingService $fileConfigGettingService)
    {
        $this->client = $client;
        $this->fileConfigGettingService = $fileConfigGettingService;
    }

    /**
     * @return BaseHttpClient
     */
    public function create(): BaseHttpClient
    {
        $tarantoolApiConfig = $this->fileConfigGettingService
            ->getConfig(FileConfigNameDictionary::TARANTOOL_API_CONFIG);
        $tarantoolApiUrl = $tarantoolApiConfig['apiUrl'];

        return new BaseHttpClient($this->client, $tarantoolApiUrl);
    }
}
