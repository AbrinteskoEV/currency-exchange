<?php

declare(strict_types=1);

namespace Infrastructure\Client;

use Domain\Dictionary\Config\FileConfigNameDictionary;
use GuzzleHttp\Client;
use Infrastructure\Service\Config\FileConfigGettingService;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;

class BinanceHttpClientFactory
{
    private Client $client;
    private FileConfigGettingService $fileConfigGettingService;
    private BinanceApiDataCachingService $binanceApiDataCachingService;

    /**
     * @param Client $client
     * @param FileConfigGettingService $fileConfigGettingService
     * @param BinanceApiDataCachingService $binanceApiDataCachingService
     */
    public function __construct(
        Client $client,
        FileConfigGettingService $fileConfigGettingService,
        BinanceApiDataCachingService $binanceApiDataCachingService
    ) {
        $this->client = $client;
        $this->fileConfigGettingService = $fileConfigGettingService;
        $this->binanceApiDataCachingService = $binanceApiDataCachingService;
    }

    /**
     * @return BinanceHttpClient
     */
    public function create(): BinanceHttpClient
    {
        $binanceApiConfig = $this->fileConfigGettingService
            ->getConfig(FileConfigNameDictionary::BINANCE_API_CONFIG);
        $binanceApiUrl = $binanceApiConfig['apiUrl'];
        $baseHttpClient = new BaseHttpClient($this->client, $binanceApiUrl);
        $requestSettings = $binanceApiConfig['requestSettings'];

        return new BinanceHttpClient(
            $baseHttpClient,
            $this->binanceApiDataCachingService,
            $requestSettings
        );
    }
}
